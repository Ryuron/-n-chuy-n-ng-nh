<?php
class AdminController {
    public function index() {
        require 'app/views/admin/dashboard.php';
    } 
    public function User() {
        $me = SessionHelper::get('user');
        $users = UserModel::getAll();
        require 'app/views/admin/User.php';
    } 
    public function dashboard()
    {
        include ROOT_PATH . "/app/views/shares/header.php";
        include ROOT_PATH . "/app/views/admin/dashboard.php";
        include ROOT_PATH . "/app/views/shares/footer.php";
    }
        public function edit() {
    $me = SessionHelper::get('user');
    
    $id = $_GET['id'] ?? null;
    if (!$id) die("Thiếu ID");

    $userModel = new UserModel(Database::getConnection());
    $user = $userModel->getUserById($id);
    if (!$user) die("Không tìm thấy user");

    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $role     = $_POST['role'] ?? 'User';
        $grade    = (int)($_POST['gradeLevel'] ?? 1);
        $level    = $_POST['currentLevel'] ?? 'TB';

        if ($username === '' || $email === '') {
            $errors[] = "Vui lòng nhập đầy đủ thông tin.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email không hợp lệ.";
        }

        if (!$errors) {
            $ok = $userModel->updateUser($id, $username, $email, $role, $grade, $level);
            if ($ok) {
                header("Location: index.php?controller=admin&action=index");
                exit;
            } else {
                $errors[] = "Không thể cập nhật tài khoản.";
            }
        }
    }

    require 'app/views/admin/UserEdit.php';
}

public function delete() {
    $me = SessionHelper::get('user');


    $id = $_GET['id'] ?? null;
    if ($id) {
        $userModel = new UserModel(Database::getConnection());
        $userModel->deleteUser($id);
    }

    header("Location: index.php?controller=admin&action=User");
    exit;
}
}
