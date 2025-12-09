<?php include ROOT_PATH . '/app/views/shares/header.php'; ?>

<h2>Kết quả bài kiểm tra: <?= htmlspecialchars($result['SubjectName']) ?></h2>
<p>Tổng số câu hỏi: <?= $result['TotalQuestions'] ?></p>
<p>Đúng: <?= $result['CorrectAnswers'] ?></p>
<p>Sai: <?= $result['WrongAnswers'] ?></p>
<p>Điểm: <?= $result['Score'] ?>/10</p>
<p>Trình độ: <?= $result['FinalLevel'] ?></p>

<h3>Chi tiết câu hỏi</h3>
<ol>
<?php foreach ($questions as $q): ?>
    <li>
        <p><strong><?= htmlspecialchars($q['Content']) ?></strong></p>
        <p>Đáp án của bạn: <?= htmlspecialchars($q['UserAnswer'] ?? '-') ?></p>
        <p>Đáp án đúng: <?= htmlspecialchars($q['CorrectAnswer']) ?></p>
        <p style="color: <?= $q['IsCorrect'] ? 'green' : 'red' ?>;"><?= $q['IsCorrect'] ? 'Đúng' : 'Sai' ?></p>
    </li>
<?php endforeach; ?>
</ol>

<p><a href="index.php?controller=user&action=index">Quay lại trang chủ</a></p>

<?php include ROOT_PATH . '/app/views/shares/footer.php'; ?>
