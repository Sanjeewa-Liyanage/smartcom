ALTER TABLE `students` 
    ADD COLUMN `parent_name` VARCHAR(100) DEFAULT NULL,
    ADD COLUMN `parent_email` VARCHAR(100) DEFAULT NULL;

ALTER TABLE `parents`
    DROP FOREIGN KEY `fk_parent_student`,
    DROP COLUMN `linked_student_id`;
