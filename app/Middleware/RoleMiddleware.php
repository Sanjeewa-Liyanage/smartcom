<?php

namespace App\Middleware;

/**
 * RoleMiddleware — RBAC Role Enforcement
 *
 * Compares the authenticated user's session role against the required role.
 * Shows a 403 Forbidden page if the roles don't match.
 *
 * Usage (in public/index.php):
 *   $requireAdmin = function () {
 *       (new AuthMiddleware())->handle();
 *       (new RoleMiddleware('admin'))->handle();
 *   };
 */
class RoleMiddleware
{
    public function __construct(private readonly string $requiredRole)
    {
    }

    public function handle(): void
    {
        $userRole = $_SESSION['role'] ?? '';

        if ($userRole !== $this->requiredRole) {
            http_response_code(403);
            if (file_exists(VIEW_PATH . '/errors/403.php')) {
                require VIEW_PATH . '/errors/403.php';
            } else {
                echo '<h1>403 — Access Denied</h1>';
                echo '<p>You do not have permission to access this page.</p>';
            }
            exit;
        }
    }
}
