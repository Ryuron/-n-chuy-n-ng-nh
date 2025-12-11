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

        /* ----------- Nút chuông báo lỗi ----------- */
        #error-btn {
            position: relative;
        }

        #error-btn.error-active {
            background: #e53935 !important; /* Màu đỏ khi có lỗi mới */
        }

        #error-count {
            background: yellow;
            color: black;
            padding: 3px 7px;
            border-radius: 10px;
            margin-left: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Trang quản trị hệ thống</h1>

    <div class="btn-container">
        <a class="btn" href="index.php?controller=question&action=index">📚 Quản lý câu hỏi</a>
        <a class="btn" href="index.php?controller=subject&action=index">📖 Quản lý môn học</a>
        <a class="btn" href="index.php?controller=admin&action=User">👤 Quản lý tài khoản</a>

        <!-- 🔔 Nút báo lỗi -->
        <a class="btn" href="index.php?controller=admin&action=errors" id="error-btn">
            🔔 Báo lỗi <span id="error-count">0</span>
        </a>
    </div>

    <!-- ----------- Script load số lỗi ----------- -->
    <script>
        function loadErrorCount() {
            fetch("index.php?controller=admin&action=getErrorCountAjax")
                .then(res => res.json())
                .then(data => {
                    const count = data.count;
                    const btn = document.getElementById("error-btn");
                    const badge = document.getElementById("error-count");

                    badge.textContent = count;

                    if (count > 0) {
                        btn.classList.add("error-active");  // Đổi thành màu đỏ
                    } else {
                        btn.classList.remove("error-active"); // Trở lại màu xanh
                    }
                })
                .catch(err => console.error("Lỗi khi load số lỗi:", err));
        }

        loadErrorCount();
        setInterval(loadErrorCount, 5000); // cập nhật mỗi 5 giây
    </script>

</body>
</html>
