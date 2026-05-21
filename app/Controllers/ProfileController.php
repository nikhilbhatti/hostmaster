<?php

namespace App\Controllers;

use App\Models\AdminModel;

class ProfileController extends BaseController
{
    public function index()
    {
        $adminId = $this->session->get('admin_id'); 
        
        if (!$adminId) {
            return redirect()->to(base_url('login'))->with('error', 'Please login first');
        }

        $model = new AdminModel();
        $data['user'] = $model->find($adminId);
        $data['title'] = "My Profile";

        return view('profile/index', $data);
    }

    public function update()
    {
        $adminId = $this->session->get('admin_id');
        $model = new AdminModel();

        if (!$adminId) {
            return redirect()->to(base_url('login'));
        }

        // --- VALIDATION FIX ---
        // 'is_unique' mein table name, column name, field name aur ID format ka sahi hona zaroori hai
        $rules = [
            'username' => "required|min_length[3]|is_unique[admins.username,id,{$adminId}]",
            'name'     => 'required|min_length[3]',
            'email'    => "required|valid_email|is_unique[admins.email,id,{$adminId}]",
        ];

        if (!$this->validate($rules)) {
            // Debugging ke liye: Errors ko dikhane ke liye flashdata change kiya hai
            $validationErrors = implode(" ", $this->validator->getErrors());
            return redirect()->back()->withInput()->with('error', $validationErrors);
        }

        $updateData = [
            'username' => (string)$this->request->getPost('username'),
            'name'     => (string)$this->request->getPost('name'),
            'email'    => (string)$this->request->getPost('email'),
        ];

        // Password update
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // --- DATABASE UPDATE ---
        if ($model->update($adminId, $updateData)) {
            
            // Session refresh
            $this->session->set('user_name', $updateData['name']);
            // Agar aap username bhi session mein store karte hain toh usey bhi update karein
            $this->session->set('username', $updateData['username']);
            
            $this->log_activity("Profile updated to username: " . $updateData['username'], "profile_update");

            return redirect()->to(base_url('profile'))->with('status', 'Profile updated successfully!');
        }

        return redirect()->back()->with('error', 'Database update failed.');
    }
}