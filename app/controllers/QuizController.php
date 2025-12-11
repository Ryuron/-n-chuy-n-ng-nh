<?php
require_once __DIR__ . '/../models/Question.php';
require_once __DIR__ . '/../models/Test.php';
require_once __DIR__ . '/../models/UserSubjectLevel.php';

class QuizController {
    private $db;
    private $questionModel;
    private $testModel;
    private $levelModel;

    public function __construct() {
        SessionHelper::start();
        $this->db = new PDO('mysql:host=localhost;dbname=AdaptiveQuizDB', 'root', '');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->questionModel = new Question($this->db);
        $this->testModel = new Test($this->db);
        $this->levelModel = new UserSubjectLevel($this->db);
    }
public function start() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userId = $_SESSION['user']['UserId'];
        $subjectId = intval($_POST['subject_id']);
        $gradeLevel = $_SESSION['user']['GradeLevel'] ?? 1;

        // Lấy trình độ hiện tại của học sinh với môn này
        $currentLevel = $this->levelModel->getCurrentLevel($userId, $subjectId) ?? 'TB';

        // Mapping CurrentLevel -> DifficultyLevel
        $levelMap = ['Yếu'=>'Dễ', 'TB'=>'TB', 'Khá'=>'Khó', 'Giỏi'=>'Khó'];
        $difficulty = $levelMap[$currentLevel] ?? 'TB';

        // Lấy danh sách câu hỏi cùng lớp
        $questions = $this->questionModel->getQuestions($subjectId, $gradeLevel, $difficulty, 20);

        // Nếu không có câu hỏi nào cùng lớp -> báo lỗi
        if (!$questions) {
            die("Không có câu hỏi nào cho môn này với lớp của bạn.");
        }

        // Tạo bài kiểm tra mới
        $testId = $this->testModel->createTest($userId, $subjectId, $gradeLevel, $currentLevel, $questions);

