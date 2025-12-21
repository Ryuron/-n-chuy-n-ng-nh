<?php include 'app/views/shares/header.php'; ?>

<style>
    .quiz-container {
        max-width: 450px;
        margin: 40px auto;
        background: #ffffff;
        padding: 25px 30px;
        border: 2px solid #1e90ff; /* viền xanh */
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(30,144,255,0.2);
        font-family: Arial, sans-serif;
    }

    .quiz-container h2 {
        text-align: center;
        color: #1e90ff;
        margin-bottom: 20px;
    }

    .quiz-container label {
        font-weight: bold;
        color: #333;
    }

    .quiz-container select {
        width: 100%;
        padding: 10px;
        border: 1px solid #1e90ff;
        border-radius: 6px;
        margin-top: 8px;
    }

    .quiz-container button {
        width: 100%;
        background: #1e90ff;
        color: white;
        padding: 12px;
        border: none;
        border-radius: 6px;
        margin-top: 20px;
        font-size: 16px;
        cursor: pointer;
    }

    .quiz-container button:hover {
        background: #187bcd;
    }

    .quiz-container a {
        display: block;
        margin-top: 15px;
        text-align: center;
        color: #1e90ff;
        text-decoration: none;
    }

    .quiz-container a:hover {
        text-decoration: underline;
    }
</style>
<nav class="container">
  <ul>
    <li><strong><a href="index.php?controller=user&action=index">AQS</a></strong></li>
  </ul>
  <ul>
    <!-- <li><a href="index.php?controller=account&action=login">Đăng nhập</a></li>
    <li><a href="index.php?controller=account&action=register">Đăng ký</a></li> -->
    <li><a href="index.php?controller=account&action=profile">Hồ sơ</a></li>
  </ul>
</nav>
<main class="container">
<div class="quiz-container">
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

        <button type="submit">Bắt đầu làm bài</button>

        <a href="index.php?controller=account&action=logout">Đăng xuất</a>
    </form>
</div>

<?php include 'app/views/shares/footer.php'; ?>
