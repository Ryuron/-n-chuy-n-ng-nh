<?php
class AccountController
{
    private $model;

    public function __construct()
    {
        SessionHelper::start();
        $this->model = new AccountModel();
    }

    public function login()
    {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $identifier = trim($_POST['identifier'] ?? '');
            $password   = $_POST['password'] ?? '';

            if ($identifier === '' || $password === '') {
                $errors[] = "Vui lòng nhập đầy đủ thông tin.";
            } else {
                $user = $this->model->verifyLogin($identifier, $password);
                if ($user) {
                    // Lưu thông tin cần thiết vào session
                    SessionHelper::set('user', [
                        'UserId'   => $user['UserId'],
                        'Username' => $user['Username'],
                        'Email'    => $user['Email'],
                        'Role'     => $user['Role'],
                        'FullName' => $user['FullName'] ?? '',
                        'GradeLevel' => $user['GradeLevel'] ?? 1
                    ]);
                    if (strtolower(trim($user['Role'])) === 'admin') {
                        // Admin -> dashboard
                        header("Location: index.php?controller=Admin&action=dashboard");
                    } else {
                        // User thường -> user/index
                        header("Location: index.php?controller=User&action=index");
                    }
                    exit;
                } else {
                    $errors[] = "Sai tài khoản hoặc mật khẩu.";
                }
            }
        }
        include ROOT_PATH . "/app/views/shares/header.php";
        include ROOT_PATH . "/app/views/account/login.php";
        include ROOT_PATH . "/app/views/shares/footer.php";
    }

    public function register()
    {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username     = trim($_POST['username'] ?? '');
            $email        = trim($_POST['email'] ?? '');
            $password     = $_POST['password'] ?? '';
            $confirm      = $_POST['confirm'] ?? '';
            $fullName     = trim($_POST['fullName'] ?? '');
            $gradeLevel    = $_POST['gradeLevel'] ?? 1;
            $currentLevel = $_POST['currentLevel'] ?? '';


            if ($username === '' || $email === '' || $password === '' || $confirm === '') {
                $errors[] = "Vui lòng điền đầy đủ thông tin.";
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email không hợp lệ.";
            }
            if ($password !== $confirm) {
                $errors[] = "Mật khẩu xác nhận không khớp.";
            }
            if ($this->model->isUsernameTaken($username)) {
                $errors[] = "Username đã tồn tại.";
            }
            if ($this->model->isEmailTaken($email)) {
                $errors[] = "Email đã được sử dụng.";
            }

            if (!$errors) {
                $this->model->create($username, $email, $password, $fullName, 'Student', $gradeLevel, $currentLevel);
                header("Location: index.php?controller=account&action=login");
                exit;
            }
        }
        include ROOT_PATH . "/app/views/shares/header.php";
        include ROOT_PATH . "/app/views/account/register.php";
        include ROOT_PATH . "/app/views/shares/footer.php";
    }

    public function logout()
    {
        SessionHelper::destroy();
        header("Location: index.php");
        exit;
    }
    public function index()
    {
        $me = SessionHelper::get('user');
        if ($me['Role'] !== 'admin') {
            // Có thể chuyển hướng hoặc báo lỗi
            die('Bạn không có quyền truy cập');
        }
        // Lấy danh sách user từ model
        $users = UserModel::getAll(); // Viết hàm này trong UserModel
        require 'app/views/admin/User.php';
    }

    public function profile()
    {
        AuthHelper::requireLogin();
        $user = SessionHelper::get('user');
        $errors = [];
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email        = trim($_POST['email'] ?? '');
            $fullName     = trim($_POST['fullName'] ?? $user['FullName']);
            $newPass      = $_POST['newPassword'] ?? '';

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email không hợp lệ.";
            }
            if (!$errors) {
                $ok = $this->model->updateProfile($user['UserId'], $email, $fullName, $newPass);
                if ($ok) {
                    // cập nhật session
                    $fresh = $this->model->findById($user['UserId']);
                    SessionHelper::set('user', [
                        'UserId'   => $fresh['UserId'],
                        'Username' => $fresh['Username'],
                        'Email'    => $fresh['Email'],
                        'Role'     => $fresh['Role'],
                        'FullName' => $fresh['FullName'] ?? ''
                    ]);
                    $success = "Cập nhật hồ sơ thành công.";
                } else {
                    $errors[] = "Không thể cập nhật hồ sơ.";
                }
            }
        }

        include ROOT_PATH . "/app/views/shares/header.php";
        include ROOT_PATH . "/app/views/account/profile.php";
        include ROOT_PATH . "/app/views/shares/footer.php";
    }
}
