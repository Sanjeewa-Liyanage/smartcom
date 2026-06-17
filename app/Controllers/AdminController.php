<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

/**
 * AdminController
 *
 * RBAC: admin role only (enforced by RoleMiddleware in routes).
 *
 * Features:
 *  • Dashboard with stats
 *  • Full user management table (filter by role/status)
 *  • Pending approvals list (approve / reject)
 *  • Suspend / re-activate active accounts
 *  • Admin password reset for any user
 */
class AdminController extends Controller
{
    // ── Dashboard ─────────────────────────────────────────────────────────────

    public function dashboard(): void
    {
        $userModel = new User();

        $stats = [
            'students' => $userModel->countByRole('student'),
            'tutors'   => $userModel->countByRole('tutor'),
            'parents'  => $userModel->countByRole('parent'),
            'pending'  => $userModel->countByStatus('pending'),
        ];

        $user    = $this->currentUser();
        $success = $this->getFlash('success');

        $this->render('admin.dashboard', compact('stats', 'user', 'success'));
    }

    // ── User Management Table ─────────────────────────────────────────────────

    public function users(): void
    {
        $roleFilter   = $this->queryParam('role');
        $statusFilter = $this->queryParam('status');

        $userModel = new User();
        $users     = $userModel->getAllWithFilters(
            $roleFilter   !== '' ? $roleFilter   : null,
            $statusFilter !== '' ? $statusFilter : null
        );

        $csrf = $this->csrfToken();
        $user = $this->currentUser();

        $this->render('admin.users', compact('users', 'csrf', 'user', 'roleFilter', 'statusFilter'));
    }

    // ── Pending Approvals ─────────────────────────────────────────────────────

    public function pending(): void
    {
        $userModel    = new User();
        $pendingUsers = $userModel->getPendingUsers();
        $csrf         = $this->csrfToken();
        $user         = $this->currentUser();
        $success      = $this->getFlash('success');

        $this->render('admin.pending', compact('pendingUsers', 'csrf', 'user', 'success'));
    }

    // ── Approve User ──────────────────────────────────────────────────────────

    public function approveUser(): void
    {
        $this->verifyCsrf();

        $userId = (int) $this->input('user_id');
        if ($userId > 0) {
            (new User())->approve($userId);
            $this->flash('success', 'User account approved successfully.');
        }

        $this->redirect('/admin/pending');
    }

    // ── Reject User ───────────────────────────────────────────────────────────

    public function rejectUser(): void
    {
        $this->verifyCsrf();

        $userId = (int) $this->input('user_id');
        if ($userId > 0) {
            (new User())->reject($userId);
            $this->flash('success', 'User application rejected.');
        }

        $this->redirect('/admin/pending');
    }

    // ── Toggle (Suspend / Activate) User ─────────────────────────────────────

    public function toggleUser(): void
    {
        $this->verifyCsrf();

        $userId = (int) $this->input('user_id');
        $action = $this->input('action');   // 'suspend' | 'activate'

        if ($userId > 0) {
            $userModel = new User();
            if ($action === 'suspend') {
                $userModel->suspend($userId);
                $this->flash('success', 'User account suspended.');
            } elseif ($action === 'activate') {
                $userModel->activate($userId);
                $this->flash('success', 'User account re-activated.');
            }
        }

        $this->redirect('/admin/users');
    }


    // ── Enable Parent Control ──────────────────────────────────────────────────
    
