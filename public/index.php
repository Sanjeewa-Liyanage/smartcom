<?php
declare(strict_types=1);

// ── Paths ─────────────────────────────────────────────────────────────────────
define('ROOT_PATH', dirname(__DIR__));           // d:/project.../comclz
define('VIEW_PATH', ROOT_PATH . '/views');

// ── Load Config ───────────────────────────────────────────────────────────────
require ROOT_PATH . '/config/app.php';
require ROOT_PATH . '/config/database.php';
if (file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    require ROOT_PATH . '/vendor/autoload.php';
}

// ── Load .env ─────────────────────────────────────────────────────────────────
$dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

// ── PSR-4-style Autoloader ────────────────────────────────────────────────────
// Maps App\Core\Router  →  app/Core/Router.php
spl_autoload_register(static function (string $class): void {
    $file = ROOT_PATH . '/' .
            str_replace(['App\\', '\\'], ['app/', '/'], $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// ── Session (secure configuration) ───────────────────────────────────────────
ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', (string) SESSION_LIFETIME);
session_name(SESSION_NAME);
session_start();

// ── JWT Init ──────────────────────────────────────────────────────────────────
App\Core\JWT::init();

// ── Middleware Closures ───────────────────────────────────────────────────────
$requireAuth = static function (): void {
    (new App\Middleware\AuthMiddleware())->handle();
};
$requireAdmin = static function (): void {
    (new App\Middleware\AuthMiddleware())->handle();
    (new App\Middleware\RoleMiddleware('admin'))->handle();
};
$requireTutor = static function (): void {
    (new App\Middleware\AuthMiddleware())->handle();
    (new App\Middleware\RoleMiddleware('tutor'))->handle();
};
$requireStudent = static function (): void {
    (new App\Middleware\AuthMiddleware())->handle();
    (new App\Middleware\RoleMiddleware('student'))->handle();
};
$requireParent = static function (): void {
    (new App\Middleware\AuthMiddleware())->handle();
    (new App\Middleware\RoleMiddleware('parent'))->handle();
};

// ── Router ────────────────────────────────────────────────────────────────────
$router = new App\Core\Router();

use App\Controllers\AuthController;
use App\Controllers\AdminController;
use App\Controllers\TutorController;
use App\Controllers\StudentController;
use App\Controllers\ParentController;

// ── Public / Auth Routes ──────────────────────────────────────────────────────
$router->get('/',                [AuthController::class, 'index']);
$router->get('/login',           [AuthController::class, 'loginForm']);
$router->post('/login',          [AuthController::class, 'login']);
$router->get('/logout',          [AuthController::class, 'logout']);

// Student Registration
$router->get('/register',        [AuthController::class, 'registerStudentForm']);
$router->post('/register',       [AuthController::class, 'registerStudent']);

// Tutor Setup (First Login)
$router->get('/tutor/setup',     [TutorController::class, 'setupForm'], [$requireTutor]);
$router->post('/tutor/setup',    [TutorController::class, 'setup'],     [$requireTutor]);

// Parent Registration (Removed, now part of Student Registration and Admin controlled)

// Pending Approval Info Page
$router->get('/pending-approval',[AuthController::class, 'pendingApproval']);

// ── Admin Routes ──────────────────────────────────────────────────────────────
$router->get('/admin/dashboard',      [AdminController::class, 'dashboard'],        [$requireAdmin]);
$router->get('/admin/tutors/create',  [AdminController::class, 'createTutorForm'],  [$requireAdmin]);
$router->post('/admin/tutors/create', [AdminController::class, 'createTutor'],      [$requireAdmin]);
$router->get('/admin/users',          [AdminController::class, 'users'],            [$requireAdmin]);
$router->get('/admin/pending',        [AdminController::class, 'pending'],          [$requireAdmin]);
$router->post('/admin/approve-user',  [AdminController::class, 'approveUser'],      [$requireAdmin]);
$router->post('/admin/reject-user',   [AdminController::class, 'rejectUser'],       [$requireAdmin]);
$router->post('/admin/toggle-user',   [AdminController::class, 'toggleUser'],       [$requireAdmin]);
$router->post('/admin/enable-parent-control', [AdminController::class, 'enableParentControl'], [$requireAdmin]);
// Forgot Password (OTP-based self-service)
$router->get('/forgot-password',  [AuthController::class, 'forgotPasswordForm']);
$router->post('/forgot-password', [AuthController::class, 'forgotPassword']);
$router->get('/verify-otp',       [AuthController::class, 'verifyOtpForm']);
$router->post('/verify-otp',      [AuthController::class, 'verifyOtp']);
$router->get('/reset-password',   [AuthController::class, 'resetPasswordForm']);
$router->post('/reset-password',  [AuthController::class, 'resetPassword']);

// ── Tutor Routes ──────────────────────────────────────────────────────────────
$router->get('/tutor/dashboard',  [TutorController::class,   'dashboard'], [$requireTutor]);

// ── Student Routes ────────────────────────────────────────────────────────────
$router->get('/student/dashboard',[StudentController::class, 'dashboard'], [$requireStudent]);

// ── Parent Routes ─────────────────────────────────────────────────────────────
$router->get('/parent/dashboard', [ParentController::class,  'dashboard'], [$requireParent]);

// ── Dispatch ──────────────────────────────────────────────────────────────────
$router->dispatch();
