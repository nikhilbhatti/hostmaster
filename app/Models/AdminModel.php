<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model {
    protected $table      = 'admins';
    protected $primaryKey = 'id';
    
    // Yahan 'joining_date' add kar diya gaya hai
    protected $allowedFields = [
        'username', 
        'name', 
        'email', 
        'password', 
        'role', 
        'status', 
        'is_active',
        'joining_date',   // <-- Ye zaroori hai data save karne ke liye
        'lms_permissions', 
        'updated_at',      
        'created_at'
    ];

    protected $returnType = 'array';
    
    protected $useTimestamps = true; 
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}