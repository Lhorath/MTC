-- =============================================================================
-- MyTinyCollege — MySQL / MariaDB schema for HeidiSQL
-- Run the whole file (F9) or execute selected statements.
-- Edit the database name below if you want something other than mytinycollege.
-- =============================================================================

CREATE DATABASE IF NOT EXISTS `mytinycollege`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `mytinycollege`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `enrollments`;
DROP TABLE IF EXISTS `password_resets`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `courses`;
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `departments`;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------- departments
CREATE TABLE `departments` (
  `DepartmentID` INT NOT NULL AUTO_INCREMENT,
  `Name` VARCHAR(100) NOT NULL,
  `Budget` DECIMAL(18,2) NULL DEFAULT NULL,
  `StartDate` DATE NULL DEFAULT NULL,
  `AdministratorId` INT NULL DEFAULT NULL,
  PRIMARY KEY (`DepartmentID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------- students
CREATE TABLE `students` (
  `ID` INT NOT NULL AUTO_INCREMENT,
  `LastName` VARCHAR(65) NOT NULL,
  `FirstName` VARCHAR(50) NOT NULL,
  `Email` VARCHAR(255) NULL DEFAULT NULL,
  `EnrollmentDate` DATE NOT NULL,
  PRIMARY KEY (`ID`),
  INDEX `idx_students_names` (`LastName`, `FirstName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------- courses
CREATE TABLE `courses` (
  `CourseID` INT NOT NULL,
  `Title` VARCHAR(50) NOT NULL,
  `Credits` INT NOT NULL,
  `DepartmentID` INT NOT NULL,
  PRIMARY KEY (`CourseID`),
  INDEX `idx_courses_department` (`DepartmentID`),
  CONSTRAINT `fk_courses_department`
    FOREIGN KEY (`DepartmentID`) REFERENCES `departments` (`DepartmentID`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------ enrollments
CREATE TABLE `enrollments` (
  `EnrollmentID` INT NOT NULL AUTO_INCREMENT,
  `CourseID` INT NOT NULL,
  `StudentID` INT NOT NULL,
  `Grade` CHAR(1) NULL DEFAULT NULL,
  PRIMARY KEY (`EnrollmentID`),
  INDEX `idx_enrollments_course` (`CourseID`),
  INDEX `idx_enrollments_student` (`StudentID`),
  CONSTRAINT `fk_enrollments_course`
    FOREIGN KEY (`CourseID`) REFERENCES `courses` (`CourseID`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_enrollments_student`
    FOREIGN KEY (`StudentID`) REFERENCES `students` (`ID`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------ users
CREATE TABLE `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(256) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------- password_resets
CREATE TABLE `password_resets` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(256) NOT NULL,
  `token` CHAR(64) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_password_resets_token` (`token`),
  INDEX `idx_password_resets_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- Seed data
-- =============================================================================

INSERT INTO `departments` (`DepartmentID`, `Name`, `Budget`, `StartDate`) VALUES
  (1, 'English', 350000.00, '2007-09-01'),
  (2, 'Mathematics', 100000.00, '2007-09-01'),
  (3, 'Engineering', 350000.00, '2007-09-01'),
  (4, 'Economics', 100000.00, '2007-09-01');

INSERT INTO `courses` (`CourseID`, `Title`, `Credits`, `DepartmentID`) VALUES
  (1045, 'Calculus', 4, 2),
  (3141, 'Trigonometry', 4, 2),
  (2021, 'Composition', 3, 1),
  (2042, 'Literature', 4, 1);

INSERT INTO `students` (`LastName`, `FirstName`, `Email`, `EnrollmentDate`) VALUES
  ('Alexander', 'Carson', 'calexander@example.com', '2016-09-01'),
  ('Alonso', 'Meredith', 'malonso@example.com', '2017-09-01'),
  ('Anand', 'Arturo', 'aanand@example.com', '2018-09-01'),
  ('Barzdukas', 'Gytis', 'gbarzdukas@example.com', '2016-09-01'),
  ('Li', 'Yan', 'yan@example.com', '2017-09-01');

INSERT INTO `enrollments` (`CourseID`, `StudentID`, `Grade`) VALUES
  (1045, 1, 'A'),
  (3141, 1, NULL),
  (2021, 2, 'B'),
  (2042, 2, 'B'),
  (1045, 3, 'C'),
  (3141, 4, NULL);

-- App users: use Register in the web UI, or INSERT with a bcrypt hash from PHP:
--   php -r "echo password_hash('YourPassword', PASSWORD_DEFAULT), PHP_EOL;"

-- =============================================================================
-- Done. Point config/config.php at this database:
--   dsn => 'mysql:host=127.0.0.1;dbname=mytinycollege;charset=utf8mb4'
-- =============================================================================
