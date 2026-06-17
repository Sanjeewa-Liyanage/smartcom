<?php

namespace App\Middleware;

use App\Core\JWT;

/**
 * AuthMiddleware
 *
 * Ensures the request comes from a logged-in user.
 * Checks PHP session first; falls back to the JWT HttpOnly cookie.
 * Redirects to /login if neither is valid.
 */
class AuthMiddleware
{
    public function handle(): void
    {
        // 1. Valid PHP session already established
        if (!empty($_SESSION['user_id'])) {
            return;
        }

        // 2. Try the JWT HttpOnly cookie as a fallback (e.g. session expired)
        $token = $_COOKIE['jwt_token'] ?? '';
        if ($token !== '') {
            $payload = JWT::decode($token);
            if ($payload !== null) {
                // Restore session from JWT payload
                $_SESSION['user_id'] = $payload['user_id'];
                $_SESSION['role']    = $payload['role'];
                $_SESSION['name']    = $payload['name'];
                $_SESSION['email']   = $payload['email'] ?? '';
                return;
            }
        }

        // 3. Not authenticated — redirect to login
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}
