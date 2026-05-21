<?php

namespace App\Controllers;

use App\Models\AdminModel;

class Auth extends BaseController
{
    public function login()
    {
        // Redirect if already logged in
        if (session()->get('isLoggedIn')) {
            return $this->redirectUserByRole(session()->get('user_role'));
        }
        return view('auth/login');
    }

    public function authenticate() 
    {
        $session = session();
        $model = new AdminModel();
        
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Database se user fetch karna
        $admin = $model->where('username', $username)->first();
        
        if ($admin) {
            if (password_verify($password, $admin['password'])) {
                
                $raw_perms = $admin['lms_permissions'] ?? '[]';
                $lms_perms = json_decode($raw_perms, true);
                
                // --- SESSION DATA SETTING ---
                // 'name' key database ke column se uthayi ja rahi hai
                $sessionData = [
                    'admin_id'        => $admin['id'],     
                    'user_id'         => $admin['id'],      
                    'user_name'       => $admin['username'], // Username (e.g. admin123)
                    'name'            => $admin['name'],     // Actual Name (e.g. Deepak Sharma)
                    'user_role'       => $admin['role'],    
                    'role'            => $admin['role'],    
                    'lms_permissions' => $lms_perms,        
                    'isLoggedIn'      => true
                ];

                $session->set($sessionData);

                // Role based redirect
                return $this->redirectUserByRole($admin['role']);

            } else {
                return redirect()->back()->with('error', 'Invalid password.');
            }
        } else {
            return redirect()->back()->with('error', 'Username not found.');
        }
    }

    private function redirectUserByRole($role)
    {
        // Case insensitive check
        if (strtolower((string)$role) === 'admin') {
            return redirect()->to(base_url('admin/dashboard')); 
        } else {
            return redirect()->to(base_url('staff/dashboard'));
        }
    }

    public function logout()
    {
        session()->destroy();
        // Login page par wapas bhejna
        return redirect()->to(base_url('login'));
    }
}