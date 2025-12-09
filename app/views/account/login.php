<h2>Đăng nhập</h2>
<?php if (!empty($errors)): ?>
<article class="contrast">
  <ul><?php foreach ($errors as $e) echo "<li>$e</li>"; ?></ul>
</article>
<?php endif; ?>

<form method="post">
  <label>Tài khoản hoặc Email
    <input type="text" name="identifier" placeholder="username hoặc email" required>
  </label>
  <label>Mật khẩu
    <input type="password" name="password" required>
  </label>
  <button type="submit">Đăng nhập</button>
</form>
<p>Chưa có tài khoản? <a href="index.php?controller=account&action=register">Đăng ký</a></p>
