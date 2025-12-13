<?php
require_once __DIR__ . '/../models/Question.php';
require_once __DIR__ . '/../models/Subject.php'; // thêm model môn học

class QuestionController
{
    private $db;
    private $questionModel;
    private $subjectModel;

    public function __construct()
    {
        SessionHelper::start(); // khởi tạo session

        // kiểm tra admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'Admin') {
            header("Location: index.php?controller=account&action=login");
            exit;
        }

        $this->db = new PDO('mysql:host=localhost;dbname=AdaptiveQuizDB', 'root', '');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->questionModel = new Question($this->db);
        $this->subjectModel  = new Subject($this->db);
    }

    public function index()
    {
        $questions = $this->questionModel->getAll();
        require 'app/views/question/index.php';
    }

    public function create()
    {
        // Lấy danh sách môn học từ DB
        $subjects = $this->subjectModel->getAll();
        require 'app/views/question/create.php';
    }

    public function store()
    {
        $this->questionModel->create($_POST);
        header("Location: index.php?controller=question&action=index");
        exit();
    }

    public function edit()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            die("Thiếu ID câu hỏi");
        }

        $question = $this->questionModel->getById($id);

        // Lấy danh sách môn học để hiển thị tên
        $subjects = $this->subjectModel->getAll();

        require 'app/views/question/edit.php';
    }

    public function update()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            die("Thiếu ID câu hỏi");
        }

        $this->questionModel->update($id, $_POST);
        header("Location: index.php?controller=question&action=index");
        exit();
    }

    public function delete()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            die("Thiếu ID câu hỏi");
        }

        $this->questionModel->delete($id);
        header("Location: index.php?controller=question&action=index");
        exit();
    }

    /**
     * Hiển thị form import câu hỏi từ file
     */
 public function import()
{
    $subjects = $this->subjectModel->getAll();
    require 'app/views/question/import.php';
}


    /**
     * Xử lý upload và import câu hỏi từ file
     */
    public function processImport()
{
    // 1. Kiểm tra file upload
    if (!isset($_FILES['question_file']) || $_FILES['question_file']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['import_error'] = 'Vui lòng chọn file để upload';
        header("Location: index.php?controller=question&action=import");
        exit();
    }

    // 2. Lấy default môn & lớp (BẮT BUỘC)
    $defaultSubjectId = $_POST['default_subject_id'] ?? null;
    $defaultGrade     = $_POST['default_grade_level'] ?? null;

    if (empty($defaultSubjectId) || empty($defaultGrade)) {
        $_SESSION['import_error'] = 'Vui lòng chọn môn học và khối lớp';
        header("Location: index.php?controller=question&action=import");
        exit();
    }

    // 3. Thông tin file
    $file     = $_FILES['question_file'];
    $fileTmp  = $file['tmp_name'];
    $fileExt  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    // 4. Kiểm tra định dạng
    if (!in_array($fileExt, ['csv', 'docx'])) {
        $_SESSION['import_error'] = 'Chỉ chấp nhận file CSV hoặc Word (.docx)';
        header("Location: index.php?controller=question&action=import");
        exit();
    }

    try {
        // 5. Parse file
        if ($fileExt === 'csv') {
            $questions = $this->parseCSV($fileTmp);
        } else {
            $questions = $this->parseWord($fileTmp);
        }

        if (empty($questions)) {
            throw new Exception('File không có dữ liệu hoặc định dạng không đúng');
        }

        // 6. Gán môn + lớp + tính độ khó
        foreach ($questions as &$q) {
            $q['subjectId']  = (int)$defaultSubjectId;
            $q['gradeLevel'] = (int)$defaultGrade;

            $q['difficultyLevel'] = $this->calculateDifficulty(
                $q['content'],
                $q['subjectId']
            );
            
        }
        unset($q);

        // 7. Insert DB
        $result = $this->questionModel->bulkInsert($questions);

        // 8. Thông báo
        $message = "Đã thêm thành công {$result['success']} câu hỏi.";
        if ($result['failed'] > 0) {
            $message .= " Có {$result['failed']} câu hỏi thất bại.";
            if (!empty($result['errors'])) {
                $message .= "<br><br><strong>Chi tiết lỗi:</strong><br>"
                         . implode("<br>", array_slice($result['errors'], 0, 10));
            }
        }

        $_SESSION[$result['success'] > 0 ? 'import_success' : 'import_error'] = $message;
        header("Location: index.php?controller=question&action=import");
        exit();

    } catch (Exception $e) {
        $_SESSION['import_error'] = 'Lỗi xử lý file: ' . $e->getMessage();
        header("Location: index.php?controller=question&action=import");
        exit();
    }
}


    /**
     * Parse file CSV
     * @param string $filePath Đường dẫn file
     * @return array Mảng các câu hỏi
     */
