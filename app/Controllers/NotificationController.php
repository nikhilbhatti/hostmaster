<?php
namespace App\Controllers;

use App\Models\NotificationModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class NotificationController extends BaseController
{
    protected $notifModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->notifModel = new NotificationModel();
    }

    /**
     * Saari notifications ki list dikhane ke liye (FIXED)
     */
    public function index()
    {
        $session = session();
        
        // --- FIX 1: Correct Role Key ('user_role' instead of 'role') ---
        $rawRole = $session->get('user_role'); 
        $userRole = !empty($rawRole) ? strtolower(trim((string)$rawRole)) : 'staff';

        // --- FIX 2: Builder Initialization ---
        // Hum Model builder use karenge taaki complex queries handle ho sakein
        $builder = $this->notifModel->builder();

        // Filter: Staff aur Admin ke liye Dashboard se exact match logic
        if ($userRole === 'admin') {
            // Admin can see everything
            $builder->where('is_admin_only >=', 0);
        } else {
            // Staff logic (Only Expiry for dashboard consistency)
            $builder->where('is_admin_only', 0)
                    ->groupStart()
                        ->where('type', 'expiry')
                        ->orWhere('type', 'expiry_alert')
                    ->groupEnd();
        }

        // Saari notifications fetch karein (No Limit for History Page)
        $notifications = $builder->orderBy('created_at', 'DESC')->get()->getResultArray();

        $data['all_notifs'] = $notifications;
        $data['title'] = "Notification History";
        $data['user_role'] = $userRole;

        // --- FIX 3: BaseController Data Merge ---
        // Isse header ka count (9) aur list sync ho jayegi
        $viewData = array_merge(($this->data ?? []), $data);

        return view('notifications/index', $viewData); 
    }

    /**
     * Notification par click karne par ye link par bhejega
     */
    public function go($id = null)
    {
        if (!$id) {
            return redirect()->to(base_url('dashboard'));
        }

        $notification = $this->notifModel->find($id);

        if ($notification) {
            // Read mark karo
            $this->notifModel->update($id, [
                'is_read' => 1,
                // read_at column check kar lena database mein hai ya nahi
                'updated_at' => date('Y-m-d H:i:s') 
            ]);

            $link = is_array($notification) ? ($notification['link'] ?? '') : ($notification->link ?? '');

            if (empty($link)) {
                return redirect()->to(base_url('dashboard'));
            }

            // Clean link logic
            $finalUrl = (strpos($link, 'http') === 0) ? $link : base_url(trim($link, '/'));

            return redirect()->to($finalUrl);
        }

        return redirect()->to(base_url('dashboard'));
    }

    /**
     * Single notification ko read mark karna (AJAX Support)
     */
    public function markAsRead($id = null)
    {
        if ($id) {
            $this->notifModel->update($id, ['is_read' => 1]);
        }
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Read marked!']);
        }

        return redirect()->back()->with('status', 'Notification marked as read');
    }

    /**
     * Bulk Mark All Read (FIXED)
     */
    public function markAllRead()
    {
        $session = session();
        $rawRole = $session->get('user_role'); 
        $userRole = !empty($rawRole) ? strtolower(trim((string)$rawRole)) : 'staff';

        $builder = $this->notifModel->where('is_read', 0);
        
        if ($userRole !== 'admin') {
            $builder->where('is_admin_only', 0);
        }

        $builder->set(['is_read' => 1])->update();
        
        return redirect()->back()->with('status', 'All notifications cleared!');
    }
}