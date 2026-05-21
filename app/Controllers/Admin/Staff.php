<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class Staff extends BaseController {
    
    // 1. Staff List Dikhana (Sirf View and Permission Button ke liye)
    public function index() {
        $model = new AdminModel();
        
        // Sirf staff members ko fetch karna hai
        $data['staff_members'] = $model->where('role', 'staff')->findAll();
        $data['title'] = "Staff Permissions";
        
        return view('admin/staff/index', $data);
    }

    // 2. Permissions Page Load Karna
    public function permissions($id) 
    {
        $db = \Config\Database::connect();
        
        // Staff details check karein (Ensure ki staff valid hai)
        $data['staff'] = $db->table('admins')->where('id', $id)->where('role', 'staff')->get()->getRowArray(); 
        
        if (!$data['staff']) {
            return redirect()->to('admin/staff')->with('error', 'Staff member not found!');
        }
        
        // Modules list jinpar control dena hai
        $data['modules'] = ['clients', 'orders', 'backups', 'notifications'];
        
        // Is specific user ki current permissions database se nikalna
        $current = $db->table('permissions')->where('user_id', $id)->get()->getResultArray();
        
        // Module name ko key bana kar array organize karna taaki view me check karna aasan ho
        $data['current_permissions'] = array_column($current, null, 'module_name');
        
        return view('admin/staff/permissions', $data);
    }

    // 3. Permissions Save/Update Karna
    public function updatePermissions($id)
    {
        $db = \Config\Database::connect();
        $permissions = $this->request->getPost('permissions');

        // Step 1: Purani saari permissions delete karein taaki fresh update ho sake
        $db->table('permissions')->where('user_id', $id)->delete();

        // Step 2: Agar permissions select ki gayi hain, tabhi loop chalayein
        if ($permissions) {
            foreach ($permissions as $module => $actions) {
                $db->table('permissions')->insert([
                    'user_id'     => $id,
                    'module_name' => $module,
                    'can_view'    => isset($actions['can_view']) ? 1 : 0,
                    'can_add'     => isset($actions['can_add']) ? 1 : 0,
                    'can_edit'    => isset($actions['can_edit']) ? 1 : 0,
                    'can_delete'  => isset($actions['can_delete']) ? 1 : 0,
                ]);
            }
        }

        return redirect()->to('admin/staff')->with('status', 'Permissions updated successfully!');
    }
}