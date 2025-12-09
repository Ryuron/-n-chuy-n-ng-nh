<?php
class LevelController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function updateLevelProgress($userId, $score) {
        $stmt = $this->db->prepare("SELECT CurrentLevel, LevelProgress FROM Users WHERE UserId = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $currentLevel = $user['CurrentLevel'];
        $progress = (int)$user['LevelProgress'];

        if ($score > 75) $progress++;
        elseif ($score < 50) $progress--;

        if ($progress >= 10) {
            if ($currentLevel === 'Yếu') $currentLevel = 'TB';
            elseif ($currentLevel === 'TB') $currentLevel = 'Giỏi';
            $progress = 0;
        } elseif ($progress <= -10) {
            if ($currentLevel === 'Giỏi') $currentLevel = 'TB';
            elseif ($currentLevel === 'TB') $currentLevel = 'Yếu';
            $progress = 0;
        }

        $updateStmt = $this->db->prepare("UPDATE Users SET CurrentLevel = ?, LevelProgress = ? WHERE UserId = ?");
        $updateStmt->execute([$currentLevel, $progress, $userId]);

        return ['CurrentLevel' => $currentLevel, 'LevelProgress' => $progress];
    }
}
