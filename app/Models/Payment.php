<?php

namespace App\Models;

use App\Core\Model;

class Payment extends Model
{
    protected string $table = 'payments';

    protected function getPrimaryKey(): string
    {
        return 'payment_id';
    }

    /**
     * Get payments for a specific student, optionally filtered by class
     */
    public function getStudentPayments(int $studentId, ?int $classId = null): array
    {
        $sql = "SELECT p.*, c.name as class_name, u.name as collected_by_name 
                FROM payments p
                JOIN classes c ON p.class_id = c.class_id
                JOIN users u ON p.collected_by = u.user_id
                WHERE p.student_id = ?";
        
        $params = [$studentId];
        
        if ($classId !== null) {
            $sql .= " AND p.class_id = ?";
            $params[] = $classId;
        }
        
        $sql .= " ORDER BY p.payment_date DESC";
        
        return $this->db->query($sql, $params)->fetchAll();
    }
    
    /**
     * Check if a student has paid for a specific class and month
     */
    public function hasPaid(int $studentId, int $classId, string $month): bool
    {
        $sql = "SELECT payment_id FROM payments WHERE student_id = ? AND class_id = ? AND month = ?";
        $result = $this->db->query($sql, [$studentId, $classId, $month])->fetch();
        return $result !== false && $result !== null;
    }
}
