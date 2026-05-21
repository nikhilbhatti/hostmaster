<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LeaveModel;

class StaffController extends BaseController 
{
    // --- PERMISSION CHECK (With Loop Protection) ---
    private function hasPerm($module, $action = 'view') 
    {
        $role = session()->get('role') ?? session()->get('user_role');
        if ($role === 'admin') return true;

        $perms = session()->get('lms_permissions');
        
        if (!is_array($perms)) return false;
        
        return (isset($perms[$module][$action]) && $perms[$module][$action] == 1);
    }

    // --- STAFF DASHBOARD ---
    public function index() 
    {
        // Try all possible session keys
        $userId = session()->get('admin_id') ?? session()->get('user_id') ?? session()->get('id'); 

        if (!$userId) {
            return redirect()->to(base_url('login'))->with('error', 'Session expired. Please login.');
        }

        $db = \Config\Database::connect();
        $year = date('Y');

        // Leave Balance Logic
        $leaveBalances = $db->table('leave_types lt')
            ->select('lt.leave_name, sla.leave_limit, lt.id as type_id')
            ->select("(SELECT SUM(DATEDIFF(to_date, from_date) + 1) 
                      FROM leave_requests 
                      WHERE user_id = $userId 
                      AND leave_type_id = lt.id 
                      AND status = 'approved' 
                      AND YEAR(from_date) = $year) as used_leaves")
            ->join('staff_leave_allocation sla', "sla.leave_type_id = lt.id AND sla.user_id = $userId AND sla.year = $year", 'left')
            ->get()->getResultArray();

        $data['recent_leaves'] = $db->table('leave_requests lr')
            ->select('lr.*, lt.leave_name')
            ->join('leave_types lt', 'lt.id = lr.leave_type_id')
            ->where('lr.user_id', $userId)
            ->orderBy('lr.id', 'DESC') 
            ->limit(5)
            ->get()->getResultArray();

        $data['leaveBalances'] = $leaveBalances;
        $data['title'] = "Staff Dashboard";

        return view('admin/staff/dashboard', $data);
    }

    // --- APPLY LEAVE PAGE ---
    public function applyLeave() 
    {
        if (!$this->hasPerm('leave_manage', 'add')) {
            return redirect()->to(base_url('staff/dashboard'))->with('error', 'Permission denied.');
        }

        $db = \Config\Database::connect();
        $data['leave_types'] = $db->table('leave_types')->get()->getResultArray();
        $data['title'] = "Apply New Leave";
        
        return view('admin/staff/apply_leave', $data);
    }

    // --- SAVE LEAVE LOGIC ---
    public function storeLeave() 
    {
        if (!$this->hasPerm('leave_manage', 'add')) {
            return redirect()->to(base_url('staff/dashboard'))->with('error', 'Action not allowed.');
        }

        $leaveModel = new \App\Models\LeaveModel();
        $session = session();

        // Get User ID from session (Crucial Fix)
        $userId = $session->get('admin_id') ?? $session->get('user_id') ?? $session->get('id');
        $username = $session->get('user_name') ?? 'Staff Member';

        if (!$userId) {
            return redirect()->to(base_url('login'))->with('error', 'Session expired. Please login again.');
        }

        if (!$this->request->getPost('leave_type')) {
            return redirect()->back()->with('error', 'Please select a leave type.');
        }

        // Total Days Calculation
        $fromDate = $this->request->getPost('from_date');
        $toDate = $this->request->getPost('to_date');
        $diff = strtotime($toDate) - strtotime($fromDate);
        $totalDays = ($diff > 0) ? round($diff / (60 * 60 * 24)) + 1 : 1;

        $data = [
            'user_id'       => $userId, 
            'leave_type_id' => $this->request->getPost('leave_type'),
            'from_date'     => $fromDate,
            'to_date'       => $toDate,
            'total_days'    => $totalDays,
            'reason'        => $this->request->getPost('reason'),
            'status'        => 'pending',
            'applied_on'    => date('Y-m-d H:i:s')
        ];

        if ($leaveModel->save($data)) {
            // Notification logic
            if (method_exists($this, 'sendNotif')) {
                $this->sendNotif(1, "New Leave Request", $username . " has applied for leave.", "admin/leaves/requests", 1); 
            }
            return redirect()->to(base_url('staff/dashboard'))->with('success', 'Leave applied successfully!');
        } else {
            return redirect()->back()->with('error', 'Something went wrong while saving.');
        }
    }

    // --- HISTORY PAGE ---
    public function leaveHistory() 
    {
        if (!$this->hasPerm('leave_req', 'view')) {
            return redirect()->to(base_url('staff/dashboard'))->with('error', 'Access denied.');
        }

        $db = \Config\Database::connect();
        $userId = session()->get('admin_id') ?? session()->get('user_id') ?? session()->get('id');
        
        $data['title'] = "My Leave History";
        $data['my_leaves'] = $db->table('leave_requests lr')
            ->select('lr.*, lt.leave_name')
            ->join('leave_types lt', 'lt.id = lr.leave_type_id')
            ->where('lr.user_id', $userId)
            ->orderBy('lr.id', 'DESC')
            ->get()->getResultArray();

        return view('admin/staff/leave_history', $data);
    }

    // --- HOLIDAY JSON ---
    public function getOfficialHolidays() 
    {
        $db = \Config\Database::connect();
        $results = $db->table('holidays')->get()->getResultArray();
        $events = [];
        foreach($results as $row) {
            $events[] = [
                'title'           => $row['holiday_name'], 
                'start'           => $row['holiday_date'], 
                'backgroundColor' => '#dc3545', 
                'borderColor'     => '#dc3545',
                'allDay'          => true
            ];
        }
        return $this->response->setJSON($events);
    }
}