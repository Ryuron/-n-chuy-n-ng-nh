<h2>Sửa tài khoản</h2>

<?php if (!empty($errors)): ?>
    <div style="color:red;">
        <?php foreach ($errors as $err): ?>
            <p><?php echo htmlspecialchars($err); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post">
    <label>Username:</label><br>
    <input type="text" name="username" value="<?php echo htmlspecialchars($user['Username']); ?>"><br><br>

    <label>Email:</label><br>
    <input type="text" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>"><br><br>

    <label>Role:</label><br>
    <select name="role">
        <option value="User" <?php if ($user['Role']=='User') echo 'selected'; ?>>User</option>
        <option value="Admin" <?php if ($user['Role']=='Admin') echo 'selected'; ?>>Admin</option>
    </select><br><br>

    <label>Lớp:</label><br>
    <input type="number" name="gradeLevel" value="<?php echo htmlspecialchars($user['GradeLevel']); ?>"><br><br>

    <label>Trình độ:</label><br>
    <select name="currentLevel">
        <option value="Yếu" <?php if ($user['CurrentLevel']=='Yếu') echo 'selected'; ?>>Yếu</option>
        <option value="TB" <?php if ($user['CurrentLevel']=='TB') echo 'selected'; ?>>TB</option>
        <option value="Giỏi" <?php if ($user['CurrentLevel']=='Giỏi') echo 'selected'; ?>>Giỏi</option>
    </select><br><br>

    <button type="submit">Lưu thay đổi</button>
    <a href="index.php?controller=account&action=index">Hủy</a>
</form>
