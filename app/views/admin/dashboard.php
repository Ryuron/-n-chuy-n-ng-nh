<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
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
            margin: 0 0 30px;
        }
        .btn-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .btn {
            background: #4CAF50;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            font-size: 18px;
            border-radius: 8px;
            transition: 0.3s;
        }
        .btn:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <h1>Trang quản trị hệ thống</h1>
    <div class="btn-container">
        <a class="btn" href="index.php?controller=question&action=index">📚 Quản lý câu hỏi</a>
        <a class="btn" href="index.php?controller=subject&action=index">📖 Quản lý môn học</a>
        <a class="btn" href="index.php?controller=admin&action=User">👤 Quản lý tài khoản</a>

    </div>
</body>
</html>
