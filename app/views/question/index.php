<h2 style="text-align:center; color:#2c3e50;">📋 Danh sách câu hỏi</h2>
<div style="margin-top:20px;">
    <a href="index.php?controller=admin&action=index"
        style="display:inline-block; padding:10px 15px; background:#7f8c8d; color:white; text-decoration:none; border-radius:6px;">
        ← Quay lại trang quản trị
    </a>
</div>
<div style="margin-bottom:15px; text-align:right;">
    <a href="index.php?controller=question&action=create"
        style="background:#27ae60; color:white; padding:8px 12px; border-radius:6px; text-decoration:none; margin-right:10px;">
        ➕ Thêm câu hỏi
    </a>
    <a href="index.php?controller=question&action=import"
        style="background:#3498db; color:white; padding:8px 12px; border-radius:6px; text-decoration:none;">
        📁 Nhập từ file
    </a> 
    <a href="index.php?controller=fact&action=index"
        style="background:#9b59b6; color:white; padding:8px 12px; border-radius:6px; text-decoration:none;">
        📊 Dữ kiện
    </a>
        <a href="index.php?controller=question&action=previewDifficulty"
        style="background:#e67e22; color:white; padding:8px 12px; border-radius:6px;">
        📈 Điều chỉnh độ khó
    </a>
</div>

<table style="width:100%; border-collapse:collapse; font-family:Arial, sans-serif; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
    <thead style="background:#34495e; color:white;">
        <tr>
            <th style="padding:10px;">Nội dung</th>
            <th>Đáp án A</th>
            <th>Đáp án B</th>
            <th>Đáp án C</th>
            <th>Đáp án D</th>
            <th>Đúng</th>
            <th>Môn</th>
            <th>Khối</th>
            <th>Độ khó</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($questions as $q): ?>
            <tr style="text-align:center; border-bottom:1px solid #ddd;">
                <td style="padding:8px; text-align:left;"><?= htmlspecialchars($q['Content']) ?></td>
                <td><?= htmlspecialchars($q['OptionA']) ?></td>
                <td><?= htmlspecialchars($q['OptionB']) ?></td>
                <td><?= htmlspecialchars($q['OptionC']) ?></td>
                <td><?= htmlspecialchars($q['OptionD']) ?></td>
                <td style="font-weight:bold; color:#27ae60;"><?= $q['CorrectAnswer'] ?></td>
                <td><?= htmlspecialchars($q['SubjectName']) ?></td>
                <td><?= $q['GradeLevel'] ?></td>
                <td><?= $q['DifficultyLevel'] ?></td>
                <td>
                    <a href="index.php?controller=question&action=edit&id=<?= $q['QuestionId'] ?>"
                        style="color:#2980b9; text-decoration:none; margin-right:5px;">✏️ Sửa</a>
                    <a href="index.php?controller=question&action=delete&id=<?= $q['QuestionId'] ?>"
                        onclick="return confirm('Xóa câu hỏi này?')"
                        style="color:#e74c3c; text-decoration:none;">🗑️ Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div style="margin-top:20px;">
    <a href="index.php?controller=admin&action=index"
        style="display:inline-block; padding:10px 15px; background:#7f8c8d; color:white; text-decoration:none; border-radius:6px;">
        ← Quay lại trang quản trị
    </a>
</div>