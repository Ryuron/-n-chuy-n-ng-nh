<?php include ROOT_PATH . '/app/views/shares/header.php'; ?>

<h2>Bài kiểm tra</h2>

<form method="POST" action="index.php?controller=quiz&action=submit&test_id=<?= $testId ?>">
    <?php foreach ($questions as $index => $q): ?>
        <div style="margin-bottom:20px;">
            <p><strong>Câu <?= $index + 1 ?>:</strong> <?= htmlspecialchars($q['Content']) ?></p>
            <label><input type="radio" name="answer[<?= $q['TestQuestionId'] ?>]" value="<?= htmlspecialchars($q['OptionA']) ?>"> <?= htmlspecialchars($q['OptionA']) ?></label><br>
            <label><input type="radio" name="answer[<?= $q['TestQuestionId'] ?>]" value="<?= htmlspecialchars($q['OptionB']) ?>"> <?= htmlspecialchars($q['OptionB']) ?></label><br>
            <label><input type="radio" name="answer[<?= $q['TestQuestionId'] ?>]" value="<?= htmlspecialchars($q['OptionC']) ?>"> <?= htmlspecialchars($q['OptionC']) ?></label><br>
            <label><input type="radio" name="answer[<?= $q['TestQuestionId'] ?>]" value="<?= htmlspecialchars($q['OptionD']) ?>"> <?= htmlspecialchars($q['OptionD']) ?></label>
        </div>
    <?php endforeach; ?>

    <button type="submit">Nộp bài</button>
</form>

<?php include ROOT_PATH . '/app/views/shares/footer.php'; ?>
