<?php

namespace App\Models;
use CodeIgniter\Model;

class LeaveModel extends Model {
    protected $table = 'leave_requests';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'leave_type_id', 'from_date', 'to_date', 'reason', 'status', 'admin_remark'];

    // Admin ko summary dikhane ke liye logic
    public function getLeaveStats($user_id) {
        return $this->db->table('leave_requests')
                    ->select('leave_types.leave_name, COUNT(leave_requests.id) as taken')
                    ->join('leave_types', 'leave_types.id = leave_requests.leave_type_id')
                    ->where('user_id', $user_id)
                    ->where('status', 'approved')
                    ->groupBy('leave_type_id')
                    ->get()->getResultArray();
    }
}