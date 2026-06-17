<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Student;

/** StudentController — RBAC: student role only */
class StudentController extends Controller
{
    public function dashboard(): void
    {
        $user    = $this->currentUser();
        $student = (new Student())->findByUserId($user['user_id']);
        $this->render('student.dashboard', compact('user', 'student'));
    }
}
