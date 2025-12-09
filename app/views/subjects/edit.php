<h1>Sửa môn học</h1>

<form method="POST" action="index.php?controller=subject&action=update&id=<?= $subject['SubjectId'] ?>">
    <label>Tên môn học:</label><br>
    <input type="text" name="SubjectName" value="<?= htmlspecialchars($subject['SubjectName']) ?>" required><br><br>
    <button type="submit">Cập nhật</button>
</form>

<br>
<a href="index.php?controller=subject&action=index">← Quay lại danh sách môn học</a> | 
<a href="index.php?controller=admin&action=dashboard">⬅ Quay lại trang Admin</a>
