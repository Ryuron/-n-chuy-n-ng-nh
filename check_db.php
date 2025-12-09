<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=AdaptiveQuizDB', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== Cấu trúc bảng Users ===\n";
    $stmt = $pdo->query('DESCRIBE Users');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . ' - ' . $row['Type'] . "\n";
    }

    echo "\n=== Dữ liệu mẫu ===\n";
    $stmt2 = $pdo->query('SELECT * FROM Users LIMIT 1');
    $sample = $stmt2->fetch(PDO::FETCH_ASSOC);
    if ($sample) {
        foreach ($sample as $key => $value) {
            echo "$key: $value\n";
        }
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
