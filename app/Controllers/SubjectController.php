<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Subject;

class SubjectController extends Controller
{
    /**
     * List all subjects
     */
    public function index(): void
    {
        $subjectModel = new Subject();
        $subjects = $subjectModel->findAll('name ASC');

        $this->render('admin/subjects/index', [
            'title' => 'Manage Subjects',
            'subjects' => $subjects
        ]);
    }

    /**
     * Show create form
     */
    public function createForm(): void
    {
        $this->render('admin/subjects/create', [
            'title' => 'Add New Subject'
        ]);
    }

    /**
     * Handle creation
     */
    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/subjects');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $code = trim($_POST['code'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($name) || empty($code)) {
            $this->flash('error', 'Name and Code are required.');
            $this->redirect('/admin/subjects/create');
            return;
        }

        $subjectModel = new Subject();

        // Check if code exists
        if ($subjectModel->findBy('code', $code)) {
            $this->flash('error', 'Subject code already exists.');
            $this->redirect('/admin/subjects/create');
            return;
        }

        $subjectModel->create([
            'name' => $name,
            'code' => $code,
            'description' => $description
        ]);

        $this->flash('success', 'Subject created successfully.');
        $this->redirect('/admin/subjects');
    }

    /**
     * Show edit form
     */
    public function editForm(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $subjectModel = new Subject();
        $subject = $subjectModel->find($id);

        if (!$subject) {
            $this->flash('error', 'Subject not found.');
            $this->redirect('/admin/subjects');
            return;
        }

        $this->render('admin/subjects/edit', [
            'title' => 'Edit Subject',
            'subject' => $subject
        ]);
    }

    /**
     * Handle update
     */
    public function edit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/subjects');
            return;
        }

        $id = (int)($_POST['subject_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $code = trim($_POST['code'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($name) || empty($code)) {
            $this->flash('error', 'Name and Code are required.');
            $this->redirect('/admin/subjects/edit?id=' . $id);
            return;
        }

        $subjectModel = new Subject();
        $subject = $subjectModel->find($id);

        if (!$subject) {
            $this->flash('error', 'Subject not found.');
            $this->redirect('/admin/subjects');
            return;
        }

        // Check if code exists and belongs to a different subject
        $existing = $subjectModel->findBy('code', $code);
        if ($existing && $existing['subject_id'] !== $id) {
            $this->flash('error', 'Subject code already exists.');
            $this->redirect('/admin/subjects/edit?id=' . $id);
            return;
        }

        $subjectModel->update($id, [
            'name' => $name,
            'code' => $code,
            'description' => $description
        ]);

        $this->flash('success', 'Subject updated successfully.');
        $this->redirect('/admin/subjects');
    }

    /**
     * Handle deletion
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/subjects');
            return;
        }

        $id = (int)($_POST['subject_id'] ?? 0);
        $subjectModel = new Subject();

        if ($subjectModel->find($id)) {
            $subjectModel->delete($id);
            $this->flash('success', 'Subject deleted successfully.');
        } else {
            $this->flash('error', 'Subject not found.');
        }

        $this->redirect('/admin/subjects');
    }
}
