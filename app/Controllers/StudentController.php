<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Student;
use App\Models\CourseClass;
use App\Models\Topic;
use App\Models\Material;

/** StudentController — RBAC: student role only */
class StudentController extends Controller
{
    private function getStudentId(): ?int
    {
        $user = $this->currentUser();
        if (!$user) return null;
        $studentModel = new Student();
        $student = $studentModel->findByUserId($user['user_id']);
        return $student ? (int)$student['student_id'] : null;
    }

    public function dashboard(): void
    {
        $user = $this->currentUser();
        $student = (new Student())->findByUserId($user['user_id']);
        $this->render('student.dashboard', compact('user', 'student'));
    }

    public function classes(): void
    {
        $user = $this->currentUser();
        $studentId = $this->getStudentId();

        $classModel = new CourseClass();
        $classes = $classModel->getEnrolledClasses($studentId);

        $this->render('student.classes', [
            'title' => 'My Classes',
            'user' => $user,
            'classes' => $classes
        ]);
    }

    public function viewClass(): void
    {
        $user = $this->currentUser();
        $studentId = $this->getStudentId();
        $classId = (int)($_GET['id'] ?? 0);

        // Verification that the student is actually enrolled
        $classModel = new CourseClass();
        $enrolledClasses = $classModel->getEnrolledClasses($studentId);
        
        $isEnrolled = false;
        $class = null;
        foreach ($enrolledClasses as $c) {
            if ((int)$c['class_id'] === $classId) {
                $isEnrolled = true;
                $class = $c;
                break;
            }
        }

        if (!$isEnrolled || !$class) {
            $this->flash('error', 'Class not found or unauthorized.');
            $this->redirect('/student/classes');
            return;
        }

        $topicModel = new Topic();
        $topics = $topicModel->getTopicsByClass($classId);

        $this->render('student.class_view', [
            'title' => $class['name'] . ' - Topics',
            'user' => $user,
            'class' => $class,
            'topics' => $topics
        ]);
    }

    public function viewTopic(): void
    {
        $user = $this->currentUser();
        $studentId = $this->getStudentId();
        $topicId = (int)($_GET['id'] ?? 0);

        $topicModel = new Topic();
        $topic = $topicModel->find($topicId);

        if (!$topic) {
            $this->flash('error', 'Topic not found.');
            $this->redirect('/student/classes');
            return;
        }

        // Verify student is enrolled in the topic's class
        $classModel = new CourseClass();
        $enrolledClasses = $classModel->getEnrolledClasses($studentId);
        
        $isEnrolled = false;
        $class = null;
        foreach ($enrolledClasses as $c) {
            if ((int)$c['class_id'] === (int)$topic['class_id']) {
                $isEnrolled = true;
                $class = $c;
                break;
            }
        }

        if (!$isEnrolled || !$class) {
            $this->flash('error', 'Unauthorized access to topic.');
            $this->redirect('/student/classes');
            return;
        }

        $materialModel = new Material();
        $materials = $materialModel->getMaterialsByTopic($topicId);

        // Filter materials that are not yet released
        $now = date('Y-m-d H:i:s');
        $visibleMaterials = [];
        foreach ($materials as $mat) {
            if (empty($mat['release_time']) || $mat['release_time'] <= $now) {
                $visibleMaterials[] = $mat;
            }
        }

        $this->render('student.topic_view', [
            'title' => 'Materials',
            'user' => $user,
            'class' => $class,
            'topic' => $topic,
            'materials' => $visibleMaterials
        ]);
    }
}
