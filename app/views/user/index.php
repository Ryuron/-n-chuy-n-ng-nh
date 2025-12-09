<?php include 'app/views/shares/header.php'; ?>
<h2>Chào mừng bạn đến với Quiz!</h2>

<form method="POST" action="index.php?controller=quiz&action=start">
    <label>Chọn môn học:</label>
    <select name="subject_id" required>
        <option value="">-- Chọn môn học --</option>
        <?php foreach ($subjects as $s): ?>
            <option value="<?= $s['SubjectId'] ?>">
                <?= htmlspecialchars($s['SubjectName']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>
    <button type="submit">Bắt đầu làm bài</button>
    <p><a href="index.php?controller=account&action=logout">Đăng xuất</a></p> 
</form>
<?php include 'app/views/shares/footer.php'; ?>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f5f5f5;
    margin: 0;
    padding: 20px;
    text-align: center;
}   
h2 {
    color: #333;
}
form {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: inline-block;
}
label {
    font-weight: bold;
}
select {
    width: 200px;
    padding: 8px;
    margin: 10px 0;
    border-radius: 4px;
    border: 1px solid #ccc;
}
button {
    background: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
button:hover {
    background: #45a049;
}
p {
    margin-top: 20px;
}
</style>
<?php

