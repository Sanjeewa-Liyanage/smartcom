<?php
/**
 * Admin Seeder — Smart Commerce Core
 * ─────────────────────────────────────────────────────────────────
 * Run this script ONCE to create the system administrator account.
 *
 * How to run:
 *   Open your browser and visit:
 *   http://localhost/comclz/scripts/seed_admin.php
 *
 * After the admin is created, DELETE or rename this file to prevent
 * accidental re-use!
 *
 * Default credentials (change these before running!):
 *   Email:    admin@smartcommerce.lk
 *   Password: Admin@2025!
 */

declare(strict_types=1);

// Bootstrap
define('ROOT_PATH', dirname(__DIR__));
define('VIEW_PATH',  ROOT_PATH . '/views');

require ROOT_PATH . '/config/app.php';
require ROOT_PATH . '/config/database.php';

// ── Autoloader ────────────────────────────────────────────────────────────────
spl_autoload_register(static function (string $class): void {
    $file = ROOT_PATH . '/' .
            str_replace(['App\\', '\\'], ['app/', '/'], $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// ── Admin Credentials ─────────────────────────────────────────────────────────
// CHANGE THESE before running!
$adminName     = 'System Administrator';
$adminEmail    = 'admin@smartcommerce.lk';
$adminPassword = 'Admin@2025!';   // CHANGE THIS

// ── HTML Output Helper ────────────────────────────────────────────────────────
function out(string $msg, string $type = 'info'): void {
    $colors = [
        'info'    => '#3b82f6',
        'success' => '#22c55e',
        'error'   => '#ef4444',
        'warning' => '#f59e0b',
    ];
    $color = $colors[$type] ?? '#f0f0ff';
    echo "<p style='color:{$color};margin:6px 0;font-size:0.9rem;'>{$msg}</p>\n";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Seeder — SCC</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin:0;padding:0;box-sizing:border-box; }
        body {
            font-family:'Inter',sans-serif; background:#070714; color:#f0f0ff;
            min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px;
        }
        .card {
            max-width:500px; width:100%;
            background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.09);
            border-radius:20px; padding:40px;
        }
        .logo {
            width:50px;height:50px;background:linear-gradient(135deg,#7c3aed,#a855f7);
            border-radius:14px;display:flex;align-items:center;justify-content:center;
            font-size:22px;margin-bottom:18px;box-shadow:0 6px 20px rgba(124,58,237,0.3);
        }
        h1 { font-size:1.35rem; font-weight:700; margin-bottom:5px; }
        .subtitle { color:#7b7b9d; font-size:0.875rem; margin-bottom:24px; }
        .log { background:rgba(0,0,0,0.3); border:1px solid rgba(255,255,255,0.06); border-radius:12px; padding:20px; margin-bottom:20px; }
        .log h2 { font-size:0.82rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:12px; }
        .warning-box {
            background:rgba(245,158,11,0.08); border:1px solid rgba(251,191,36,0.2);
            border-radius:10px; padding:14px 18px; font-size:0.8rem; color:#fcd34d; line-height:1.6;
        }
        .warning-box strong { color:#fbbf24; }
        .btn {
            display:inline-block; margin-top:18px; padding:11px 22px;
            background:linear-gradient(135deg,#7c3aed,#a855f7);
            border-radius:10px; color:#fff; text-decoration:none;
            font-weight:600; font-size:0.875rem;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="logo">🌱</div>
    <h1>Admin Account Seeder</h1>
    <p class="subtitle">Smart Commerce Core — One-time Setup</p>

    <div class="log">
        <h2>📋 Seeder Log</h2>
<?php

// ── Run seeder ────────────────────────────────────────────────────────────────
try {
    $db = \App\Core\Database::getInstance();

    // Check if admin already exists
    $existing = $db->query(
        "SELECT user_id FROM users WHERE email = ? LIMIT 1",
        [$adminEmail]
    )->fetch();

    if ($existing) {
        out("⚠ An account with email <strong>{$adminEmail}</strong> already exists.", 'warning');
        out("Seeder skipped — no changes made.", 'warning');
    } else {
        // Create user row
        $hashedPassword = password_hash($adminPassword, PASSWORD_BCRYPT, ['cost' => 12]);

        $db->query(
            "INSERT INTO users (name, email, password, role, status, is_active)
             VALUES (?, ?, ?, 'admin', 'active', 1)",
            [$adminName, $adminEmail, $hashedPassword]
        );
        $userId = (int) $db->getPdo()->lastInsertId();
        out("✅ User record created. ID: <strong>#{$userId}</strong>", 'success');

        // Create admin profile row
        $db->query(
            "INSERT INTO admins (user_id) VALUES (?)",
            [$userId]
        );
        out("✅ Admin profile created.", 'success');

        out("", 'info');
        out("🎉 <strong>Admin account created successfully!</strong>", 'success');
        out("📧 Email: <strong>{$adminEmail}</strong>", 'info');
        out("🔑 Password: <strong>{$adminPassword}</strong>", 'info');
        out("", 'info');
        out("⚠ <strong>Delete or rename this file now!</strong> It should not be accessible in production.", 'warning');
    }
} catch (\Exception $e) {
    out("❌ Error: " . htmlspecialchars($e->getMessage()), 'error');
    out("Make sure:", 'info');
    out("• MySQL is running in XAMPP", 'info');
    out("• The database 'comclz_db' exists (import sql/schema.sql first)", 'info');
    out("• config/database.php credentials are correct", 'info');
}
?>
    </div>

    <div class="warning-box">
        <strong>🔒 Security Reminder:</strong><br>
        After running this seeder, <strong>delete or rename</strong> <code>scripts/seed_admin.php</code>
        to prevent anyone from accessing it again. Your admin credentials are shown above — save them and change the password after first login via the admin panel.
    </div>

    <a href="<?= BASE_URL ?>/login" class="btn">→ Go to Login Page</a>
</div>
</body>
</html>
