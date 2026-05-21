<?php

namespace App\Models;

use CodeIgniter\Model;

class SmtpSettingsModel extends Model
{
    protected $table            = 'smtp_settings'; 
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // HR_EMAIL add kar diya hai taaki frontend se data save ho sake
    protected $allowedFields = [
        'smtp_host', 
        'smtp_port', 
        'smtp_user', 
        'smtp_pass', 
        'from_email', 
        'from_name',
        'hr_email' // <--- Ye field add karna zaroori tha
    ];

    protected $useTimestamps = false; 
}