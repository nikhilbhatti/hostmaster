<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LeaveModel;

class StaffController extends BaseController 
{
    /**
     * 1. LEAVE DASHBOARD LOGIC
     * Iska naam 'index' se badal kar 'leaveDashboard' kar diya hai
     * taaki ye CRM dashboard se mix na ho.
     */
    public function leaveDashboard() 
{
    $db = \Config\Database::connect();
    $userId = session()->get('user_id');

    if (!$userId) {
        return redirect()->to(base_url('login'))->with('error', 'Session expired. Please login.');
    }

    $year = date('Y');

    /**
     * LEAVE BALANCE LOGIC (FULL & SECURE)
     * 1. COALESCE use kiya hai taaki agar allocation na ho toh NULL ki jagah 0 aaye.
     * 2. Used leaves mein Half-Day (0.5) ka accurate logic add kiya hai.
     * 3. Left Join aur Subqueries ko optimize kiya hai taaki koi entry miss na ho.
     */
    $leaveBalances = $db->table('leave_types lt')
        ->select('lt.leave_name, lt.id as type_id')
        
        // Step 1: Allocation (Total Quota) fetch karein
        ->select("COALESCE((SELECT leave_limit FROM staff_leave_allocation 
                  WHERE user_id = $userId AND leave_type_id = lt.id AND year = $year), 0) as total_quota")
        
        // Step 2: Approved Used Leaves fetch karein (Half-day logic ke saath)
        ->select("COALESCE((SELECT SUM(CASE WHEN leave_duration = 'half_day' THEN 0.5 ELSE (DATEDIFF(to_date, from_date) + 1) END) 
                  FROM leave_requests 
                  WHERE user_id = $userId 
                  AND leave_type_id = lt.id 
                  AND status = 'approved' 
                  AND YEAR(from_date) = $year), 0) as used_leaves")
        ->get()->getResultArray();

    /**
     * ZERO-NEGATIVE PROTECTOR:
     * Har leave type ka final calculation yahan kar rahe hain.
     * Agar Quota 0 hai aur Used 5, toh dashboard par -5 nahi balki 0 dikhega.
     */
    foreach ($leaveBalances as &$lb) {
        $quota = floatval($lb['total_quota']);
        $used  = floatval($lb['used_leaves']);
        
        $remaining = $quota - $used;
        
        // Logic: Bacha hua balance kabhi bhi 0 se niche nahi jayega
        $lb['available_balance'] = ($remaining < 0) ? 0 : $remaining;
        
        // Purana variable name maintain rakha hai taaki view file na chhedni pade
        $lb['leave_limit'] = $quota; 
    }

    // Recent 5 Requests fetching logic (No logic cut here)
    $data['recent_leaves'] = $db->table('leave_requests lr')
        ->select('lr.*, lt.leave_name')
        ->join('leave_types lt', 'lt.id = lr.leave_type_id')
        ->where('lr.user_id', $userId)
        ->orderBy('lr.id', 'DESC') 
        ->limit(5)
        ->get()->getResultArray();

    $data['leaveBalances'] = $leaveBalances;
    $data['title'] = "LMS Dashboard";

    return view('admin/staff/dashboard', $data);
}

    // 2. Apply Leave Page
    // app/Controllers/StaffController.php mein is function ko update karein
public function applyLeave() 
{
    $db = \Config\Database::connect();
    
    // 1. Session se User ID lein (Jo login ke waqt set ki gayi thi)
    $userId = session()->get('user_id');

    if (!$userId) {
        return redirect()->to(base_url('login'))->with('error', 'Session expired. Please login.');
    }

    // 2. Database se is user ka latest status check karein
    // Hum 'admins' table se 'is_active' column check kar rahe hain
    $user = $db->table('admins')->where('id', $userId)->get()->getRowArray();

    /**
     * SCREEN LOGIC:
     * Agar user table mein 'is_active' column 0 hai, toh permission denied dikhayega.
     * Isse session refresh ka jhanjhat khatam ho jayega.
     */
    if (!$user || (int)$user['is_active'] !== 1) {
        session()->setFlashdata('error', 'Aapko Leave apply karne ki permission nahi hai. Kripya Admin se sampark karein.');
        return redirect()->to(base_url('staff/dashboard'));
    }

    // 3. Agar status 1 hai (Active), toh aage badhein
    $data['leave_types'] = $db->table('leave_types')->get()->getResultArray();
    $data['holidays']    = $db->table('holidays')->get()->getResultArray(); 
    $data['title']       = "Apply New Leave";
    
    return view('admin/staff/apply_leave', $data);
}
    // 3. Save Leave Logic
