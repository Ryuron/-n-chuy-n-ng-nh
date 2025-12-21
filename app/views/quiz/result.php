<style>
/* Nút chấm than */
.btn-report {
    background: red;
    color: white;
    border: none;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    cursor: pointer;
    font-weight: bold;
    line-height: 20px;
    text-align: center;
}

/* Khung báo lỗi */
.error-box {
    display: none;
    background: #fff0f0;
    border: 1px solid #ffb3b3;
    padding: 10px;
    margin-top: 8px;
    border-radius: 6px;
}

/* Nút chọn lỗi */
.error-btn {
    width: 100%;
    padding: 6px;
    background: #ffe5e5;
    border: none;
    cursor: pointer;
    text-align: left;
    border-radius: 4px;
    margin-bottom: 4px;
}
.error-btn:hover {
    background: #ffcccc;
}

/* Layout câu hỏi */
.question-row {
    margin-bottom: 20px;
}
.question-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.correct { color: green; }
.wrong { color: red; }

/* Khung tổng kết */
.summary-box {
    background: white;
    border: 2px solid #0099ff;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 8px;
    color: #003d66;
}
</style>
<a href="index.php" 
   style="
        display:inline-block;
        padding:8px 15px;
        background:#3498db;
        color:white;
        border-radius:6px;
        text-decoration:none;
        margin-bottom:15px;
        font-weight:bold;
        box-shadow:0 2px 4px rgba(0,0,0,0.15);
   ">
    ⬅ Quay lại trang chính
</a>

<h3>Chi tiết câu hỏi</h3>

<?php
// Tính tổng câu và câu đúng
$totalQuestions = count($questions);
$correctCount = 0;

foreach ($questions as $q) {
    if ($q['IsCorrect']) $correctCount++;
}

// Điểm = số câu đúng
$totalScore = $correctCount . "/" . $totalQuestions;
?>

<!-- HIỂN THỊ TỔNG KẾT -->
<div class="summary-box">
    <h3 style="margin:0 0 10px 0;">Kết quả bài làm</h3>
    <p><strong>Số câu đúng:</strong> <?= $correctCount ?> / <?= $totalQuestions ?></p>
    <p><strong>Tổng điểm:</strong> <?= $totalScore ?></p>
</div>

<?php if (isset($_GET['reported'])): ?>
<div style="background:#d4ffd4; border:1px solid green; padding:10px; margin-bottom:10px;">
    ✔ Báo lỗi thành công!
</div>
<?php endif; ?>

<ol>
<?php foreach ($questions as $q): ?>
    <li class="question-row">

        <!-- Tiêu đề câu hỏi + nút ! -->
        <div class="question-header">
            <strong><?= htmlspecialchars($q['Content']) ?></strong>
            <button class="btn-report" onclick="toggleErrorBox(<?= $q['QuestionId'] ?>)">!</button>
        </div>

        <p>Đáp án của bạn: <?= htmlspecialchars($q['UserAnswer'] ?? '-') ?></p>
        <p>Đáp án đúng: <?= htmlspecialchars($q['CorrectAnswer']) ?></p>

        <p class="<?= $q['IsCorrect'] ? 'correct' : 'wrong' ?>">
            <?= $q['IsCorrect'] ? 'Đúng' : 'Sai' ?>
        </p>

        <!-- Hộp chọn lỗi -->
        <div id="error-box-<?= $q['QuestionId'] ?>" class="error-box">
            <form method="POST" action="index.php?controller=quiz&action=quickErrorReport&test_id=<?= $result['TestId'] ?>">
                <input type="hidden" name="question_id" value="<?= $q['QuestionId'] ?>">

                <?php 
                    $errorList = [
                        "Sai đáp án",
                        "Lỗi chính tả",
                        "Nội dung sai",
                        "Dữ liệu không rõ ràng",
                        "Đáp án trùng lặp"
                    ];
                ?>

                <?php foreach ($errorList as $err): ?>
                    <button class="error-btn" name="error_text" value="<?= $err ?>"><?= $err ?></button>
                <?php endforeach; ?>
            </form>
        </div>

    </li>
<?php endforeach; ?>
</ol>

<script>
function toggleErrorBox(id) {
    let box = document.getElementById("error-box-" + id);
    box.style.display = (box.style.display === "block") ? "none" : "block";
}
</script>