private function parseCSV($filePath, $defaultSubjectId = null, $defaultGrade = null)
    {
        $questions = [];

        // Thử nhiều encoding khác nhau
        $encodings = ['UTF-8', 'Windows-1252', 'ISO-8859-1'];
        $content = file_get_contents($filePath);

        // Phát hiện encoding
        $encoding = mb_detect_encoding($content, $encodings, true);
        if ($encoding && $encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
            file_put_contents($filePath, $content);
        }

        if (($handle = fopen($filePath, 'r')) !== false) {
            $rowIndex = 0;
            while (($data = fgetcsv($handle, 10000, ',')) !== false) {
                $rowIndex++;

                // Skip empty rows
                if (empty(array_filter($data))) {
                    continue;
                }

                // Bỏ qua dòng đầu nếu là tiêu đề (kiểm tra xem có phải chữ không)
                if ($rowIndex === 1 && !is_numeric($data[6] ?? '')) {
                    continue;
                }

                // Phải có đủ 9 cột
                if (count($data) < 9) {
                    continue;
                }

                // Xử lý đáp án đúng: nếu là A/B/C/D thì chuyển thành nội dung tương ứng
                $correctAnswer = trim($data[5]);
                if (in_array(strtoupper($correctAnswer), ['A', 'B', 'C', 'D'])) {
                    $answerMap = [
                        'A' => trim($data[1]),
                        'B' => trim($data[2]),
                        'C' => trim($data[3]),
                        'D' => trim($data[4])
                    ];
                    $correctAnswer = $answerMap[strtoupper($correctAnswer)];
                }

                $questions[] = [
                    'content' => trim($data[0]),
                    'optionA' => trim($data[1]),
                    'optionB' => trim($data[2]),
                    'optionC' => trim($data[3]),
                    'optionD' => trim($data[4]),
                    'correctAnswer' => $correctAnswer,
                    'subjectId' => !empty($data[6]) ? (int)$data[6] : (int)$defaultSubjectId,
                    'gradeLevel' => !empty($data[7]) ? (int)$data[7] : (int)$defaultGrade,
                ];
            }
            fclose($handle);
        }

        return $questions;
    }

    /**
     * Parse file Excel - Chuyển đổi Excel thành CSV rồi parse
     * @param string $filePath Đường dẫn file
     * @return array Mảng các câu hỏi
     */
    private function parseExcel($filePath)
    {
        // Cách đơn giản: yêu cầu user lưu Excel thành CSV
        throw new Exception('File Excel chưa được hỗ trợ trực tiếp. Vui lòng mở file Excel và "Lưu dưới dạng CSV UTF-8" rồi upload lại.');
    }

    /**
     * Parse file Word (.docx)
     * @param string $filePath Đường dẫn file
     * @return array Mảng các câu hỏi
     */
