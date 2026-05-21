<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class HolidayController extends BaseController {
    
    public function saveHoliday() {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Direct access not allowed']);
        }

        $db = \Config\Database::connect();
        
        $h_date = $this->request->getPost('holiday_date');
        $h_name = $this->request->getPost('holiday_name');
        $h_desc = $this->request->getPost('description') ?? $this->request->getPost('remarks');

        if (empty($h_date) || empty($h_name)) {
            return $this->response->setJSON([
                'status' => 'error', 
                'message' => 'Date and Title are required!'
            ]);
        }

        $data = [
            'holiday_date' => $h_date,
            'holiday_name' => $h_name,
            'description'  => $h_desc,
        ];
        
        $builder = $db->table('holidays');
        $existing = $builder->where('holiday_date', $h_date)->get()->getRow();
        
        if($existing) {
            $builder->where('id', $existing->id)->update($data);
            $msg = 'Holiday Updated Successfully!';
            $this->log_activity("Holiday updated: $h_name on $h_date", "Holiday Create");
        } else {
            $builder->insert($data);
            $msg = 'Holiday Set Successfully!';
            $this->log_activity("Holiday added: $h_name on $h_date", "Holiday Create");
        }

        return $this->response->setJSON([
            'status' => 'success', 
            'message' => $msg
        ]);
    }

    public function getHolidays() {
        $db = \Config\Database::connect();
        $results = $db->table('holidays')->get()->getResultArray();
        
        $events = [];
        foreach($results as $row) {
            $events[] = [
                'id'              => $row['id'],
                'title'           => $row['holiday_name'],
                'start'           => $row['holiday_date'],
                'description'     => $row['description'] ?? '',
                'backgroundColor' => '#dc3545',
                'borderColor'     => '#dc3545',
                'textColor'       => '#ffffff',
                'allDay'          => true
            ];
        }
        return $this->response->setJSON($events);
    }

    public function deleteHoliday($id = null) {
        $db = \Config\Database::connect();
        
        if ($id == null) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID nahi mili!']);
        }

        $builder = $db->table('holidays');
        $check = $builder->where('id', $id)->get()->getRow();
        
        if ($check) {
            $holidayName = $check->holiday_name ?? 'Holiday';
            $holidayDate = $check->holiday_date ?? '';
            
            $builder->where('id', $id)->delete();
            
            // ✅ FIX - "Leave Delete" se "Holiday Create" kiya
            $this->log_activity("Holiday deleted: $holidayName on $holidayDate", "Holiday Create");
            
            return $this->response->setJSON(['status' => 'success', 'message' => 'Holiday delete ho gayi!']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Database mein ye ID nahi mili: ' . $id]);
        }
    }
}