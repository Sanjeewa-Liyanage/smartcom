<?php

namespace App\Models;

use App\Core\Model;

/** Tutor profile model — one-to-one with users */
class Tutor extends Model
{
    protected string $table = 'tutors';

    protected function getPrimaryKey(): string
    {
        return 'tutor_id';
    }

    public function findByUserId(int $userId): ?array
    {
        return $this->findBy('user_id', $userId);
    }

    /**
     * Return tutor profile joined with user name and email.
     */
    public function getAllWithUserDetails(): array
    {
        return $this->db->query(
            'SELECT t.*, u.name, u.email, u.status, u.created_at
             FROM `tutors` t
             INNER JOIN `users` u ON u.user_id = t.user_id
             ORDER BY u.created_at DESC'
        )->fetchAll();
    }
}
