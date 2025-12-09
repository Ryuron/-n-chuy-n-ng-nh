<h2>Bài kiểm tra</h2>

<form method="POST" action="index.php?controller=quiz&action=submit&test_id=<?= intval($_GET['test_id']) ?>">
    <?php foreach ($questions as $index => $q): ?>
        <div style="margin-bottom:20px; padding:10px; border:1px solid #ccc; border-radius:5px;">
            <p><strong>Câu <?= $index + 1 ?>:</strong> <?= htmlspecialchars($q['Content']) ?></p>
            <?php foreach ($q['ShuffledOptions'] as $option): ?>
                <label style="display:block; margin-bottom:5px;">
                    <input type="radio" name="answer[<?= $q['TestQuestionId'] ?>]" value="<?= htmlspecialchars($option) ?>" required>
                    <?= htmlspecialchars($option) ?>
                </label>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <button type="submit" style="padding:10px 20px; font-size:16px; border:none; border-radius:5px; background-color:#4CAF50; color:white; cursor:pointer;">
        Nộp bài
    </button>
</form>
