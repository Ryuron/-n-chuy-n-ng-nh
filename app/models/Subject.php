<?php
class Subject {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM Subjects");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM Subjects WHERE SubjectId = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO Subjects (SubjectName) VALUES (?)");
        return $stmt->execute([$data['SubjectName']]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE Subjects SET SubjectName = ? WHERE SubjectId = ?");
        return $stmt->execute([$data['SubjectName'], $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM Subjects WHERE SubjectId = ?");
        return $stmt->execute([$id]);
    }
}
