<h2>Đăng ký</h2>
<?php if (!empty($errors)): ?>
<article class="contrast">
  <ul><?php foreach ($errors as $e) echo "<li>$e</li>"; ?></ul>
</article>
<?php endif; ?>

<form method="post">
  <div class="grid">
    <label>Username
      <input type="text" name="username" required>
    </label>
    <label>Email
      <input type="email" name="email" required>
    </label>
  </div>
  <div class="grid">
    <label>Mật khẩu
      <input type="password" name="password" required>
    </label>
    <label>Nhập lại mật khẩu
      <input type="password" name="confirm" required>
    </label>
  </div>
  <div class="grid">
    <label>Lớp (GradeLevel)
      <input type="number" name="gradeLevel" min="1" max="12" value="10" required>
    </label>
  </div>
  <button type="submit">Tạo tài khoản</button>
</form>
<p>Đã có tài khoản? <a href="index.php?controller=account&action=login">Đăng nhập</a></p>
