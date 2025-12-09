<h2 style="text-align:center; color:#2c3e50; margin-bottom:20px;">
    📝 Đề thi môn <?= htmlspecialchars($questions[0]['SubjectName'] ?? '') ?>
</h2>

<form method="POST" action="index.php?controller=quiz&action=submit" style="max-width:800px; margin:0 auto; font-family:Arial, sans-serif;">
    <!-- Quiz ID ẩn -->
    <input type="hidden" name="quiz_id" value="<?= $quizId ?? 0 ?>">

    <?php foreach ($questions as $index => $q): ?>
        <div style="margin-bottom:20px; padding:15px; border:1px solid #ddd; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.05); background:#fdfdfd;">
            <p style="font-weight:bold; margin-bottom:10px; color:#2c3e50;">
                Câu <?= $index + 1 ?>: <?= htmlspecialchars($q['Content']) ?>
            </p>
            
            <label style="display:block; margin:6px 0; cursor:pointer;">
                <input type="radio" name="answers[<?= $q['QuestionId'] ?>]" value="A" required>
                A. <?= htmlspecialchars($q['OptionA']) ?>
            </label>
            
            <label style="display:block; margin:6px 0; cursor:pointer;">
                <input type="radio" name="answers[<?= $q['QuestionId'] ?>]" value="B">
                B. <?= htmlspecialchars($q['OptionB']) ?>
            </label>
            
            <label style="display:block; margin:6px 0; cursor:pointer;">
                <input type="radio" name="answers[<?= $q['QuestionId'] ?>]" value="C">
                C. <?= htmlspecialchars($q['OptionC']) ?>
            </label>
            
            <label style="display:block; margin:6px 0; cursor:pointer;">
                <input type="radio" name="answers[<?= $q['QuestionId'] ?>]" value="D">
                D. <?= htmlspecialchars($q['OptionD']) ?>
            </label>
        </div>
    <?php endforeach; ?>

    <div style="text-align:center; margin-top:30px;">
        <button type="submit" 
                style="padding:12px 25px; font-size:16px; border:none; border-radius:8px; 
                       background:#27ae60; color:white; cursor:pointer; font-weight:bold; 
                       box-shadow:0 2px 5px rgba(0,0,0,0.15);">
            ✅ Nộp bài
        </button>
    </div>
</form>
