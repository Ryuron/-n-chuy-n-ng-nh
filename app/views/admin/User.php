<?php
$me = SessionHelper::get('user');
// $users là mảng dữ liệu user lấy từ controller
?>
<h2 style="text-align:center; color:#2c3e50;">👥 Danh sách tài khoản</h2>

<table style="width:100%; border-collapse:collapse; font-family:Arial, sans-serif; box-shadow:0 2px 5px rgba(0,0,0,0.1); margin-top:20px;">
    <thead style="background:#34495e; color:white;">
        <tr>
            <th style="padding:10px;">Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Lớp</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($users) && is_array($users)): ?>       
            <?php foreach ($users as $u): ?>
                <tr style="text-align:center; border-bottom:1px solid #ddd;">
                    <td style="padding:8px;"><?php echo htmlspecialchars($u['Username']); ?></td>
                    <td><?php echo htmlspecialchars($u['Email']); ?></td>
                    <td style="font-weight:bold; color:<?= $u['Role'] === 'Admin' ? '#e67e22' : '#2980b9' ?>;">
                        <?php echo htmlspecialchars($u['Role']); ?>
                    </td>
                    <td><?php echo htmlspecialchars($u['GradeLevel']); ?></td>
                    <td>
                        <a href="index.php?controller=admin&action=edit&id=<?php echo $u['UserId']; ?>" 
                           style="color:#2980b9; text-decoration:none; margin-right:10px;">✏️ Sửa</a>
                        <a href="index.php?controller=admin&action=delete&id=<?php echo $u['UserId']; ?>" 
                           onclick="return confirm('Xóa tài khoản này?');" 
                           style="color:#e74c3c; text-decoration:none;">🗑️ Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align:center; padding:12px; color:#7f8c8d;">
                    Không có dữ liệu người dùng.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<div style="margin-top:20px; text-align:left;">
    <a class="btn" href="index.php?controller=admin&action=index" 
       style="display:inline-block; padding:10px 15px; background:#7f8c8d; color:white; 
              text-decoration:none; border-radius:6px; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
       ← Quay lại trang quản trị
    </a>
</div>