public function storeLeave() 
{
    $leaveModel = new \App\Models\LeaveModel();
    $db = \Config\Database::connect();
    $email = \Config\Services::email(); 
    
    $userId = session()->get('user_id');
    $username = session()->get('name') ?? session()->get('username') ?? 'Staff Member';

    $leaveTypeId = $this->request->getPost('leave_type');
    $fromDate    = $this->request->getPost('from_date');
    $toDate      = $this->request->getPost('to_date');
    $duration    = $this->request->getPost('leave_duration') ?? 'full_day';

    if (!$leaveTypeId) {
        return redirect()->back()->with('error', 'Please select a leave type.');
    }

    // 1. Days Calculation logic
    $start = new \DateTime($fromDate);
    $end = new \DateTime($toDate);
    $interval = $start->diff($end);
    $daysApplied = ($duration === 'half_day') ? 0.5 : ($interval->days + 1);

    // 2. Fetch Balance & Validation
    $year = date('Y');
    $allocation = $db->table('staff_leave_allocation')
        ->where(['user_id' => $userId, 'leave_type_id' => $leaveTypeId, 'year' => $year])
        ->get()->getRowArray();

    if (!$allocation) {
        return redirect()->back()->with('error', 'Leave allocation not found for this year.');
    }

    $used = $db->table('leave_requests')
        ->select('SUM(CASE WHEN leave_duration = "half_day" THEN 0.5 ELSE (DATEDIFF(to_date, from_date) + 1) END) as total_used')
        ->where(['user_id' => $userId, 'leave_type_id' => $leaveTypeId, 'status' => 'approved'])
        ->get()->getRow();

    $currentBalance = floatval($allocation['leave_limit']) - floatval($used->total_used ?? 0);

    if ($daysApplied > $currentBalance) {
        return redirect()->back()->with('error', "Insufficient Balance! Left: $currentBalance, Requested: $daysApplied");
    }

    // 3. Save to Database
    $data = [
        'user_id'        => $userId,
        'leave_type_id'  => $leaveTypeId,
        'from_date'      => $fromDate,
        'to_date'        => $toDate,
        'reason'         => $this->request->getPost('reason'),
        'status'         => 'pending',
        'leave_duration' => $duration
    ];

    if ($leaveModel->save($data)) {
        
        $settings = $db->table('smtp_settings')->where('id', 1)->get()->getRowArray();
        
        if ($settings) {
            $config = [
                'protocol'   => 'smtp',
                'SMTPHost'   => trim($settings['smtp_host']),
                'SMTPUser'   => trim($settings['smtp_user']),
                'SMTPPass'   => trim($settings['smtp_pass']),
                'SMTPPort'   => (int)$settings['smtp_port'],
                'SMTPCrypto' => 'tls',
                'mailType'   => 'html',
                'newline'    => "\r\n",
                'CRLF'       => "\r\n"
            ];
            
            $leaveType = $db->table('leave_types')->where('id', $leaveTypeId)->get()->getRowArray();
            $leaveName = $leaveType['leave_name'] ?? 'General Leave';
            
            // --- HOSTMASTER COMMON DETAILS ---
            $commonDetails = "
                <div style='background: #f8fbfd; border: 1px solid #eef2f6; border-radius: 10px; padding: 20px; margin: 20px 0;'>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr><td style='padding: 8px 0; color: #888; font-size: 13px; width: 40%;'>STAFF NAME</td><td style='padding: 8px 0; color: #333; font-weight: 600;'>$username</td></tr>
                        <tr><td style='padding: 8px 0; color: #888; font-size: 13px;'>LEAVE TYPE</td><td style='padding: 8px 0; color: #333; font-weight: 600;'>$leaveName</td></tr>
                        <tr><td style='padding: 8px 0; color: #888; font-size: 13px;'>DURATION</td><td style='padding: 8px 0; color: #333; font-weight: 600;'>$fromDate to $toDate</td></tr>
                        <tr><td style='padding: 8px 0; color: #888; font-size: 13px;'>TOTAL DAYS</td><td style='padding: 8px 0; color: #333; font-weight: 600;'>$daysApplied Day(s)</td></tr>
                        <tr><td style='padding: 8px 0; color: #888; font-size: 13px;'>REASON</td><td style='padding: 8px 0; color: #555; font-style: italic;'>" . ($this->request->getPost('reason') ?: 'No reason provided') . "</td></tr>
                    </table>
                </div>
                <div style='text-align: center; margin-top: 30px;'>
                    <a href='https://slysis.com/master' style='background: #f39c12; color: #ffffff; padding: 14px 30px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px; display: inline-block;'>OPEN HOSTMASTER PORTAL</a>
                </div>";

            $footer = "<div style='background: #fdfdfd; border-top: 1px solid #eee; padding: 20px; text-align: center;'>
                        <p style='margin: 0; font-size: 12px; color: #999;'>This is an automated message from <b>Hostmaster CRM</b>.</p>
                       </div></div>";

            // --- 1. ADMIN MAIL (Hostmaster Blue) ---
            $adminMessage = "
            <div style='font-family: \"Segoe UI\", Arial, sans-serif; max-width: 600px; margin: 20px auto; border: 1px solid #eee; border-radius: 12px; overflow: hidden;'>
                <div style='background: #1a2a6c; color: #ffffff; padding: 30px 20px; text-align: center;'>
                    <h1 style='margin: 0; font-size: 22px; letter-spacing: 1px;'>HOSTMASTER ADMIN</h1>
                </div>
                <div style='padding: 30px; background: #ffffff;'>
                    <p style='font-size: 16px; color: #333;'><b>Hello Admin,</b></p>
                    <p style='font-size: 14px; color: #666;'>A new staff leave application requires your review on Hostmaster.</p>
                    $commonDetails
                </div>
            $footer";

            // --- 2. HR MAIL (Hostmaster Grey) ---
            $hrMessage = "
            <div style='font-family: \"Segoe UI\", Arial, sans-serif; max-width: 600px; margin: 20px auto; border: 1px solid #eee; border-radius: 12px; overflow: hidden;'>
                <div style='background: #2c3e50; color: #ffffff; padding: 30px 20px; text-align: center;'>
                    <h1 style='margin: 0; font-size: 22px; letter-spacing: 1px;'>HOSTMASTER HR</h1>
                </div>
                <div style='padding: 30px; background: #ffffff;'>
                    <p style='font-size: 16px; color: #333;'><b>Hello HR Team,</b></p>
                    <p style='font-size: 14px; color: #666;'>New staff leave record has been updated for Hostmaster maintenance.</p>
                    $commonDetails
                </div>
            $footer";

            // SEND ADMIN
            $email->initialize($config);
            $email->setFrom($settings['from_email'], 'Hostmaster Admin');
            $email->setTo($settings['from_email']); 
            $email->setSubject('Hostmaster: New Leave Request - ' . $username);
            $email->setMessage($adminMessage);
            $email->send();

            // SEND HR
            if (!empty($settings['hr_email'])) {
                $email->clear(true);
                $email->initialize($config);
                $email->setFrom($settings['from_email'], 'Hostmaster HR');
                $email->setTo(trim($settings['hr_email'])); 
                $email->setSubject('Hostmaster HR: New Request - ' . $username);
                $email->setMessage($hrMessage);
                $email->send();
            }
        }

        // Notification table with Hostmaster Branding
        $db->table('notifications')->insert([
            'user_id' => 1, 
            'message' => "Hostmaster: $username applied for $leaveName ($daysApplied days)", 
            'is_read' => 0, 
            'type' => 'leave_request', 
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('staff/leave-dashboard'))->with('success', 'Leave applied! Hostmaster Admin & HR notified.');
    }
    
    return redirect()->back()->with('error', 'Hostmaster Error: Could not process request.');
}
    // 4. History Page
    public function leaveHistory() 
    {
        $db = \Config\Database::connect();
        $userId = session()->get('user_id');
        $data['title'] = "My Leave History";

        $data['my_leaves'] = $db->table('leave_requests lr')
            ->select('lr.*, lt.leave_name')
            ->join('leave_types lt', 'lt.id = lr.leave_type_id')
            ->where('lr.user_id', $userId)
            ->orderBy('lr.id', 'DESC')
            ->get()->getResultArray();

        return view('admin/staff/leave_history', $data);
    }

    // 5. Holiday JSON for Calendar
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
    public function add_report()
{
    // Agar aapko projects ki list database se lani hai toh yahan fetch karein
    // $data['projects'] = $this->projectModel->findAll();
    
   return view('admin/staff/add_report');// Check karein file ka path sahi ho
}
    // Report submit karne ka function
public function save_report()
{
    // 1. Validation: Sirf 'description' check karein kyunki wahi 'report_text' mein jayega
    $rules = [
        'description' => 'required|min_length[5]'
    ];

    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()->with('error', 'Report mein kam se kam 5 characters likhna zaroori hai.');
    }

    // 2. Attachment Handling (Optional): 
    // Agar aapke DB mein 'attachment' column nahi hai, toh ise use mat karein.
    // Filhal maine ise hata diya hai taaki error na aaye.

    // 3. Data Prepare Karna (Aapke DB Columns ke hisab se)
    $data = [
        'staff_id'    => session()->get('user_id'), // User Session ID
        'report_text' => $this->request->getPost('description'), // View ka textarea
        'created_at'  => date('Y-m-d H:i:s')
    ];

    // 4. Model Load karke Insert karna
    $reportModel = new \App\Models\ReportModel(); 
    
    if ($reportModel->insert($data)) {
        return redirect()->to('staff/dashboard')->with('success', 'Daily report successfully submit ho gayi!');
    } else {
        return redirect()->back()->withInput()->with('error', 'Kuch error aaya, please firse try karein.');
    }
}
}