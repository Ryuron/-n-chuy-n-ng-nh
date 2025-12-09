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

    // Tạo bài kiểm tra khi user chọn môn học
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

            // Lấy danh sách câu hỏi ngẫu nhiên
            $questions = $this->questionModel->getQuestions($subjectId, $gradeLevel, $difficulty, 20);

            if (!$questions) {
                // fallback: lấy câu hỏi bất kỳ trong môn
                $questions = $this->questionModel->getQuestions($subjectId, null, $difficulty, 20);
                if (!$questions) {
                    die("Không có đủ câu hỏi phù hợp.");
                }
            }

            // Tạo bài kiểm tra mới
            $testId = $this->testModel->createTest($userId, $subjectId, $gradeLevel, $currentLevel, $questions);

            // Chuyển sang view làm bài
            header("Location: index.php?controller=quiz&action=take&test_id=$testId");
            exit;
        }
    }

    // Hiển thị bài kiểm tra
    public function take() {
        $testId = intval($_GET['test_id'] ?? 0);
        if (!$testId) die("Bài kiểm tra không tồn tại.");

        $stmt = $this->db->prepare("
            SELECT tq.*, q.Content, q.OptionA, q.OptionB, q.OptionC, q.OptionD 
            FROM TestQuestions tq
            JOIN Questions q ON tq.QuestionId = q.QuestionId
            WHERE tq.TestId=?
        ");
        $stmt->execute([$testId]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

                if ($isCorrect) $correct++;
                else $wrong++;
            }

            $total = count($testQuestions);
            $score = round(($correct / $total) * 10, 2); // điểm trên 10

            // Xác định trình độ
            $finalLevel = ($score < 5) ? 'Yếu' : (($score < 7) ? 'TB' : (($score < 9) ? 'Khá' : 'Giỏi'));

            // Cập nhật bảng Results
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
}
