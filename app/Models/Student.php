<?php

namespace App\Models;

use App\Core\Model;

/** Student profile model — one-to-one with users */
class Student extends Model
{
    protected string $table = 'students';

    protected function getPrimaryKey(): string
    {
        return 'student_id';
    }

    public function findByUserId(int $userId): ?array
    {
        return $this->findBy('user_id', $userId);
    }

    /**
     * Get full student details joined with user info.
     */
    public function getWithUserDetails(int $studentId): ?array
    {
        $row = $this->db->query(
            'SELECT s.*, u.name, u.email, u.status, u.created_at
             FROM `students` s
             INNER JOIN `users` u ON u.user_id = s.user_id
             WHERE s.student_id = ? LIMIT 1',
            [$studentId]
        )->fetch();

        return $row ?: null;
    }

    /**
     * Generate a unique QR code string for a student (based on student_id + random bytes).
     */
    public function generateQrCode(int $studentId): string
    {
        $code = 'SCC-' . strtoupper(bin2hex(random_bytes(6))) . '-' . $studentId;
        $this->update($studentId, ['qr_code' => $code]);
        return $code;
    }
}
