<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\CourseClass;
use App\Models\Payment;
use App\Core\Database;

class FinanceController extends Controller
{
    /**
     * Show the fee collection interface
     */
    public function collectFee(): void
    {
        $this->render('admin/finance/collect', [
            'title' => 'Collect Fees'
        ]);
    }

    /**
     * API: Fetch student details and their enrolled classes with fees
     */
    public function fetchStudent(): void
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $sccId = trim($input['scc_id'] ?? '');

        if (empty($sccId)) {
            echo json_encode(['success' => false, 'message' => 'Student ID (SCC-ID) or QR Code is required.']);
            return;
        }

        $userModel = new User();
        // The QR code for students might be exactly the SCC ID, or we check both
        $studentUser = $userModel->findBySccId($sccId);

        if (!$studentUser) {
            // Check by QR code if needed, assuming qr_code might just be the SCC ID or similar
            // For now, lookup by scc_id
            $db = Database::getInstance();
            $studentUser = $db->query("SELECT u.*, s.student_id, s.qr_code 
                                       FROM users u 
                                       JOIN students s ON u.user_id = s.user_id 
                                       WHERE u.scc_id = ? OR s.qr_code = ?", [$sccId, $sccId])->fetch();
        } else {
            $db = Database::getInstance();
            $studentUser = $db->query("SELECT u.*, s.student_id 
                                       FROM users u 
                                       JOIN students s ON u.user_id = s.user_id 
                                       WHERE u.user_id = ?", [$studentUser['user_id']])->fetch();
        }

        if (!$studentUser) {
            echo json_encode(['success' => false, 'message' => 'Student not found.']);
            return;
        }

        // Get enrolled classes with their fees
        $classModel = new CourseClass();
        $enrolledClasses = $classModel->getEnrolledClasses($studentUser['student_id']);

        echo json_encode([
            'success' => true,
            'student' => [
                'student_id' => $studentUser['student_id'],
                'name' => $studentUser['name'],
                'scc_id' => $studentUser['scc_id'],
                'email' => $studentUser['email']
            ],
            'classes' => $enrolledClasses
        ]);
    }

    /**
     * Process the payment submission
     */
    public function processPayment(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/finance/collect');
            return;
        }

        $studentId = (int)($_POST['student_id'] ?? 0);
        $classId = (int)($_POST['class_id'] ?? 0);
        $months = $_POST['months'] ?? []; // Array of months selected, e.g. ["2026-01", "2026-02"]
        $totalAmount = (float)($_POST['total_amount'] ?? 0.00);

        if (!$studentId || !$classId || empty($months) || $totalAmount <= 0) {
            $this->flash('error', 'Invalid payment details submitted.');
            $this->redirect('/admin/finance/collect');
            return;
        }

        $classModel = new CourseClass();
        $class = $classModel->find($classId);
        if (!$class) {
            $this->flash('error', 'Class not found.');
            $this->redirect('/admin/finance/collect');
            return;
        }

        // Calculate expected per month to validate
        $expectedPerMonth = (float)$class['monthly_fee'];
        $numMonths = count($months);
        $expectedTotal = $expectedPerMonth * $numMonths;

        // Simple validation: Ensure total submitted is roughly what's expected
        // (Floating point comparison)
        if (abs($totalAmount - $expectedTotal) > 0.1) {
            $this->flash('error', 'Payment amount mismatch. Expected Rs. ' . number_format($expectedTotal, 2));
            $this->redirect('/admin/finance/collect');
            return;
        }

        $paymentModel = new Payment();
        $transactionRef = uniqid('txn_');
        $collectedBy = $_SESSION['user_id'];
        
        $db = Database::getInstance();
        $db->getPdo()->beginTransaction();

        try {
            foreach ($months as $month) {
                // Check if already paid for this month
                if ($paymentModel->hasPaid($studentId, $classId, $month)) {
                    throw new \Exception("Student has already paid for $month in this class.");
                }

                $paymentModel->create([
                    'student_id' => $studentId,
                    'class_id' => $classId,
                    'amount' => $expectedPerMonth,
                    'month' => $month,
                    'transaction_ref' => $transactionRef,
                    'collected_by' => $collectedBy
                ]);
            }
            $db->getPdo()->commit();
            $this->flash('success', 'Payment of Rs. ' . number_format($totalAmount, 2) . ' collected successfully.');
        } catch (\Exception $e) {
            $db->getPdo()->rollBack();
            $this->flash('error', 'Payment failed: ' . $e->getMessage());
        }

        $this->redirect('/admin/finance/collect');
    }

    /**
     * Show all payment records
     */
    public function records(): void
    {
        $db = Database::getInstance();
        $payments = $db->query("
            SELECT p.*, s.student_id, u_student.name as student_name, u_student.scc_id,
                   c.name as class_name, c.subject_id, sub.name as subject_name,
                   u_admin.name as collected_by_name
            FROM payments p
            JOIN students s ON p.student_id = s.student_id
            JOIN users u_student ON s.user_id = u_student.user_id
            JOIN classes c ON p.class_id = c.class_id
            JOIN subjects sub ON c.subject_id = sub.subject_id
            JOIN users u_admin ON p.collected_by = u_admin.user_id
            ORDER BY p.payment_date DESC
        ")->fetchAll();

        $this->render('admin/finance/records', [
            'title' => 'Payment Records',
            'payments' => $payments
        ]);
    }
}
