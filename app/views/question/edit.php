<h2>Sửa câu hỏi</h2>

<form method="post" action="index.php?controller=question&action=update&id=<?= $question['QuestionId'] ?>">
    <label>Nội dung câu hỏi:</label><br>
    <textarea name="content" rows="4" cols="60" required><?= htmlspecialchars($question['Content']) ?></textarea><br><br>

    <label>Đáp án A:</label><br>
    <input type="text" name="optionA" value="<?= htmlspecialchars($question['OptionA']) ?>" required><br>

    <label>Đáp án B:</label><br>
    <input type="text" name="optionB" value="<?= htmlspecialchars($question['OptionB']) ?>" required><br>

    <label>Đáp án C:</label><br>
    <input type="text" name="optionC" value="<?= htmlspecialchars($question['OptionC']) ?>" required><br>

    <label>Đáp án D:</label><br>
    <input type="text" name="optionD" value="<?= htmlspecialchars($question['OptionD']) ?>" required><br><br>

    <label>Đáp án đúng:</label><br>
    <select name="correctAnswer" required>
        <option value="<?= htmlspecialchars($question['OptionA']) ?>" <?= $question['CorrectAnswer'] === $question['OptionA'] ? 'selected' : '' ?>>
            A - <?= htmlspecialchars($question['OptionA']) ?>
        </option>
        <option value="<?= htmlspecialchars($question['OptionB']) ?>" <?= $question['CorrectAnswer'] === $question['OptionB'] ? 'selected' : '' ?>>
            B - <?= htmlspecialchars($question['OptionB']) ?>
        </option>
        <option value="<?= htmlspecialchars($question['OptionC']) ?>" <?= $question['CorrectAnswer'] === $question['OptionC'] ? 'selected' : '' ?>>
            C - <?= htmlspecialchars($question['OptionC']) ?>
        </option>
        <option value="<?= htmlspecialchars($question['OptionD']) ?>" <?= $question['CorrectAnswer'] === $question['OptionD'] ? 'selected' : '' ?>>
            D - <?= htmlspecialchars($question['OptionD']) ?>
        </option>
    </select><br><br>

    <label>Môn học:</label><br>
    <select name="subjectId" required>
        <?php foreach ($subjects as $subject): ?>
            <option value="<?= $subject['SubjectId'] ?>" <?= $question['SubjectId'] == $subject['SubjectId'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($subject['SubjectName']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Khối lớp:</label><br>
    <input type="number" name="gradeLevel" value="<?= $question['GradeLevel'] ?>" required><br><br>

    <label>Độ khó:</label><br>
    <select name="difficultyLevel" required>
        <?php foreach (['Dễ', 'TB', 'Khó'] as $level): ?>
            <option value="<?= $level ?>" <?= $question['DifficultyLevel'] === $level ? 'selected' : '' ?>>
                <?= $level ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <input type="submit" value="Cập nhật câu hỏi">
</form>

<br>
<a href="index.php?controller=question&action=index">← Quay lại danh sách câu hỏi</a>
