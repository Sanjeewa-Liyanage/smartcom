<?php

namespace App\Models;

use App\Core\Model;

class AttendanceRecord extends Model
{
    protected string $table = 'attendance_records';

    protected function getPrimaryKey(): string
    {
        return 'id';
    }

    /**
     * Check if a user has already marked attendance for a session
     */
    public function isPresent(int $sessionId, int $userId): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} 
                WHERE attendance_session_id = ? AND user_id = ?";
        return (bool) $this->db->query($sql, [$sessionId, $userId])->fetchColumn();
    }

    /**
     * Get attendance stats for a session
     */
    public function getSessionStats(int $sessionId): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE attendance_session_id = ?";
        return (int) $this->db->query($sql, [$sessionId])->fetchColumn();
    }
}
