<h2>Thêm câu hỏi mới</h2>

<form method="post" action="index.php?controller=question&action=store">
    <label>Nội dung câu hỏi:</label><br>
    <textarea name="content" rows="4" cols="60" required></textarea><br><br>

    <label>Đáp án A:</label><br>
    <input type="text" name="optionA" required><br>

    <label>Đáp án B:</label><br>
    <input type="text" name="optionB" required><br>

    <label>Đáp án C:</label><br>
    <input type="text" name="optionC" required><br>

    <label>Đáp án D:</label><br>
    <input type="text" name="optionD" required><br><br>

    <label>Đáp án đúng (nhập đúng nội dung của một trong các phương án):</label><br>
    <input type="text" name="correctAnswer" required><br><br>

    <?php
    // Lấy danh sách môn học
    $stmt = $this->db->query("SELECT SubjectId, SubjectName FROM Subjects ORDER BY SubjectName ASC");
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <label>Môn học:</label><br>
    <select name="subjectId" required>
        <?php foreach ($subjects as $subject): ?>
            <option value="<?= $subject['SubjectId'] ?>">
                <?= htmlspecialchars($subject['SubjectName']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Khối lớp:</label><br>
    <select name="gradeLevel" required>
        <?php for ($i = 1; $i <= 12; $i++): ?>
            <option value="<?= $i ?>"><?= $i ?></option>
        <?php endfor; ?>
    </select><br><br>

    <label>Độ khó:</label><br>
    <select name="difficultyLevel" required>
        <option value="Dễ">Dễ</option>
        <option value="TB">Trung bình</option>
        <option value="Khó">Khó</option>
    </select><br><br>

    <input type="submit" value="Thêm câu hỏi">
</form>

<br>
<a href="index.php?controller=question&action=index">← Quay lại danh sách câu hỏi</a>

