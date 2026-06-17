-- ================================================================
-- Smart Commerce Core (SCC) — Phase 1 Database Schema
-- Auth & User Management
-- ================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `comclz_db`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `comclz_db`;

-- ================================================================
-- TABLE: users
-- Core authentication table shared by all roles
-- ================================================================
CREATE TABLE IF NOT EXISTS `users` (
    `user_id`    INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(100)     NOT NULL,
    `email`      VARCHAR(100)     NOT NULL,
    `password`   VARCHAR(255)     NOT NULL,
    `role`       ENUM('admin','tutor','student','parent') NOT NULL,
    `status`     ENUM('pending','active','rejected','suspended') NOT NULL DEFAULT 'pending',
    `is_active`  TINYINT(1)       NOT NULL DEFAULT 0,
    `created_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`),
    UNIQUE KEY `uq_email` (`email`),
    KEY `idx_role`   (`role`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABLE: admins
-- Admin profile (one-to-one with users)
-- ================================================================
CREATE TABLE IF NOT EXISTS `admins` (
    `admin_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`  INT UNSIGNED NOT NULL,
    PRIMARY KEY (`admin_id`),
    UNIQUE KEY `uq_admin_user` (`user_id`),
    CONSTRAINT `fk_admin_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABLE: students
-- Student profile (one-to-one with users)
-- ================================================================
CREATE TABLE IF NOT EXISTS `students` (
    `student_id`     INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `user_id`        INT UNSIGNED  NOT NULL,
    `qr_code`        VARCHAR(255)  DEFAULT NULL,
    `access_granted` TINYINT(1)   NOT NULL DEFAULT 0,
    `parent_id`      INT UNSIGNED  DEFAULT NULL,
    PRIMARY KEY (`student_id`),
    UNIQUE KEY `uq_student_user` (`user_id`),
    CONSTRAINT `fk_student_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABLE: tutors
-- Tutor profile (one-to-one with users)
-- ================================================================
CREATE TABLE IF NOT EXISTS `tutors` (
    `tutor_id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`             INT UNSIGNED NOT NULL,
    `first_name`          VARCHAR(100) DEFAULT NULL,
    `last_name`           VARCHAR(100) DEFAULT NULL,
    `gender`              ENUM('male', 'female', 'other') DEFAULT NULL,
    `profile_completed`   TINYINT(1) NOT NULL DEFAULT 0,
    `assigned_subject_id` INT UNSIGNED DEFAULT NULL,
    `subject`             VARCHAR(100) DEFAULT NULL,
    PRIMARY KEY (`tutor_id`),
    UNIQUE KEY `uq_tutor_user` (`user_id`),
    CONSTRAINT `fk_tutor_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABLE: parents
-- Parent profile linked to a student (one-to-one with users)
-- ================================================================
CREATE TABLE IF NOT EXISTS `parents` (
    `parent_id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`           INT UNSIGNED NOT NULL,
    `linked_student_id` INT UNSIGNED DEFAULT NULL,
    PRIMARY KEY (`parent_id`),
    UNIQUE KEY `uq_parent_user` (`user_id`),
    CONSTRAINT `fk_parent_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_parent_student`
        FOREIGN KEY (`linked_student_id`) REFERENCES `students` (`student_id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- Add FK for students.parent_id → parents.parent_id
-- (Added after parents table is created)
-- ================================================================
ALTER TABLE `students`
    ADD CONSTRAINT `fk_student_parent`
        FOREIGN KEY (`parent_id`) REFERENCES `parents` (`parent_id`)
        ON DELETE SET NULL ON UPDATE CASCADE;

SET FOREIGN_KEY_CHECKS = 1;
