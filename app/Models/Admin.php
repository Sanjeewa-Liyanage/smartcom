<?php

namespace App\Models;

use App\Core\Model;

/** Admin profile model — one-to-one with users */
class Admin extends Model
{
    protected string $table = 'admins';

    protected function getPrimaryKey(): string
    {
        return 'admin_id';
    }

    public function findByUserId(int $userId): ?array
    {
        return $this->findBy('user_id', $userId);
    }
}
