<?php include ROOT_PATH . '/app/views/shares/header.php'; ?>

<h2>📛 Danh sách báo lỗi câu hỏi</h2>

<table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
    <tr>
        <th>ID</th>
        <th>Nội dung câu hỏi</th>
        <th>Đáp án</th>
        <th>Đáp án đúng</th>
        <th>Lỗi được báo</th>
        <th>Ngày báo lỗi</th>
        <th>Hành động</th>
    </tr>

    <?php foreach ($errors as $e): ?>
    <tr>
        <td><?= $e['ErrorId'] ?></td>

        <td><?= htmlspecialchars($e['Content']) ?></td>

        <td>
            A. <?= htmlspecialchars($e['OptionA'] ?? '') ?><br>
            B. <?= htmlspecialchars($e['OptionB'] ?? '') ?><br>
            C. <?= htmlspecialchars($e['OptionC'] ?? '') ?><br>
            D. <?= htmlspecialchars($e['OptionD'] ?? '') ?>
        </td>

        <td style="color:blue; font-weight:bold;">
            <?= $e['CorrectAnswer'] ?>
        </td>

        <td style="color:red;">
            <?= nl2br(htmlspecialchars($e['ErrorText'])) ?>
        </td>

        <td><?= $e['ReportedAt'] ?></td>

        <td>
            <a href="index.php?controller=question&action=edit&id=<?= $e['ErrorId'] ?>">✏ Sửa</a><br><br>

            <a href="index.php?controller=admin&action=deleteError&id=<?= $e['ErrorId'] ?>"
               onclick="return confirm('Xóa báo lỗi này?');"
               style="color:red;">
               🗑 Xóa
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<p><a href="index.php?controller=admin&action=index">⬅ Quay lại trang quản trị</a></p>

<?php include ROOT_PATH . '/app/views/shares/footer.php'; ?>
