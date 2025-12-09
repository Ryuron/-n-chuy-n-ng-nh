<?php
require_once __DIR__ . '/../models/Question.php';
require_once __DIR__ . '/../models/Subject.php';
require_once __DIR__ . '/../models/Quiz.php';
require_once __DIR__ . '/../models/UserSubjectLevelModel.php';
// require_once __DIR__ . '/LevelController.php'; // Cần khi gọi submit

class QuizController
{
    private $db;
    private $questionModel;
    private $subjectModel;
    private $quizModel;
    private $userSubjectLevelModel;

    public function __construct()
    {
        // Khởi tạo Session
        if (!class_exists('SessionHelper') || !SessionHelper::start()) {
            // Fallback nếu SessionHelper không tồn tại
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
        }

        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=account&action=login");
            exit;
        }

        $this->db = new PDO('mysql:host=localhost;dbname=AdaptiveQuizDB', 'root', '');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->questionModel = new Question($this->db);
        $this->subjectModel = new Subject($this->db); // Cần nếu SubjectModel có hàm getById
        $this->quizModel = new Quiz($this->db);
        $this->userSubjectLevelModel = new UserSubjectLevelModel($this->db);
    }

    // ===================== START QUIZ ======================
    public function start()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
             header("Location: index.php"); // Quay về trang chủ nếu không phải POST
             exit();
        }

        $user = $_SESSION['user'];
        $userId = $user['UserId'];
        $subjectId = $_POST['subject_id'] ?? null;
        $totalQuestions = 40; 

        if (!$subjectId) {
             die("Vui lòng chọn môn học!");
        }

        $gradeLevel = $user['GradeLevel'] ?? 1;

        // 1. Lấy/Khởi tạo cấp độ hiện tại của học sinh
        $currentLevel = $this->userSubjectLevelModel->getLevel($userId, $subjectId);
        if ($currentLevel === null) {
            $this->userSubjectLevelModel->initLevel($userId, $subjectId);
            $currentLevel = 1; // Mặc định Level 1 ('Yếu')
        }
        
        // Map số level (1, 2, 3) sang ENUM ('Yếu', 'TB', 'Giỏi') cho Model Question (vì Model Question dùng ENUM)
        $currentLevelEnum = $this->mapLevelToEnum($currentLevel);

        // 2. Lấy câu hỏi (có logic dự phòng)
        $questions = $this->questionModel->getRandomBySubjectGradePerformance(
            $subjectId,
            $gradeLevel,
            $currentLevelEnum, // Truyền ENUM
            $totalQuestions
        );

        if (empty($questions)) {
             die("Chưa có câu hỏi nào phù hợp cho môn học và khối lớp này!");
        }

        // 3. Tạo Quiz và lưu các câu hỏi
        $testId = $this->quizModel->createTempTest(
            $subjectId,
            $gradeLevel,
            $userId,
            $questions
        );

        // 4. Chuyển hướng đến trang làm bài
        header("Location: index.php?controller=quiz&action=take&test_id=$testId");
        exit();
    }
    
    // ===================== TAKE QUIZ ======================
    public function take()
    {
        $userId = $_SESSION['user']['UserId'];
        $testId = $_GET['test_id'] ?? null;

        if (!$testId) {
             header("Location: index.php?controller=user"); 
             exit();
        }

        $test = $this->quizModel->getTestById($testId);
        $questions = $this->quizModel->getTestQuestions($testId);
        
        // Kiểm tra
        if (!$test || $test['CreatedBy'] != $userId) {
            die("Bài kiểm tra không hợp lệ hoặc không thuộc về bạn.");
        }
        
        // Lấy thông tin môn học và level để hiển thị
        $subject = $this->subjectModel->getById($test['SubjectId']);
        $currentLevel = $this->userSubjectLevelModel->getLevel($userId, $test['SubjectId']);

        $test['SubjectName'] = $subject['SubjectName'] ?? 'Unknown';
        $test['CurrentLevelText'] = $this->mapLevelToDifficulty($currentLevel); 
        require 'app/views/quiz/take.php'; 
    }private function mapLevelToEnum($level)
    {
        return match ((int)$level) {
            1 => 'Yếu',
            2 => 'TB',
            3 => 'Giỏi',
            default => 'Yếu'
        };
    }
    // Map Level → DifficultyLevel
    private function mapLevelToDifficulty($level)
    {
        return match ($level) {
            1 => 'Dễ',
            2 => 'TB',
            3 => 'Khó',
            default => 'Dễ'
        };
    }

    // ===================== SUBMIT QUIZ ======================
    public function submit()
    {
        $userId = $_SESSION['user']['UserId'];
        $testId = $_POST['test_id'] ?? null;
        $answers = $_POST['answers'] ?? [];

        if (!$testId || empty($answers)) die("Thiếu dữ liệu nộp bài!");

        $questions = $this->quizModel->getTestQuestions($testId);

        $correctCount = 0;
        $resultDetails = [];

        foreach ($questions as $q) {
            $qid = $q['QuestionId'];

            $userAnswer = isset($answers[$qid]) ? trim($answers[$qid]) : '';
            $correctAnswer = trim($q['CorrectAnswer']);

            $options = [
                'A' => $q['OptionA'],
                'B' => $q['OptionB'],
                'C' => $q['OptionC'],
                'D' => $q['OptionD']
            ];

            $userAnswerContent = $options[$userAnswer] ?? '';
            $correctAnswerContent = trim($correctAnswer);

            $isCorrect = ($userAnswerContent === $correctAnswerContent);
            if ($isCorrect) $correctCount++;

            $resultDetails[] = [
                'QuestionId' => $qid,
                'UserAnswer' => $userAnswer,
                'UserAnswerContent' => $userAnswerContent,
                'IsCorrect' => $isCorrect,
                'CorrectAnswer' => $correctAnswer,
                'CorrectAnswerContent' => $correctAnswerContent,
                'Options' => $options
            ];
        }

        $score = round(($correctCount / count($questions)) * 100, 2);

        $quizResultId = $this->quizModel->saveQuizResult($testId, $userId, $score);

        foreach ($resultDetails as $detail) {
            $this->quizModel->saveQuizResultDetail(
                $quizResultId,
                $detail['QuestionId'],
                $detail['UserAnswer'],
                $detail['IsCorrect']
            );
        }

        // Cập nhật tiến độ Level
        require_once __DIR__ . '/LevelController.php';
        $levelController = new LevelController($this->db);

        $levelInfo = $levelController->updateLevelProgress($userId, $score);

        $_SESSION['user']['CurrentLevel'] = $levelInfo['CurrentLevel'];
        $_SESSION['user']['LevelProgress'] = $levelInfo['LevelProgress'];

        $_SESSION['last_result_details'] = $resultDetails;
        $_SESSION['last_score'] = $score;

        header("Location: index.php?controller=quiz&action=result&quizResultId=$quizResultId");
        exit();
    }

    // ===================== RESULT ======================
    public function result()
    {
        $quizResultId = $_GET['quizResultId'] ?? null;
        if (!$quizResultId) die("Thiếu QuizResultId");

        $details = $this->quizModel->getQuizResultDetails($quizResultId);

        foreach ($details as &$d) {
            $options = [
                'A' => $d['OptionA'],
                'B' => $d['OptionB'],
                'C' => $d['OptionC'],
                'D' => $d['OptionD']
            ];

            $d['UserAnswerContent'] = $options[$d['UserAnswer']] ?? '';
            $d['CorrectAnswerContent'] = $options[$d['CorrectAnswer']] ?? '';
            $d['Options'] = $options;
        }

        unset($d);

        $quizResult = $this->quizModel->getQuizResult($quizResultId);
        $_SESSION['last_score'] = $quizResult['Score'] ?? 0;

        require 'app/views/quiz/result.php';
    }

    // ===================== HISTORY ======================
    public function history()
    {
        $userId = $_SESSION['user']['UserId'];
        $results = $this->quizModel->getUserQuizResults($userId);

        require 'app/views/quiz/history.php';
    }

    // ===================== VIEW RESULT DETAILS ======================
    public function viewResult()
    {
        $quizResultId = $_GET['quizResultId'] ?? null;
        if (!$quizResultId) die("Thiếu QuizResultId");

        $details = $this->quizModel->getQuizResultDetailsByResultId($quizResultId);

        foreach ($details as &$d) {
            $options = [
                'A' => $d['OptionA'],
                'B' => $d['OptionB'],
                'C' => $d['OptionC'],
                'D' => $d['OptionD']
            ];

            $d['UserAnswerContent'] = $options[$d['UserAnswer']] ?? '';
            $d['CorrectAnswerContent'] = $options[$d['CorrectAnswer']] ?? '';
            $d['Options'] = $options;
        }

        unset($d);

        require 'app/views/quiz/viewResult.php';
    }
}
