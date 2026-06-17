<?php

namespace App\Models;

use App\Core\Model;

/**
 * PasswordReset Model
 *
 * Manages OTP tokens for the self-service forgot-password flow.
 * Each OTP is valid for 10 minutes and can only be used once.
 */
class PasswordReset extends Model
{
    protected string $table = 'password_resets';

    protected function getPrimaryKey(): string
    {
        return 'id';
    }

    // ── Create OTP ───────────────────────────────────────────────────────────

    /**
     * Generate a 6-digit OTP for the given user.
     * Invalidates any previous unused OTPs for the same user.
     *
     * @param  int    $userId
     * @return string The 6-digit OTP code
     */
    public function createOtp(int $userId): string
    {
        // Invalidate previous OTPs for this user
        $this->db->query(
            "UPDATE `{$this->table}` SET `used` = 1 WHERE `user_id` = ? AND `used` = 0",
            [$userId]
        );

        // Generate a random 6-digit code
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store with 10-minute expiry (use MySQL's NOW() so the clock matches verifyOtp)
        $this->db->query(
            "INSERT INTO `{$this->table}` (`user_id`, `otp`, `expires_at`) VALUES (?, ?, NOW() + INTERVAL 10 MINUTE)",
            [$userId, $otp]
        );

        return $otp;
    }

    // ── Verify OTP ───────────────────────────────────────────────────────────

    /**
     * Check if a valid, unexpired, unused OTP exists for the user.
     *
     * @param  int    $userId
     * @param  string $otp
     * @return bool
     */
    public function verifyOtp(int $userId, string $otp): bool
    {
        $row = $this->db->query(
            "SELECT * FROM `{$this->table}`
             WHERE `user_id` = ?
               AND `otp` = ?
               AND `used` = 0
               AND `expires_at` > NOW()
             ORDER BY `created_at` DESC
             LIMIT 1",
            [$userId, $otp]
        )->fetch();

        return $row !== false;
    }

    // ── Mark Used ────────────────────────────────────────────────────────────

    /**
     * Mark all OTPs for a user as used (after successful password reset).
     */
    public function markUsed(int $userId): void
    {
        $this->db->query(
            "UPDATE `{$this->table}` SET `used` = 1 WHERE `user_id` = ?",
            [$userId]
        );
    }

    // ── Cleanup ──────────────────────────────────────────────────────────────

    /**
     * Delete expired or used OTP records older than 1 hour.
     */
    public function cleanupExpired(): void
    {
        $this->db->query(
            "DELETE FROM `{$this->table}`
             WHERE `used` = 1
                OR `expires_at` < NOW() - INTERVAL 1 HOUR"
        );
    }

    // ── Rate Limit Check ─────────────────────────────────────────────────────

    /**
     * Check if the user has requested an OTP within the last 60 seconds.
     *
     * @param  int  $userId
     * @return bool True if they must wait, false if they can request
     */
    public function isRateLimited(int $userId): bool
    {
        $row = $this->db->query(
            "SELECT * FROM `{$this->table}`
             WHERE `user_id` = ?
               AND `created_at` > NOW() - INTERVAL 60 SECOND
             ORDER BY `created_at` DESC
             LIMIT 1",
            [$userId]
        )->fetch();

        return $row !== false;
    }
}
