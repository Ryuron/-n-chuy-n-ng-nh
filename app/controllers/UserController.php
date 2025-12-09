<?php
require_once __DIR__ . '/../models/Subject.php';

class UserController {
    private $db;
    private $subjectModel;

    public function __construct() {
        SessionHelper::start();
        $this->db = new PDO('mysql:host=localhost;dbname=AdaptiveQuizDB', 'root', '');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->subjectModel = new Subject($this->db);
    }

    // Trang chính của user, hiển thị chọn môn học
    public function index() {
        $subjects = $this->subjectModel->getAll();
        require 'app/views/user/index.php';
    }
}
