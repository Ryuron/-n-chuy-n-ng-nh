<h2 style="text-align:center; color:#2c3e50; margin-bottom:20px;">📜 Lịch sử làm bài</h2>

<?php if (empty($results)): ?>
    <p style="text-align:center; font-size:16px; color:#7f8c8d;">Bạn chưa làm bài nào.</p>
<?php else: ?>
    <table style="width:100%; border-collapse:collapse; font-family:Arial, sans-serif; 
                  box-shadow:0 2px 5px rgba(0,0,0,0.1); margin-top:20px;">
        <thead style="background:#34495e; color:white;">
            <tr>
                <th style="padding:10px;">#</th>
                <th>Môn học</th>
                <th>Điểm</th>
                <th>Ngày làm</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $index => $r): 
                $score = (int)$r['Score'];
                $scoreColor = $score >= 80 ? '#27ae60' : ($score >= 50 ? '#f39c12' : '#e74c3c');
            ?>
                <tr style="text-align:center; border-bottom:1px solid #ddd;">
                    <td style="padding:8px;"><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($r['SubjectName']) ?></td>
                    <td style="font-weight:bold; color:<?= $scoreColor ?>;">
                        <?= $score ?>%
                    </td>
                    <td><?= date("d/m/Y H:i", strtotime($r['StartTime'])) ?></td>
                    <td>
                        <a href="index.php?controller=quiz&action=viewResult&quizResultId=<?= $r['QuizResultId'] ?>" 
                           style="color:#2980b9; text-decoration:none; font-weight:bold;">
                           🔍 Xem chi tiết
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<div style="margin-top:20px; text-align:left;">
    <a href="index.php?controller=account&action=profile" 
       style="display:inline-block; padding:10px 20px; background:#7f8c8d; color:white; 
              text-decoration:none; border-radius:6px; font-weight:bold; 
              box-shadow:0 2px 5px rgba(0,0,0,0.15);">
       🔄 Quay lại trang cá nhân
    </a>
</div>
