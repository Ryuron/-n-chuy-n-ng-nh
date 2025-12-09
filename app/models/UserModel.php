<?php
class UserModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Đăng ký
    public function register($username, $passwordHash, $email, $gradeLevel, $currentLevel, $role = "User") {
        $stmt = $this->pdo->prepare("INSERT INTO Users (Username, PasswordHash, Email, Role, GradeLevel, CurrentLevel) 
                                     VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$username, $passwordHash, $email, $role, $gradeLevel, $currentLevel]);
    }

    // Kiểm tra username tồn tại
    public function usernameExists($username) {
        $stmt = $this->pdo->prepare("SELECT UserId FROM Users WHERE Username = ?");
        $stmt->execute([$username]);
        return $stmt->rowCount() > 0;
    }

    // Lấy thông tin user theo username
    public function getUserByUsername($username) {
        $stmt = $this->pdo->prepare("SELECT * FROM Users WHERE Username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }           

    public static function getAll() {
        require_once 'app/config/database.php'; // Đảm bảo đã include file Database
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getUserById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Users WHERE UserId = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật user
    public function updateUser($id, $username, $email, $role, $gradeLevel, $currentLevel) {
        $stmt = $this->pdo->prepare("
            UPDATE Users 
            SET Username = ?, Email = ?, Role = ?, GradeLevel = ?, CurrentLevel = ?, UpdatedAt = NOW()
            WHERE UserId = ?
        ");
        return $stmt->execute([$username, $email, $role, $gradeLevel, $currentLevel, $id]);
    }

    // Xóa user
    public function deleteUser($id) {
        $stmt = $this->pdo->prepare("DELETE FROM Users WHERE UserId = ?");
        return $stmt->execute([$id]);
    }
}
