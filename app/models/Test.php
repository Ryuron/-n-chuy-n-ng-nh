<?php
class Test {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createTest($userId, $subjectId, $gradeLevel, $currentLevel, $questions) {
        // Tạo bài kiểm tra
        $stmt = $this->db->prepare("
            INSERT INTO Tests (UserId, SubjectId, GradeLevel, TotalQuestions, CurrentLevel)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$userId, $subjectId, $gradeLevel, count($questions), $currentLevel]);
        $testId = $this->db->lastInsertId();

        // Lưu câu hỏi vào TestQuestions
        $stmt = $this->db->prepare("
            INSERT INTO TestQuestions (TestId, QuestionId) VALUES (?, ?)
        ");
        foreach ($questions as $q) {
            $stmt->execute([$testId, $q['QuestionId']]);
        }

        return $testId;
    }
    // Lấy lịch sử làm bài của user
    public function getUserHistory($userId) {
        $stmt = $this->db->prepare("
            SELECT t.TestId, t.SubjectId, s.SubjectName, t.Score, t.CompletedAt, t.CurrentLevel
            FROM Tests t
            JOIN Subjects s ON t.SubjectId = s.SubjectId
            WHERE t.UserId = ?
            ORDER BY t.CompletedAt DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
