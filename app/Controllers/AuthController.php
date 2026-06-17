<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\JWT;
use App\Models\User;
use App\Models\Student;
use App\Models\Tutor;
use App\Models\ParentModel;

/**
 * AuthController
 *
 * Handles all authentication flows:
 *  • Login / Logout
 *  • Student self-registration   → requires admin approval
 *  • Tutor self-registration     → requires admin approval (via /tutor-register)
 *  • Parent self-registration    → immediately active (requires valid student_id)
 *  • Pending approval info page
 */
class AuthController extends Controller
{
    // ── Root redirect ─────────────────────────────────────────────────────────

    public function index(): void
    {
        $this->redirect('/login');
    }

    // ── Login ─────────────────────────────────────────────────────────────────

    public function loginForm(): void
    {
        // Already logged in → go to dashboard
        if (!empty($_SESSION['user_id'])) {
            $this->redirect('/' . $_SESSION['role'] . '/dashboard');
        }

        $csrf   = $this->csrfToken();
        $errors = [];
        $this->render('auth.login', compact('csrf', 'errors'));
    }

    public function login(): void
    {
        $this->verifyCsrf();

        $email    = $this->input('email');
        $password = $this->input('password');
        $errors   = [];

        // Basic validation
        if ($email === '' || $password === '') {
            $errors[] = 'Email and password are required.';
        }

        if (empty($errors)) {
            $userModel = new User();
            $user      = $userModel->findByEmail($email);

            if (!$user || !$userModel->verifyPassword($password, $user['password'])) {
                $errors[] = 'Invalid email or password.';
            } elseif ($user['status'] === 'pending') {
                // Account exists but awaiting admin approval
                $this->redirect('/pending-approval');
            } elseif ($user['status'] === 'rejected') {
                $errors[] = 'Your account application was rejected. Contact the administrator.';
            } elseif ($user['status'] === 'suspended') {
                $errors[] = 'Your account has been suspended. Contact the administrator.';
            } elseif (!(bool) $user['is_active']) {
                $errors[] = 'Your account is inactive. Contact the administrator.';
            }
        }

        // Show errors
        if (!empty($errors)) {
            $csrf = $this->csrfToken();
            $this->render('auth.login', compact('csrf', 'errors', 'email'));
            return;
        }

        // ── Set PHP Session ────────────────────────────────────────────────
        session_regenerate_id(true);   // prevent session fixation
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role']    = $user['role'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['email']   = $user['email'];

        // ── Issue JWT Cookie ───────────────────────────────────────────────
        $token = JWT::encode([
            'user_id' => $user['user_id'],
            'role'    => $user['role'],
            'name'    => $user['name'],
            'email'   => $user['email'],
        ]);
        setcookie('jwt_token', $token, [
            'expires'  => time() + JWT_EXPIRY,
            'path'     => BASE_PATH !== '' ? BASE_PATH . '/' : '/',
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        // ── Redirect by Role ───────────────────────────────────────────────
        if ($user['role'] === 'tutor') {
            $tutorModel = new Tutor();
            $tutor = $tutorModel->findByUserId($user['user_id']);
            if ($tutor && !(bool) $tutor['profile_completed']) {
                $this->redirect('/tutor/setup');
                return;
            }
        }

        $this->redirect('/' . $user['role'] . '/dashboard');
    }

    // ── Logout ────────────────────────────────────────────────────────────────

    public function logout(): void
    {
        // Destroy session
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();

        // Clear JWT cookie
        setcookie('jwt_token', '', [
            'expires'  => time() - 3600,
            'path'     => BASE_PATH !== '' ? BASE_PATH . '/' : '/',
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        $this->redirect('/login');
    }

    // ── Student Registration ──────────────────────────────────────────────────

    public function registerStudentForm(): void
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect('/' . $_SESSION['role'] . '/dashboard');
        }

        $csrf   = $this->csrfToken();
        $errors = [];
        $name   = '';
        $email  = '';
        $parentName = '';
        $parentEmail = '';
        $this->render('auth.register_student', compact('csrf', 'errors', 'name', 'email', 'parentName', 'parentEmail'));
    }

    public function registerStudent(): void
    {
        $this->verifyCsrf();

        $name     = $this->input('name');
        $email    = $this->input('email');
        $password = $this->input('password');
        $confirm  = $this->input('confirm_password');
        $parentName = $this->input('parent_name');
        $parentEmail = $this->input('parent_email');
        $errors   = [];

        // Validate
        if ($name === '')    $errors[] = 'Full name is required.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email address is required.';
        if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
        if ($password !== $confirm)  $errors[] = 'Passwords do not match.';
        if ($parentName === '') $errors[] = 'Parent name is required.';
        if (!filter_var($parentEmail, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid parent email address is required.';

        $userModel = new User();
        if (empty($errors) && $userModel->emailExists($email)) {
            $errors[] = 'This email is already registered.';
        }

        if (!empty($errors)) {
            $csrf = $this->csrfToken();
            $this->render('auth.register_student', compact('csrf', 'errors', 'name', 'email', 'parentName', 'parentEmail'));
            return;
        }

        // Create user (pending approval)
        $userId = $userModel->createUser([
            'name'      => $name,
            'email'     => $email,
            'password'  => $password,
            'role'      => 'student',
            'status'    => 'pending',
            'is_active' => 0,
        ]);

        // Create student profile row
        $studentModel = new Student();
        $studentModel->create([
            'user_id' => $userId,
            'parent_name' => $parentName,
            'parent_email' => $parentEmail
        ]);

        $this->redirect('/pending-approval');
    }



    // ── Pending Approval Info Page ────────────────────────────────────────────

    public function pendingApproval(): void
    {
        $this->render('auth.pending_approval');
    }

    // ── Forgot Password (OTP-based) ──────────────────────────────────────────

    /** Step 1: Show the "enter email" form */
    public function forgotPasswordForm(): void
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect('/' . $_SESSION['role'] . '/dashboard');
        }

        $csrf    = $this->csrfToken();
        $errors  = [];
        $email   = '';
        $success = null;
        $this->render('auth.forgot_password', compact('csrf', 'errors', 'email', 'success'));
    }

    /** Step 1 POST: Validate email, generate OTP, send via Mailer */
    public function forgotPassword(): void
    {
        $this->verifyCsrf();

        $email  = $this->input('email');
        $errors = [];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }

        $userModel = new User();
        $user      = null;

        if (empty($errors)) {
            $user = $userModel->findByEmail($email);
            if ($user === null) {
                $errors[] = 'No account found with that email address.';
            }
        }

        if (!empty($errors)) {
            $csrf = $this->csrfToken();
            $this->render('auth.forgot_password', compact('csrf', 'errors', 'email'));
            return;
        }

        // Rate limiting: 60-second cooldown
        $resetModel = new \App\Models\PasswordReset();
        if ($resetModel->isRateLimited($user['user_id'])) {
            $errors[] = 'Please wait 60 seconds before requesting another code.';
            $csrf = $this->csrfToken();
            $this->render('auth.forgot_password', compact('csrf', 'errors', 'email'));
            return;
        }

        // Generate OTP
        $otp = $resetModel->createOtp($user['user_id']);

        // Send OTP via email
        try {
            $subject = 'Your Password Reset Code — Smart Commerce Core';
            $body    = "
                <div style='font-family:Inter,sans-serif;max-width:480px;margin:0 auto;'>
                    <h2 style='color:#1a1a2e;'>Password Reset</h2>
                    <p style='color:#555;line-height:1.6;'>
                        Hello <strong>{$this->e($user['name'])}</strong>,
                    </p>
                    <p style='color:#555;line-height:1.6;'>
                        Your verification code is:
                    </p>
                    <div style='background:#f5f6fa;border:2px solid #e53935;border-radius:12px;padding:20px;text-align:center;margin:20px 0;'>
                        <span style='font-size:32px;font-weight:800;letter-spacing:0.3em;color:#e53935;'>{$otp}</span>
                    </div>
                    <p style='color:#555;line-height:1.6;'>
                        This code is valid for <strong>10 minutes</strong>. If you didn't request this, please ignore this email.
                    </p>
                    <hr style='border:none;border-top:1px solid #e8eaed;margin:24px 0;'>
                    <p style='color:#999;font-size:12px;'>Smart Commerce Core — Intelligent LMS for Commerce Education</p>
                </div>
            ";
            $altBody = "Your password reset code is: {$otp}. It expires in 10 minutes.";

            \App\Core\Mailer::send($email, $user['name'], $subject, $body, $altBody);
        } catch (\Exception $e) {
            $errors[] = 'Could not send verification email. Please try again later.';
            $csrf = $this->csrfToken();
            $this->render('auth.forgot_password', compact('csrf', 'errors', 'email'));
            return;
        }

        // Store user context in session for the next steps
        $_SESSION['reset_user_id'] = $user['user_id'];
        $_SESSION['reset_email']   = $email;

        $this->redirect('/verify-otp');
    }

    /** Step 2: Show the "enter OTP" form */
    public function verifyOtpForm(): void
    {
        // Guard: must have come from step 1
        if (empty($_SESSION['reset_user_id'])) {
            $this->redirect('/forgot-password');
        }

        $csrf        = $this->csrfToken();
        $errors      = [];
        $maskedEmail = $this->maskEmail($_SESSION['reset_email'] ?? '');
        $success     = null;
        $this->render('auth.verify_otp', compact('csrf', 'errors', 'maskedEmail', 'success'));
    }

    /** Step 2 POST: Validate the OTP */
    public function verifyOtp(): void
    {
        $this->verifyCsrf();

        if (empty($_SESSION['reset_user_id'])) {
            $this->redirect('/forgot-password');
        }

        $otp    = $this->input('otp');
        $errors = [];
        $userId = (int) $_SESSION['reset_user_id'];

        if ($otp === '' || strlen($otp) !== 6 || !ctype_digit($otp)) {
            $errors[] = 'Please enter a valid 6-digit verification code.';
        }

        if (empty($errors)) {
            $resetModel = new \App\Models\PasswordReset();
            if (!$resetModel->verifyOtp($userId, $otp)) {
                $errors[] = 'Invalid or expired verification code. Please try again or request a new code.';
            }
        }

        if (!empty($errors)) {
            $csrf        = $this->csrfToken();
            $maskedEmail = $this->maskEmail($_SESSION['reset_email'] ?? '');
            $this->render('auth.verify_otp', compact('csrf', 'errors', 'maskedEmail'));
            return;
        }

        // OTP verified — allow password reset
        $_SESSION['otp_verified'] = true;

        $this->redirect('/reset-password');
    }

    /** Step 3: Show the "set new password" form */
    public function resetPasswordForm(): void
    {
        // Guard: must have passed OTP verification
        if (empty($_SESSION['otp_verified']) || empty($_SESSION['reset_user_id'])) {
            $this->redirect('/forgot-password');
        }

        $csrf   = $this->csrfToken();
        $errors = [];
        $this->render('auth.reset_password_form', compact('csrf', 'errors'));
    }

    /** Step 3 POST: Save the new password */
    public function resetPassword(): void
    {
        $this->verifyCsrf();

        if (empty($_SESSION['otp_verified']) || empty($_SESSION['reset_user_id'])) {
            $this->redirect('/forgot-password');
        }

        $password = $this->input('password');
        $confirm  = $this->input('confirm_password');
        $errors   = [];
        $userId   = (int) $_SESSION['reset_user_id'];

        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Passwords do not match.';
        }

        if (!empty($errors)) {
            $csrf = $this->csrfToken();
            $this->render('auth.reset_password_form', compact('csrf', 'errors'));
            return;
        }

        // Update password
        $userModel = new User();
        $userModel->resetPassword($userId, $password);

        // Mark OTPs as used
        $resetModel = new \App\Models\PasswordReset();
        $resetModel->markUsed($userId);

        // Clean up session
        unset(
            $_SESSION['reset_user_id'],
            $_SESSION['reset_email'],
            $_SESSION['otp_verified']
        );

        // Flash success and redirect to login
        $this->flash('pw_reset_success', 'Your password has been reset successfully. Please sign in with your new password.');
        $this->redirect('/login');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Mask an email for display (e.g. "praba@gmail.com" → "pr***@gmail.com")
     */
    private function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return '***';
        }
        $local  = $parts[0];
        $domain = $parts[1];

        if (strlen($local) <= 2) {
            $masked = $local[0] . '***';
        } else {
            $masked = substr($local, 0, 2) . str_repeat('*', max(3, strlen($local) - 2));
        }

        return $masked . '@' . $domain;
    }
}
