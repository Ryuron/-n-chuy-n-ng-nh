<?php
class Quiz {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Tạo quiz tạm thời
    public function createTempQuiz($subjectId, $gradeLevel, $userId, $questions = []) {
        $stmt = $this->db->prepare("
            INSERT INTO Quizzes (Title, SubjectId, GradeLevel, EasyPercent, MediumPercent, HardPercent, TotalQuestions, CreatedBy) 
            VALUES (?, ?, ?, 0, 0, 0, ?, ?)
        ");
        $totalQuestions = count($questions);
        $stmt->execute([
            "Quiz tạm thời",
            $subjectId,
            $gradeLevel,
            $totalQuestions,
            $userId
        ]);
        $quizId = $this->db->lastInsertId();

        // Lưu câu hỏi vào QuizQuestions
        $stmtQ = $this->db->prepare("
            INSERT INTO QuizQuestions (QuizId, QuestionId) VALUES (?, ?)
        ");
        foreach ($questions as $q) {
            $stmtQ->execute([$quizId, $q['QuestionId']]);
        }

        return $quizId;
    }

    public function getQuizQuestions($quizId) {
        $stmt = $this->db->prepare("
            SELECT q.*, s.SubjectName 
            FROM QuizQuestions qq
            JOIN Questions q ON qq.QuestionId = q.QuestionId
            JOIN Subjects s ON q.SubjectId = s.SubjectId
            WHERE qq.QuizId = ?
        ");
        $stmt->execute([$quizId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveQuizResult($quizId, $userId, $score) {
        $stmt = $this->db->prepare("
            INSERT INTO QuizResults (QuizId, UserId, Score, StartTime, EndTime) VALUES (?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$quizId, $userId, $score]);
        return $this->db->lastInsertId();
    }

    public function saveQuizResultDetail($quizResultId, $questionId, $userAnswer, $isCorrect) {
        $stmt = $this->db->prepare("
            INSERT INTO QuizResultDetails (QuizResultId, QuestionId, UserAnswer, IsCorrect) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$quizResultId, $questionId, $userAnswer, $isCorrect ? 1 : 0]);
    }

    public function getQuizResult($quizResultId) {
        $stmt = $this->db->prepare("SELECT * FROM QuizResults WHERE QuizResultId = ?");
        $stmt->execute([$quizResultId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getQuizResultDetails($quizResultId) {
        $stmt = $this->db->prepare("
            SELECT q.*, d.UserAnswer, d.IsCorrect , q.CorrectAnswer
            FROM QuizResultDetails d
            JOIN Questions q ON d.QuestionId = q.QuestionId
            WHERE d.QuizResultId = ?
        ");
        $stmt->execute([$quizResultId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getUserQuizResults($userId) {
        $stmt = $this->db->prepare("
            SELECT qr.QuizResultId, qr.Score, qr.StartTime, qr.EndTime, s.SubjectName
            FROM QuizResults qr
            JOIN Quizzes q ON qr.QuizId = q.QuizId
            JOIN Subjects s ON q.SubjectId = s.SubjectId
            WHERE qr.UserId = ?
            ORDER BY qr.StartTime DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy chi tiết 1 lần làm bài
    public function getQuizResultDetailsByResultId($quizResultId) {
        $stmt = $this->db->prepare("
            SELECT q.*, d.UserAnswer, d.IsCorrect , q.CorrectAnswer
            FROM QuizResultDetails d
            JOIN Questions q ON d.QuestionId = q.QuestionId
            WHERE d.QuizResultId = ?
        ");
        $stmt->execute([$quizResultId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}