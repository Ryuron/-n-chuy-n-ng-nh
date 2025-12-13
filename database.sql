-- --------------------------------------------------------
-- Máy chủ:                      127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Phiên bản:           12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table adaptivequizdb.questionerrorreports
CREATE TABLE IF NOT EXISTS `questionerrorreports` (
  `ErrorId` int NOT NULL AUTO_INCREMENT,
  `QuestionId` int NOT NULL,
  `ErrorText` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ReportedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `IsRead` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`ErrorId`),
  KEY `QuestionId` (`QuestionId`),
  CONSTRAINT `questionerrorreports_ibfk_1` FOREIGN KEY (`QuestionId`) REFERENCES `questions` (`QuestionId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table adaptivequizdb.questions
CREATE TABLE IF NOT EXISTS `questions` (
  `QuestionId` int NOT NULL AUTO_INCREMENT,
  `Content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `OptionA` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `OptionB` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `OptionC` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `OptionD` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `CorrectAnswer` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `AnswerCount` int NOT NULL DEFAULT '0',
  `CorrectCount` int NOT NULL DEFAULT '0',
  `SubjectId` int NOT NULL,
  `GradeLevel` int NOT NULL,
  `DifficultyLevel` enum('Dễ','TB','Khó') COLLATE utf8mb4_unicode_ci NOT NULL,
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`QuestionId`),
  KEY `SubjectId` (`SubjectId`),
  CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`SubjectId`) REFERENCES `subjects` (`SubjectId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table adaptivequizdb.questionstats
CREATE TABLE IF NOT EXISTS `questionstats` (
  `QuestionId` int NOT NULL,
  `WordCount` int NOT NULL,
  `FactCount` int NOT NULL,
  `Score` float NOT NULL,
  PRIMARY KEY (`QuestionId`),
  CONSTRAINT `fk_qstats_question` FOREIGN KEY (`QuestionId`) REFERENCES `questions` (`QuestionId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table adaptivequizdb.results
CREATE TABLE IF NOT EXISTS `results` (
  `ResultId` int NOT NULL AUTO_INCREMENT,
  `TestId` int NOT NULL,
  `UserId` int NOT NULL,
  `SubjectId` int NOT NULL,
  `TotalQuestions` int NOT NULL,
  `CorrectAnswers` int NOT NULL,
  `WrongAnswers` int NOT NULL,
  `Score` decimal(5,2) NOT NULL,
  `FinalLevel` enum('Yếu','TB','Khá','Giỏi') COLLATE utf8mb4_unicode_ci NOT NULL,
  `CompletedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ResultId`),
  UNIQUE KEY `TestId` (`TestId`),
  KEY `UserId` (`UserId`),
  KEY `SubjectId` (`SubjectId`),
  CONSTRAINT `results_ibfk_1` FOREIGN KEY (`TestId`) REFERENCES `tests` (`TestId`) ON DELETE CASCADE,
  CONSTRAINT `results_ibfk_2` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`) ON DELETE CASCADE,
  CONSTRAINT `results_ibfk_3` FOREIGN KEY (`SubjectId`) REFERENCES `subjects` (`SubjectId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table adaptivequizdb.subjectfacts
CREATE TABLE IF NOT EXISTS `subjectfacts` (
  `FactId` int NOT NULL AUTO_INCREMENT,
  `SubjectId` int NOT NULL,
  `FactText` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Weight` float DEFAULT '1',
  PRIMARY KEY (`FactId`),
  KEY `fk_subjectfacts_subject` (`SubjectId`),
  CONSTRAINT `fk_subjectfacts_subject` FOREIGN KEY (`SubjectId`) REFERENCES `subjects` (`SubjectId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table adaptivequizdb.subjects
CREATE TABLE IF NOT EXISTS `subjects` (
  `SubjectId` int NOT NULL AUTO_INCREMENT,
  `SubjectName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Description` text COLLATE utf8mb4_unicode_ci,
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`SubjectId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table adaptivequizdb.testquestions
CREATE TABLE IF NOT EXISTS `testquestions` (
  `TestQuestionId` int NOT NULL AUTO_INCREMENT,
  `TestId` int NOT NULL,
  `QuestionId` int NOT NULL,
  `UserAnswer` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `IsCorrect` tinyint(1) DEFAULT NULL,
  `AnsweredAt` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`TestQuestionId`),
  KEY `TestId` (`TestId`),
  KEY `QuestionId` (`QuestionId`),
  CONSTRAINT `testquestions_ibfk_1` FOREIGN KEY (`TestId`) REFERENCES `tests` (`TestId`) ON DELETE CASCADE,
  CONSTRAINT `testquestions_ibfk_2` FOREIGN KEY (`QuestionId`) REFERENCES `questions` (`QuestionId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table adaptivequizdb.tests
CREATE TABLE IF NOT EXISTS `tests` (
  `TestId` int NOT NULL AUTO_INCREMENT,
  `UserId` int NOT NULL,
  `SubjectId` int NOT NULL,
  `GradeLevel` int NOT NULL,
  `TotalQuestions` int DEFAULT '40',
  `Score` int DEFAULT '0',
  `CurrentLevel` enum('Yếu','TB','Khá','Giỏi') COLLATE utf8mb4_unicode_ci DEFAULT 'TB',
  `StartedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `CompletedAt` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`TestId`),
  KEY `UserId` (`UserId`),
  KEY `SubjectId` (`SubjectId`),
  CONSTRAINT `tests_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`) ON DELETE CASCADE,
  CONSTRAINT `tests_ibfk_2` FOREIGN KEY (`SubjectId`) REFERENCES `subjects` (`SubjectId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table adaptivequizdb.users
CREATE TABLE IF NOT EXISTS `users` (
  `UserId` int NOT NULL AUTO_INCREMENT,
  `Username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `FullName` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `GradeLevel` int NOT NULL,
  `Role` enum('Admin','Student') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Student',
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`UserId`),
  UNIQUE KEY `Username` (`Username`),
  UNIQUE KEY `Email` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table adaptivequizdb.usersubjectlevel
CREATE TABLE IF NOT EXISTS `usersubjectlevel` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `UserId` int NOT NULL,
  `SubjectId` int NOT NULL,
  `CurrentLevel` enum('Yếu','TB','Giỏi') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Yếu',
  `st` int NOT NULL DEFAULT '0',
  `UpdatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `uq_user_subject` (`UserId`,`SubjectId`),
  KEY `fk_usl_subject` (`SubjectId`),
  CONSTRAINT `fk_usl_subject` FOREIGN KEY (`SubjectId`) REFERENCES `subjects` (`SubjectId`) ON DELETE CASCADE,
  CONSTRAINT `fk_usl_user` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;