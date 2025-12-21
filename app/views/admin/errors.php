<?php include ROOT_PATH . '/app/views/shares/header.php'; ?>

<style>
    h2 {
        color: #d9534f;
        text-align: center;
        margin-bottom: 20px;
    }

    .error-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        font-size: 15px;
    }

    .error-table th {
        background: #0275d8;
        color: white;
        padding: 10px;
        text-align: center;
    }

    .error-table td {
        padding: 10px;
        border-bottom: 1px solid #ddd;
        vertical-align: top;
    }

    .error-table tr:hover {
        background: #f2f8ff;
    }

    .correct {
        color: #0b5ed7;
        font-weight: bold;
    }

    .error-text {
        color: #d9534f;
        font-weight: 500;
        white-space: pre-line;
    }

    .actions a {
        display: block;
        margin-bottom: 6px;
        text-decoration: none;
    }

    .btn-edit {
        color: #0275d8;
        font-weight: bold;
    }

    .btn-edit:hover {
        text-decoration: underline;
    }

    .btn-delete {
        color: red;
        font-weight: bold;
    }

    .btn-delete:hover {
        text-decoration: underline;
    }

    .back-link {
        display: inline-block;
        margin-top: 20px;
        color: #0275d8;
        font-size: 16px;
        text-decoration: none;
        font-weight: bold;
    }

    .back-link:hover {
        text-decoration: underline;
    }
</style>

<h2>📛 Danh sách báo lỗi câu hỏi</h2>

<table class="error-table">
    <tr>
        <th>ID Báo Lỗi</th>
        <th>Nội dung câu hỏi</th>
        <th>Các đáp án</th>
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

        <td class="correct">
            <?= htmlspecialchars($e['CorrectAnswer']) ?>
        </td>

        <td class="error-text">
            <?= nl2br(htmlspecialchars($e['ErrorText'])) ?>
        </td>

        <td><?= $e['ReportedAt'] ?></td>

        <td class="actions">
            <a class="btn-edit" href="index.php?controller=question&action=edit&id=<?= $e['QuestionId'] ?>">✏ Sửa câu hỏi</a>

            <a class="btn-delete"
               href="index.php?controller=admin&action=deleteError&id=<?= $e['ErrorId'] ?>"
               onclick="return confirm('Xóa báo lỗi này?');">
               🗑 Xóa
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<p>
    <a class="back-link" href="index.php?controller=admin&action=index">⬅ Quay lại trang quản trị</a>
</p>

<?php include ROOT_PATH . '/app/views/shares/footer.php'; ?>
