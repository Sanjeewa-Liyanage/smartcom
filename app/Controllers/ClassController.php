<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\CourseClass;
use App\Models\Subject;
use App\Core\Database;

class ClassController extends Controller
{
    /**
     * List all classes
     */
    public function index(): void
    {
        $db = Database::getInstance();
        $classes = $db->query("
            SELECT c.*, s.name as subject_name, t.first_name as tutor_fname, t.last_name as tutor_lname
            FROM classes c
            JOIN subjects s ON c.subject_id = s.subject_id
            JOIN tutors t ON c.tutor_id = t.tutor_id
            ORDER BY c.created_at DESC
        ")->fetchAll();

        $this->render('admin/classes/index', [
            'title' => 'Manage Classes',
            'classes' => $classes
        ]);
    }

    /**
     * Show create form
     */
    public function createForm(): void
    {
        $db = Database::getInstance();
        $subjects = (new Subject())->findAll('name ASC');
        $tutors = $db->query("SELECT * FROM tutors")->fetchAll();

        $this->render('admin/classes/create', [
            'title' => 'Create New Class',
            'subjects' => $subjects,
            'tutors' => $tutors
        ]);
    }

    /**
     * Handle creation
     */
    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/classes');
            return;
        }

        $subjectId = (int)($_POST['subject_id'] ?? 0);
        $tutorId = (int)($_POST['tutor_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $classType = trim($_POST['class_type'] ?? 'theory');
        $scheduleDate = trim($_POST['schedule_day'] ?? '');
        $scheduleTime = trim($_POST['schedule_time'] ?? '');
        
        $formattedTime = $scheduleTime ? date('h:i A', strtotime($scheduleTime)) : '';
        $schedule = trim("$scheduleDate $formattedTime");
        $monthlyFee = (float)($_POST['monthly_fee'] ?? 0.00);

        if (!$subjectId || !$tutorId || empty($name)) {
            $this->flash('error', 'Subject, Tutor, and Class Name are required.');
            $this->redirect('/admin/classes/create');
            return;
        }

        $coverImage = null;
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('cover_') . '.' . $ext;
            $uploadDir = ROOT_PATH . '/public/cover_images';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $uploadDir . '/' . $filename)) {
                $coverImage = $filename;
            }
        }

        $classModel = new CourseClass();
        $classModel->create([
            'subject_id' => $subjectId,
            'tutor_id' => $tutorId,
            'name' => $name,
            'class_type' => $classType,
            'schedule_details' => $schedule,
            'status' => 'active',
            'cover_image' => $coverImage,
            'monthly_fee' => $monthlyFee
        ]);

        $this->flash('success', 'Class created successfully.');
        $this->redirect('/admin/classes');
    }

    /**
     * Show edit form
     */
    public function editForm(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $classModel = new CourseClass();
        $class = $classModel->find($id);

        if (!$class) {
            $this->flash('error', 'Class not found.');
            $this->redirect('/admin/classes');
            return;
        }

        $db = Database::getInstance();
        $subjects = (new Subject())->findAll('name ASC');
        $tutors = $db->query("SELECT * FROM tutors")->fetchAll();

        $this->render('admin/classes/edit', [
            'title' => 'Edit Class',
            'class' => $class,
            'subjects' => $subjects,
            'tutors' => $tutors
        ]);
    }

    /**
     * Handle update
     */
    public function edit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/classes');
            return;
        }

        $id = (int)($_POST['class_id'] ?? 0);
        $subjectId = (int)($_POST['subject_id'] ?? 0);
        $tutorId = (int)($_POST['tutor_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $classType = trim($_POST['class_type'] ?? 'theory');
        $scheduleDate = trim($_POST['schedule_day'] ?? '');
        $scheduleTime = trim($_POST['schedule_time'] ?? '');
        
        $formattedTime = $scheduleTime;
        if ($scheduleTime && !strpos($scheduleTime, 'AM') && !strpos($scheduleTime, 'PM')) {
            $formattedTime = date('h:i A', strtotime($scheduleTime));
        }
        $schedule = trim("$scheduleDate $formattedTime");
        $status = trim($_POST['status'] ?? 'active');
        $monthlyFee = (float)($_POST['monthly_fee'] ?? 0.00);

        if (!$subjectId || !$tutorId || empty($name)) {
            $this->flash('error', 'Subject, Tutor, and Class Name are required.');
            $this->redirect('/admin/classes/edit?id=' . $id);
            return;
        }

        $classModel = new CourseClass();
        $existingClass = $classModel->find($id);
        if (!$existingClass) {
            $this->flash('error', 'Class not found.');
            $this->redirect('/admin/classes');
            return;
        }

        $coverImage = $existingClass['cover_image'] ?? null;
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('cover_') . '.' . $ext;
            $uploadDir = ROOT_PATH . '/public/cover_images';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $uploadDir . '/' . $filename)) {
                $coverImage = $filename;
                // Optional: delete old image if it existed and wasn't placeholder
                if (!empty($existingClass['cover_image']) && $existingClass['cover_image'] !== 'placeholder.jpeg') {
                    $oldPath = $uploadDir . '/' . $existingClass['cover_image'];
                    if (file_exists($oldPath)) unlink($oldPath);
                }
            }
        }

        $classModel->update($id, [
            'subject_id' => $subjectId,
            'tutor_id' => $tutorId,
            'name' => $name,
            'class_type' => $classType,
            'schedule_details' => $schedule,
            'status' => $status,
            'cover_image' => $coverImage,
            'monthly_fee' => $monthlyFee
        ]);

        $this->flash('success', 'Class updated successfully.');
        $this->redirect('/admin/classes');
    }

    /**
     * Handle deletion
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/classes');
            return;
        }

        $id = (int)($_POST['class_id'] ?? 0);
        $classModel = new CourseClass();

        if ($classModel->find($id)) {
            $classModel->delete($id);
            $this->flash('success', 'Class deleted successfully.');
        } else {
            $this->flash('error', 'Class not found.');
        }

        $this->redirect('/admin/classes');
    }

    /**
     * Show enrollments for a specific class
     */
    public function enrollments(): void
    {
        $classId = (int)($_GET['class_id'] ?? 0);
        
        $classModel = new CourseClass();
        $class = $classModel->find($classId);
        
        if (!$class) {
            $this->flash('error', 'Class not found.');
            $this->redirect('/admin/classes');
            return;
        }

        $db = Database::getInstance();
        
        // Get currently enrolled students
        $enrollments = $db->query("
            SELECT e.enrollment_id, e.status as enrollment_status, e.enrollment_date,
                   u.user_id, u.name, u.email, s.student_id
            FROM enrollments e
            JOIN students s ON e.student_id = s.student_id
            JOIN users u ON s.user_id = u.user_id
            WHERE e.class_id = ?
            ORDER BY u.name ASC
        ", [$classId])->fetchAll();

        // Get active students who are NOT enrolled in this class
        $availableStudents = $db->query("
            SELECT u.user_id, u.name, u.scc_id, s.student_id
            FROM users u
            JOIN students s ON u.user_id = s.user_id
            WHERE u.role = 'student' AND u.status = 'active'
            AND s.student_id NOT IN (
                SELECT student_id FROM enrollments WHERE class_id = ?
            )
            ORDER BY u.name ASC
        ", [$classId])->fetchAll();

        $this->render('admin/classes/enrollments', [
            'title' => 'Class Enrollments',
            'class' => $class,
            'enrollments' => $enrollments,
            'availableStudents' => $availableStudents,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ]);
    }

    /**
     * Enroll a student
     */
    public function enroll(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/classes');
            return;
        }

        $classId = (int)($_POST['class_id'] ?? 0);
        $studentId = (int)($_POST['student_id'] ?? 0);

        if (!$classId || !$studentId) {
            $this->flash('error', 'Class and Student are required.');
            $this->redirect('/admin/classes/enrollments?class_id=' . $classId);
            return;
        }

        $db = Database::getInstance();

        // Check if already enrolled
        $existing = $db->query("SELECT enrollment_id, status FROM enrollments WHERE student_id = ? AND class_id = ?", [$studentId, $classId])->fetch();

        if ($existing) {
            if ($existing['status'] !== 'active') {
                $db->query("UPDATE enrollments SET status = 'active' WHERE enrollment_id = ?", [$existing['enrollment_id']]);
                $this->flash('success', 'Student enrollment re-activated.');
            } else {
                $this->flash('error', 'Student is already actively enrolled.');
            }
        } else {
            $db->query("INSERT INTO enrollments (student_id, class_id, status) VALUES (?, ?, 'active')", [$studentId, $classId]);
            $this->flash('success', 'Student enrolled successfully.');
        }

        $this->redirect('/admin/classes/enrollments?class_id=' . $classId);
    }

    /**
     * Unenroll/suspend a student
     */
    public function unenroll(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/classes');
            return;
        }

        $enrollmentId = (int)($_POST['enrollment_id'] ?? 0);
        $classId = (int)($_POST['class_id'] ?? 0);

        if ($enrollmentId) {
            $db = Database::getInstance();
            $db->query("UPDATE enrollments SET status = 'dropped' WHERE enrollment_id = ?", [$enrollmentId]);
            $this->flash('success', 'Student unenrolled successfully.');
        }

        $this->redirect('/admin/classes/enrollments?class_id=' . $classId);
    }
}
