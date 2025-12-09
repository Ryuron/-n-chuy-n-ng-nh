<?php
require_once __DIR__ . '/../models/Subject.php';

class SubjectController {
    private $db;
    private $subjectModel;

    public function __construct() {
        SessionHelper::start(); // khởi tạo session

        // kiểm tra admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'Admin') {
            header("Location: index.php?controller=account&action=login");
            exit;
        }

        $this->db = new PDO('mysql:host=localhost;dbname=AdaptiveQuizDB', 'root', '');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->subjectModel = new Subject($this->db);
    }

    public function index() {
        $subjects = $this->subjectModel->getAll();
        require 'app/views/subjects/index.php';
    }

    public function create() {
        require 'app/views/subjects/create.php';
    }

    public function store() {
        $this->subjectModel->create($_POST);
        header("Location: index.php?controller=subject&action=index");
        exit();
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) die("Thiếu ID môn học");

        $subject = $this->subjectModel->getById($id);
        require 'app/views/subjects/edit.php';
    }

    public function update() {
        $id = $_GET['id'] ?? null;
        if (!$id) die("Thiếu ID môn học");

        $this->subjectModel->update($id, $_POST);
        header("Location: index.php?controller=subject&action=index");
        exit();
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if (!$id) die("Thiếu ID môn học");

        $this->subjectModel->delete($id);
        header("Location: index.php?controller=subject&action=index");
        exit();
    }
}
