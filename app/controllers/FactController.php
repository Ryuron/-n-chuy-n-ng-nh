<?php
require_once __DIR__ . '/../models/SubjectFact.php';

class FactController {
    private $db;
    private $factModel;

    public function __construct() {
        SessionHelper::start();

        if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'Admin') {
            header("Location: index.php?controller=account&action=login");
            exit;
        }

        $this->db = new PDO(
            'mysql:host=localhost;dbname=AdaptiveQuizDB',
            'root',
            ''
        );
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->factModel = new SubjectFact($this->db);
    }

    public function index() {
        $facts = $this->factModel->getAll();
        require 'app/views/fact/index.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->factModel->create(
                $_POST['subject_id'],
                $_POST['fact_text'],
                $_POST['weight']
            );
            header("Location: index.php?controller=fact&action=index");
            exit;
        }

        $subjects = $this->db
            ->query("SELECT * FROM Subjects")
            ->fetchAll(PDO::FETCH_ASSOC);

        require 'app/views/fact/create.php';
    }
}
