<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface {

    public function before(RequestInterface $request, $arguments = null) {
        $session = session();
        $uri = trim($request->getUri()->getPath(), '/');

        // Debug: Agar loop ho raha hai toh yahan stop karke check karein
        // die("Current URI: " . $uri . " | Role: " . $session->get('role'));

        // 1. Basic Check: Agar user logged in nahi hai aur login page par bhi nahi hai
        if (!$session->get('isLoggedIn')) {
            if ($uri !== 'login' && $uri !== '') {
                return redirect()->to(base_url('login'));
            }
            return;
        }

        // 2. Simple Role Check: Agar logged in user login page par jane ki koshish kare
        if ($uri === 'login' || $uri === '') {
            $role = $session->get('role'); // Check karein aapne 'role' use kiya hai ya 'user_role'
            if ($role === 'admin') {
                return redirect()->to(base_url('admin/leaves'));
            } else {
                return redirect()->to(base_url('staff/dashboard'));
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}