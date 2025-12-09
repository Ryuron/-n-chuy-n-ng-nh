<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý môn học</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        h1 {
            background: #4CAF50;
            color: white;
            padding: 15px 0;
            margin: 0 0 20px;
        }
        .btn-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .btn {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            transition: 0.3s;
        }
        .btn:hover {
            background: #45a049;
        }
        table {
            margin: 0 auto 20px;
            border-collapse: collapse;
            width: 80%;
            background: white;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        table th {
            background: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Danh sách môn học</h1>

    <div class="btn-container">
        <a class="btn" href="index.php?controller=admin&action=index">← Quay lại trang quản trị</a>
        <a class="btn" href="index.php?controller=subject&action=create">➕ Thêm môn học</a>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Tên môn học</th>
            <th>Hành động</th>
        </tr>
        <?php foreach ($subjects as $subject): ?>
        <tr>
            <td><?= $subject['SubjectId'] ?></td>
            <td><?= htmlspecialchars($subject['SubjectName']) ?></td>
            <td>
                <a href="index.php?controller=subject&action=edit&id=<?= $subject['SubjectId'] ?>">✏ Sửa</a> | 
                <a href="index.php?controller=subject&action=delete&id=<?= $subject['SubjectId'] ?>" onclick="return confirm('Xóa môn học này?')">🗑 Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
