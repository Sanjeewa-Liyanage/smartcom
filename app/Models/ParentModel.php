<?php

namespace App\Models;

use App\Core\Model;

/** Parent profile model — one-to-one with users, linked to a student */
class ParentModel extends Model
{
    protected string $table = 'parents';

    protected function getPrimaryKey(): string
    {
        return 'parent_id';
    }

    public function findByUserId(int $userId): ?array
    {
        return $this->findBy('user_id', $userId);
    }

    /**
     * Get parent record joined with user details and linked student's name.
     */
    public function getWithDetails(int $parentId): ?array
    {
        $row = $this->db->query(
            'SELECT p.*, u.name, u.email, u.status,
                    su.name AS student_name, s.student_id
             FROM `parents` p
             INNER JOIN `users` u  ON u.user_id  = p.user_id
             LEFT  JOIN `students` s  ON s.parent_id = p.parent_id
             LEFT  JOIN `users` su ON su.user_id  = s.user_id
             WHERE p.parent_id = ? LIMIT 1',
            [$parentId]
        )->fetch();

        return $row ?: null;
    }

    /**
     * Get parent record by user_id, joined with linked student details.
     */
    public function getByUserIdWithDetails(int $userId): ?array
    {
        $row = $this->db->query(
            'SELECT p.*, u.name, u.email,
                    su.name AS student_name, s.student_id
             FROM `parents` p
             INNER JOIN `users` u  ON u.user_id  = p.user_id
             LEFT  JOIN `students` s  ON s.parent_id = p.parent_id
             LEFT  JOIN `users` su ON su.user_id  = s.user_id
             WHERE p.user_id = ? LIMIT 1',
            [$userId]
        )->fetch();

        return $row ?: null;
    }

    /**
     * Get all students linked to this parent.
     */
    public function getLinkedStudents(int $parentId): array
    {
        return $this->db->query(
            'SELECT s.student_id, su.name AS student_name
             FROM `students` s
             INNER JOIN `users` su ON su.user_id = s.user_id
             WHERE s.parent_id = ?',
            [$parentId]
        )->fetchAll();
    }
}
