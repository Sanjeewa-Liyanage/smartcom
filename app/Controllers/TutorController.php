<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Tutor;
use App\Models\User;

/** TutorController — RBAC: tutor role only */
class TutorController extends Controller
{
    public function dashboard(): void
    {
        $user  = $this->currentUser();
        $tutorModel = new Tutor();
        $tutor = $tutorModel->findByUserId($user['user_id']);
        if ($tutor && !(bool) $tutor['profile_completed']) {
            $this->redirect('/tutor/setup');
            return;
        }

        $this->render('tutor.dashboard', compact('user'));
    }

    public function setupForm(): void
    {
        $user  = $this->currentUser();
        $tutorModel = new Tutor();
        $tutor = $tutorModel->findByUserId($user['user_id']);

        if ($tutor && (bool) $tutor['profile_completed']) {
            $this->redirect('/tutor/dashboard');
        }

        $csrf   = $this->csrfToken();
        $errors = [];
        $firstName = '';
        $lastName = '';
        $gender = '';
        $subject = '';

        $this->render('tutor.setup', compact('csrf', 'errors', 'firstName', 'lastName', 'gender', 'subject', 'user'));
    }

    public function setup(): void
    {
        $this->verifyCsrf();

        $user  = $this->currentUser();
        $tutorModel = new Tutor();
        $tutor = $tutorModel->findByUserId($user['user_id']);

        if ($tutor && (bool) $tutor['profile_completed']) {
            $this->redirect('/tutor/dashboard');
        }

        $firstName = $this->input('first_name');
        $lastName  = $this->input('last_name');
        $gender    = $this->input('gender');
        $subject   = $this->input('subject');
        $password  = $this->input('password');
        $confirm   = $this->input('confirm_password');
        $errors    = [];

        if ($firstName === '') $errors[] = 'First name is required.';
        if ($lastName === '')  $errors[] = 'Last name is required.';
        if (!in_array($gender, ['male', 'female', 'other'])) $errors[] = 'Please select a valid gender.';
        if ($subject === '')   $errors[] = 'Subject is required.';
        if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
        if ($password !== $confirm)  $errors[] = 'Passwords do not match.';

        if (!empty($errors)) {
            $csrf = $this->csrfToken();
            $this->render('tutor.setup', compact('csrf', 'errors', 'firstName', 'lastName', 'gender', 'subject', 'user'));
            return;
        }

        // Update tutor profile
        $tutorModel->update($tutor['tutor_id'], [
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'gender'     => $gender,
            'subject'    => $subject,
            'profile_completed' => 1
        ]);

        // Update user name and password
        $userModel = new User();
        $userModel->update($user['user_id'], [
            'name'     => $firstName . ' ' . $lastName,
            'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12])
        ]);
        
        // Update session name
        $_SESSION['name'] = $firstName . ' ' . $lastName;

        $this->redirect('/tutor/dashboard');
    }
}
