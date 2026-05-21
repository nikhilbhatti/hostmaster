<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class LeaveReportController extends BaseController {

    /**
     * Report Page Load
     */
    public function index() {
        $db = \Config\Database::connect();
        
        // Staff list for dropdown
        $data['staff'] = $db->table('admins')
                            ->where('role', 'staff')
                            ->orderBy('name', 'ASC')
                            ->get()
                            ->getResultArray();

        $data['title'] = "Staff Leave Reporting";
        
        // Handling view data safely
        $viewData = isset($this->data) ? array_merge($this->data, $data) : $data;
        
        return view('admin/leaves/leave_reporting', $viewData);
    }

    /**
     * AJAX Endpoint for Monthly Data
     */
    public function getMonthlyData() {
        $db = \Config\Database::connect();
        
        // Use uppercase GET parameters
        $staffId = $this->request->getVar('staff_id');
        $month = $this->request->getVar('month'); // Input format: YYYY-MM

        $builder = $db->table('leave_requests lr')
            ->select('lr.*, lt.leave_name, a.name as staff_name, a.employee_id')
            ->join('leave_types lt', 'lt.id = lr.leave_type_id', 'left') // Left join safety
            ->join('admins a', 'a.id = lr.user_id', 'left');

        // 1. FILTER: Sirf Approved aur Non-deleted records
        $builder->where('lr.status', 'approved');
        $builder->where('lr.is_deleted_by_admin', 0);

        // 2. FILTER: Staff Selection
        if (!empty($staffId)) {
            $builder->where('lr.user_id', $staffId);
        }
        
        // 3. FILTER: Monthly Logic (DATE_FORMAT is more reliable for YYYY-MM input)
        if (!empty($month)) {
            $builder->where("DATE_FORMAT(lr.from_date, '%Y-%m')", $month);
        }

        // Fetch Results
        $results = $builder->orderBy('lr.from_date', 'ASC')->get()->getResultArray();

        // 4. CALCULATION LOGIC: Kyunki DB mein total_days 0 hai
        foreach ($results as &$row) {
            // Half day logic
            if (isset($row['leave_duration']) && $row['leave_duration'] === 'half_day') {
                $row['calculated_days'] = 0.5;
            } else {
                // Full day calculation from date objects
                $from = new \DateTime($row['from_date']);
                $to = new \DateTime($row['to_date']);
                $diff = $from->diff($to);
                $row['calculated_days'] = $diff->days + 1;
            }
        }
        
        // Return JSON with Debug Header
        return $this->response
                    ->setHeader('X-Debug-Count', (string)count($results))
                    ->setJSON($results);
    }
}