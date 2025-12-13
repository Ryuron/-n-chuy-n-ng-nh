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
    require_once ROOT_PATH . '/app/models/SubjectModel.php';

    AuthHelper::requireLogin();
    $user = SessionHelper::get('user');

    $subjectModel = new SubjectModel();
    $subjects = $subjectModel->getAll();

    $selectedSubjectId = $_GET['subject_id'] ?? null;
    $currentLevel = null;

    if ($selectedSubjectId) {
        $stmt = $this->model->getDB()->prepare("
            SELECT CurrentLevel 
            FROM usersubjectlevel 
            WHERE UserId = ? AND SubjectId = ?
        ");
        $stmt->execute([$user['UserId'], $selectedSubjectId]);
        $currentLevel = $stmt->fetchColumn();
    }

    include ROOT_PATH . "/app/views/shares/header.php";
    include ROOT_PATH . "/app/views/account/profile.php";
    include ROOT_PATH . "/app/views/shares/footer.php";
}
public function getSubjectLevel()
{
    AuthHelper::requireLogin();
    $user = SessionHelper::get('user');

    $subjectId = $_GET['subject_id'] ?? null;
    if (!$subjectId) {
        echo json_encode(['level' => null]);
        return;
    }

    $stmt = Database::getInstance()->prepare("
        SELECT CurrentLevel
        FROM usersubjectlevel
        WHERE UserId = ? AND SubjectId = ?
    ");
    $stmt->execute([$user['UserId'], $subjectId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'level' => $row['CurrentLevel'] ?? 'Chưa có'
    ]);
}

}
