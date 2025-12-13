<?php

class SubjectModel
{
    private $db;

    public function __construct()
    {
        // Database::getInstance() TRẢ VỀ PDO
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        $stmt = $this->db->query("
            SELECT SubjectId, SubjectName
            FROM subjects
            ORDER BY SubjectName
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