        header("Location: index.php?controller=quiz&action=take&test_id=$testId");
        exit;
    }
}

    // Hiển thị bài kiểm tra
    public function take() {
    $testId = intval($_GET['test_id'] ?? 0);
    if (!$testId) die("Bài kiểm tra không tồn tại.");

    $stmt = $this->db->prepare("
        SELECT tq.*, q.Content, q.OptionA, q.OptionB, q.OptionC, q.OptionD, q.CorrectAnswer
        FROM TestQuestions tq
        JOIN Questions q ON tq.QuestionId = q.QuestionId
        WHERE tq.TestId=?
    ");
    $stmt->execute([$testId]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Trộn đáp án
    foreach ($questions as &$q) {
        $options = [
            $q['OptionA'],
            $q['OptionB'],
            $q['OptionC'],
            $q['OptionD']
        ];

        // Trộn
        shuffle($options);

        // Gán lại các option để hiển thị
        $q['ShuffledOptions'] = $options;

        // Đảm bảo CorrectAnswer vẫn là nội dung đáp án đúng
        // (không cần thay đổi, vì đã lưu nội dung)
    }
    unset($q);

    require ROOT_PATH . "/app/views/quiz/take.php";
}


    // Nộp bài & chấm điểm
    public function submit() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $testId = intval($_GET['test_id'] ?? 0);
        if (!$testId) die("Bài kiểm tra không tồn tại.");

        $userId = $_SESSION['user']['UserId'];
        $answers = $_POST['answer'] ?? [];

        // Lấy tất cả câu hỏi trong bài
        $stmt = $this->db->prepare("
            SELECT tq.TestQuestionId, tq.QuestionId, q.CorrectAnswer
            FROM TestQuestions tq
            JOIN Questions q ON tq.QuestionId = q.QuestionId
            WHERE tq.TestId=?
        ");
        $stmt->execute([$testId]);
        $testQuestions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $correct = 0;
        $wrong = 0;

        foreach ($testQuestions as $q) {

            $userAnswer = $answers[$q['TestQuestionId']] ?? null;
            $isCorrect = ($userAnswer === $q['CorrectAnswer']) ? 1 : 0;

            // Cập nhật TestQuestions
            $update = $this->db->prepare("
                UPDATE TestQuestions 
                SET UserAnswer=?, IsCorrect=?, AnsweredAt=NOW()
                WHERE TestQuestionId=?
            ");
            $update->execute([$userAnswer, $isCorrect, $q['TestQuestionId']]);

            // --- NEW: Cập nhật thống kê câu hỏi ---
            $this->db->prepare("
                UPDATE Questions 
                SET AnswerCount = AnswerCount + 1,
                    CorrectCount = CorrectCount + ?
                WHERE QuestionId = ?
            ")->execute([$isCorrect, $q['QuestionId']]);

            if ($isCorrect) $correct++;
            else $wrong++;
        }

        $total = count($testQuestions);
        $score = round(($correct / $total) * 10, 2);

        // Xác định trình độ của bài thi
        $finalLevel = ($score < 5) ? 'Yếu' :
                      (($score < 7) ? 'TB' :
                      (($score < 9) ? 'Khá' : 'Giỏi'));

        // Lưu kết quả vào bảng Results
        $stmt = $this->db->prepare("
            INSERT INTO Results (TestId, UserId, SubjectId, TotalQuestions, CorrectAnswers, WrongAnswers, Score, FinalLevel)
            SELECT t.TestId, t.UserId, t.SubjectId, ?, ?, ?, ?, ?
            FROM Tests t WHERE t.TestId=?
        ");
        $stmt->execute([$total, $correct, $wrong, $score, $finalLevel, $testId]);

        // Cập nhật bảng Tests
        $updateTest = $this->db->prepare("
            UPDATE Tests SET Score=?, CurrentLevel=?, CompletedAt=NOW() WHERE TestId=?
        ");
        $updateTest->execute([$score, $finalLevel, $testId]);

        // =========================
        //      UPDATE ST + LEVEL
        // =========================

        // Lấy môn của bài test
        $testInfo = $this->db->prepare("SELECT SubjectId FROM Tests WHERE TestId=?");
        $testInfo->execute([$testId]);
        $subjectId = $testInfo->fetchColumn();

        // Lấy ST và CurrentLevel hiện tại
        $levelInfo = $this->levelModel->getLevelInfo($userId, $subjectId);
        $currentST = $levelInfo['ST'] ?? 0;
        $currentLevelDB = $levelInfo['CurrentLevel'] ?? 'Yếu';

        // Tính ST mới
        if ($score < 5) {
            $currentST -= 1;
        } elseif ($score > 7) {
            $currentST += 1;
        }

        // Xét tăng / giảm LEVEL
        $newLevel = $currentLevelDB;

        if ($currentST >= 10) {
            $newLevel = $this->levelModel->increaseLevel($currentLevelDB);
            $currentST = 0;
        } elseif ($currentST <= -10) {
            $newLevel = $this->levelModel->decreaseLevel($currentLevelDB);
            $currentST = 0;
        }

        // Lưu lại ST + Level
        $this->levelModel->updateSTAndLevel($userId, $subjectId, $currentST, $newLevel);

        // Chuyển sang trang kết quả
        header("Location: index.php?controller=quiz&action=result&test_id=$testId");
        exit;
    }
}



    // Xem kết quả bài kiểm tra
    public function result() {
        $testId = intval($_GET['test_id'] ?? 0);
        if (!$testId) die("Bài kiểm tra không tồn tại.");

        $stmt = $this->db->prepare("
            SELECT r.*, s.SubjectName
            FROM Results r
            JOIN Subjects s ON r.SubjectId = s.SubjectId
            WHERE r.TestId=?
        ");
        $stmt->execute([$testId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $this->db->prepare("
            SELECT tq.*, q.Content, q.OptionA, q.OptionB, q.OptionC, q.OptionD, q.CorrectAnswer
            FROM TestQuestions tq
            JOIN Questions q ON tq.QuestionId = q.QuestionId
            WHERE tq.TestId=?
        ");
        $stmt->execute([$testId]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require ROOT_PATH . "/app/views/quiz/result.php";
    }
    public function history() {
    $userId = $_SESSION['user']['UserId'];
    $history = $this->testModel->getUserHistory($userId);
    require ROOT_PATH . "/app/views/quiz/history.php";
}
public function quickErrorReport() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $questionId = intval($_POST['question_id']);
        $errorText = trim($_POST['error_text']);

        $stmt = $this->db->prepare("
            INSERT INTO QuestionErrorReports (QuestionId, ErrorText)
            VALUES (?, ?)
        ");
        $stmt->execute([$questionId, $errorText]);

        $testId = $_GET['test_id'];
        header("Location: index.php?controller=quiz&action=result&test_id=$testId&reported=1");
        exit;
    }
}

}
