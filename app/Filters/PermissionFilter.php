<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $db = \Config\Database::connect();

        // Agar Admin hai toh sab allow hai
        if ($session->get('user_role') == 'admin') return;

        // --- FIXED LINES START ---
        // $request->uri ki jagah $request->getUri() use karna hai
        $uri = $request->getUri(); 
        $module = $uri->getSegment(1); // Pehla part (e.g., clients, orders)
        $action = $uri->getTotalSegments() >= 2 ? $uri->getSegment(2) : 'view'; 
        // --- FIXED LINES END ---

        $userId = $session->get('admin_id');
        
        // Database se permission check karein
        $permission = $db->table('permissions')
                         ->where('user_id', $userId)
                         ->where('module_name', $module)
                         ->get()->getRowArray();

        // Agar permission nahi hai toh Dashboard par bhej do
        if (!$permission) {
            return redirect()->to('dashboard')->with('error', 'You do not have permission to access this module.');
        }

        // Action wise check (e.g., add, edit, delete)
        if ($action == 'add' && !$permission['can_add']) {
            return redirect()->to('dashboard')->with('error', 'Access Denied: Cannot Add.');
        }
        
        if (($action == 'edit' || $action == 'update') && !$permission['can_edit']) {
            return redirect()->to('dashboard')->with('error', 'Access Denied: Cannot Edit.');
        }

        if ($action == 'delete' && !$permission['can_delete']) {
            return redirect()->to('dashboard')->with('error', 'Access Denied: Cannot Delete.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed
    }
}