private function parseWord($filePath, $defaultSubjectId = null, $defaultGrade = null)
{
    if (!class_exists('ZipArchive')) {
        throw new Exception('ZipArchive không được cài đặt.');
    }

    $zip = new ZipArchive();
    if ($zip->open($filePath) !== true) {
        throw new Exception('Không thể mở file .docx');
    }

    $content = $zip->getFromName('word/document.xml');
    $zip->close();

    if (!$content) {
        throw new Exception('Không tìm thấy document.xml');
    }

    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($content);
    if ($xml === false) {
        throw new Exception('Lỗi parse XML');
    }

    $xml->registerXPathNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

    $lines = [];
    foreach ($xml->xpath('//w:p') as $p) {
        $text = '';
        foreach ($p->xpath('.//w:t') as $t) {
            $text .= (string)$t;
        }
        if (trim($text) !== '') {
            $lines[] = trim($text);
        }
    }

    if (empty($lines)) {
        throw new Exception('File Word không có nội dung');
    }

    // ❌ KHÔNG LẤY $_POST Ở ĐÂY
    // ✅ DÙNG THAM SỐ TRUYỀN VÀO

    return $this->parseWordLines($lines, $defaultSubjectId, $defaultGrade);
}


    /**
     * Parse các dòng text từ Word thành câu hỏi
     * Định dạng mỗi câu hỏi:
     * - Dòng 1: Nội dung câu hỏi
     * - Dòng 2: A. Đáp án A
     * - Dòng 3: B. Đáp án B
     * - Dòng 4: C. Đáp án C
     * - Dòng 5: D. Đáp án D
     * - Dòng 6: Đáp án: X (hoặc ĐA: X, hoặc DA: X)
     * - Dòng 7: Môn: X | Lớp: X | Độ khó: X (hoặc từng dòng riêng)
     * - Dòng trống để phân cách câu hỏi
     * 
     * HOẶC định dạng dạng bảng (tab-separated):
     * Câu hỏi [TAB] A [TAB] B [TAB] C [TAB] D [TAB] Đáp án [TAB] Môn [TAB] Lớp [TAB] Độ khó
     */
private function parseWordLines($lines, $defaultSubjectId, $defaultGrade)
{
    $questions = [];
    $i = 0;

    while ($i < count($lines)) {

        // 1. Lấy câu hỏi
        $questionText = trim($lines[$i]);

        if (preg_match('/^(A\.|B\.|C\.|D\.|Đáp án)/ui', $questionText)) {
            $i++;
            continue;
        }

        $currentQuestion = [
            'content'   => $questionText,
            'subjectId' => $defaultSubjectId,
            'gradeLevel'=> $defaultGrade
        ];

        $i++;

        // 2. Lấy 4 đáp án
        $options = [];
        for ($j = 0; $j < 4 && $i < count($lines); $j++, $i++) {
            $opt = preg_replace('/^[A-D][\.\)\:]\s*/i', '', trim($lines[$i]));
            $options[] = $opt;
        }

        if (count($options) < 4) {
            continue;
        }

        $currentQuestion['optionA'] = $options[0];
        $currentQuestion['optionB'] = $options[1];
        $currentQuestion['optionC'] = $options[2];
        $currentQuestion['optionD'] = $options[3];

        // 3. Đọc đáp án
        if ($i < count($lines)) {
            $answerLine = trim($lines[$i]);

            if (preg_match('/(đáp\s*án|đa|da)\s*[:：]?\s*([A-D])/ui', $answerLine, $m)) {
                $map = [
                    'A' => $options[0],
                    'B' => $options[1],
                    'C' => $options[2],
                    'D' => $options[3],
                ];
                $currentQuestion['correctAnswer'] = $map[strtoupper($m[2])];
            } else {
                $i++;
                continue;
            }
            $i++;
        }

        // 4. Thêm câu hỏi
        $questions[] = $currentQuestion;
    }

    return $questions;
}


    /**
     * Tạo câu hỏi từ mảng parts (tab hoặc pipe separated)
     */
