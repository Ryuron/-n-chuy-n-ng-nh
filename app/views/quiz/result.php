<style>
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
.error-box {
    display: none;
    background: #fff0f0;
    border: 1px solid #ffb3b3;
    padding: 10px;
    margin-top: 5px;
    border-radius: 6px;
}
.error-btn {
    width: 100%;
    padding: 5px;
    background: #ffe5e5;
    border: none;
    cursor: pointer;
    text-align: left;
    margin-bottom: 4px;
}
.error-btn:hover {
    background: #ffcccc;
}
</style>
<h3>Chi tiết câu hỏi</h3>

<?php if (isset($_GET['reported'])): ?>
<div style="background:#d4ffd4; border:1px solid green; padding:10px; margin-bottom:10px;">
    ✔ Báo lỗi thành công!
</div>
<?php endif; ?>

<ol>
<?php foreach ($questions as $q): ?>
    <li style="margin-bottom: 20px;">
        
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <strong><?= htmlspecialchars($q['Content']) ?></strong>

            <!-- nút ! -->
            <button class="btn-report" onclick="toggleErrorBox(<?= $q['QuestionId'] ?>)">!</button>
        </div>

        <p>Đáp án của bạn: <?= htmlspecialchars($q['UserAnswer'] ?? '-') ?></p>
        <p>Đáp án đúng: <?= htmlspecialchars($q['CorrectAnswer']) ?></p>
        <p style="color: <?= $q['IsCorrect'] ? 'green' : 'red' ?>;">
            <?= $q['IsCorrect'] ? 'Đúng' : 'Sai' ?>
        </p>

        <!-- hộp lỗi -->
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
    box.style.display = (box.style.display === "none" || box.style.display === "") ? "block" : "none";
}
</script>
