<?php

namespace App\Core;

/**
 * Controller — Base Controller
 *
 * All controllers extend this class to get access to:
 *  - render()       Load a view file with extracted data
 *  - redirect()     Redirect to a path within the app
 *  - json()         Send JSON response
 *  - flash()        Set a one-time session flash message
 *  - getFlash()     Read & clear a flash message
 *  - csrfToken()    Get/generate CSRF token
 *  - verifyCsrf()   Validate submitted CSRF token (dies on failure)
 *  - input()        Read POST input (sanitised)
 *  - queryParam()   Read GET param (sanitised)
 */
abstract class Controller
{
    // ── Views ─────────────────────────────────────────────────────────────────

    /**
     * Render a view file.
     *
     * @param string $view  Dot-separated path relative to views/ (e.g. 'auth.login')
     * @param array  $data  Variables to extract into the view scope
     */
    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        $file = VIEW_PATH . '/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($file)) {
            throw new \RuntimeException("View not found: [{$view}] → {$file}");
        }
        require $file;
    }

    // ── Redirects ─────────────────────────────────────────────────────────────

    /**
     * Redirect to an app-relative path.
     * e.g. redirect('/admin/dashboard') → Location: http://localhost/comclz/admin/dashboard
     */
    protected function redirect(string $path): never
    {
        header('Location: ' . BASE_URL . $path);
        exit;
    }

    // ── JSON ──────────────────────────────────────────────────────────────────

    protected function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ── Flash Messages ────────────────────────────────────────────────────────

    /** Store a one-time flash message in the session */
    protected function flash(string $key, string $message): void
    {
        $_SESSION['_flash'][$key] = $message;
    }

    /** Read and remove a flash message (returns null if not set) */
    protected function getFlash(string $key): ?string
    {
        $msg = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $msg;
    }

    // ── CSRF ──────────────────────────────────────────────────────────────────

    /** Get the current CSRF token (generates one if it doesn't exist) */
    protected function csrfToken(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    /**
     * Verify the CSRF token submitted with a POST request.
     * Terminates with 403 if the token is missing or wrong.
     */
    protected function verifyCsrf(): void
    {
        $submitted = $_POST['csrf_token'] ?? '';
        $stored    = $_SESSION['_csrf']   ?? '';

        if (!$stored || !hash_equals($stored, $submitted)) {
            http_response_code(403);
            die('<h2 style="font-family:sans-serif;color:#ef4444;padding:2rem">
                403 — Invalid CSRF token. Please go back and try again.
            </h2>');
        }
    }

    // ── Input Helpers ─────────────────────────────────────────────────────────

    /** Read and sanitise a POST field */
    protected function input(string $key, string $default = ''): string
    {
        return trim($_POST[$key] ?? $default);
    }

    /** Read and sanitise a GET param */
    protected function queryParam(string $key, string $default = ''): string
    {
        return trim($_GET[$key] ?? $default);
    }

    // ── Auth Helpers ──────────────────────────────────────────────────────────

    /** Return the currently logged-in user's session data */
    protected function currentUser(): array
    {
        return [
            'user_id' => $_SESSION['user_id'] ?? null,
            'role'    => $_SESSION['role']    ?? null,
            'name'    => $_SESSION['name']    ?? '',
            'email'   => $_SESSION['email']   ?? '',
        ];
    }

    /** Escape output for safe HTML display */
    protected function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
