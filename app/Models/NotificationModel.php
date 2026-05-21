<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    // is_admin_only aur user_id ko list mein add kar diya hai
    protected $allowedFields    = ['client_id', 'user_id', 'title', 'message', 'type', 'is_read', 'link', 'is_admin_only', 'created_at'];

    // Global count for the dashboard
    public function getUnreadCount($role) {
        $builder = $this->where('is_read', 0);
        if ($role !== 'admin') {
            $builder->where('is_admin_only', 0);
        }
        return $builder->countAllResults();
    }
}