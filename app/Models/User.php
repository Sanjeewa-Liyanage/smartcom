<?php

namespace App\Models;

use App\Core\Model;

/**
 * User Model
 *
 * Handles all database operations for the `users` table.
 * Passwords are always hashed with bcrypt (cost 12) — never stored in plain text.
 */
class User extends Model
{
    protected string $table = 'users';

    protected function getPrimaryKey(): string
    {
        return 'user_id';
    }

    // ── Lookup ────────────────────────────────────────────────────────────────

    public function findByEmail(string $email): ?array
    {
        return $this->findBy('email', $email);
    }

    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }

    public function findBySccId(string $sccId): ?array
    {
        return $this->findBy('scc_id', $sccId);
    }

    // ── Creation ──────────────────────────────────────────────────────────────

    /**
     * Create a user with a bcrypt-hashed password.
     * Pass plain-text password in $data['password']; it will be hashed here.
     */
    public function createUser(array $data): int
    {
        $data['password'] = password_hash(
            $data['password'],
            PASSWORD_BCRYPT,
            ['cost' => 12]
        );
        
        $userId = $this->create($data);
        
        // Generate and set scc_id
        $sccId = 'SCC-' . str_pad((string)$userId, 6, '0', STR_PAD_LEFT);
        $this->update($userId, ['scc_id' => $sccId]);
        
        return $userId;
    }

    // ── Auth ──────────────────────────────────────────────────────────────────

    public function verifyPassword(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }

    // ── Statistics ────────────────────────────────────────────────────────────

    public function countByRole(string $role): int
    {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM `users` WHERE `role` = ?",
            [$role]
        )->fetchColumn();
    }

    public function countByStatus(string $status): int
    {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM `users` WHERE `status` = ?",
            [$status]
        )->fetchColumn();
    }

    // ── Filtering ─────────────────────────────────────────────────────────────

    /**
     * Return all users, optionally filtered by role and/or status.
     */
    public function getAllWithFilters(?string $role = null, ?string $status = null): array
    {
        $sql    = 'SELECT u.*, s.parent_id, s.parent_name, s.parent_email, s.qr_code FROM `users` u LEFT JOIN `students` s ON u.user_id = s.user_id WHERE 1=1';
        $params = [];

        if ($role !== null && $role !== '') {
            $sql      .= ' AND u.`role` = ?';
            $params[]  = $role;
        }
        if ($status !== null && $status !== '') {
            $sql      .= ' AND u.`status` = ?';
            $params[]  = $status;
        }

        $sql .= ' ORDER BY u.`created_at` DESC';

        return $this->db->query($sql, $params)->fetchAll();
    }

    public function getPendingUsers(): array
    {
        return $this->db->query(
            "SELECT * FROM `users` WHERE `status` = 'pending' ORDER BY `created_at` DESC"
        )->fetchAll();
    }

    // ── Admin Actions ─────────────────────────────────────────────────────────

    public function approve(int $userId): void
    {
        $this->update($userId, ['status' => 'active', 'is_active' => 1]);
    }

    public function reject(int $userId): void
    {
        $this->update($userId, ['status' => 'rejected', 'is_active' => 0]);
    }

    public function suspend(int $userId): void
    {
        $this->update($userId, ['status' => 'suspended', 'is_active' => 0]);
    }

    public function activate(int $userId): void
    {
        $this->update($userId, ['status' => 'active', 'is_active' => 1]);
    }

    public function resetPassword(int $userId, string $newPassword): void
    {
        $this->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]),
        ]);
    }
}
