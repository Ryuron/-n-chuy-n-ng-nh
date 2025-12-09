<h2 style="text-align:center; color:#2c3e50; margin-bottom:20px;">📑 Chi tiết bài làm</h2>

<div style="text-align:center; margin-bottom:25px; font-size:16px;">
    <p><b>Điểm số:</b> <span style="color:#27ae60; font-size:18px;"><?= htmlspecialchars($score ?? 0) ?>%</span></p>
    <p><b>Ngày làm bài:</b> <?= date("d/m/Y H:i", strtotime($quizResult['CreatedAt'] ?? '')) ?></p>
</div>

<?php foreach ($details as $index => $d): ?>
    <div style="margin-bottom:20px; padding:15px; border:1px solid #ddd; border-radius:8px;
                background:#fafafa; box-shadow:0 2px 5px rgba(0,0,0,0.05);">
        <p style="font-weight:bold; margin-bottom:10px; color:#2c3e50;">
            Câu <?= $index + 1 ?>: <?= htmlspecialchars($d['Content']) ?>
        </p>
        
        <?php foreach (['A','B','C','D'] as $optKey): ?>
            <?php
                $style = '';
                if ($optKey === $d['UserAnswer']) {
                    $style = $d['IsCorrect'] 
                        ? 'background:#d4edda; color:#155724; font-weight:bold; padding:4px 8px; border-radius:5px;' 
                        : 'background:#f8d7da; color:#721c24; font-weight:bold; padding:4px 8px; border-radius:5px;';
                } elseif ($optKey === $d['CorrectAnswer']) {
                    $style = !$d['IsCorrect'] 
                        ? 'background:#d4edda; color:#155724; padding:4px 8px; border-radius:5px;' 
                        : '';
                }
            ?>
            <div style="margin:4px 0; <?= $style ?>">
                <?= $optKey ?>. <?= htmlspecialchars($d['Option'.$optKey]) ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<div style="text-align:center; margin-top:25px;">
    <a href="index.php?controller=quiz&action=history" 
       style="display:inline-block; padding:10px 20px; background:#3498db; color:white; 
              text-decoration:none; border-radius:6px; font-weight:bold; 
              box-shadow:0 2px 5px rgba(0,0,0,0.15);">
       🔙 Quay lại lịch sử làm bài
    </a>
</div>
