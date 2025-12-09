-- =====================================================
-- DATABASE: AdaptiveQuizDB
-- Hệ thống thi trắc nghiệm thích ứng
-- =====================================================

-- 1. Bảng Users - Quản lý người dùng (Admin và Student)
CREATE TABLE Users (
    UserId INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(255) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Email VARCHAR(255) NOT NULL UNIQUE,
    FullName VARCHAR(255),
    Role ENUM('Admin', 'Student') NOT NULL DEFAULT 'Student',
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Bảng Subjects - Quản lý các môn học
CREATE TABLE Subjects (
    SubjectId INT AUTO_INCREMENT PRIMARY KEY,
    SubjectName VARCHAR(255) NOT NULL,
    Description TEXT,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Bảng Questions - Quản lý câu hỏi
CREATE TABLE Questions (
    QuestionId INT AUTO_INCREMENT PRIMARY KEY,
    Content TEXT NOT NULL,
    OptionA VARCHAR(500) NOT NULL,
    OptionB VARCHAR(500) NOT NULL,
    OptionC VARCHAR(500) NOT NULL,
    OptionD VARCHAR(500) NOT NULL,
    CorrectAnswer VARCHAR(500) NOT NULL,  -- Lưu nội dung đáp án đúng (không phải A/B/C/D)
    SubjectId INT NOT NULL,
    GradeLevel INT NOT NULL,  -- Khối lớp (1-12)
    DifficultyLevel ENUM('Dễ', 'TB', 'Khó') NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (SubjectId) REFERENCES Subjects(SubjectId) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Bảng Tests - Quản lý các bài kiểm tra
CREATE TABLE Tests (
    TestId INT AUTO_INCREMENT PRIMARY KEY,
    UserId INT NOT NULL,
    SubjectId INT NOT NULL,
    GradeLevel INT NOT NULL,
    TotalQuestions INT DEFAULT 40,
    Score INT DEFAULT 0,
    CurrentLevel ENUM('Yếu', 'TB', 'Khá', 'Giỏi') DEFAULT 'TB',
    StartedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CompletedAt TIMESTAMP NULL,
    FOREIGN KEY (UserId) REFERENCES Users(UserId) ON DELETE CASCADE,
    FOREIGN KEY (SubjectId) REFERENCES Subjects(SubjectId) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Bảng TestQuestions - Liên kết câu hỏi với bài kiểm tra
CREATE TABLE TestQuestions (
    TestQuestionId INT AUTO_INCREMENT PRIMARY KEY,
    TestId INT NOT NULL,
    QuestionId INT NOT NULL,
    UserAnswer VARCHAR(500),
    IsCorrect BOOLEAN DEFAULT NULL,
    AnsweredAt TIMESTAMP NULL,
    FOREIGN KEY (TestId) REFERENCES Tests(TestId) ON DELETE CASCADE,
    FOREIGN KEY (QuestionId) REFERENCES Questions(QuestionId) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Bảng Results - Kết quả chi tiết của bài kiểm tra
CREATE TABLE Results (
    ResultId INT AUTO_INCREMENT PRIMARY KEY,
    TestId INT NOT NULL UNIQUE,
    UserId INT NOT NULL,
    SubjectId INT NOT NULL,
    TotalQuestions INT NOT NULL,
    CorrectAnswers INT NOT NULL,
    WrongAnswers INT NOT NULL,
    Score DECIMAL(5,2) NOT NULL,
    FinalLevel ENUM('Yếu', 'TB', 'Khá', 'Giỏi') NOT NULL,
    CompletedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (TestId) REFERENCES Tests(TestId) ON DELETE CASCADE,
    FOREIGN KEY (UserId) REFERENCES Users(UserId) ON DELETE CASCADE,
    FOREIGN KEY (SubjectId) REFERENCES Subjects(SubjectId) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DỮ LIỆU MẪU
-- =====================================================

-- Thêm tài khoản Admin mặc định (password: admin123)
INSERT INTO Users (Username, Password, Email, FullName, Role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com', 'Quản trị viên', 'Admin');

-- Thêm một số tài khoản học sinh mẫu (password: 123456)
INSERT INTO Users (Username, Password, Email, FullName, Role) VALUES
('student1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student1@example.com', 'Nguyễn Văn A', 'Student'),
('student2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student2@example.com', 'Trần Thị B', 'Student');

-- Thêm các môn học
INSERT INTO Subjects (SubjectName, Description) VALUES
('Toán học', 'Môn Toán - Tất cả các khối'),
('Ngữ văn', 'Môn Ngữ văn - Tất cả các khối'),
('Tiếng Anh', 'Môn Tiếng Anh - Tất cả các khối'),
('Vật lý', 'Môn Vật lý - Khối THPT'),
('Hóa học', 'Môn Hóa học - Khối THPT'),
('Sinh học', 'Môn Sinh học - Tất cả các khối'),
('Lịch sử', 'Môn Lịch sử - Tất cả các khối'),
('Địa lý', 'Môn Địa lý - Tất cả các khối');

-- Thêm một số câu hỏi mẫu
INSERT INTO Questions (Content, OptionA, OptionB, OptionC, OptionD, CorrectAnswer, SubjectId, GradeLevel, DifficultyLevel) VALUES
('2 + 2 = ?', '3', '4', '5', '6', '4', 1, 1, 'Dễ'),
('5 x 7 = ?', '30', '35', '40', '45', '35', 1, 2, 'TB'),
('Thủ đô của Việt Nam là?', 'Hà Nội', 'TP. Hồ Chí Minh', 'Đà Nẵng', 'Huế', 'Hà Nội', 2, 3, 'Dễ'),
('Ai là tác giả truyện Kiều?', 'Nguyễn Du', 'Hồ Chí Minh', 'Nguyễn Trãi', 'Lý Thái Tổ', 'Nguyễn Du', 2, 9, 'TB'),
('Trái Đất quay quanh?', 'Mặt Trời', 'Mặt Trăng', 'Sao Hỏa', 'Sao Kim', 'Mặt Trời', 6, 5, 'Dễ');

-- =====================================================
-- INDEX ĐỂ TỐI ƯU HIỆU NĂNG
-- =====================================================

CREATE INDEX idx_questions_subject ON Questions(SubjectId);
CREATE INDEX idx_questions_grade ON Questions(GradeLevel);
CREATE INDEX idx_questions_difficulty ON Questions(DifficultyLevel);
CREATE INDEX idx_tests_user ON Tests(UserId);
CREATE INDEX idx_tests_subject ON Tests(SubjectId);
CREATE INDEX idx_results_user ON Results(UserId);
CREATE INDEX idx_testquestions_test ON TestQuestions(TestId);
CREATE INDEX idx_testquestions_question ON TestQuestions(QuestionId);
