ALTER TABLE `tutors`
    ADD COLUMN `first_name` VARCHAR(100) DEFAULT NULL,
    ADD COLUMN `last_name` VARCHAR(100) DEFAULT NULL,
    ADD COLUMN `gender` ENUM('male', 'female', 'other') DEFAULT NULL,
    ADD COLUMN `profile_completed` TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN `subject` VARCHAR(100) DEFAULT NULL;
