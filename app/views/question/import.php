<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhập câu hỏi từ file</title>
    <style>
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
        }

        h2 {
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        input[type="file"] {
            padding: 10px;
            border: 2px dashed #4CAF50;
            border-radius: 4px;
            width: 100%;
            cursor: pointer;
        }

        .btn {
            background: #4CAF50;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn:hover {
            background: #45a049;
        }

        .btn-secondary {
            background: #666;
            margin-left: 10px;
        }

        .btn-secondary:hover {
            background: #555;
        }

        .info-box {
            background: #e7f3fe;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
        }

        .info-box h3 {
            margin-top: 0;
            color: #2196F3;
        }

        .info-box ul {
            margin: 10px 0;
            padding-left: 20px;
        }

        .example-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 14px;
        }

        .example-table th,
        .example-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .example-table th {
            background: #4CAF50;
            color: white;
        }

        .example-table tr:nth-child(even) {
            background: #f2f2f2;
        }

        .download-template {
            display: inline-block;
            margin: 10px 0;
            padding: 10px 20px;
            background: #2196F3;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .download-template:hover {
            background: #0b7dda;
        }

        .error {
            background: #ffebee;
            border-left: 4px solid #f44336;
            padding: 15px;
            margin: 20px 0;
            color: #c62828;
        }

        .success {
            background: #e8f5e9;
            border-left: 4px solid #4CAF50;
            padding: 15px;
            margin: 20px 0;
            color: #2e7d32;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>📁 Nhập câu hỏi từ file CSV/Excel</h2>

        <?php if (isset($_SESSION['import_error'])): ?>
            <div class="error">
                <strong>❌ Lỗi:</strong> <?= htmlspecialchars($_SESSION['import_error']) ?>
            </div>
            <?php unset($_SESSION['import_error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['import_success'])): ?>
            <div class="success">
                <strong>✅ Thành công:</strong> <?= htmlspecialchars($_SESSION['import_success']) ?>
            </div>
            <?php unset($_SESSION['import_success']); ?>
        <?php endif; ?>

        <form method="post" action="index.php?controller=question&action=processImport" enctype="multipart/form-data">
            <div class="form-group">
                <label>Chọn file CSV hoặc Word (.csv, .docx):</label>
                <input type="file" name="question_file" accept=".csv,.docx" required>
            </div>

            <button type="submit" class="btn">📤 Tải lên và nhập câu hỏi</button>
            <a href="index.php?controller=question&action=index" class="btn btn-secondary">← Quay lại</a>
        </form>

        <div class="info-box">
            <h3>📋 Hướng dẫn định dạng file</h3>

            <h4>📄 Định dạng 1: CSV/Excel (Dạng bảng - Khuyên dùng)</h4>
            <p>File CSV/Excel cần có các cột theo thứ tự sau (không cần tiêu đề):</p>
            <ol>
                <li><strong>Cột 1:</strong> Nội dung câu hỏi</li>
                <li><strong>Cột 2:</strong> Đáp án A</li>
                <li><strong>Cột 3:</strong> Đáp án B</li>
                <li><strong>Cột 4:</strong> Đáp án C</li>
                <li><strong>Cột 5:</strong> Đáp án D</li>
                <li><strong>Cột 6:</strong> Đáp án đúng (A/B/C/D hoặc nội dung đầy đủ)</li>
                <li><strong>Cột 7:</strong> Mã môn học (SubjectId - số nguyên)</li>
                <li><strong>Cột 8:</strong> Khối lớp (1-12)</li>
                <li><strong>Cột 9:</strong> Độ khó (Dễ/TB/Khó)</li>
            </ol>

            <h4>Ví dụ:</h4>
            <table class="example-table">
                <thead>
                    <tr>
                        <th>Câu hỏi</th>
                        <th>A</th>
                        <th>B</th>
                        <th>C</th>
                        <th>D</th>
                        <th>Đáp án</th>
                        <th>Môn</th>
                        <th>Lớp</th>
                        <th>Độ khó</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2 + 2 = ?</td>
                        <td>3</td>
                        <td>4</td>
                        <td>5</td>
                        <td>6</td>
                        <td>B</td>
                        <td>1</td>
                        <td>1</td>
                        <td>Dễ</td>
                    </tr>
                    <tr>
                        <td>Thủ đô Việt Nam?</td>
                        <td>Hà Nội</td>
                        <td>TP.HCM</td>
                        <td>Đà Nẵng</td>
                        <td>Huế</td>
                        <td>Hà Nội</td>
                        <td>2</td>
                        <td>3</td>
                        <td>Dễ</td>
                    </tr>
                </tbody>
            </table>

            <h4 style="margin-top: 30px;">📝 Định dạng 2: Word (.docx) - Dạng văn bản</h4>
            <p>Mỗi câu hỏi chiếm nhiều dòng theo cấu trúc:</p>
            <div style="background: #f5f5f5; padding: 15px; border-radius: 4px; font-family: monospace; margin: 10px 0;">
                <strong>Câu hỏi của bạn ở đây?</strong><br>
                A. Đáp án A<br>
                B. Đáp án B<br>
                C. Đáp án C<br>
                D. Đáp án D<br>
                Đáp án: B<br>
                Môn: 1<br>
                Lớp: 5<br>
                Độ khó: Dễ<br>
                <br>
                <strong>Câu hỏi tiếp theo?</strong><br>
                A. Đáp án A<br>
                ...
            </div>

            <h4 style="margin-top: 20px;">📊 Định dạng 3: Word dạng bảng (Tab-separated)</h4>
            <p>Trong Word, tạo bảng 9 cột hoặc dùng Tab để phân cách:</p>
            <div style="background: #f5f5f5; padding: 15px; border-radius: 4px; font-family: monospace; margin: 10px 0; font-size: 12px;">
                Câu hỏi [TAB] A [TAB] B [TAB] C [TAB] D [TAB] Đáp án [TAB] Môn [TAB] Lớp [TAB] Độ khó
            </div>

            <a href="index.php?controller=question&action=downloadTemplate" class="download-template">
                ⬇️ Tải file mẫu CSV
            </a>
            <a href="index.php?controller=question&action=downloadWordTemplate" class="download-template" style="background: #9C27B0;">
                ⬇️ Tải file mẫu Word
            </a>
        </div>

        <div class="info-box">
            <h3>⚠️ Lưu ý quan trọng</h3>
            <ul>
                <li>Mã môn học phải tồn tại trong database (kiểm tra bảng Subjects)</li>
                <li>Khối lớp phải từ 1 đến 12</li>
                <li>Độ khó chỉ nhận 3 giá trị: <strong>Dễ</strong>, <strong>TB</strong>, <strong>Khó</strong></li>
                <li>Đáp án đúng có thể là chữ cái (A/B/C/D) hoặc nội dung đầy đủ của đáp án</li>
                <li><strong>File CSV:</strong> Nên dùng mã hóa UTF-8 để hiển thị tiếng Việt đúng</li>
                <li><strong>File Excel:</strong> Lưu dưới dạng CSV UTF-8 trước khi upload</li>
                <li><strong>File Word:</strong> Chỉ hỗ trợ .docx (không hỗ trợ .doc cũ)</li>
                <li><strong>File Word:</strong> Có thể dùng 2 định dạng - văn bản nhiều dòng hoặc dạng bảng</li>
            </ul>
        </div>
    </div>
</body>

</html>