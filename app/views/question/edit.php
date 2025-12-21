<?php include ROOT_PATH . '/app/views/shares/header.php'; ?>

<style>
    .form-container {
        max-width: 700px;
        margin: auto;
        background: #f8f9fa;
        padding: 20px 25px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        font-size: 16px;
    }
    .form-container h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #007bff;
    }
    .form-container label {
        font-weight: bold;
        margin-top: 12px;
        display: block;
    }
    .form-container input[type="text"],
    .form-container input[type="number"],
    .form-container textarea,
    .form-container select {
        width: 100%;
        padding: 8px 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 6px;
    }
    .form-container input[type="submit"] {
        margin-top: 18px;
        padding: 10px 20px;
        background: #28a745;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
    }
    .form-container input[type="submit"]:hover {
        background: #218838;
    }
    .back-link {
        display: inline-block;
        margin-top: 20px;
        color: #007bff;
        text-decoration: none;
        font-size: 16px;
    }
    .back-link:hover {
        text-decoration: underline;
    }
</style>

<div class="form-container">
    <h2>✏ Sửa câu hỏi</h2>

    <form method="post" action="index.php?controller=question&action=update&id=<?= $question['QuestionId'] ?>">

        <label>Nội dung câu hỏi:</label>
        <textarea name="content" rows="4" required><?= htmlspecialchars($question['Content']) ?></textarea>

        <label>Đáp án A:</label>
        <input type="text" name="optionA" value="<?= htmlspecialchars($question['OptionA']) ?>" required>

        <label>Đáp án B:</label>
        <input type="text" name="optionB" value="<?= htmlspecialchars($question['OptionB']) ?>" required>

        <label>Đáp án C:</label>
        <input type="text" name="optionC" value="<?= htmlspecialchars($question['OptionC']) ?>" required>

        <label>Đáp án D:</label>
        <input type="text" name="optionD" value="<?= htmlspecialchars($question['OptionD']) ?>" required>

        <label>Đáp án đúng:</label>
        <select name="correctAnswer" required>
            <option value="<?= $question['OptionA'] ?>" <?= $question['CorrectAnswer'] === $question['OptionA'] ? 'selected' : '' ?>>
                A - <?= htmlspecialchars($question['OptionA']) ?>
            </option>
            <option value="<?= $question['OptionB'] ?>" <?= $question['CorrectAnswer'] === $question['OptionB'] ? 'selected' : '' ?>>
                B - <?= htmlspecialchars($question['OptionB']) ?>
            </option>
            <option value="<?= $question['OptionC'] ?>" <?= $question['CorrectAnswer'] === $question['OptionC'] ? 'selected' : '' ?>>
                C - <?= htmlspecialchars($question['OptionC']) ?>
            </option>
            <option value="<?= $question['OptionD'] ?>" <?= $question['CorrectAnswer'] === $question['OptionD'] ? 'selected' : '' ?>>
                D - <?= htmlspecialchars($question['OptionD']) ?>
            </option>
        </select>

        <label>Môn học:</label>
        <select name="subjectId" required>
            <?php foreach ($subjects as $subject): ?>
                <option value="<?= $subject['SubjectId'] ?>" <?= $question['SubjectId'] == $subject['SubjectId'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($subject['SubjectName']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Khối lớp:</label>
        <input type="number" name="gradeLevel" value="<?= $question['GradeLevel'] ?>" required>

        <label>Độ khó:</label>
        <select name="difficultyLevel" required>
            <?php foreach (['Dễ', 'TB', 'Khó'] as $level): ?>
                <option value="<?= $level ?>" <?= $question['DifficultyLevel'] === $level ? 'selected' : '' ?>>
                    <?= $level ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="submit" value="Cập nhật câu hỏi">
    </form>

    <a class="back-link" href="index.php?controller=question&action=index">← Quay lại danh sách câu hỏi</a>
</div>

<?php include ROOT_PATH . '/app/views/shares/footer.php'; ?>
