<h2 style="
    text-align:center;
    color:#2c3e50;
    margin-bottom:20px;
    font-family:Arial, sans-serif;">
    ➕ Thêm câu hỏi mới
</h2>

<div style="
    background:white;
    width:750px;
    margin:0 auto;
    padding:25px;
    border:2px solid #3498db;
    border-radius:12px;
    box-shadow:0 3px 10px rgba(0,0,0,0.05);
    font-family:Arial, sans-serif;
">

<form method="post" action="index.php?controller=question&action=store">

    <label style="font-weight:bold; color:#2c3e50;">Nội dung câu hỏi:</label>
    <textarea name="content" rows="4" style="
        width:100%; padding:8px; border:1px solid #3498db; border-radius:6px; margin-bottom:15px;
    " required></textarea>

    <label style="font-weight:bold; color:#2c3e50;">Đáp án A:</label>
    <input type="text" name="optionA" style="
        width:100%; padding:8px; border:1px solid #3498db; border-radius:6px; margin-bottom:10px;
    " required>

    <label style="font-weight:bold; color:#2c3e50;">Đáp án B:</label>
    <input type="text" name="optionB" style="
        width:100%; padding:8px; border:1px solid #3498db; border-radius:6px; margin-bottom:10px;
    " required>

    <label style="font-weight:bold; color:#2c3e50;">Đáp án C:</label>
    <input type="text" name="optionC" style="
        width:100%; padding:8px; border:1px solid #3498db; border-radius:6px; margin-bottom:10px;
    " required>

    <label style="font-weight:bold; color:#2c3e50;">Đáp án D:</label>
    <input type="text" name="optionD" style="
        width:100%; padding:8px; border:1px solid #3498db; border-radius:6px; margin-bottom:15px;
    " required>

    <label style="font-weight:bold; color:#2c3e50;">Đáp án đúng (nhập đúng nội dung A/B/C/D):</label>
    <input type="text" name="correctAnswer" style="
        width:100%; padding:8px; border:1px solid #3498db; border-radius:6px; margin-bottom:20px;
    " required>

    <?php
    $stmt = $this->db->query("SELECT SubjectId, SubjectName FROM Subjects ORDER BY SubjectName ASC");
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <label style="font-weight:bold; color:#2c3e50;">Môn học:</label>
    <select name="subjectId" style="
        width:100%; padding:8px; border:1px solid #3498db; border-radius:6px; margin-bottom:20px;
    " required>
        <?php foreach ($subjects as $subject): ?>
            <option value="<?= $subject['SubjectId'] ?>">
                <?= htmlspecialchars($subject['SubjectName']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label style="font-weight:bold; color:#2c3e50;">Khối lớp:</label>
    <select name="gradeLevel" style="
        width:100%; padding:8px; border:1px solid #3498db; border-radius:6px; margin-bottom:20px;
    " required>
        <?php for ($i = 1; $i <= 12; $i++): ?>
            <option value="<?= $i ?>"><?= $i ?></option>
        <?php endfor; ?>
    </select>

    <label style="font-weight:bold; color:#2c3e50;">Độ khó:</label>
    <select name="difficultyLevel" style="
        width:100%; padding:8px; border:1px solid #3498db; border-radius:6px; margin-bottom:25px;
    " required>
        <option value="Dễ">Dễ</option>
        <option value="TB">Trung bình</option>
        <option value="Khó">Khó</option>
    </select>

    <button type="submit" style="
        width:100%; padding:12px;
        background:#3498db; color:white;
        border:none; border-radius:8px;
        font-size:16px; font-weight:bold;
        cursor:pointer;
        transition:0.2s;
    ">
        ✔ Thêm câu hỏi
    </button>
</form>

</div>

<!-- Nút quay lại -->
<div style="text-align:center; margin-top:20px;">
    <a href="index.php?controller=question&action=index" style="
        color:#3498db; font-weight:bold;
        text-decoration:none;
        border:2px solid #3498db;
        padding:8px 14px;
        border-radius:6px;
        transition:0.2s;
    ">
        ← Quay lại danh sách câu hỏi
    </a>
</div>
