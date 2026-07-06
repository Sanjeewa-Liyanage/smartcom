<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Tutor;
use App\Models\CourseClass;
use App\Models\Topic;
use App\Models\Material;

class TutorMaterialController extends Controller
{
    private function getTutorId(): ?int
    {
        $user = $this->currentUser();
        if (!$user) return null;
        $tutorModel = new Tutor();
        $tutor = $tutorModel->findByUserId($user['user_id']);
        return $tutor ? (int)$tutor['tutor_id'] : null;
    }

    public function classes(): void
    {
        $user = $this->currentUser();
        $tutorId = $this->getTutorId();
        
        $classModel = new CourseClass();
        $classes = $classModel->getClassesByTutor($tutorId);

        $this->render('tutor.classes', [
            'title' => 'My Classes',
            'user' => $user,
            'classes' => $classes
        ]);
    }

    public function viewClass(): void
    {
        $user = $this->currentUser();
        $tutorId = $this->getTutorId();
        $classId = (int)($_GET['id'] ?? 0);

        $classModel = new CourseClass();
        $class = $classModel->getClassWithDetails($classId);

        // Verify ownership
        if (!$class || (int)$class['tutor_id'] !== $tutorId) {
            $this->flash('error', 'Class not found or unauthorized.');
            $this->redirect('/tutor/classes');
            return;
        }

        $topicModel = new Topic();
        $topics = $topicModel->getTopicsByClass($classId);

        $materialModel = new Material();
        $materialsByTopic = [];
        foreach ($topics as $topic) {
            $materialsByTopic[$topic['topic_id']] = $materialModel->getMaterialsByTopic($topic['topic_id']);
        }

        $this->render('tutor.class_view', [
            'title' => 'Class Materials - ' . $class['name'],
            'user' => $user,
            'class' => $class,
            'topics' => $topics,
            'materialsByTopic' => $materialsByTopic,
            'csrf' => $this->csrfToken()
        ]);
    }

    public function viewTopic(): void
    {
        $user = $this->currentUser();
        $tutorId = $this->getTutorId();
        $topicId = (int)($_GET['id'] ?? 0);

        $topicModel = new Topic();
        $topic = $topicModel->find($topicId);

        if (!$topic) {
            $this->flash('error', 'Topic not found.');
            $this->redirect('/tutor/classes');
            return;
        }

        $classModel = new CourseClass();
        $class = $classModel->getClassWithDetails($topic['class_id']);

        if (!$class || (int)$class['tutor_id'] !== $tutorId) {
            $this->flash('error', 'Unauthorized.');
            $this->redirect('/tutor/classes');
            return;
        }

        $materialModel = new Material();
        $materials = $materialModel->getMaterialsByTopic($topicId);

        $this->render('tutor.topic_view', [
            'title' => 'Topic Materials',
            'user' => $user,
            'class' => $class,
            'topic' => $topic,
            'materials' => $materials,
            'csrf' => $this->csrfToken()
        ]);
    }

    public function storeTopic(): void
    {
        $this->verifyCsrf();

        $tutorId = $this->getTutorId();
        $classId = (int)($_POST['class_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        // Verify ownership
        $classModel = new CourseClass();
        $class = $classModel->getClassWithDetails($classId);
        if (!$class || (int)$class['tutor_id'] !== $tutorId) {
            $this->flash('error', 'Unauthorized.');
            $this->redirect('/tutor/classes');
            return;
        }

        if (empty($name)) {
            $this->flash('error', 'Topic name is required.');
        } else {
            $coverImage = null;
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid('topic_cover_') . '.' . $ext;
                $uploadDir = ROOT_PATH . '/public/cover_images';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $uploadDir . '/' . $filename)) {
                    $coverImage = $filename;
                }
            }

            $topicModel = new Topic();
            $topicModel->create([
                'class_id' => $classId,
                'name' => $name,
                'description' => $description,
                'cover_image' => $coverImage
            ]);
            $this->flash('success', 'Topic created successfully.');
        }

        $this->redirect('/tutor/classes/view?id=' . $classId);
    }

    public function uploadMaterial(): void
    {
        $this->verifyCsrf();

        $tutorId = $this->getTutorId();
        $classId = (int)($_POST['class_id'] ?? 0);
        $topicId = (int)($_POST['topic_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $type = $_POST['type'] ?? '';
        $releaseTime = $_POST['release_time'] ?? null;
        if (empty($releaseTime)) {
            $releaseTime = null;
        } else {
            $releaseTime = date('Y-m-d H:i:s', strtotime($releaseTime));
        }

        if (empty($title) || empty($type)) {
            $this->flash('error', 'Title and Type are required.');
            $this->redirect('/tutor/classes/view?id=' . $classId);
            return;
        }

        $filePath = null;

        if ($type === 'link') {
            $filePath = trim($_POST['link_url'] ?? '');
            if (empty($filePath)) {
                $this->flash('error', 'Link URL is required.');
                $this->redirect('/tutor/classes/view?id=' . $classId);
                return;
            }
        } else {
            if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                $this->flash('error', 'File upload failed.');
                $this->redirect('/tutor/classes/view?id=' . $classId);
                return;
            }

            $fileSize = $_FILES['file']['size'];
            
            // Check size limits
            if ($type === 'zip' && $fileSize > 20 * 1024 * 1024) {
                $this->flash('error', 'ZIP files must be under 20MB.');
                $this->redirect('/tutor/classes/view?id=' . $classId);
                return;
            } elseif ($type !== 'zip' && $fileSize > 8 * 1024 * 1024) {
                $this->flash('error', 'Documents must be under 8MB.');
                $this->redirect('/tutor/classes/view?id=' . $classId);
                return;
            }

            // Move file
            $uploadDir = ROOT_PATH . '/public/uploads/materials';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('mat_') . '.' . $ext;
            $destination = $uploadDir . '/' . $filename;

            if (move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {
                $filePath = '/uploads/materials/' . $filename;
            } else {
                $this->flash('error', 'Failed to save file.');
                $this->redirect('/tutor/classes/view?id=' . $classId);
                return;
            }
        }

        $materialModel = new Material();
        $materialModel->create([
            'topic_id' => $topicId,
            'tutor_id' => $tutorId,
            'title' => $title,
            'type' => $type,
            'file_path' => $filePath,
            'release_time' => $releaseTime
        ]);

        $this->flash('success', 'Material uploaded successfully.');
        $this->redirect('/tutor/classes/view?id=' . $classId);
    }

    public function deleteMaterial(): void
    {
        $this->verifyCsrf();

        $materialId = (int)($_POST['material_id'] ?? 0);
        $classId = (int)($_POST['class_id'] ?? 0);
        $tutorId = $this->getTutorId();

        $materialModel = new Material();
        $material = $materialModel->find($materialId);

        if ($material && (int)$material['tutor_id'] === $tutorId) {
            if ($material['type'] !== 'link' && $material['file_path']) {
                $fullPath = ROOT_PATH . '/public' . $material['file_path'];
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
            $materialModel->delete($materialId);
            $this->flash('success', 'Material deleted.');
        } else {
            $this->flash('error', 'Unauthorized or not found.');
        }

        $this->redirect('/tutor/classes/view?id=' . $classId);
    }
}
