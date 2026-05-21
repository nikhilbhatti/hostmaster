<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class LeavePermission extends BaseController
{
    public function index($id = null)
    {
        if (!$id) {
            return redirect()->to(base_url('admin/leaves/manage-staff'))->with('error', 'Invalid Staff ID.');
        }

        $adminModel = new AdminModel();
        $staffData = $adminModel->find($id);

        if (!$staffData) {
            return redirect()->to(base_url('admin/leaves/manage-staff'))->with('error', 'Staff member not found!');
        }

        $data = [
            'staff'       => $staffData,
            'title'       => "LMS Permissions - " . ($staffData['name'] ?? $staffData['username']),
            // Database se permissions fetch karke array mein convert karein
            'current_lms' => json_decode($staffData['lms_permissions'] ?? '{}', true)
        ];
        
        return view('admin/staff/leave_permissions', $data);
    }

    public function update($id = null)
    {
        if (!$id) {
            return redirect()->back()->with('error', 'User ID missing.');
        }

        $adminModel = new AdminModel();
        
        /**
         * Humne view mein names rakhe hain:
         * 1. lms_permissions[leave_manage][view] -> Dashboard toggle
         * 2. lms_permissions[leave_manage][add]  -> Apply Leave toggle
         * 3. lms_permissions[leave_req][view]    -> Leave History toggle
         */
        $lms_data = $this->request->getPost('lms_permissions') ?? [];
        
        // Ensure values are stored as integers (1 or 0) for better consistency
        $final_permissions = [];
        foreach ($lms_data as $module => $actions) {
            foreach ($actions as $action => $value) {
                $final_permissions[$module][$action] = (int)$value;
            }
        }

        $updateData = [
            'lms_permissions' => json_encode($final_permissions), // JSON string bana kar save karein
            'updated_at'      => date('Y-m-d H:i:s')
        ];

        // Model mein 'lms_permissions' field allowedFields mein hona chahiye
        if ($adminModel->update($id, $updateData)) {
            return redirect()->back()->with('success', 'LMS Permissions Updated Successfully!');
        } else {
            return redirect()->back()->with('error', 'Update failed. Check AdminModel allowedFields.');
        }
    }
}