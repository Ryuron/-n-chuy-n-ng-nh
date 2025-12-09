<h2>Hồ sơ cá nhân</h2>

<?php if (!empty($errors)): ?>
<article class="contrast">
  <ul><?php foreach ($errors as $e) echo "<li>$e</li>"; ?></ul>
</article>
<?php endif; ?>

<?php if (!empty($success)): ?>
<article class="contrast">
  <p><?php echo $success; ?></p>
</article>
<?php endif; ?>

<?php $me = SessionHelper::get('user'); ?>
<article>
  <header>
    <strong><?= htmlspecialchars($me['Username']); ?></strong>
    <small>Role: <?= htmlspecialchars($me['Role']); ?></small>
  </header>

  <form method="post">
    <label>Email
      <input type="email" name="email" value="<?= htmlspecialchars($me['Email']); ?>" required>
    </label>
    <div class="grid">
      <label>Lớp (GradeLevel)
        <input type="number" name="gradeLevel" min="1" max="12" value="<?= (int)$me['GradeLevel']; ?>" required>
      </label>
      <label>Trình độ hiện tại
        <input type="text" value="<?= htmlspecialchars($me['CurrentLevel']); ?>" readonly>
      </label>
    </div>
    <label>Đổi mật khẩu (để trống nếu không đổi)
      <input type="password" name="newPassword" placeholder="Mật khẩu mới">
    </label>
    <button type="submit">Lưu thay đổi</button>
    <a class="secondary" href="index.php?controller=account&action=logout">Đăng xuất</a>
    <!-- Nút xem lịch sử làm bài -->
    <a class="secondary" href="index.php?controller=quiz&action=history">📄 Lịch sử làm bài</a>
  </form>
</article>
