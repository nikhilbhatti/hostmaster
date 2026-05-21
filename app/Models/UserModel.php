<?php

namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
    // Agar Hostmaster mein table ka naam 'admin' hai toh yahan 'admin' likho
    protected $table      = 'users'; 
    protected $primaryKey = 'id';

    // Allowed fields mein 'username' hona chahiye
    protected $allowedFields = ['username', 'full_name', 'email', 'password', 'is_active'];
}