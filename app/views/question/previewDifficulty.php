<h2>📊 Đề xuất điều chỉnh độ khó</h2>

<form method="post" action="index.php?controller=question&action=applyDifficulty">
<table border="1" cellpadding="8" width="100%">
    <tr style="background:#34495e;color:white">
        <th>Nội dung</th>
        <th>Lần làm</th>
        <th>Tỉ lệ đúng</th>
        <th>Hiện tại</th>
        <th>Đề xuất</th>
        <th>Chọn</th>
    </tr>

<?php foreach ($data as $q): ?>
    <?php
        if ($q['Suggested'] === null || $q['Suggested'] === $q['DifficultyLevel']) {
            continue;
        }
    ?>
    <tr>
        <td><?= htmlspecialchars($q['Content']) ?></td>
        <td align="center"><?= $q['AnswerCount'] ?></td>
        <td align="center"><?= round($q['CorrectRate'] * 100, 1) ?>%</td>
        <td align="center"><?= $q['DifficultyLevel'] ?></td>
        <td align="center" style="color:red;font-weight:bold">
            <?= $q['Suggested'] ?>
        </td>
        <td align="center">
            <input type="checkbox"
                   name="apply[<?= $q['QuestionId'] ?>]"
                   value="<?= $q['Suggested'] ?>"
                   checked>
        </td>
    </tr>
<?php endforeach; ?>
</table>

<br>
<button type="submit" style="padding:10px 20px;background:#27ae60;color:white;border:none;">
    ✅ Xác nhận cập nhật
</button>
</form>
<br><br>

<a href="index.php?controller=question&action=index"
   style="
        display:inline-block;
        padding:10px 18px;
        background:#7f8c8d;
        color:white;
        text-decoration:none;
        border-radius:6px;
   ">
   ⬅️ Quay lại danh sách câu hỏi
</a>
