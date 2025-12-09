<h2 style="text-align:center; color:#2c3e50; margin-bottom:20px;">📊 Kết quả bài thi</h2>

<div style="text-align:center; margin-bottom:30px; font-size:18px; font-weight:bold;">
    Điểm số: <span style="color:#27ae60;"><?= $_SESSION['last_score'] ?? 0 ?>%</span>
</div>

<?php foreach ($details as $index => $d): 
    $options = $d['Options'] ?? [
        'A' => $d['OptionA'] ?? '',
        'B' => $d['OptionB'] ?? '',
        'C' => $d['OptionC'] ?? '',
        'D' => $d['OptionD'] ?? ''
    ];
    $userAnswerContent = trim((string)($d['UserAnswerContent'] ?? ''));
    $correctAnswerContent = trim((string)($d['CorrectAnswerContent'] ?? ''));
?>
    <div style="margin-bottom:20px; padding:15px; border:1px solid #ddd; border-radius:8px; 
                box-shadow:0 2px 5px rgba(0,0,0,0.05); background:#fafafa;">
        <p style="font-weight:bold; margin-bottom:10px; color:#2c3e50;">
            Câu <?= $index + 1 ?>: <?= htmlspecialchars($d['Content'] ?? '') ?>
        </p>

        <?php foreach ($options as $optKey => $optVal): 
            $optValStr = trim((string)$optVal);
            $style = '';
            if ($optValStr === $userAnswerContent) {
                $style = $userAnswerContent === $correctAnswerContent 
                         ? 'background:#d4edda; color:#155724; font-weight:bold; padding:4px 8px; border-radius:5px;' 
                         : 'background:#f8d7da; color:#721c24; font-weight:bold; padding:4px 8px; border-radius:5px;';
            } elseif ($optValStr === $correctAnswerContent) {
                $style = 'background:#d4edda; color:#155724; padding:4px 8px; border-radius:5px;';
            }
        ?>
            <div style="margin:4px 0; <?= $style ?>">
                <?= $optKey ?>. <?= htmlspecialchars($optVal) ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<div style="text-align:center; margin-top:30px;">
    <a href="index.php?controller=User&action=index" 
       style="display:inline-block; padding:10px 20px; background:#3498db; color:white; 
              text-decoration:none; border-radius:8px; font-weight:bold; 
              box-shadow:0 2px 5px rgba(0,0,0,0.15);">
       🔄 Quay lại
    </a>
</div>
