<?php

namespace App\Models;

use App\Core\Model;

class AttendanceSession extends Model
{
    protected string $table = 'attendance_sessions';

    protected function getPrimaryKey(): string
    {
        return 'id';
    }

    /**
     * Find the currently open session for a given class today
     */
    public function getOpenSessionToday(int $classId): ?array
    {
        $today = date('Y-m-d');
        $sql = "SELECT * FROM {$this->table} 
                WHERE class_id = ? AND session_date = ? AND status = 'open' LIMIT 1";
        $row = $this->db->query($sql, [$classId, $today])->fetch();
        return $row ?: null;
    }
}
