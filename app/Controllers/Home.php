<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\NotificationModel;

class Home extends BaseController
{
    /**
     * Dashboard Main View
     */
public function index()
{
    $db = \Config\Database::connect();
    $session = session();
    
    // --- [INTACT] Date calculation logic ---
    $todayObj = new \DateTime('today'); 
    $today    = $todayObj->format('Y-m-d');
    
    $expiry30Obj = clone $todayObj;
    $expiry30 = $expiry30Obj->modify('+30 days')->format('Y-m-d');
    
    $expiry5DaysObj = clone $todayObj;
    $expiry5Days = $expiry5DaysObj->modify('+5 days')->format('Y-m-d');

    // --- [INTACT] Auth Session Data ---
    $rawRole = $session->get('user_role'); 
    $userRole = !empty($rawRole) ? strtolower(trim((string)$rawRole)) : 'staff';
    $userId = $session->get('admin_id');
    
    $currentUserName = $session->get('name') ?: ($session->get('user_name') ?: 'Me');

    // 1. Total Active Clients Count
    $clientModel = new \App\Models\ClientModel();
    $data['all_clients'] = $clientModel->findAll();

    // 2. Total Orders Count for Card
    $data['total_orders_count'] = $db->table('orders')->countAllResults();

    // 3. 30 Days Expiry Logic
    $builder = $db->table('orders')
        ->select('orders.*, clients.client_name, clients.email_1, clients.phone, order_types.type_name')
        ->join('clients', 'clients.id = orders.client_id')
        ->join('order_types', 'order_types.id = orders.order_type_id', 'left');

    if ($db->fieldExists('provider_id', 'orders')) {
        $builder->select('service_providers.provider_name');
        $builder->join('service_providers', 'service_providers.id = orders.provider_id', 'left');
    }

    $data['expiring_orders'] = $builder->groupStart()
            ->groupStart()
                ->where('domain_expiry_date >=', $today)->where('domain_expiry_date <=', $expiry30)
            ->groupEnd()
            ->orGroupStart()
                ->where('hosting_expiry_date >=', $today)->where('hosting_expiry_date <=', $expiry30)
            ->groupEnd()
        ->groupEnd()
        ->orderBy('domain_expiry_date', 'ASC')->get()->getResultArray();

    // 4. Critical Expiry Count (5 Days)
    $data['critical_expiry_count'] = $db->table('orders')
        ->groupStart()
            ->groupStart()
                ->where('domain_expiry_date >=', $today)->where('domain_expiry_date <=', $expiry30) 
            ->groupEnd()
            ->orGroupStart()
                ->where('hosting_expiry_date >=', $today)->where('hosting_expiry_date <=', $expiry30)
            ->groupEnd()
        ->groupEnd()->countAllResults();

    // 5. Backup Overdue Logic
    try {
        $data['backup_alerts'] = $db->query("
            SELECT b.*, c.client_name, c.email_1 as email FROM backups b 
            JOIN clients c ON b.client_id = c.id
            WHERE DATE_ADD(b.last_backup_date, INTERVAL b.backup_interval MONTH) <= ?
        ", [$today])->getResultArray();
    } catch (\Exception $e) { $data['backup_alerts'] = []; }

    // --- [LOGIC] Staff Activity, Daily Reports & Leave Requests (Role Based) ---
    $reportBuilder = $db->table('daily_reports')
        ->select('daily_reports.*, IFNULL(admins.name, "Deleted Staff") as staff_name')
        ->join('admins', 'admins.id = daily_reports.staff_id', 'left');

    if ($userRole === 'admin') {
        $data['staff_activities'] = $db->table('activity_logs')
            ->select('activity_logs.*, IFNULL(admins.name, activity_logs.user_name) as staff_name')
            ->join('admins', 'admins.id = activity_logs.user_id', 'left')
            ->orderBy('created_at', 'DESC')->limit(10)->get()->getResultArray();

        $data['daily_reports'] = $reportBuilder->orderBy('created_at', 'DESC')->limit(5)->get()->getResultArray(); 
        
        // [FIXED] Admin sees all leave requests with leave_name
        $data['recent_leaves'] = $db->table('leave_requests')
            ->select('leave_requests.*, admins.name as staff_name, leave_types.leave_name as leave_type')
            ->join('admins', 'admins.id = leave_requests.user_id', 'left')
            ->join('leave_types', 'leave_types.id = leave_requests.leave_type_id', 'left')
            ->orderBy('leave_requests.id', 'DESC')
            ->limit(5)->get()->getResultArray();

    } else {
        $data['staff_activities'] = $db->table('activity_logs')
            ->select('activity_logs.*, user_name as staff_name') 
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')->limit(5)->get()->getResultArray();

        $data['daily_reports'] = $reportBuilder->where('staff_id', $userId)
            ->orderBy('created_at', 'DESC')->limit(5)->get()->getResultArray();
            
        // [FIXED] Staff sees only their own leaves with leave_name
        $data['recent_leaves'] = $db->table('leave_requests')
            ->select('leave_requests.*, admins.name as staff_name, leave_types.leave_name as leave_type')
            ->join('admins', 'admins.id = leave_requests.user_id', 'left')
            ->join('leave_types', 'leave_types.id = leave_requests.leave_type_id', 'left')
            ->where('leave_requests.user_id', $userId)
            ->orderBy('leave_requests.id', 'DESC')
            ->limit(5)->get()->getResultArray();
    }

    // --- 6. DASHBOARD NOTIFICATION LOGIC ---
    $notifBuilder = $db->table('notifications')
                       ->select('notifications.*, IFNULL(admins.name, "System") as staff_name')
                       ->join('admins', 'admins.id = notifications.user_id', 'left');

    if ($userRole === 'admin') {
        $notifBuilder->where('is_admin_only >=', 0);
    } else {
        $notifBuilder->where('is_admin_only', 0)
                     ->groupStart()
                        ->where('type', 'expiry')
                        ->orWhere('type', 'expiry_alert')
                     ->groupEnd();
    }

    $countClone = clone $notifBuilder;
    $data['unread_count'] = $countClone->where('is_read', 0)->countAllResults();
    $data['notifications'] = $notifBuilder->orderBy('created_at', 'DESC')->limit(15)->get()->getResultArray();

    // --- View Preparation ---
    $data['title'] = "Command Center";
    $data['user_role'] = $userRole;
    $data['user_name'] = $currentUserName;

    $viewData = array_merge(($this->data ?? []), $data);
    $viewData['user_role'] = $userRole; 
    $viewData['role'] = $userRole;
    $viewData['unread_count'] = $data['unread_count']; 
    $viewData['total_orders'] = $data['total_orders_count'];

    return view('dashboard', $viewData);
}  public function activity_logs()
    {
        $db = \Config\Database::connect();
        $session = session();
        
        $data['title'] = "System Activity Logs";
        $data['unread_count'] = $db->table('notifications')->where('is_read', 0)->countAllResults();
        $data['notifications'] = $db->table('notifications')->orderBy('created_at', 'DESC')->limit(5)->get()->getResultArray();

        $data['logs'] = $db->table('activity_logs')
            ->select('activity_logs.*, IFNULL(admins.name, activity_logs.user_name) as staff_name')
            ->join('admins', 'admins.id = activity_logs.user_id', 'left')
            ->orderBy('created_at', 'DESC')
            ->get()->getResultArray();

        $data['user_role'] = $session->get('user_role');

        return view('admin/activity_logs', $data);
    }

    public function submitReport()
    {
        $db = \Config\Database::connect();
        $reportText = $this->request->getPost('report_text');
        $staffId = session()->get('admin_id');
        $staffName = session()->get('name') ?? 'Staff Member';

        if ($reportText) {
            $db->table('daily_reports')->insert([
                'staff_id'    => $staffId,
                'report_text' => $reportText,
                'created_at'  => date('Y-m-d H:i:s')
            ]);

            if (function_exists('add_notification')) {
                add_notification("Daily Report", "$staffName has submitted a daily work report.", "staff_alert", 1, $staffId);
            }

            log_activity("Submitted a daily work report.", "staff_alert", 1);
            return redirect()->to('dashboard')->with('status', 'Report submitted successfully!');
        }
        return redirect()->to('dashboard')->with('error', 'Report content cannot be empty!');
    }

    public function updateReport($id)
    {
        $db = \Config\Database::connect();
        $reportText = $this->request->getPost('report_text');
        
        $check = $db->table('daily_reports')->where('id', $id)->get()->getRowArray();
        if (session()->get('user_role') !== 'admin' && $check['staff_id'] != session()->get('admin_id')) {
            return redirect()->back()->with('error', 'You can only edit your own reports!');
        }

        if ($reportText) {
            $db->table('daily_reports')->where('id', $id)->update(['report_text' => $reportText]);
            log_activity("Updated a work report (ID: $id).", "staff_alert", 1);
            return redirect()->back()->with('success', 'Report updated successfully.');
        }
        return redirect()->back()->with('error', 'Content cannot be empty.');
    }

    public function deleteReport($id)
    {
        if (session()->get('user_role') !== 'admin') {
            return redirect()->back()->with('error', 'Only Admin can delete reports!');
        }

        $db = \Config\Database::connect();
        $db->table('daily_reports')->where('id', $id)->delete();
        
        log_activity("Deleted a daily work report (ID: $id).", "admin_alert", 1);
        return redirect()->back()->with('success', 'Report deleted successfully.');
    }

    public function sendNotice()
    {
        $db = \Config\Database::connect();
        $smtp = $db->table('smtp_settings')->where('id', 1)->get()->getRowArray();
        if (!$smtp) return redirect()->back()->with('error', 'SMTP settings not found!');

        $email_to = $this->request->getPost('email');
        $message  = $this->request->getPost('message');
        $domain   = $this->request->getPost('domain_name');

        $email = \Config\Services::email();
        $config = [
            'protocol' => 'smtp',
            'SMTPHost' => trim($smtp['smtp_host']),
            'SMTPUser' => trim($smtp['smtp_user']),
            'SMTPPass' => trim($smtp['smtp_pass']), 
            'SMTPPort' => (int)$smtp['smtp_port'],
            'SMTPCrypto' => 'tls',
            'mailType' => 'html',
            'newline' => "\r\n",
        ];

        $email->initialize($config);
        $email->setFrom($smtp['from_email'], $smtp['from_name']);
        $email->setTo($email_to);
        $email->setSubject("Renewal Alert: " . ($domain ?: 'Service Update'));
        $email->setMessage($message);

        if ($email->send()) {
            log_activity("Sent renewal notice to: $email_to", "staff_alert", 1);
            return redirect()->to('dashboard')->with('status', "Notice sent!");
        }
        return redirect()->to('dashboard')->with('error', 'Email delivery failed.');
    }

    public function markBackupDone($id)
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');
        if ($db->table('backups')->where('id', $id)->update(['last_backup_date' => $today])) {
            log_activity("Marked backup (ID: $id) as completed.", "staff_alert", 1);
            return redirect()->to('dashboard')->with('status', 'Backup updated!');
        }
        return redirect()->to('dashboard')->with('error', 'Update failed.');
    }

    public function notificationGo($id) {
        \Config\Database::connect()->table('notifications')->where('id', $id)->update(['is_read' => 1]);
        return redirect()->to('dashboard');
    }

    public function markAllRead() {
        (new NotificationModel())->where('is_read', 0)->set(['is_read' => 1])->update();
        return redirect()->to('dashboard')->with('status', 'All read!');
    }

    public function allReports() {// Testing my new macro command
        $db = \Config\Database::connect();
        $data['daily_reports'] = $db->table('daily_reports')
            ->select('daily_reports.*, admins.name as staff_name') 
            ->join('admins', 'admins.id = daily_reports.staff_id', 'left')
            ->orderBy('daily_reports.created_at', 'DESC')->get()->getResultArray(); 
        $data['user_role'] = session()->get('user_role');
        return view('reports/index', $data);
    }
}// Final sync test checking sftp connection