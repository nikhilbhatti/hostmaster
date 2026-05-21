<?php

namespace App\Models;

use CodeIgniter\Model;

class ReportModel extends Model
{
    protected $table            = 'daily_reports'; 
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    // Sahi column names jo aapke database (image_f2e5f8.jpg) mein hain
    protected $allowedFields    = ['staff_id', 'report_text', 'created_at']; 

    // Timestamps configuration
    // Kyunki aapki table mein updated_at nahi hai, isliye ise false karna zaroori hai
    protected $useTimestamps = false; 
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // Ise khali chhod dein kyunki column exist nahi karta
}