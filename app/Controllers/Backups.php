<?php

namespace App\Controllers;

use App\Models\ClientModel;
use CodeIgniter\Controller;

class Backups extends BaseController {
    
    // 1. List All Backups
    public function index() {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT b.*, c.client_name FROM backups b JOIN clients c ON b.client_id = c.id ORDER BY b.last_backup_date ASC");
        
        // BaseController ka data preserve karne ke liye $this->data use karein
        $this->data['backups'] = $query->getResultArray();
        
        return view('backups/index', $this->data);
    }

    // 2. Add Backup Form
    public function add() {
        $model = new ClientModel();
        $this->data['clients'] = $model->findAll();
        
        return view('backups/add', $this->data);
    }

    // 3. Save New Backup (MODIFIED)
    public function store() {
        $db = \Config\Database::connect();
        $staffName = session()->get('name') ?? 'Staff';
        $staffId   = session()->get('admin_id');

        $data = [
            'client_id'        => $this->request->getPost('client_id'),
            'last_backup_date' => $this->request->getPost('last_backup_date'),
            'backup_interval'  => $this->request->getPost('backup_interval'),
            'notes'            => $this->request->getPost('notes')
        ];
        
        if ($db->table('backups')->insert($data)) {
            // --- NOTIFICATION FOR ADMIN ---
            if (function_exists('add_notification')) {
                add_notification(
                    "New Backup Log Created", 
                    "$staffName ne system mein ek naya backup log add kiya hai.", 
                    "backup_alert", 
                    1, // is_admin_only = 1 (Taki staff ko na dikhe)
                    $staffId
                );
            }
            log_activity("Added a new backup log.", "backup_alert", 1);
        }

        return redirect()->to(base_url('backups'))->with('status', 'Backup log added successfully!');
    }

    // 4. View Single Backup Detail
    public function view($id) {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT b.*, c.client_name, c.email_1 as email, c.phone 
            FROM backups b 
            JOIN clients c ON b.client_id = c.id 
            WHERE b.id = ?
        ", [$id]);
        
        $this->data['backup'] = $query->getRowArray();

        if (!$this->data['backup']) {
            return redirect()->to(base_url('backups'))->with('error', 'Record not found!');
        }
        
        return view('backups/view', $this->data);
    }

    // 5. Edit Backup Form
    public function edit($id) {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT b.*, c.client_name FROM backups b JOIN clients c ON b.client_id = c.id WHERE b.id = ?", [$id]);
        $this->data['backup'] = $query->getRowArray();
        
        if (!$this->data['backup']) {
            return redirect()->to(base_url('backups'))->with('error', 'Record not found!');
        }
        
        $this->data['clients'] = (new ClientModel())->findAll();
        return view('backups/edit', $this->data);
    }

    // 6. Update Backup Data
    public function update($id) {
        $db = \Config\Database::connect();
        $data = [
            'last_backup_date' => $this->request->getPost('last_backup_date'),
            'backup_interval'  => $this->request->getPost('backup_interval'),
            'notes'            => $this->request->getPost('notes')
        ];
        
        $db->table('backups')->where('id', $id)->update($data);
        return redirect()->to(base_url('backups'))->with('status', 'Backup log updated successfully!');
    }

    // 7. Quick Mark Done (MODIFIED)
    public function markDone($id) {
        $db = \Config\Database::connect();
        $staffName = session()->get('name') ?? 'Staff';
        $staffId   = session()->get('admin_id');

        $db->table('backups')->where('id', $id)->update([
            'last_backup_date' => date('Y-m-d')
        ]);
        
        // --- NOTIFICATION FOR ADMIN ---
        if (function_exists('add_notification')) {
            add_notification(
                "Backup Completed", 
                "$staffName ne Backup ID: $id ko complete mark kiya hai.", 
                "backup_done", 
                1, // is_admin_only = 1
                $staffId
            );
        }
        log_activity("Marked backup as completed.", "backup_done", 1);
        
        return redirect()->to(base_url('backups'))->with('status', 'Backup date updated to today!');
    }

    // 8. Delete Backup Log
    public function delete($id) {
        $db = \Config\Database::connect();
        $db->table('backups')->where('id', $id)->delete();
        return redirect()->to(base_url('backups'))->with('status', 'Backup log deleted successfully!');
    }
    public function getClientOrders($clientId)
{
    $db = \Config\Database::connect();
    
    // JOIN lagaya hai taaki order_types table se service ka naam (type_name) mil sake
    $orders = $db->table('orders o') 
                 ->select('ot.type_name as service, o.domain_name, o.created_at') 
                 ->join('order_types ot', 'ot.id = o.order_type_id', 'left')
                 ->where('o.client_id', $clientId)
                 ->orderBy('o.id', 'DESC')
                 ->get()
                 ->getResultArray();

    return $this->response->setJSON($orders ?: []);
}
}