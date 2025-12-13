<h2>➕ Thêm dữ kiện</h2>

<form method="POST">
    <label>Môn học:</label><br>
    <select name="subject_id" required>
        <option value="">-- Chọn môn --</option>
        <?php foreach ($subjects as $s): ?>
            <option value="<?= $s['SubjectId'] ?>">
                <?= htmlspecialchars($s['SubjectName']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Dữ kiện (từ khóa):</label><br>
    <input type="text" name="fact_text" required style="width:300px;">
    <br><br>

    <label>Weight (độ quan trọng):</label><br>
    <input type="number" step="0.1" name="weight" value="1" required>
    <br><br>

    <button type="submit"
        style="padding:8px 12px; background:#27ae60; color:white; border:none; border-radius:6px;">
        💾 Lưu
    </button>

    <a href="index.php?controller=fact&action=index"
       style="margin-left:10px;">Hủy</a>
</form>
