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
}
