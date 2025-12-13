<h2>📊 Quản lý dữ kiện</h2>

<a href="index.php?controller=fact&action=create"
   style="padding:8px 12px; background:#27ae60; color:white; text-decoration:none; border-radius:6px;">
   ➕ Thêm dữ kiện
</a>

<table border="1" cellpadding="8" cellspacing="0" style="margin-top:15px; width:100%;">
    <tr style="background:#34495e; color:white;">
        <th>Môn học</th>
        <th>Dữ kiện</th>
        <th>Weight</th>
    </tr>

    <?php foreach ($facts as $f): ?>
    <tr>
        <td><?= htmlspecialchars($f['SubjectName']) ?></td>
        <td><?= htmlspecialchars($f['FactText']) ?></td>
        <td><?= $f['Weight'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<br>
<a href="index.php?controller=question&action=index">← Quay lại câu hỏi</a>
