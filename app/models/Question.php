<?php
class Question
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO Questions 
            (Content, OptionA, OptionB, OptionC, OptionD, CorrectAnswer, SubjectId, GradeLevel, DifficultyLevel) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['content'],
            $data['optionA'],
            $data['optionB'],
            $data['optionC'],
            $data['optionD'],
            $data['correctAnswer'],
            $data['subjectId'],
            $data['gradeLevel'],
            $data['difficultyLevel']
        ]);
    }

    public function getAll()
    {
        $sql = "SELECT q.*, s.SubjectName 
                FROM Questions q
                JOIN Subjects s ON q.SubjectId = s.SubjectId
                ORDER BY q.CreatedAt DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT q.*, s.SubjectName 
                                    FROM Questions q
                                    JOIN Subjects s ON q.SubjectId = s.SubjectId
                                    WHERE q.QuestionId = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare("UPDATE Questions SET 
            Content=?, OptionA=?, OptionB=?, OptionC=?, OptionD=?, CorrectAnswer=?, SubjectId=?, GradeLevel=?, DifficultyLevel=?
            WHERE QuestionId=?");
        return $stmt->execute([
            $data['content'],
            $data['optionA'],
            $data['optionB'],
            $data['optionC'],
            $data['optionD'],
            $data['correctAnswer'],
            $data['subjectId'],
            $data['gradeLevel'],
            $data['difficultyLevel'],
            $id
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM Questions WHERE QuestionId = ?");
        return $stmt->execute([$id]);
    }

    // Lấy ngẫu nhiên N câu hỏi
    public function getRandom($limit = 40)
    {
        $stmt = $this->db->prepare("SELECT q.*, s.SubjectName 
                                    FROM Questions q
                                    JOIN Subjects s ON q.SubjectId = s.SubjectId
                                    ORDER BY RAND() LIMIT :limit");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy ngẫu nhiên N câu hỏi theo môn học
    public function getRandomBySubject($subjectId, $limit = 40)
    {
        $stmt = $this->db->prepare("SELECT q.*, s.SubjectName 
                                    FROM Questions q
                                    JOIN Subjects s ON q.SubjectId = s.SubjectId
                                    WHERE q.SubjectId = :subjectId
                                    ORDER BY RAND() LIMIT :limit");
        $stmt->bindValue(':subjectId', (int)$subjectId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy ngẫu nhiên N câu hỏi theo môn, lớp, trình độ học sinh
    /**
     * Lấy ngẫu nhiên N câu hỏi theo môn, lớp, và trình độ học sinh (có logic dự phòng).
     *
     * @param int $subjectId
     * @param int $gradeLevel
     * @param string $currentLevel Cấp độ ENUM của học sinh ('Yếu', 'TB', 'Khá', 'Giỏi')
     * @param int $limit Số lượng câu hỏi mong muốn
     * @return array Danh sách câu hỏi
     */
    public function getRandomBySubjectGradePerformance($subjectId, $gradeLevel, $currentLevel, $limit = 40)
    {
        $questions = [];
        $requiredCount = $limit;

        // 1. Ánh xạ cấp độ và tìm cấp độ dự phòng
        $difficultyMap = $this->mapUserLevelToQuestionDifficulty($currentLevel);
        $primaryDifficulty = $difficultyMap['primary']; // Ví dụ: 'Khó'

        // 2. Ưu tiên lấy câu hỏi ở cấp độ chính
        $questions = $this->getQuestionsByFilter(
            $subjectId, 
            $gradeLevel, 
            $primaryDifficulty, 
            $requiredCount
        );
        $count = count($questions);
        $remaining = $requiredCount - $count;

        // 3. Logic Dự phòng (Fallback) nếu thiếu
        if ($remaining > 0) {
            $fallbackDifficulty = $difficultyMap['fallback']; // Ví dụ: 'TB'

            if ($fallbackDifficulty) {
                // Thử lấy thêm ở cấp độ dự phòng
                $fallbackQuestions = $this->getQuestionsByFilter(
                    $subjectId, 
                    $gradeLevel, 
                    $fallbackDifficulty, 
                    $remaining
                );
                $questions = array_merge($questions, $fallbackQuestions);
                $count = count($questions);
                $remaining = $requiredCount - $count;
            }
        }
        
        // 4. Dự phòng cuối cùng: lấy ngẫu nhiên bất kể độ khó (để đảm bảo không lỗi)
        if ($remaining > 0) {
            $randomQuestions = $this->getRandomQuestionsBySubjectGrade($subjectId, $gradeLevel, $remaining);
            $questions = array_merge($questions, $randomQuestions);
        }

        // Loại bỏ trùng lặp và giới hạn số lượng cuối cùng
        $uniqueQuestions = [];
        $seenIds = [];
        foreach ($questions as $q) {
            if (!in_array($q['QuestionId'], $seenIds)) {
                $uniqueQuestions[] = $q;
                $seenIds[] = $q['QuestionId'];
            }
        }

        return array_slice($uniqueQuestions, 0, $limit);
    }
private function mapUserLevelToQuestionDifficulty(string $userLevel): array 
    {
        // Yếu: Ưu tiên Dễ, dự phòng TB
        // TB: Ưu tiên TB, dự phòng Dễ
        // Khá: Ưu tiên TB, dự phòng Khó (thử thách)
        // Giỏi: Ưu tiên Khó, dự phòng TB
        return match ($userLevel) {
            'Yếu' => ['primary' => 'Dễ', 'fallback' => 'TB'],
            'TB'  => ['primary' => 'TB', 'fallback' => 'Dễ'], 
            'Khá' => ['primary' => 'TB', 'fallback' => 'Khó'],
            'Giỏi' => ['primary' => 'Khó', 'fallback' => 'TB'],
            default => ['primary' => 'TB', 'fallback' => 'Dễ'], 
        };
    }
    private function getQuestionsByFilter($subjectId, $gradeLevel, $difficulty, $limit)
    {
        if ($limit <= 0) return [];
        $sql = "SELECT q.QuestionId, q.Content, q.OptionA, q.OptionB, q.OptionC, q.OptionD, q.CorrectAnswer, q.DifficultyLevel
                FROM Questions q
                WHERE q.SubjectId = :subjectId AND q.GradeLevel = :gradeLevel AND q.DifficultyLevel = :difficulty
                ORDER BY RAND() LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':subjectId', (int)$subjectId, PDO::PARAM_INT);
        $stmt->bindValue(':gradeLevel', (int)$gradeLevel, PDO::PARAM_INT);
        $stmt->bindValue(':difficulty', $difficulty, PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getRandomQuestionsBySubjectGrade($subjectId, $gradeLevel, $limit)
    {
        if ($limit <= 0) return [];
        $sql = "SELECT q.QuestionId, q.Content, q.OptionA, q.OptionB, q.OptionC, q.OptionD, q.CorrectAnswer, q.DifficultyLevel
                FROM Questions q
                WHERE q.SubjectId = :subjectId AND q.GradeLevel = :gradeLevel
                ORDER BY RAND() LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':subjectId', (int)$subjectId, PDO::PARAM_INT);
        $stmt->bindValue(':gradeLevel', (int)$gradeLevel, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Thêm nhiều câu hỏi cùng lúc (bulk insert)
     * @param array $questions Mảng các câu hỏi, mỗi phần tử là array với các key: content, optionA, optionB, optionC, optionD, correctAnswer, subjectId, gradeLevel, difficultyLevel
     * @return array ['success' => số câu thêm thành công, 'failed' => số câu thất bại, 'errors' => mảng lỗi]
     */
    public function bulkInsert($questions)
    {
        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($questions as $index => $data) {
            try {
                // Validate dữ liệu
                $validation = $this->validateQuestionData($data);
                if ($validation !== true) {
                    $failed++;
                    $errors[] = "Dòng " . ($index + 1) . ": " . $validation;
                    continue;
                }

                // Insert câu hỏi
                $stmt = $this->db->prepare("INSERT INTO Questions 
                    (Content, OptionA, OptionB, OptionC, OptionD, CorrectAnswer, SubjectId, GradeLevel, DifficultyLevel) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $result = $stmt->execute([
                    $data['content'],
                    $data['optionA'],
                    $data['optionB'],
                    $data['optionC'],
                    $data['optionD'],
                    $data['correctAnswer'],
                    $data['subjectId'],
                    $data['gradeLevel'],
                    $data['difficultyLevel']
                ]);

                if ($result) {
                    $success++;
                } else {
                    $failed++;
                    $errors[] = "Dòng " . ($index + 1) . ": Không thể thêm vào database";
                }
            } catch (PDOException $e) {
                $failed++;
                $errors[] = "Dòng " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        return [
            'success' => $success,
            'failed' => $failed,
            'errors' => $errors
        ];
    }

    /**
     * Validate dữ liệu câu hỏi
     * @param array $data
     * @return true|string Trả về true nếu hợp lệ, ngược lại trả về thông báo lỗi
     */
    private function validateQuestionData($data)
    {
        // Kiểm tra các trường bắt buộc
        $required = ['content', 'optionA', 'optionB', 'optionC', 'optionD', 'correctAnswer', 'subjectId', 'gradeLevel', 'difficultyLevel'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                return "Thiếu trường bắt buộc: $field";
            }
        }

        // Validate SubjectId
        if (!is_numeric($data['subjectId']) || $data['subjectId'] <= 0) {
            return "SubjectId không hợp lệ";
        }

        // Kiểm tra SubjectId có tồn tại không
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM Subjects WHERE SubjectId = ?");
        $stmt->execute([$data['subjectId']]);
        if ($stmt->fetchColumn() == 0) {
            return "SubjectId {$data['subjectId']} không tồn tại trong database";
        }

        // Validate GradeLevel
        if (!is_numeric($data['gradeLevel']) || $data['gradeLevel'] < 1 || $data['gradeLevel'] > 12) {
            return "GradeLevel phải từ 1 đến 12";
        }

        // Validate DifficultyLevel
        $validDifficulty = ['Dễ', 'TB', 'Khó'];
        if (!in_array($data['difficultyLevel'], $validDifficulty)) {
            return "DifficultyLevel phải là: Dễ, TB hoặc Khó";
        }

        // Validate CorrectAnswer
        $options = [$data['optionA'], $data['optionB'], $data['optionC'], $data['optionD']];
        if (!in_array($data['correctAnswer'], $options)) {
            return "CorrectAnswer phải khớp với một trong các đáp án A, B, C, D";
        }

        return true;
    }
     // Lấy câu hỏi theo môn, lớp, trình độ, số lượng
 public function getQuestions($subjectId, $gradeLevel = null, $difficulty = null, $limit = 20) {
        $limit = intval($limit); // đảm bảo là số nguyên
        $params = [];
        $conditions = [];

        if ($subjectId !== null) {
            $conditions[] = "SubjectId = ?";
            $params[] = $subjectId;
        }
        if ($gradeLevel !== null) {
            $conditions[] = "GradeLevel = ?";
            $params[] = $gradeLevel;
        }
        if ($difficulty !== null) {
            $conditions[] = "DifficultyLevel = ?";
            $params[] = $difficulty;
        }

        $where = count($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

        $sql = "SELECT * FROM Questions $where ORDER BY RAND() LIMIT $limit";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDifficultyAnalysis()
{
    $sql = "
        SELECT
            QuestionId,
            Content,
            DifficultyLevel,
            AnswerCount,
            CorrectCount,
            CASE
                WHEN AnswerCount = 0 THEN NULL
                ELSE CorrectCount / AnswerCount
            END AS CorrectRate
        FROM questions
    ";

    return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}
public function updateDifficulty($questionId, $newLevel)
{
    $stmt = $this->db->prepare("
        UPDATE questions
        SET DifficultyLevel = ?
        WHERE QuestionId = ?
    ");
    return $stmt->execute([$newLevel, $questionId]);
}



}
    

