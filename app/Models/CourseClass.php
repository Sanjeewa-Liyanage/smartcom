<?php

namespace App\Models;

use App\Core\Model;

class CourseClass extends Model
{
    protected string $table = 'classes';

    protected function getPrimaryKey(): string
    {
        return 'class_id';
    }

    /**
     * Get class details along with subject and tutor info
     */
    public function getClassWithDetails(int $classId): ?array
    {
        $sql = "
            SELECT c.*, s.name as subject_name, s.code as subject_code, 
                   t.first_name as tutor_fname, t.last_name as tutor_lname
            FROM classes c
            JOIN subjects s ON c.subject_id = s.subject_id
            JOIN tutors t ON c.tutor_id = t.tutor_id
            WHERE c.class_id = ?
        ";
        $row = $this->db->query($sql, [$classId])->fetch();
        return $row ?: null;
    }

    /**
     * Get classes assigned to a specific tutor
     */
    public function getClassesByTutor(int $tutorId): array
    {
        $sql = "
            SELECT c.*, s.name as subject_name, s.code as subject_code
            FROM classes c
            JOIN subjects s ON c.subject_id = s.subject_id
            WHERE c.tutor_id = ?
            ORDER BY c.created_at DESC
        ";
        return $this->db->query($sql, [$tutorId])->fetchAll();
    }

    /**
     * Get classes a student is enrolled in
     */
    public function getEnrolledClasses(int $studentId): array
    {
        $sql = "
            SELECT c.*, s.name as subject_name, s.code as subject_code,
                   t.first_name as tutor_fname, t.last_name as tutor_lname
            FROM classes c
            JOIN enrollments e ON c.class_id = e.class_id
            JOIN subjects s ON c.subject_id = s.subject_id
            JOIN tutors t ON c.tutor_id = t.tutor_id
            WHERE e.student_id = ? AND e.status = 'active'
            ORDER BY c.created_at DESC
        ";
        return $this->db->query($sql, [$studentId])->fetchAll();
    }
}
