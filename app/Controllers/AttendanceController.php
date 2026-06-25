<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Core\Database;

class AttendanceController extends Controller
{
    /**
     * Render the frontend scanner view
     */
    public function scan(): void
    {
        $db = Database::getInstance();
        
        // Get all classes to allow admin to start a new session
        $classes = $db->query("
            SELECT c.class_id, c.name, s.name as subject_name 
            FROM classes c
            JOIN subjects s ON c.subject_id = s.subject_id
            WHERE c.status = 'active'
        ")->fetchAll();

        // Get currently open sessions for today
        $openSessions = $db->query("
            SELECT s.id as session_id, c.name as class_name, c.class_id
            FROM attendance_sessions s
            JOIN classes c ON s.class_id = c.class_id
            WHERE s.session_date = CURDATE() AND s.status = 'open'
        ")->fetchAll();

        // Enhance open sessions with present count and enrolled count
        foreach ($openSessions as &$os) {
            $os['enrolled_count'] = $db->query("SELECT COUNT(*) FROM enrollments WHERE class_id = ? AND status = 'active'", [$os['class_id']])->fetchColumn();
            $os['present_count'] = $db->query("SELECT COUNT(*) FROM attendance_records WHERE attendance_session_id = ?", [$os['session_id']])->fetchColumn();
        }

        $this->render('admin/attendance/scan', [
            'title' => 'QR Attendance Scanner',
            'classes' => $classes,
            'openSessions' => $openSessions,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ]);
    }

    /**
     * API Endpoint: Verify the scanned QR code
     */
    public function verifyQrScan(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $qrPayload = $input['qr_data'] ?? '';
        $manualCode = $input['manual_code'] ?? '';

        $userId = null;
        $searchStudentId = null;

        if ($manualCode !== '') {
            $searchStudentId = (int) preg_replace('/[^0-9]/', '', $manualCode);
        } else if ($qrPayload !== '') {
            $decodedJson = base64_decode($qrPayload);
            if ($decodedJson === false) {
                echo json_encode(['success' => false, 'message' => 'Invalid QR Code format.']);
                return;
            }
            $data = json_decode($decodedJson, true);
            if (!isset($data['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'QR Code missing user information.']);
                return;
            }
            $userId = (int) $data['user_id'];
        } else {
            echo json_encode(['success' => false, 'message' => 'No QR data or manual code provided.']);
            return;
        }

        $db = Database::getInstance();

        // 1. Check if user exists and is a student
        if ($searchStudentId) {
            $userRow = $db->query("
                SELECT u.user_id, u.name, u.email, s.student_id 
                FROM users u
                JOIN students s ON u.user_id = s.user_id
                WHERE s.student_id = ? AND u.role = 'student'
            ", [$searchStudentId])->fetch();
        } else {
            $userRow = $db->query("
                SELECT u.user_id, u.name, u.email, s.student_id 
                FROM users u
                JOIN students s ON u.user_id = s.user_id
                WHERE u.user_id = ? AND u.role = 'student'
            ", [$userId])->fetch();
        }

        if (!$userRow) {
            echo json_encode(['success' => false, 'message' => 'Student not found.']);
            return;
        }

        $userId = (int) $userRow['user_id'];
        $studentId = (int) $userRow['student_id'];

        // 2. Check for open sessions today
        $openSessions = $db->query("
            SELECT * FROM attendance_sessions 
            WHERE session_date = CURDATE() AND status = 'open'
        ")->fetchAll();

        if (empty($openSessions)) {
            echo json_encode(['success' => false, 'message' => 'No open attendance sessions for today.']);
            return;
        }

        // 3. Check enrollment against open sessions
        $enrolledSessionId = null;
        $classId = null;
        foreach ($openSessions as $session) {
            $isEnrolled = $db->query("
                SELECT COUNT(*) FROM enrollments 
                WHERE student_id = ? AND class_id = ? AND status = 'active'
            ", [$studentId, $session['class_id']])->fetchColumn();

            if ($isEnrolled > 0) {
                $enrolledSessionId = (int) $session['id'];
                $classId = (int) $session['class_id'];
                break; // Found the class the student is attending today
            }
        }

        if (!$enrolledSessionId) {
            echo json_encode(['success' => false, 'message' => 'Student is not actively enrolled in any of today\'s open classes.']);
            return;
        }

        // 4. Check if already marked present today in this session
        $alreadyPresent = $db->query("
            SELECT COUNT(*) FROM attendance_records 
            WHERE attendance_session_id = ? AND user_id = ?
        ", [$enrolledSessionId, $userId])->fetchColumn();

        if ($alreadyPresent > 0) {
            echo json_encode(['success' => false, 'message' => 'Student already marked present for this class today.']);
            return;
        }

        // Gather stats for the Confirmation Card
        // Total sessions held for this class so far
        $totalSessions = (int) $db->query("
            SELECT COUNT(*) FROM attendance_sessions WHERE class_id = ? AND status = 'closed'
        ", [$classId])->fetchColumn() + 1; // +1 for today

        // Past attendance count
        $pastAttendance = (int) $db->query("
            SELECT COUNT(*) FROM attendance_records ar
            JOIN attendance_sessions asession ON ar.attendance_session_id = asession.id
            WHERE ar.user_id = ? AND asession.class_id = ?
        ", [$userId, $classId])->fetchColumn();

        echo json_encode([
            'success' => true,
            'data' => [
                'session_id' => $enrolledSessionId,
                'user_id' => $userId,
                'name' => $userRow['name'],
                'photo_url' => 'https://ui-avatars.com/api/?name=' . urlencode($userRow['name']) . '&background=random',
                'id_code' => 'STU-' . str_pad((string)$studentId, 4, '0', STR_PAD_LEFT),
                'total_sessions' => $totalSessions,
                'past_attendance' => $pastAttendance,
                'payment_status' => 'Verified' // Placeholder for Phase 4
            ]
        ]);
    }

    /**
     * API Endpoint: Final confirmation to insert attendance record
     */
    public function confirmAttendance(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $sessionId = $input['session_id'] ?? null;
        $userId = $input['user_id'] ?? null;
        $method = $input['method'] ?? 'qr_scan';

        if (!$sessionId || !$userId) {
            echo json_encode(['success' => false, 'message' => 'Missing session or user data.']);
            return;
        }

        $db = Database::getInstance();

        // Validate session is still open
        $session = $db->query("SELECT * FROM attendance_sessions WHERE id = ? AND status = 'open'", [$sessionId])->fetch();
        if (!$session) {
            echo json_encode(['success' => false, 'message' => 'Attendance session is no longer open.']);
            return;
        }

        // Double check not already present
        $alreadyPresent = $db->query("
            SELECT COUNT(*) FROM attendance_records 
            WHERE attendance_session_id = ? AND user_id = ?
        ", [$sessionId, $userId])->fetchColumn();

        if ($alreadyPresent > 0) {
            echo json_encode(['success' => false, 'message' => 'Already marked present.']);
            return;
        }

        // Insert Record
        $db->query("
            INSERT INTO attendance_records (attendance_session_id, user_id, scanned_at, method) 
            VALUES (?, ?, NOW(), ?)
        ", [$sessionId, $userId, $method]);

        echo json_encode([
            'success' => true,
            'message' => 'Attendance recorded successfully.'
        ]);
    }

    /**
     * Start/Reopen a session for a class
     */
    public function startSession(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/attendance/scan');
            return;
        }

        $classId = (int)($_POST['class_id'] ?? 0);
        if (!$classId) {
            $this->flash('error', 'Please select a valid class.');
            $this->redirect('/admin/attendance/scan');
            return;
        }

        $db = Database::getInstance();
        $user = $this->currentUser();

        // Check if there's already a session for today
        $existing = $db->query("SELECT id, status FROM attendance_sessions WHERE class_id = ? AND session_date = CURDATE()", [$classId])->fetch();

        if ($existing) {
            if ($existing['status'] === 'closed') {
                $db->query("UPDATE attendance_sessions SET status = 'open' WHERE id = ?", [$existing['id']]);
                $this->flash('success', 'Session reopened successfully.');
            } else {
                $this->flash('error', 'A session is already open for this class today.');
            }
        } else {
            $db->query("
                INSERT INTO attendance_sessions (class_id, session_date, status, started_by)
                VALUES (?, CURDATE(), 'open', ?)
            ", [$classId, $user['user_id']]);
            $this->flash('success', 'New attendance session started.');
        }

        $this->redirect('/admin/attendance/scan');
    }

    /**
     * Close an active session
     */
    public function closeSession(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/attendance/scan');
            return;
        }

        $sessionId = (int)($_POST['session_id'] ?? 0);
        if (!$sessionId) {
            $this->flash('error', 'Invalid session.');
            $this->redirect('/admin/attendance/scan');
            return;
        }

        $db = Database::getInstance();
        $db->query("UPDATE attendance_sessions SET status = 'closed' WHERE id = ?", [$sessionId]);
        $this->flash('success', 'Attendance session closed.');

        $this->redirect('/admin/attendance/scan');
    }

    /**
     * View Attendance Log
     */
    public function log(): void
    {
        $db = Database::getInstance();
        
        $classId = (int)($_GET['class_id'] ?? 0);
        $date = $_GET['date'] ?? date('Y-m-d');
        
        // Get all classes for the filter dropdown
        $classes = $db->query("
            SELECT c.class_id, c.name, s.name as subject_name 
            FROM classes c
            JOIN subjects s ON c.subject_id = s.subject_id
            ORDER BY c.created_at DESC
        ")->fetchAll();

        $records = [];
        if ($classId && $date) {
            $records = $db->query("
                SELECT ar.scanned_at, ar.method, u.name, u.email, s.student_id, a_session.session_date, c.name as class_name
                FROM attendance_records ar
                JOIN attendance_sessions a_session ON ar.attendance_session_id = a_session.id
                JOIN users u ON ar.user_id = u.user_id
                JOIN students s ON u.user_id = s.user_id
                JOIN classes c ON a_session.class_id = c.class_id
                WHERE a_session.class_id = ? AND a_session.session_date = ?
                ORDER BY ar.scanned_at DESC
            ", [$classId, $date])->fetchAll();
        }

        $this->render('admin/attendance/log', [
            'title' => 'Attendance Log',
            'classes' => $classes,
            'records' => $records,
            'selectedClassId' => $classId,
            'selectedDate' => $date
        ]);
    }
}
