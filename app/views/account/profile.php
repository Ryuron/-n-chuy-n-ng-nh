<h2>Hồ sơ cá nhân</h2>

<?php if (!empty($errors)): ?>
<article class="contrast">
  <ul><?php foreach ($errors as $e) echo "<li>$e</li>"; ?></ul>
</article>
<?php endif; ?>

<?php if (!empty($success)): ?>
<article class="contrast">
  <p><?= $success ?></p>
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
      <input type="email" name="email"
             value="<?= htmlspecialchars($me['Email']); ?>" required>
    </label>

    <div class="grid">
      <label>Lớp (GradeLevel)
        <input type="number" name="gradeLevel" min="1" max="12"
               value="<?= (int)$me['GradeLevel']; ?>" required>
      </label>

      <label>Chọn môn học
        <select id="subjectSelect">
          <option value="">-- Chọn môn --</option>
          <?php foreach ($subjects as $s): ?>
            <option value="<?= $s['SubjectId'] ?>">
              <?= htmlspecialchars($s['SubjectName']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Trình độ môn
        <input type="text" id="subjectLevel" readonly value="Chưa chọn môn">
      </label>
    </div>

    <label>Đổi mật khẩu (để trống nếu không đổi)
      <input type="password" name="newPassword" placeholder="Mật khẩu mới">
    </label>

    <button type="submit">Lưu thay đổi</button>
    <a class="secondary" href="index.php?controller=account&action=logout">Đăng xuất</a>
    <a class="secondary" href="index.php?controller=quiz&action=history">
      📄 Lịch sử làm bài
    </a>
  </form>
</article>

<script>
document.getElementById('subjectSelect').addEventListener('change', function () {
    const subjectId = this.value;
    const levelInput = document.getElementById('subjectLevel');

    if (!subjectId) {
        levelInput.value = 'Chưa chọn môn';
        return;
    }

    fetch(`index.php?controller=account&action=getSubjectLevel&subject_id=${subjectId}`)
        .then(res => res.json())
        .then(data => {
            levelInput.value = data.level ?? 'Chưa có';
        });
});
</script>
