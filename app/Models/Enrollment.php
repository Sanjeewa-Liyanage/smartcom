<?php

namespace App\Models;

use App\Core\Model;

class Enrollment extends Model
{
    protected string $table = 'enrollments';

    protected function getPrimaryKey(): string
    {
        return 'enrollment_id';
    }

    /**
     * Check if a student is actively enrolled in a class
     */
    public function isEnrolled(int $studentId, int $classId): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} 
                WHERE student_id = ? AND class_id = ? AND status = 'active'";
        return (bool) $this->db->query($sql, [$studentId, $classId])->fetchColumn();
    }
}
