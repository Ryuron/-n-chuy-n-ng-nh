<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>

    <style>
        /* -------------------- Tổng thể -------------------- */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(#e8f5e9, #f1f8e9);
            margin: 0;
            padding: 0;
            text-align: center;
        }

        h1 {
            background: #43a047;
            color: white;
            padding: 18px 0;
            margin: 0 0 35px;
            font-size: 26px;
            letter-spacing: 1px;
        }

        /* -------------------- Container nút -------------------- */
        .btn-container {
            display: flex;
            justify-content: center;
            gap: 25px;
            flex-wrap: wrap;
            padding: 20px;
        }

        /* -------------------- Nút chung -------------------- */
        .btn {
            background: #4CAF50;
            color: white;
            padding: 18px 35px;
            text-decoration: none;
            font-size: 18px;
            border-radius: 10px;
            transition: 0.25s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .btn:hover {
            background: #388e3c;
            transform: translateY(-3px);
            box-shadow: 0 6px 14px rgba(0,0,0,0.15);
        }

        /* -------------------- Nút chuông báo lỗi -------------------- */
        #error-btn {
            position: relative;
        }

        #error-btn.error-active {
            background: #e53935 !important;
        }

        /* Số lỗi */
        #error-count {
            background: yellow;
            color: black;
            padding: 4px 8px;
            border-radius: 10px;
            margin-left: 5px;
            font-weight: bold;
        }

        /* Animation nhấp nháy khi có lỗi */
        #error-btn.error-active {
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255,0,0,0.6); }
            70% { box-shadow: 0 0 0 10px rgba(255,0,0,0); }
            100% { box-shadow: 0 0 0 0 rgba(255,0,0,0); }
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
                        btn.classList.add("error-active");
                    } else {
                        btn.classList.remove("error-active");
                    }
                })
                .catch(err => console.error("Lỗi khi load số lỗi:", err));
        }

        loadErrorCount();
        setInterval(loadErrorCount, 5000);
    </script>

</body>
</html>
