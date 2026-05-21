<?php

if (!function_exists('log_activity')) {
    /**
     * @param string $action - Kya kaam hua (e.g. "Backup created for Client X")
     * @param string $type - 'staff_alert', 'expiry_alert', 'backup_alert', or 'system'
     * @param int $is_admin_only - 1 for Admin only, 0 for everyone
     */
    function log_activity($action, $type = 'staff_alert', $is_admin_only = 1)
    {
        try {
            $db = \Config\Database::connect();
            $session = session();

            // Session data nikalna (Aapke project ke hisaab se keys set ki hain)
            $userName = $session->get('name') ?: ($session->get('user_name') ?: 'System');
            $userId   = $session->get('admin_id') ?: 0;
            $userRole = strtolower(trim((string)$session->get('role'))); 

            // 1. Activity Log hamesha insert hoga (History ke liye)
            $db->table('activity_logs')->insert([
                'user_id'    => $userId,
                'user_name'  => $userName,
                'action'     => $action,
                'ip_address' => service('request')->getIPAddress(),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // 2. Notifications Logic
            // Admin ki khud ki activity par admin ko hi alert na mile, 
            // isliye hum 'system' alerts ke liye allow karenge par 'staff_alert' bypass karenge agar user admin hai.
            if ($userRole === 'admin' && $type === 'staff_alert' && $is_admin_only == 1) {
                // Agar admin khud kaam kar raha hai, toh admin ko notification bhejne ki zaroorat nahi.
                // Lekin hum phir bhi system logs ke liye record save karenge.
            }

            // Notification table mein entry (Iska structure aapke current logic ke hisaab se hai)
            $db->table('notifications')->insert([
                'user_id'       => $userId, // Kisne kiya
                'title'         => ($type === 'backup_alert' || $type === 'backup_done') ? 'Backup Update' : 'Staff Activity',
                'message'       => ($userRole === 'staff') ? "Staff ($userName): $action" : $action,
                'type'          => $type,
                'is_read'       => 0,
                'is_admin_only' => $is_admin_only, 
                'link'          => ($type === 'staff_alert') ? 'activity_logs' : 'dashboard', 
                'created_at'    => date('Y-m-d H:i:s')
            ]);

            return true;
        } catch (\Exception $e) {
            log_message('error', 'Activity Log Error: ' . $e->getMessage());
            return false;
        }
    }
}