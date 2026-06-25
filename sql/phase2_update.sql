-- Phase 2 Database Schema Update
-- Subjects, Classes (Batches), Enrollments, and Attendance

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- ================================================================
-- TABLE: subjects
-- Dynamic subjects to avoid hardcoding (e.g. Accounting, Econ)
-- ================================================================
CREATE TABLE IF NOT EXISTS `subjects` (
    `subject_id`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`         VARCHAR(100) NOT NULL,
    `code`         VARCHAR(20)  NOT NULL,
    `description`  TEXT         DEFAULT NULL,
    `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`subject_id`),
    UNIQUE KEY `uq_subject_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABLE: classes
-- Represents a specific batch of a subject (e.g. 2026 Accounting)
-- ================================================================
CREATE TABLE IF NOT EXISTS `classes` (
    `class_id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `subject_id`       INT UNSIGNED NOT NULL,
    `tutor_id`         INT UNSIGNED NOT NULL,
    `name`             VARCHAR(100) NOT NULL, -- e.g., "2026 A/L Accounting - English Medium"
    `schedule_details` VARCHAR(255) DEFAULT NULL, -- e.g., "Mondays 8AM - 12PM"
    `status`           ENUM('active','completed','cancelled') NOT NULL DEFAULT 'active',
    `created_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`class_id`),
    KEY `idx_class_subject` (`subject_id`),
    KEY `idx_class_tutor`   (`tutor_id`),
    CONSTRAINT `fk_class_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_class_tutor`   FOREIGN KEY (`tutor_id`)   REFERENCES `tutors` (`tutor_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABLE: enrollments
-- Students enrolling in a specific class
-- ================================================================
CREATE TABLE IF NOT EXISTS `enrollments` (
    `enrollment_id`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `student_id`      INT UNSIGNED NOT NULL,
    `class_id`        INT UNSIGNED NOT NULL,
    `enrollment_date` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status`          ENUM('active','dropped','suspended') NOT NULL DEFAULT 'active',
    PRIMARY KEY (`enrollment_id`),
    UNIQUE KEY `uq_enrollment` (`student_id`, `class_id`),
    KEY `idx_enrollment_class` (`class_id`),
    CONSTRAINT `fk_enrollment_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_enrollment_class`   FOREIGN KEY (`class_id`)   REFERENCES `classes` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABLE: attendance_sessions
-- Daily sessions opened by tutor/admin for a specific class
-- ================================================================
CREATE TABLE IF NOT EXISTS `attendance_sessions` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `class_id`     INT UNSIGNED NOT NULL,
    `session_date` DATE         NOT NULL,
    `status`       ENUM('open','closed') NOT NULL DEFAULT 'open',
    `started_by`   INT UNSIGNED NOT NULL, -- user_id of the tutor/admin who opened it
    `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_session_class` (`class_id`),
    CONSTRAINT `fk_session_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_session_user`  FOREIGN KEY (`started_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABLE: attendance_records
-- Individual student scans/manual entries
-- ================================================================
CREATE TABLE IF NOT EXISTS `attendance_records` (
    `id`                    INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `attendance_session_id` INT UNSIGNED NOT NULL,
    `user_id`               INT UNSIGNED NOT NULL, -- the student's user_id
    `scanned_at`            DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `method`                ENUM('qr_scan','manual') NOT NULL DEFAULT 'qr_scan',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_attendance_record` (`attendance_session_id`, `user_id`),
    CONSTRAINT `fk_record_session` FOREIGN KEY (`attendance_session_id`) REFERENCES `attendance_sessions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_record_user`    FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
