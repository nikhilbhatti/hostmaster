<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $db;
    protected $session;
    protected $data = []; 

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        
        $this->db = \Config\Database::connect();
        $this->session = \Config\Services::session();

        // 1. EXPIRY LOGIC (Dashboard Notifications)
        $this->checkDomainExpiries();

        // 2. AUTO EMAIL LOGIC (15 Days Email Reminder)
        // Note: Ye initialization check hai
        $this->sendAutoExpiryEmails();

        // 3. ROLE & NOTIFICATION LOGIC
        $rawRole = $this->session->get('user_role'); 
        $userRole = !empty($rawRole) ? strtolower(trim((string)$rawRole)) : 'staff'; 
        
        // --- BASE NOTIFICATION QUERY (Fixed for TypeError) ---
        $notifBuilder = $this->db->table('notifications')
                            ->select('notifications.id, notifications.title, notifications.message, notifications.is_read, notifications.created_at, notifications.is_admin_only')
                            ->select('IFNULL(notifications.type, "system") as type', false)
                            ->select('IFNULL(notifications.link, "") as link', false)
                            ->select('IFNULL(admins.name, "System") as staff_name', false)
                            ->join('admins', 'admins.id = notifications.user_id', 'left');

        // --- ROLE BASED FILTERING ---
        if ($userRole === 'admin') {
            $notifBuilder->where('notifications.is_admin_only >=', 0);
        } else {
            $notifBuilder->where('notifications.is_admin_only', 0)
                         ->groupStart()
                            ->whereIn('notifications.type', ['expiry', 'expiry_alert', 'backup_alert', 'system', 'staff_alert', 'backup_done'])
                         ->groupEnd();
        }

        // 4. COUNT & LIST FETCHING
        $countClone = clone $notifBuilder;
        $unreadCount = $countClone->where('notifications.is_read', 0)->countAllResults();
        
        $notifications = $notifBuilder->orderBy('notifications.created_at', 'DESC')
                                     ->limit(10)
                                     ->get()
                                     ->getResultArray();

        // 5. GLOBAL DATA RENDERING
        $this->data['notifications'] = $notifications; 
        $this->data['unread_count']  = $unreadCount; 
        $this->data['title']         = 'HostMaster Pro';
        $this->data['user_role']     = $userRole; 
        $this->data['current_role']  = $userRole; 
        $this->data['admin_name']    = $this->session->get('user_name') ?? 'User';

        \Config\Services::renderer()->setData($this->data);
    }

    /**
     * Domain Expiry Check Logic (Database Notifications)
     */
    private function checkDomainExpiries()
    {
        $today = date('Y-m-d');
        $thirtyDaysLater = date('Y-m-d', strtotime('+30 days'));

        $expiryOrders = $this->db->table('orders')
            ->select('orders.id, orders.domain_name, orders.client_id, orders.domain_expiry_date, clients.client_name')
            ->join('clients', 'clients.id = orders.client_id')
            ->where('domain_expiry_date <=', $thirtyDaysLater)
            ->where('domain_expiry_date >=', $today)
            ->get()->getResultArray();

        foreach ($expiryOrders as $order) {
            $link = 'orders/view/' . ($order['id'] ?? 0);
            
            // FIX: ignore(true) ensures duplicate key error is bypassed
            $this->db->table('notifications')->ignore(true)->insert([
                'client_id'     => $order['client_id'] ?? 0,
                'user_id'       => 0, 
                'title'         => 'Domain Expiring',
                'message'       => 'The domain (' . ($order['domain_name'] ?? 'N/A') . ') for client ' . ($order['client_name'] ?? 'N/A') . ' is expiring soon.',
                'type'          => 'expiry_alert',
                'is_read'       => 0,
                'is_admin_only' => 0, 
                'link'          => (string)$link,
                'created_at'    => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * LOG ACTIVITY (Supports success logging for emails)
     */
    protected function log_activity($message, $action_type = null)
    {
        $userId   = $this->session->get('admin_id'); 
        $userName = $this->session->get('user_name') ?: 'System'; 
        $rawRole  = $this->session->get('user_role');
        $userRole = !empty($rawRole) ? strtolower(trim((string)$rawRole)) : 'staff';

        // Log entry into activity_logs
        $this->db->table('activity_logs')->insert([
            'user_id'          => $userId ?: 0,
            'user_name'        => (string)$userName,
            'activity_message' => (string)$message,
            'action'           => (string)($action_type ?? 'Staff Action'),
            'ip_address'       => $this->request->getIPAddress(),
            'created_at'       => date('Y-m-d H:i:s')
        ]);

        if ($userRole === 'staff') {
            // FIX: ignore(true) added to prevent crash on duplicate staff activity
            $this->db->table('notifications')->ignore(true)->insert([
                'user_id'       => $userId, 
                'title'         => 'Staff Activity',
                'message'       => $userName . ': ' . $message,
                'type'          => 'staff_alert', 
                'is_read'       => 0,
                'is_admin_only' => 1, 
                'link'          => 'activity_logs',
                'created_at'    => date('Y-m-d H:i:s')
            ]);
        }
        
        return true;
    }

    /**
     * AUTO EXPIRY MAIL LOGIC - Changed to PROTECTED so child classes can use it
     */
    protected function sendAutoExpiryEmails($forceOrderId = null)
    {
        $today = date('Y-m-d');
        $targetDate = date('Y-m-d', strtotime('+15 days')); 

        // Agar specific order ke liye nahi hai, toh cache check karein
        if (!$forceOrderId) {
            $lastCheck = cache()->get('last_auto_mail_check');
            if ($lastCheck === $today) {
                return; 
            }
        }

        $builder = $this->db->table('orders')
            ->select('orders.*, clients.client_name, clients.email_1')
            ->join('clients', 'clients.id = orders.client_id');

        // Logic sync: Agar manual store se aaya hai toh order ID filter lagayein
        if ($forceOrderId) {
            $builder->where('orders.id', $forceOrderId);
        } else {
            $builder->groupStart()
                ->where('domain_expiry_date', $targetDate)
                ->orWhere('hosting_expiry_date', $targetDate)
            ->groupEnd();
        }

        $expiringOrders = $builder->get()->getResultArray();

        if (!empty($expiringOrders)) {
            $smtp = $this->db->table('smtp_settings')->where('id', 1)->get()->getRowArray();
            
            if ($smtp) {
                $email = \Config\Services::email();
                $config = [
                    'protocol'   => 'smtp',
                    'SMTPHost'   => trim($smtp['smtp_host'] ?? ''),
                    'SMTPUser'   => trim($smtp['smtp_user'] ?? ''),
                    'SMTPPass'   => trim($smtp['smtp_pass'] ?? ''),
                    'SMTPPort'   => (int)($smtp['smtp_port'] ?? 587),
                    'SMTPCrypto' => 'tls',
                    'mailType'   => 'html',
                    'newline'    => "\r\n",
                ];

                foreach ($expiringOrders as $order) {
                    if (empty($order['email_1'])) continue;

                    $email->initialize($config);
                    $email->setFrom($smtp['from_email'] ?? '', $smtp['from_name'] ?? 'System');
                    $email->setTo($order['email_1']);
                    
                    $type = ($order['domain_expiry_date'] == $targetDate || $forceOrderId) ? "Service/Domain" : "Hosting";
                    $email->setSubject("Important: Your $type expires soon");

                    $message = "Hi " . ($order['client_name'] ?? 'Client') . ",<br><br>Your service for <b>" . ($order['domain_name'] ?? 'Service') . "</b> is expiring on " . ($order['domain_expiry_date'] ?? $targetDate) . ". Please renew it soon.";
                    $email->setMessage($message);

                    if ($email->send()) {
                        $this->log_activity("Auto-mail sent to " . ($order['client_name'] ?? 'Client') . " for $type expiry", "system_alert");
                        
                        // FIX: ignore(true) ensures notifications for sent emails don't crash the system
                        $this->db->table('notifications')->ignore(true)->insert([
                            'title'         => 'Auto-Expiry Mail Sent',
                            'message'       => "Reminder sent to " . ($order['client_name'] ?? 'Client') . " for " . ($order['domain_name'] ?? 'Service'),
                            'type'          => 'system',
                            'is_read'       => 0,
                            'is_admin_only' => 1,
                            'link'          => 'activity_logs',
                            'created_at'    => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }
        }

        if (!$forceOrderId) cache()->save('last_auto_mail_check', $today, 86400); 
    }
    protected function sendNotif($target_id, $title, $message, $link, $is_admin = 0) {
    $db = \Config\Database::connect();
    $data = [
        'user_id'       => $target_id,
        'title'         => $title,      // Aapki table mein title column hai
        'message'       => $message,
        'type'          => 'staff',     // Enum type set karein
        'is_read'       => 0,
        'link'          => $link,
        'is_admin_only' => $is_admin,   // Admin ke liye 1,
        'created_at'    => date('Y-m-d H:i:s')
    ];
    return $db->table('notifications')->insert($data);
}
}