-- ================================================================
-- Smart Commerce Core (SCC) — Phase 3 Database Schema Update
-- Finance Module & User SCC ID
-- ================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- 1. Add scc_id to users table
ALTER TABLE `users`
ADD COLUMN `scc_id` VARCHAR(20) DEFAULT NULL AFTER `user_id`;

-- Generate scc_id for existing users (e.g. SCC-000001)
UPDATE `users`
SET `scc_id` = CONCAT('SCC-', LPAD(`user_id`, 6, '0'));

-- Make scc_id unique
ALTER TABLE `users`
ADD UNIQUE KEY `uq_scc_id` (`scc_id`);

-- 2. Add monthly_fee to classes table
ALTER TABLE `classes`
ADD COLUMN `monthly_fee` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `status`;

-- 3. Create payments table
CREATE TABLE IF NOT EXISTS `payments` (
    `payment_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `student_id` INT UNSIGNED NOT NULL,
    `class_id` INT UNSIGNED NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `month` VARCHAR(20) NOT NULL, -- e.g. "2026-01", "2026-02"
    `transaction_ref` VARCHAR(50) DEFAULT NULL, -- UUID to group multiple months paid together
    `collected_by` INT UNSIGNED NOT NULL,
    `payment_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`payment_id`),
    UNIQUE KEY `uq_payment_month` (`student_id`, `class_id`, `month`),
    KEY `idx_payment_student` (`student_id`),
    KEY `idx_payment_class` (`class_id`),
    KEY `idx_payment_collector` (`collected_by`),
    CONSTRAINT `fk_payment_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_payment_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_payment_user` FOREIGN KEY (`collected_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
