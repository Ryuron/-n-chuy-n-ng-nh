<?php
class UserSubjectLevelModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getLevel($userId, $subjectId)
    {
        $sql = "SELECT CurrentLevel FROM UserSubjectLevel 
                WHERE UserId = ? AND SubjectId = ?";
        $stm = $this->db->prepare($sql);
        $stm->execute([$userId, $subjectId]);
        return $stm->fetchColumn();
    }
}
