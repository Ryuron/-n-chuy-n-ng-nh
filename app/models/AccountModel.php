<?php
class AccountModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByUsernameOrEmail($usernameOrEmail)
    {
        try {
            $query = "SELECT UserId, Username, Email, Password, Role, FullName, GradeLevel 
                      FROM Users 
                      WHERE Username = :username OR Email = :email";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':username', $usernameOrEmail);
            $stmt->bindParam(':email', $usernameOrEmail);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi cơ sở dữ liệu: " . $e->getMessage());
            throw new RuntimeException("Lỗi hệ thống, vui lòng thử lại sau.");
        }
    }

    public function findById($id)
    {
        $stm = $this->db->prepare("SELECT * FROM Users WHERE UserId = :id");
        $stm->execute([':id' => $id]);
        return $stm->fetch();
    }

    public function isUsernameTaken($username)
    {
        $stm = $this->db->prepare("SELECT 1 FROM Users WHERE Username = :u");
        $stm->execute([':u' => $username]);
        return (bool)$stm->fetchColumn();
    }

    public function isEmailTaken($email)
    {
        $stm = $this->db->prepare("SELECT 1 FROM Users WHERE Email = :e");
        $stm->execute([':e' => $email]);
        return (bool)$stm->fetchColumn();
    }

    // ============================
    //  HÀM CREATE MỚI – CHUẨN DB
    // ============================
        public function create($username, $email, $password, $fullName, $role, $gradeLevel, $currentLevel)
    {
        // Chuyển currentLevel mặc định nếu rỗng
        if ($currentLevel === '' || $currentLevel === null) {
            $currentLevel = 'Yếu';
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        // 1) Insert vào bảng Users (có GradeLevel)
        $sql = "INSERT INTO Users 
                (Username, Password, Email, FullName, Role, GradeLevel)
                VALUES (:u, :ph, :e, :fn, :r, :gl)";
        $stm = $this->db->prepare($sql);
        $stm->execute([
            ':u'  => $username,
            ':ph' => $hash,
            ':e'  => $email,
            ':fn' => $fullName,
            ':r'  => $role,
            ':gl' => $gradeLevel
        ]);

        $userId = $this->db->lastInsertId();

        // 2) Insert vào UserSubjectLevel cho từng môn
        $subjects = $this->db->query("SELECT SubjectId FROM Subjects")->fetchAll(PDO::FETCH_ASSOC);

        $insert = $this->db->prepare("
            INSERT INTO UserSubjectLevel (UserId, SubjectId, CurrentLevel, st)
            VALUES (?, ?, ?, ?)
        ");

        foreach ($subjects as $s) {
            $insert->execute([$userId, $s['SubjectId'], $currentLevel, 0]);
        }

        return $userId;
    }

    public function verifyLogin($usernameOrEmail, $password)
    {
        $user = $this->findByUsernameOrEmail($usernameOrEmail);
        if ($user && password_verify($password, $user['Password'])) {
            return $user;
        }
        return false;
    }

    public function updateProfile($userId, $email, $fullName = '', $newPassword = null)
    {
        if ($newPassword && trim($newPassword) !== '') {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE Users
                    SET Email = :e, FullName = :fn, Password = :ph
                    WHERE UserId = :id";
            $params = [':e' => $email, ':fn' => $fullName, ':ph' => $hash, ':id' => $userId];
        } else {
            $sql = "UPDATE Users
                    SET Email = :e, FullName = :fn
                    WHERE UserId = :id";
            $params = [':e' => $email, ':fn' => $fullName, ':id' => $userId];
        }

        $stm = $this->db->prepare($sql);
        return $stm->execute($params);
    }
}