    public function enableParentControl(): void
    {
        $this->verifyCsrf();
        $userId = (int) $this->input('user_id');
        
        if ($userId <= 0) {
            $this->redirect('/admin/users');
            return;
        }

        $studentModel = new \App\Models\Student();
        $student = $studentModel->findByUserId($userId);

        if (!$student) {
            $this->flash('error', 'Student not found.');
            $this->redirect('/admin/users');
            return;
        }
        $studentId = $student['student_id'];
        $student = $studentModel->getWithUserDetails($studentId);

        if (!$student) {
            $this->flash('error', 'Student not found.');
            $this->redirect('/admin/users');
            return;
        }

        if (empty($student['parent_email'])) {
            $this->flash('error', 'Parent email is missing for this student.');
            $this->redirect('/admin/users');
            return;
        }

        if (!empty($student['parent_id'])) {
            $this->flash('error', 'Parent control is already enabled for this student.');
            $this->redirect('/admin/users');
            return;
        }

        $userModel = new User();
        $parentUser = $userModel->findByEmail($student['parent_email']);
        $parentModel = new \App\Models\ParentModel();

        if ($parentUser) {
            // Parent user already exists, just link them
            $parentRecord = $parentModel->findByUserId($parentUser['user_id']);
            if ($parentRecord) {
                $studentModel->update($studentId, ['parent_id' => $parentRecord['parent_id']]);
                
                // Email existing parent about the new link
                try {
                    $parentName = $parentUser['name'];
                    $subject = 'New Student Linked to Your Account';
                    $body    = "Hello {$parentName},<br><br>Your child, {$student['name']}, has been successfully linked to your existing parent account.<br><br>You can now log in to monitor their progress.";
                    $altBody = "Hello {$parentName},\n\nYour child, {$student['name']}, has been successfully linked to your existing parent account.\n\nYou can now log in to monitor their progress.";

                    \App\Core\Mailer::send($parentUser['email'], $parentName, $subject, $body, $altBody);
                    $this->flash('success', 'Parent control enabled. Linked to existing parent account and email sent.');
                } catch (\Exception $e) {
                    $this->flash('success', 'Parent control enabled and linked, but email could not be sent.');
                }
            } else {
                $this->flash('error', 'A user with this email exists, but is not a parent.');
            }
        } else {
            // Create new parent user
            $password = bin2hex(random_bytes(4));
            $parentName = $student['parent_name'] ?: 'Parent of ' . $student['name'];
            $userId = $userModel->createUser([
                'name'      => $parentName,
                'email'     => $student['parent_email'],
                'password'  => $password,
                'role'      => 'parent',
                'status'    => 'active',
                'is_active' => 1,
            ]);

            $parentId = $parentModel->create([
                'user_id' => $userId,
            ]);

            $studentModel->update($studentId, ['parent_id' => $parentId]);

            // Email parent
            try {
                $subject = 'Parent Account Created';
                $body    = "Hello {$parentName},<br><br>Your parent account has been created and linked to your child, {$student['name']}. Your temporary password is: <b>{$password}</b><br><br>Please log in to monitor their progress.";
                $altBody = "Hello {$parentName},\n\nYour parent account has been created and linked to your child, {$student['name']}. Your temporary password is: {$password}\n\nPlease log in to monitor their progress.";

                \App\Core\Mailer::send($student['parent_email'], $parentName, $subject, $body, $altBody);
                $this->flash('success', 'Parent control enabled. Credentials sent to parent.');
            } catch (\Exception $e) {
                $this->flash('success', 'Parent control enabled, but email could not be sent. Temporary password is: ' . $password);
            }
        }

        $this->redirect('/admin/users');
    }

    // ── Create Tutor ──────────────────────────────────────────────────────────

    public function createTutorForm(): void
    {
        $csrf    = $this->csrfToken();
        $user    = $this->currentUser();
        $errors  = [];
        $name    = '';
        $email   = '';
        
        $this->render('admin.create_tutor', compact('csrf', 'user', 'errors', 'name', 'email'));
    }

    public function createTutor(): void
    {
        $this->verifyCsrf();

        $name   = $this->input('name');
        $email  = $this->input('email');
        $errors = [];

        if ($name === '') {
            $errors[] = 'Name is required.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email address is required.';
        }

        $userModel = new User();
        if (empty($errors) && $userModel->emailExists($email)) {
            $errors[] = 'This email is already registered.';
        }

        if (!empty($errors)) {
            $csrf = $this->csrfToken();
            $user = $this->currentUser();
            $this->render('admin.create_tutor', compact('csrf', 'user', 'errors', 'name', 'email'));
            return;
        }

        // Generate random password (8 chars)
        $password = bin2hex(random_bytes(4));

        // Create active user
        $userId = $userModel->createUser([
            'name'      => $name,
            'email'     => $email,
            'password'  => $password,
            'role'      => 'tutor',
            'status'    => 'active',
            'is_active' => 1,
        ]);

        // Create tutor profile
        $tutorModel = new \App\Models\Tutor();
        $tutorModel->create(['user_id' => $userId]);

        // Send email via Mailer class
        $mailError = null;
        try {
            $subject = 'Your Tutor Account has been created';
            $body    = "Hello {$name},<br><br>Your tutor account has been created. Your temporary password is: <b>{$password}</b><br><br>Please log in and complete your profile.";
            $altBody = "Hello {$name},\n\nYour tutor account has been created. Your temporary password is: {$password}\n\nPlease log in and complete your profile.";

            \App\Core\Mailer::send($email, $name, $subject, $body, $altBody);
        } catch (\Exception $e) {
            $mailError = "Account created, but email could not be sent. Mailer Error: {$e->getMessage()}. Temporary password is: {$password}";
        }

        if ($mailError) {
            $this->flash('success', $mailError);
        } else {
            $this->flash('success', "Tutor {$name} created successfully. An email with the password has been sent.");
        }

        $this->redirect('/admin/users');
    }
}
