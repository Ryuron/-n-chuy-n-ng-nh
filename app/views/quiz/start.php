<h2 style="
    text-align:center; 
    color:#1e90ff; 
    margin-bottom:25px;
    font-family:Arial, sans-serif;
    font-size:28px;
">
    📝 Đề thi môn <?= htmlspecialchars($questions[0]['SubjectName'] ?? '') ?>
</h2>

<form method="POST" 
      action="index.php?controller=quiz&action=submit"
      style="max-width:850px; margin:0 auto; font-family:Arial, sans-serif;">

    <?php foreach ($questions as $index => $q): ?>
        <div style="
            margin-bottom:25px; 
            padding:18px; 
            border:2px solid #1e90ff;
            border-radius:12px; 
            background:white;
            box-shadow:0 3px 8px rgba(30,144,255,0.15);
        ">

            <p style="
                font-weight:bold; 
                margin-bottom:12px; 
                color:#2c3e50;
                font-size:17px;
            ">
                Câu <?= $index + 1 ?>: <?= htmlspecialchars($q['Content']) ?>
            </p>

            <?php 
                $options = ['A' => 'OptionA', 'B' => 'OptionB', 'C' => 'OptionC', 'D' => 'OptionD'];
                foreach ($options as $key => $field):
            ?>
            <label style="
                display:block; 
                padding:10px 12px; 
                margin:6px 0; 
                border:1px solid #d6eaff;
                background:#f7fbff;
                border-radius:6px; 
                cursor:pointer;
                transition:0.2s;
            "
            onmouseover="this.style.background='#e9f4ff'"
            onmouseout="this.style.background='#f7fbff'">
                <input type="radio" 
                       name="answers[<?= $q['QuestionId'] ?>]" 
                       value="<?= $key ?>" 
                       required>
                <strong><?= $key ?>.</strong> 
                <?= htmlspecialchars($q[$field]) ?>
            </label>
            <?php endforeach; ?>

        </div>
    <?php endforeach; ?>

    <div style="text-align:center; margin-top:35px;">
        <button type="submit" 
                style="
                    padding:14px 30px; 
                    font-size:18px; 
                    border:none; 
                    border-radius:10px;
                    background:#1e90ff; 
                    color:white; 
                    cursor:pointer; 
                    font-weight:bold;
                    box-shadow:0 4px 10px rgba(30,144,255,0.3);
                    transition:0.2s;
                "
                onmouseover="this.style.background='#187bcd'"
                onmouseout="this.style.background='#1e90ff'">
            🚀 Nộp bài
        </button>
    </div>

</form>
