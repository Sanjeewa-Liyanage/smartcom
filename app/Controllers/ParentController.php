<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ParentModel;

/** ParentController — RBAC: parent role only */
class ParentController extends Controller
{
    public function dashboard(): void
    {
        $user         = $this->currentUser();
        $parentModel  = new ParentModel();
        $parentRecord = $parentModel->getByUserIdWithDetails($user['user_id']);
        
        $linkedStudents = [];
        if ($parentRecord) {
            $linkedStudents = $parentModel->getLinkedStudents($parentRecord['parent_id']);
        }

        $this->render('parent.dashboard', compact('user', 'parentRecord', 'linkedStudents'));
    }
}
