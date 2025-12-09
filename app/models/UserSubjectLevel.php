<?php
class UserSubjectLevel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getCurrentLevel($userId, $subjectId) {
        $stmt = $this->db->prepare("
            SELECT CurrentLevel FROM UserSubjectLevel
            WHERE UserId=? AND SubjectId=?
        ");
        $stmt->execute([$userId, $subjectId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['CurrentLevel'] ?? 'Yếu';
    }
}
