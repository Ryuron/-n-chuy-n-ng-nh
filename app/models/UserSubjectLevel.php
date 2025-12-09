<?php
class UserSubjectLevel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Lấy trình độ hiện tại
    public function getCurrentLevel($userId, $subjectId) {
        $stmt = $this->db->prepare("
            SELECT CurrentLevel FROM UserSubjectLevel
            WHERE UserId=? AND SubjectId=?
        ");
        $stmt->execute([$userId, $subjectId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['CurrentLevel'] ?? 'Yếu';
    }

    // Lấy cả ST và CurrentLevel
    public function getLevelInfo($userId, $subjectId) {
        $stmt = $this->db->prepare("
            SELECT CurrentLevel, ST FROM UserSubjectLevel
            WHERE UserId=? AND SubjectId=?
        ");
        $stmt->execute([$userId, $subjectId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: ['CurrentLevel'=>'Yếu', 'ST'=>0];
    }

    // Cập nhật ST và CurrentLevel
    public function updateSTAndLevel($userId, $subjectId, $st, $level) {
        $stmt = $this->db->prepare("
            UPDATE UserSubjectLevel
            SET ST=?, CurrentLevel=?
            WHERE UserId=? AND SubjectId=?
        ");
        $stmt->execute([$st, $level, $userId, $subjectId]);
    }

    // Tăng bậc CurrentLevel
    public function increaseLevel($level) {
        $map = ['Yếu'=>'TB', 'TB'=>'Khá', 'Khá'=>'Giỏi', 'Giỏi'=>'Giỏi'];
        return $map[$level] ?? 'Yếu';
    }

    // Giảm bậc CurrentLevel
    public function decreaseLevel($level) {
        $map = ['Giỏi'=>'Khá', 'Khá'=>'TB', 'TB'=>'Yếu', 'Yếu'=>'Yếu'];
        return $map[$level] ?? 'Yếu';
    }
}
