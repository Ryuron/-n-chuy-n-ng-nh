<?php include ROOT_PATH . "/app/views/shares/header.php"; ?>
<h2>Lịch sử làm bài</h2>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>STT</th>
        <th>Môn học</th>
        <th>Điểm</th>
        <th>Trình độ</th>
        <th>Ngày làm</th>
        <th>Xem chi tiết</th>
    </tr>
    <?php foreach ($history as $index => $h): ?>
    <tr>
        <td><?= $index + 1 ?></td>
        <td><?= htmlspecialchars($h['SubjectName']) ?></td>
        <td><?= $h['Score'] ?></td>
        <td><?= $h['CurrentLevel'] ?></td>
        <td><?= $h['CompletedAt'] ?></td>
        <td><a href="index.php?controller=quiz&action=result&test_id=<?= $h['TestId'] ?>">Xem</a></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php include ROOT_PATH . "/app/views/shares/footer.php"; ?>
