<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LeaveModel;

class LeaveController extends BaseController {

    // 1. DASHBOARD & RECENT LEAVES (Only Leave Logic)
    // 1. DASHBOARD & RECENT LEAVES (Grouped by Staff)
public function index() {
    $db = \Config\Database::connect();
    $user_role = session()->get('role') ?? 'staff'; 
    $admin_id = session()->get('admin_id');

    // Total Counts
    $data['total_staff'] = $db->table('admins')->where('role', 'staff')->countAllResults();
    $data['pending_leaves'] = $db->table('leave_requests')->where('status', 'pending')->countAllResults();
    $data['today_reports'] = $db->table('leave_requests')
                                ->where('from_date', date('Y-m-d')) 
                                ->countAllResults();

    // Dashboard Data Fetching
    $builder = $db->table('leave_requests lr')
        ->select('lr.*, a.name as username, lt.leave_name')
        ->join('admins a', 'a.id = lr.user_id')
        ->join('leave_types lt', 'lt.id = lr.leave_type_id');

    if ($user_role !== 'admin') {
        $builder->where('lr.user_id', $admin_id);
    } 
    
    $data['recent_leaves'] = $builder->orderBy('lr.id', 'DESC')->limit(10)->get()->getResultArray();

    $viewData = isset($this->data) ? array_merge($this->data, $data) : $data;
    return view('admin/leaves/dashboard', $viewData);
}
public function autoSyncLeaves() {
    $db = \Config\Database::connect();
    
    $today = date('Y-m-d');
    $lastDayOfMonth = date('Y-m-t');

    // Sirf mahine ke aakhri din hi logic chalega
    if ($today !== $lastDayOfMonth) {
        return "Not the last day of month. Skipping...";
    }

    $currentMonth = (int)date('m');
    $currentYear  = (int)date('Y');

    // Saari allocations nikaalein
    $allocations = $db->table('staff_leave_allocation sla')
        ->select('sla.*, a.joining_date, a.employment_type')
        ->join('admins a', 'a.id = sla.user_id')
        ->where('sla.year', $currentYear)
        ->get()->getResultArray();

    foreach ($allocations as $alc) {
        // Double check: Is mahine pehle update toh nahi hua?
        if ($alc['last_updated_month'] == $currentMonth) continue;

        $leaveType = $db->table('leave_types')->where('id', $alc['leave_type_id'])->get()->getRowArray();
        $lName = strtolower($leaveType['leave_name']);
        $newLimit = $alc['leave_limit'];

        // ❌ EARNED LEAVE LOGIC HATA DI
        // Ab earned leave admin manually set karega

        // ✅ SHORT/HALF LEAVE LOGIC - SAME RAKHA
        if (str_contains($lName, 'short') || str_contains($lName, 'half')) {
            // Purani expire ho jayegi, naye mahine ke liye sirf 1 milegi
            $newLimit = 1; 
        }
        // NOTE: Sick/Casual/Earned leaves admin manual set karega

        // Update DB
        $db->table('staff_leave_allocation')
           ->where('id', $alc['id'])
           ->update([
                'leave_limit'        => $newLimit,
                'last_updated_month' => $currentMonth,
                'updated_at'         => date('Y-m-d H:i:s')
           ]);
    }
    return "Sync Success for $lastDayOfMonth";
}
public function manageStaff() {
    $db = \Config\Database::connect();
    
    // 1. Database se staff data fetch karein (Humein naye columns bhi chahiye)
    $users = $db->table('admins')
                ->where('role', 'staff')
                ->orderBy('id', 'DESC') // Latest staff upar dikhega
                ->get()
                ->getResultArray();

    // 2. Data Processing & Safety Check
    foreach ($users as &$u) {
        // Status check
        $u['is_active'] = $u['is_active'] ?? 1; 
        
        // Joining Date check (Safety for old records)
        $u['joining_date'] = $u['joining_date'] ?? ($u['created_at'] ?? date('Y-m-d'));
        
        // --- NAYA LOGIC: Employment Type handling ---
        // Agar DB mein value null hai toh default 'Fresher' set kar rahe hain safety ke liye
        $u['employment_type'] = $u['employment_type'] ?? 'Fresher';
        
        // Employee ID handling
        $u['employee_id'] = $u['employee_id'] ?? 'N/A';

        // Calculation: Kitne months purana staff hai (View mein help karega)
        $jDate = new \DateTime($u['joining_date']);
        $today = new \DateTime();
        $diff = $jDate->diff($today);
        $u['months_completed'] = ($diff->y * 12) + $diff->m;
    }
    
    // 3. View ko data bhejna
    $data['users'] = $users;
    $data['title'] = "Manage Office Staff"; // Page title for breadcrumbs
    
    // Hostmaster structure ke hisab se data merge karna
    $viewData = isset($this->data) ? array_merge($this->data, $data) : $data;
    
    return view('admin/leaves/manage_staff', $viewData);
}
    // 2. LEAVE TYPES MANAGEMENT
    public function leaveTypes() {
        $db = \Config\Database::connect();
        $data['types'] = $db->table('leave_types')->get()->getResultArray();
        
        $viewData = isset($this->data) ? array_merge($this->data, $data) : $data;
        return view('admin/leaves/leave_types', $viewData);
    }
public function editStaff($id)
{
    $db = \Config\Database::connect();
    // Staff ka data nikalna
    $data['user'] = $db->table('admins')->where('id', $id)->get()->getRowArray();

    if (!$data['user']) {
        return redirect()->back()->with('error', 'Staff member nahi mila.');
    }

    // Aapka existing header/data agar kuch hai toh use merge karein
    $viewData = isset($this->data) ? array_merge($this->data, $data) : $data;
    
    // Yahan aapko 'edit_staff' naam ki ek view file banani hogi
    return view('admin/leaves/edit_staff', $viewData);
}
public function updateStaff($id)
{
    $db = \Config\Database::connect();
    
    $updateData = [
        'name'            => $this->request->getPost('full_name'),
        'username'        => $this->request->getPost('username'),
        'email'           => $this->request->getPost('email'),
        'employee_id'     => $this->request->getPost('employee_id'),
        'employment_type' => $this->request->getPost('employment_type'),
        'joining_date'    => $this->request->getPost('joining_date'),
    ];

    // Agar password field empty nahi hai, tabhi update karein
    $password = $this->request->getPost('password');
    if (!empty($password)) {
        $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    if ($db->table('admins')->where('id', $id)->update($updateData)) {
        return redirect()->to(base_url('admin/manage-staff'))->with('success', 'Staff details update ho gayi hain.');
    }

    return redirect()->back()->with('error', 'Update fail ho gaya.');
}
public function deleteStaff($id)
{
    $db = \Config\Database::connect();

    // Check karein ki staff exist karta hai ya nahi
    $user = $db->table('admins')->where('id', $id)->get()->getRowArray();
    
    if ($user) {
        if ($db->table('admins')->where('id', $id)->delete()) {
            return redirect()->back()->with('success', 'Staff member ko delete kar diya gaya hai.');
        }
    }

    return redirect()->back()->with('error', 'Delete karne mein problem aayi.');
}
    public function storeLeaveType() {
        $db = \Config\Database::connect();
        $leaveName = $this->request->getPost('leave_name');
        if ($leaveName) {
            $db->table('leave_types')->insert(['leave_name' => $leaveName]);
            return redirect()->to(base_url('admin/leaves/types'))->with('status', 'Category created successfully!');
        }
        return redirect()->back()->with('error', 'Name is required.');
    }

public function deleteLeaveType($id) {
    $db = \Config\Database::connect();
    
    // Step 1: Pehle is Leave Type se judi saari staff allocations delete karein
    $db->table('staff_leave_allocation')->where('leave_type_id', $id)->delete();
    
    // Step 2: Is Leave Type se judi saari leave requests delete karein
    // Isse staff dashboard ke card se used leaves hat jayengi
    $db->table('leave_requests')->where('leave_type_id', $id)->delete();
    
    // Step 3: Ab main Leave Type ko delete karein
    $db->table('leave_types')->where('id', $id)->delete();
    
    return redirect()->back()->with('success', 'Leave type and all related records deleted successfully!');
}
    // 3. PENDING & PROCESSED REQUESTS
   // 3. PENDING & PROCESSED REQUESTS (Grouped for History)
public function pendingLeaves() {
    $db = \Config\Database::connect();
    
    // 1. Pending Requests (Sahi hai, latest upar aayegi)
    $data['requests'] = $db->table('leave_requests lr')
        ->select('lr.*, a.name as username, lt.leave_name')
        ->join('admins a', 'a.id = lr.user_id')
        ->join('leave_types lt', 'lt.id = lr.leave_type_id')
        ->where('lr.status', 'pending')
        ->orderBy('lr.id', 'DESC')
        ->get()->getResultArray();

    // 2. Processed History (GROUP BY ke sath Latest entry dikhane ke liye)
    // Humne select mein 'MAX(lr.id)' ki logic ko follow karte hue ordering fix ki hai
    $data['processed'] = $db->table('leave_requests lr')
        ->select('lr.*, a.name as username, lt.leave_name, COUNT(lr.id) as total_processed')
        ->join('admins a', 'a.id = lr.user_id')
        ->join('leave_types lt', 'lt.id = lr.leave_type_id')
        ->whereIn('lr.status', ['approved', 'rejected'])
        ->groupBy('lr.user_id') 
        ->orderBy('lr.id', 'DESC') // Isse latest action pehle dikhega
        ->limit(10) 
        ->get()->getResultArray();

    $viewData = isset($this->data) ? array_merge($this->data, $data) : $data;
    return view('admin/leaves/leave_requests', $viewData);
}
    // 4. STATUS UPDATE
 public function updateLeaveStatus() {
    $db = \Config\Database::connect();
    $email = \Config\Services::email();

    // 1. SMTP Settings uthao (Jo aapne smtp_settings table mein save ki hain)
    $settings = $db->table('smtp_settings')->where('id', 1)->get()->getRowArray();
    
    if (!$settings) {
        return redirect()->back()->with('error', 'Email configuration missing in settings.');
    }

    $id = $this->request->getPost('id');
    $status = $this->request->getPost('status'); 
    $remark = $this->request->getPost('admin_remark');

    // 2. Leave details aur Staff ki Email fetch karein (admins table join)
    $leaveRequest = $db->table('leave_requests lr')
        ->select('lr.*, u.name as staff_name, u.email as staff_email')
        ->join('admins u', 'u.id = lr.user_id') 
        ->where('lr.id', $id)
        ->get()->getRowArray();
    
    if ($leaveRequest) {
        // 3. Database Update
        $db->table('leave_requests')->where('id', $id)->update([
            'status' => $status,
            'admin_remark' => $remark
        ]);

        // 4. Leave Balance Deduction (Approved hone par)
        if ($status === 'approved') {
            $deduction = ($leaveRequest['leave_duration'] === 'half_day') ? 0.5 : ( (new \DateTime($leaveRequest['from_date']))->diff(new \DateTime($leaveRequest['to_date']))->days + 1 );
            
            $db->table('staff_leave_allocation')
               ->where(['user_id' => $leaveRequest['user_id'], 'leave_type_id' => $leaveRequest['leave_type_id']])
               ->set('leave_limit', 'leave_limit - ' . (float)$deduction, FALSE)
               ->update();
        }

        // 5. --- SMTP INITIALIZE ---
        $config = [
            'protocol'   => 'smtp',
            'SMTPHost'   => $settings['smtp_host'], 
            'SMTPUser'   => $settings['smtp_user'], 
            'SMTPPass'   => $settings['smtp_pass'], 
            'SMTPPort'   => (int)$settings['smtp_port'], // Port integer hona chahiye
            'SMTPCrypto' => 'tls',
            'mailType'   => 'html',
            'charset'    => 'utf-8',
            'newline'    => "\r\n",
            'CRLF'       => "\r\n"
        ];
        $email->initialize($config);

        // 6. --- STAFF KO PROFESSIONAL EMAIL ---
        $statusColor = ($status == 'approved') ? '#27ae60' : '#e74c3c';
        $emailMsg = "
        <div style='font-family: sans-serif; max-width: 600px; border: 1px solid #eee; border-radius: 10px; overflow: hidden;'>
            <div style='background-color: $statusColor; padding: 20px; color: #fff; text-align: center;'>
                <h2 style='margin: 0;'>Leave Request " . strtoupper($status) . "</h2>
            </div>
            <div style='padding: 20px;'>
                <p>Hello <b>{$leaveRequest['staff_name']}</b>,</p>
                <p>Your leave request has been reviewed by the administrator.</p>
                <table style='width: 100%;'>
                    <tr><td><b>Status:</b></td><td style='color:$statusColor;'><b>" . strtoupper($status) . "</b></td></tr>
                    <tr><td><b>Dates:</b></td><td>{$leaveRequest['from_date']} to {$leaveRequest['to_date']}</td></tr>
                    <tr><td><b>Remark:</b></td><td>" . ($remark ?: 'No remarks provided.') . "</td></tr>
                </table>
                <br>
                <p>Regards,<br><b>{$settings['from_name']}</b></p>
            </div>
        </div>";

        $email->setFrom($settings['from_email'], $settings['from_name']);
        $email->setTo($leaveRequest['staff_email']);
        $email->setSubject('Leave Update: ' . strtoupper($status));
        $email->setMessage($emailMsg);
        
        if (!$email->send()) {
            log_message('error', 'Mail Error: ' . $email->printDebugger());
        }

        // 7. --- HR KO NOTIFICATION (Agar settings mein hr_email hai) ---
        if (!empty($settings['hr_email'])) {
            $email->clear(true);
            $email->setFrom($settings['from_email'], 'HostMaster Notification');
            $email->setTo($settings['hr_email']);
            $email->setSubject('Leave Processed: ' . $leaveRequest['staff_name']);
            $email->setMessage("HR Team,<br><br>Staff <b>{$leaveRequest['staff_name']}</b> ki leave request Admin ne <b>$status</b> kar di hai.");
            $email->send();
        }

        // 8. In-App Notification (Dashboard ke liye)
        $notifData = [
            'user_id'       => $leaveRequest['user_id'], 
            'message'       => "Your leave for " . date('d M', strtotime($leaveRequest['from_date'])) . " is " . strtoupper($status),
            'is_read'       => 0,
            'is_admin_only' => 0,
            'type'          => 'leave_response',
            'created_at'    => date('Y-m-d H:i:s')
        ];
        $db->table('notifications')->insert($notifData);

        return redirect()->back()->with('success', 'Status updated and Mails sent!');
    }

    return redirect()->back()->with('error', 'Request not found.');
}
    // 5. STAFF LEAVE SUMMARY & BALANCE
   public function leaveDetails($id) {
    $db = \Config\Database::connect();
    
    // 1. Staff ka basic data fetch karna
    $data['user'] = $db->table('admins')->where('id', $id)->get()->getRowArray();

    // 2. Full Leave History List (Wahi records jo Admin ne hide nahi kiye)
    $data['all_leaves'] = $db->table('leave_requests')
        ->select('leave_requests.*, leave_types.leave_name')
        ->join('leave_types', 'leave_types.id = leave_requests.leave_type_id')
        ->where(['user_id' => $id, 'is_deleted_by_admin' => 0]) 
        ->orderBy('from_date', 'DESC')
        ->get()->getResultArray();

    // 3. UPDATED: Card Summary (Ab sirf non-deleted records count honge)
    $data['summary'] = [
        'approved' => $db->table('leave_requests')
                        ->where(['user_id' => $id, 'status' => 'approved', 'is_deleted_by_admin' => 0])
                        ->countAllResults(),
        'rejected' => $db->table('leave_requests')
                        ->where(['user_id' => $id, 'status' => 'rejected', 'is_deleted_by_admin' => 0])
                        ->countAllResults(),
        'pending'  => $db->table('leave_requests')
                        ->where(['user_id' => $id, 'status' => 'pending', 'is_deleted_by_admin' => 0])
                        ->countAllResults(),
    ];

    $year = date('Y');
    $allocations = $db->table('staff_leave_allocation sla')
        ->select('sla.leave_limit, lt.leave_name, lt.id as type_id')
        ->join('leave_types lt', 'lt.id = sla.leave_type_id')
        ->where(['sla.user_id' => $id, 'sla.year' => $year])
        ->get()->getResultArray();

    $balanceData = [];
    foreach ($allocations as $alc) {
        // 4. UPDATED: Balance calculation mein bhi check lagaya hai
        // Taki deleted/hidden records used leaves mein count na ho
        $used = $db->table('leave_requests')
            ->select('SUM(CASE 
                        WHEN leave_duration = "half_day" THEN 0.5 
                        ELSE (DATEDIFF(to_date, from_date) + 1) 
                      END) as total_used')
            ->where([
                'user_id' => $id, 
                'leave_type_id' => $alc['type_id'], 
                'status' => 'approved',
                'is_deleted_by_admin' => 0 // Sirf visible leaves ka balance calculate karein
            ])
            ->get()->getRow();

        $usedCount = floatval($used->total_used ?? 0);
        $totalLimit = floatval($alc['leave_limit']);
        
        $balanceData[] = [
            'name'      => $alc['leave_name'],
            'total'     => $totalLimit,
            'used'      => $usedCount,
            'remaining' => max(0, $totalLimit - $usedCount) 
        ];
    }

    $data['balances'] = $balanceData;
    $data['title'] = "Staff Leave Summary";
    
    $viewData = isset($this->data) ? array_merge($this->data, $data) : $data;
    return view('admin/leaves/leave_details_view', $viewData);
}
    // 6. ALLOCATE LEAVES
    public function allocateLeaves() {
        $db = \Config\Database::connect();
        $data['users'] = $db->table('admins')->where('role', 'staff')->get()->getResultArray();
        $data['leave_types'] = $db->table('leave_types')->get()->getResultArray();
        
        $data['allocations'] = $db->table('staff_leave_allocation sla')
            ->select('sla.*, a.name as username, lt.leave_name')
            ->join('admins a', 'a.id = sla.user_id')
            ->join('leave_types lt', 'lt.id = sla.leave_type_id')
            ->orderBy('a.name', 'ASC')
            ->get()->getResultArray();

        $viewData = isset($this->data) ? array_merge($this->data, $data) : $data;
        return view('admin/leaves/allocate_leaves', $viewData);
    }
public function storeAllocation() {
    $db = \Config\Database::connect();
    
    // --- LOGIC 1: Calendar Holidays ---
    $holidayName = $this->request->getPost('holiday_name');
    if ($holidayName) {
        $holidayData = [
            'holiday_name' => $holidayName,
            'holiday_date' => $this->request->getPost('holiday_date'),
            'remarks'      => $this->request->getPost('remarks')
        ];
        $db->table('holidays')->insert($holidayData);
        // ✅ LOG ADD
        $this->log_activity("Holiday added: $holidayName on " . $this->request->getPost('holiday_date'), "Holiday Create");
        return redirect()->back()->with('status', 'Holiday marked on Calendar!');
    }

    // --- LOGIC 2: Automated & Fixed Leave Allocation ---
    $userId = $this->request->getPost('user_id');
    $manualLimits = $this->request->getPost('limits');
    $year = date('Y');
    $currentMonth = (int)date('m');

    if ($userId) {
        $staff = $db->table('admins')->where('id', $userId)->get()->getRowArray();
        $staffName = $staff['name'] ?? 'Staff';

        // ❌ EARNED LEAVE KE LIYE joining date calculation hatai
        // Ab sirf short/half ke liye use hogi - isliye ye lines hata di
        // $joiningDate = new \DateTime($staff['joining_date']);
        // $today = new \DateTime();
        // $diff = $today->diff($joiningDate);
        // $totalMonths = ($diff->y * 12) + $diff->m;

        $leaveTypes = $db->table('leave_types')->get()->getResultArray();

        foreach ($leaveTypes as $lt) {
            $leaveName = strtolower($lt['leave_name']);
            $leaveTypeId = $lt['id'];
            $finalLimit = 0;

            // --- CATEGORY A: Monthly Auto-Calculated Leaves ---
            // ❌ EARNED LEAVE WALA BLOCK HATA DIYA
            // Ab earned leave bhi manually set hogi admin se (neeche Category B mein jayegi)
            
            if (str_contains($leaveName, 'short') || str_contains($leaveName, 'half')) {
                // Monthly logic: Fixed 1 leave per month - SAME RAKHA
                $finalLimit = 1;
            } 
            
            // --- CATEGORY B: Yearly Manual Leaves (Sick, Casual, Earned, etc.) ---
            else {
                // Agar list mein manual value di gayi hai toh wo uthao, warna purani hi rehne do
                if (isset($manualLimits[$leaveTypeId]) && $manualLimits[$leaveTypeId] !== 'AUTO') {
                    $finalLimit = (int)$manualLimits[$leaveTypeId];
                } else {
                    // Purani value fetch karein agar naya data nahi hai
                    $existingEntry = $db->table('staff_leave_allocation')
                                        ->where(['user_id' => $userId, 'leave_type_id' => $leaveTypeId])
                                        ->get()->getRow();
                    $finalLimit = $existingEntry ? $existingEntry->leave_limit : 0;
                }
            }

            // Database Save or Update
            $saveData = [
                'user_id'            => $userId,
                'leave_type_id'      => $leaveTypeId,
                'leave_limit'        => $finalLimit,
                'year'               => $year,
                'last_updated_month' => $currentMonth, 
                'updated_at'         => date('Y-m-d H:i:s')
            ];

            $existing = $db->table('staff_leave_allocation')
                           ->where(['user_id' => $userId, 'leave_type_id' => $leaveTypeId, 'year' => $year])
                           ->get()->getRow();

            if ($existing) {
                $db->table('staff_leave_allocation')->where('id', $existing->id)->update($saveData);
            } else {
                $db->table('staff_leave_allocation')->insert($saveData);
            }
        }

        // Employment Type Sync - SAME RAKHA
        // Ab sirf months calculate karenge employment type ke liye
        $joiningDate = new \DateTime($staff['joining_date']);
        $today = new \DateTime();
        $diff = $today->diff($joiningDate);
        $totalMonths = ($diff->y * 12) + $diff->m;
        
        if ($totalMonths >= 6 && $staff['employment_type'] === 'Fresher') {
            $db->table('admins')->where('id', $userId)->update(['employment_type' => 'Experienced']);
        }

        // ✅ LOG ADD
        $this->log_activity("Leave allocated to: $staffName (Year: $year)", "Leave Allocation");

        return redirect()->back()->with('status', 'Allocation updated: Monthly leaves synced & Manual leaves saved!');
    }

    return redirect()->back()->with('error', 'Staff select karna zaroori hai.');
}

    // 7. STAFF INSIGHTS (AJAX)
    public function getStaffInsight($userId) {
        $db = \Config\Database::connect();
        $month = $this->request->getGet('month') ? intval($this->request->getGet('month')) : intval(date('m'));
        $year = $this->request->getGet('year') ? intval($this->request->getGet('year')) : intval(date('Y'));

        $user = $db->table('admins')->where('id', $userId)->get()->getRowArray();
        if (!$user) return $this->response->setJSON(['error' => 'Not found'])->setStatusCode(404);

        $summary = $db->table('leave_requests')
            ->select("COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved_count, COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count, COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_count")
            ->where('user_id', $userId)->where("MONTH(from_date) =", $month)->where("YEAR(from_date) =", $year)
            ->get()->getRowArray();

        $leaves = $db->table('leave_requests lr')
            ->select('lr.*, lt.leave_name')
            ->join('leave_types lt', 'lt.id = lr.leave_type_id')
            ->where('lr.user_id', $userId)->where("MONTH(lr.from_date) =", $month)->where("YEAR(lr.from_date) =", $year)
            ->orderBy('lr.from_date', 'DESC')->get()->getResultArray();

        return $this->response->setJSON([
            'username' => $user['name'],
            'summary'  => $summary,
            'leaves'   => $leaves
        ]);
    }

    // 8. HIDE LEAVE
    public function deleteLeave($id) {
    $db = \Config\Database::connect();
    $builder = $db->table('leave_requests');

    // Query execute karke check kar rahe hain
    $result = $builder->where('id', $id)->update(['is_deleted_by_admin' => 1]);

    if ($result) {
        return $this->response->setJSON([
            'status'  => 'success', 
            'message' => 'Record successfully hidden from Admin.'
        ]);
    } else {
        // Agar query fail hui toh 400 error bhejenge taaki Ajax ko error mile
        return $this->response->setJSON([
            'status'  => 'error', 
            'message' => 'Database update failed.'
        ], 400); 
    }
}
    // 9. FULL LEAVE HISTORY VIEW
    public function leaveHistory() {
        $db = \Config\Database::connect();
        $data['history'] = $db->table('leave_requests lr')
            ->select('lr.*, a.name as staff_name, lt.leave_name')
            ->join('admins a', 'a.id = lr.user_id')
            ->join('leave_types lt', 'lt.id = lr.leave_type_id')
            ->whereIn('lr.status', ['approved', 'rejected'])
            ->orderBy('lr.id', 'DESC')
            ->get()->getResultArray();

        $viewData = isset($this->data) ? array_merge($this->data, $data) : $data;
        return view('admin/leaves/leave_history', $viewData);
    }
    // FIX FOR: Controller method is not found: "toggleUser"
    public function toggleUser($id, $current_status) {
        $db = \Config\Database::connect();
        
        // Status change logic: 1 ko 0 aur 0 ko 1 banana
        $newStatus = ($current_status == 1) ? 0 : 1;
        
        // Database update (is_active column ab aapne add kar diya hai)
        $db->table('admins')
           ->where('id', $id)
           ->update(['is_active' => $newStatus]);
        
        return redirect()->back()->with('success', 'Staff status updated successfully!');
    }
 public function storeUser() {
    $db = \Config\Database::connect();
    
    // Form se basic data lein
    $name     = $this->request->getPost('full_name'); 
    $username = $this->request->getPost('username');
    $email    = $this->request->getPost('email');
    $password = $this->request->getPost('password');
    
    // --- NAYE FIELDS (Employee ID & Type) ---
    $employeeId     = $this->request->getPost('employee_id');
    $employmentType = $this->request->getPost('employment_type') ?? 'Fresher';
    
    // Joining Date logic
    $joiningDate = $this->request->getPost('joining_date');
    if (empty($joiningDate)) {
        $joiningDate = date('Y-m-d');
    }

    // Basic Validation
    if (empty($name) || empty($username) || empty($password)) {
        return redirect()->back()->with('error', 'Form data missing! Please fill name, username and password.');
    }

    // Database ke liye array taiyar karein
    $saveData = [
        'name'            => $name,
        'username'        => $username,
        'email'           => $email,
        'password'        => password_hash((string)$password, PASSWORD_DEFAULT),
        'role'            => 'staff',
        'employee_id'     => $employeeId,      // Naya field save ho raha hai
        'employment_type' => $employmentType,  // Fresher ya Experienced save ho raha hai
        'joining_date'    => $joiningDate,
        'is_active'       => 1,
        'status'          => 1,
        'created_at'      => date('Y-m-d H:i:s')
    ];

    // Data Insert karein
    if ($db->table('admins')->insert($saveData)) {
        return redirect()->to(base_url('admin/leaves/manage-staff'))
                         ->with('success', 'Staff Member "' . $name . '" added successfully as ' . $employmentType . '!');
    } else {
        return redirect()->back()->with('error', 'Database Error: Could not save staff member.');
    }
}
// --- NAYA FUNCTION: Specific Allocation Delete karne ke liye ---
   public function deleteAllocation($id)
{
    $db = \Config\Database::connect();
    
    // Log ke liye pehle info fetch karo delete se pehle
    $exists = $db->table('staff_leave_allocation sla')
        ->select('sla.*, a.name as username, lt.leave_name')
        ->join('admins a', 'a.id = sla.user_id', 'left')
        ->join('leave_types lt', 'lt.id = sla.leave_type_id', 'left')
        ->where('sla.id', $id)
        ->get()->getRowArray();
    
    if ($exists) {
        if ($db->table('staff_leave_allocation')->where('id', $id)->delete()) {
            
            // ✅ LOG ADD
            $uname = $exists['username'] ?? 'Staff';
            $lname = $exists['leave_name'] ?? 'Leave';
            $this->log_activity("Allocation deleted: $uname → $lname", "Leave Delete");
            
            return $this->response->setJSON(['status' => 'success', 'message' => 'Allocation delete ho gayi hai.']);
        }
    }

    return $this->response->setJSON(['status' => 'error', 'message' => 'Delete karne mein error aaya.']);
}
    // --- NAYA FUNCTION: Single Allocation Update karne ke liye (Modal Se) ---
   public function updateAllocationSingle()
{
    $db = \Config\Database::connect();
    
    $id = $this->request->getPost('id');
    $limit = $this->request->getPost('leave_limit');

    if (!$id) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'ID missing hai.']);
    }

    $updateData = [
        'leave_limit' => $limit,
        'updated_at'  => date('Y-m-d H:i:s')
    ];

    if ($db->table('staff_leave_allocation')->where('id', $id)->update($updateData)) {
        
        // ✅ LOG - update ke baad info fetch karo
        $alloc = $db->table('staff_leave_allocation sla')
            ->select('sla.*, a.name as username, lt.leave_name')
            ->join('admins a', 'a.id = sla.user_id', 'left')
            ->join('leave_types lt', 'lt.id = sla.leave_type_id', 'left')
            ->where('sla.id', $id)
            ->get()->getRowArray();

        $uname = $alloc['username'] ?? 'Staff';
        $lname = $alloc['leave_name'] ?? 'Leave';
        $this->log_activity("Quota updated: $uname → $lname = $limit days", "Leave Edit");

        return $this->response->setJSON(['status' => 'success', 'message' => 'Leave limit update ho gayi hai.']);
    }

    return $this->response->setJSON(['status' => 'error', 'message' => 'Update fail ho gaya.']);
}
}