<?php
class SubjectFact {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        return $this->db->query("
            SELECT sf.*, s.SubjectName
            FROM SubjectFacts sf
            JOIN Subjects s ON sf.SubjectId = s.SubjectId
            ORDER BY s.SubjectName
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($subjectId, $factText, $weight) {
        $stmt = $this->db->prepare("
            INSERT INTO SubjectFacts (SubjectId, FactText, Weight)
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$subjectId, $factText, $weight]);
    }

    public function delete($factId) {
        $stmt = $this->db->prepare("DELETE FROM SubjectFacts WHERE FactId = ?");
        return $stmt->execute([$factId]);
    }
}