private function createQuestionFromParts($parts, $defaultSubjectId = null, $defaultGrade = null)

    {
        // Xử lý đáp án đúng
        $correctAnswer = trim($parts[5]);
        if (in_array(strtoupper($correctAnswer), ['A', 'B', 'C', 'D'])) {
            $answerMap = [
                'A' => trim($parts[1]),
                'B' => trim($parts[2]),
                'C' => trim($parts[3]),
                'D' => trim($parts[4])
            ];
            $correctAnswer = $answerMap[strtoupper($correctAnswer)];
        }

            return [
                'content' => trim($parts[0]),
                'optionA' => trim($parts[1]),
                'optionB' => trim($parts[2]),
                'optionC' => trim($parts[3]),
                'optionD' => trim($parts[4]),
                'correctAnswer' => $correctAnswer,
                'subjectId' => !empty($parts[6]) ? (int)$parts[6] : (int)$defaultSubjectId,
                'gradeLevel' => !empty($parts[7]) ? (int)$parts[7] : (int)$defaultGrade,
                'difficultyLevel' => trim($parts[8])
            ];
    }

    /**
     * Tạo và tải file CSV mẫu
     */
    public function downloadTemplate()
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="mau_cau_hoi.csv"');

        // Thêm BOM để Excel hiển thị đúng tiếng Việt
        echo "\xEF\xBB\xBF";

        $output = fopen('php://output', 'w');

        // Header
        fputcsv($output, [
            'Nội dung câu hỏi',
            'Đáp án A',
            'Đáp án B',
            'Đáp án C',
            'Đáp án D',
            'Đáp án đúng',
            'Mã môn học',
            'Khối lớp',
            'Độ khó'
        ]);

        // Dữ liệu mẫu
        $samples = [
            [
                '2 + 2 = ?',
                '3',
                '4',
                '5',
                '6',
                'B',
                '1',
                '1',
                'Dễ'
            ],
            [
                'Thủ đô của Việt Nam là?',
                'Hà Nội',
                'TP. Hồ Chí Minh',
                'Đà Nẵng',
                'Huế',
                'Hà Nội',
                '2',
                '3',
                'Dễ'
            ],
            [
                '5 x 7 = ?',
                '30',
                '35',
                '40',
                '45',
                'B',
                '1',
                '2',
                'TB'
            ]
        ];

        foreach ($samples as $sample) {
            fputcsv($output, $sample);
        }

        fclose($output);
        exit();
    }

    /**
     * Tạo và tải file Word mẫu (.docx)
     */
    public function downloadWordTemplate()
    {
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="mau_cau_hoi.docx"');

        // Tạo file .docx đơn giản bằng cách tạo cấu trúc ZIP
        $zip = new ZipArchive();
        $tempFile = tempnam(sys_get_temp_dir(), 'docx');

        if ($zip->open($tempFile, ZipArchive::CREATE) === true) {
            // [Content_Types].xml
            $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
</Types>';
            $zip->addFromString('[Content_Types].xml', $contentTypes);

            // _rels/.rels
            $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
</Relationships>';
            $zip->addFromString('_rels/.rels', $rels);

            // word/document.xml với nội dung mẫu
            $sampleContent = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
<w:body>
<w:p><w:r><w:t>HƯỚNG DẪN NHẬP CÂU HỎI TỪ FILE WORD</w:t></w:r></w:p>
<w:p><w:r><w:t></w:t></w:r></w:p>
<w:p><w:r><w:t>Cách 1: Định dạng văn bản nhiều dòng</w:t></w:r></w:p>
<w:p><w:r><w:t>=====================================</w:t></w:r></w:p>
<w:p><w:r><w:t></w:t></w:r></w:p>
<w:p><w:r><w:t>2 + 2 = ?</w:t></w:r></w:p>
<w:p><w:r><w:t>A. 3</w:t></w:r></w:p>
<w:p><w:r><w:t>B. 4</w:t></w:r></w:p>
<w:p><w:r><w:t>C. 5</w:t></w:r></w:p>
<w:p><w:r><w:t>D. 6</w:t></w:r></w:p>
<w:p><w:r><w:t>Đáp án: B</w:t></w:r></w:p>
<w:p><w:r><w:t>Môn: 1</w:t></w:r></w:p>
<w:p><w:r><w:t>Lớp: 1</w:t></w:r></w:p>
<w:p><w:r><w:t>Độ khó: Dễ</w:t></w:r></w:p>
<w:p><w:r><w:t></w:t></w:r></w:p>
<w:p><w:r><w:t>Thủ đô của Việt Nam là gì?</w:t></w:r></w:p>
<w:p><w:r><w:t>A. Hà Nội</w:t></w:r></w:p>
<w:p><w:r><w:t>B. TP. Hồ Chí Minh</w:t></w:r></w:p>
<w:p><w:r><w:t>C. Đà Nẵng</w:t></w:r></w:p>
<w:p><w:r><w:t>D. Huế</w:t></w:r></w:p>
<w:p><w:r><w:t>Đáp án: A</w:t></w:r></w:p>
<w:p><w:r><w:t>Môn: 2</w:t></w:r></w:p>
<w:p><w:r><w:t>Lớp: 3</w:t></w:r></w:p>
<w:p><w:r><w:t>Độ khó: Dễ</w:t></w:r></w:p>
<w:p><w:r><w:t></w:t></w:r></w:p>
<w:p><w:r><w:t></w:t></w:r></w:p>
<w:p><w:r><w:t>Cách 2: Định dạng dạng bảng (dùng Tab hoặc |)</w:t></w:r></w:p>
<w:p><w:r><w:t>================================================</w:t></w:r></w:p>
<w:p><w:r><w:t></w:t></w:r></w:p>
<w:p><w:r><w:t>5 x 7 = ?	30	35	40	45	B	1	2	TB</w:t></w:r></w:p>
<w:p><w:r><w:t>Ai là tác giả truyện Kiều?|Nguyễn Du|Hồ Chí Minh|Nguyễn Trãi|Lý Thái Tổ|A|3|9|TB</w:t></w:r></w:p>
<w:p><w:r><w:t></w:t></w:r></w:p>
<w:p><w:r><w:t></w:t></w:r></w:p>
<w:p><w:r><w:t>LƯU Ý:</w:t></w:r></w:p>
<w:p><w:r><w:t>- Mỗi câu hỏi cách nhau bằng 1 dòng trống</w:t></w:r></w:p>
<w:p><w:r><w:t>- Đáp án có thể ghi A/B/C/D hoặc nội dung đầy đủ</w:t></w:r></w:p>
<w:p><w:r><w:t>- Mã môn học phải tồn tại trong database</w:t></w:r></w:p>
<w:p><w:r><w:t>- Độ khó: Dễ, TB, Khó</w:t></w:r></w:p>
</w:body>
</w:document>';
            $zip->addFromString('word/document.xml', $sampleContent);

            $zip->close();

            // Đọc và xuất file
            readfile($tempFile);
            unlink($tempFile);
        }

        exit();
    }
    public function errorDetail() {
    $id = intval($_GET['id'] ?? 0);
    if (!$id) {
        die("ID câu hỏi không hợp lệ.");
    }

    // Lấy nội dung câu hỏi
    $stmt = $this->db->prepare("
        SELECT * FROM Questions WHERE QuestionId = ?
    ");
    $stmt->execute([$id]);
    $question = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$question) {
        die("Câu hỏi không tồn tại.");
    }

    // Lấy danh sách lỗi
    $stmt = $this->db->prepare("
        SELECT * FROM QuestionErrorReports 
        WHERE QuestionId = ?
        ORDER BY CreatedAt DESC
    ");
    $stmt->execute([$id]);
    $errors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    require ROOT_PATH . '/app/views/questions/error_detail.php';
}
private function calculateDifficulty($content, $subjectId)
{
    // L = số từ
    $L = str_word_count(strip_tags($content));

    // D = số dữ kiện tìm được theo môn
    $stmt = $this->db->prepare("
        SELECT FactText, Weight 
        FROM SubjectFacts 
        WHERE SubjectId = ?
    ");
    $stmt->execute([$subjectId]);
    $facts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $D = 0;
    foreach ($facts as $fact) {
        if (stripos($content, $fact['FactText']) !== false) {
            $D += ($fact['Weight'] ?? 1);
        }
    }

    // hệ số thực nghiệm (bạn chỉnh thoải mái)
    $a = 1;
    $b = 1;

    $score = ($a * $D + $b) > 0 ? $L / ($a * $D + $b) : $L;

    // Map score → độ khó
    if ($score <= 1.5) return 'Dễ';
    if ($score <= 3) return 'TB';
    return 'Khó';
}
public function previewDifficulty()
{
    $data = $this->questionModel->getDifficultyAnalysis();

    foreach ($data as &$q) {
        if ($q['AnswerCount'] < 5) {
            $q['Suggested'] = null;
            continue;
        }

        $rate = $q['CorrectRate'];

        if ($rate >= 0.8) {
            $q['Suggested'] = 'Dễ';
        } elseif ($rate >= 0.5) {
            $q['Suggested'] = 'TB';
        } else {
            $q['Suggested'] = 'Khó';
        }
    }

    require 'app/views/question/previewDifficulty.php';
}

private function suggestDifficulty($rate)
{
    if ($rate >= 0.8) return 'Dễ';
    if ($rate >= 0.4) return 'TB';
    return 'Khó';
}
public function applyDifficulty()
{
    if (!isset($_POST['apply'])) {
        header("Location: index.php?controller=question&action=previewDifficulty");
        exit();
    }

    foreach ($_POST['apply'] as $questionId => $newLevel) {
        $this->questionModel->updateDifficulty($questionId, $newLevel);
    }

    $_SESSION['import_success'] = 'Đã cập nhật độ khó các câu hỏi đã chọn';
    header("Location: index.php?controller=question&action=index");
}


}
