<?php
require_once __DIR__ . '/../models/Subject.php';

class UserController {
    private $db;

    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;dbname=AdaptiveQuizDB', 'root', '');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function index() {
        // Lấy danh sách môn học
        $subjectModel = new Subject($this->db);
        $subjects = $subjectModel->getAll();

        require 'app/views/user/index.php';
    }

    public function startQuiz() {
        // Sau này có thể random hoặc chọn quiz theo môn
        header("Location: index.php?controller=quiz&action=start");
        exit();
    }
}
