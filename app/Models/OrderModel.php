<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true; // Ye add karna achha hota hai
    protected $returnType       = 'array';
    
    // In fields ka hona bahut zaroori hai data save karne ke liye
    protected $allowedFields    = [
        'client_id', 
        'order_type_id',  // <--- Ye missing tha
        'provider_id',    // <--- Ye bhi missing tha
        'domain_name', 
        'domain_expiry_date', 
        'hosting_plan', 
        'hosting_expiry_date', 
        'total_amount', 
        'status'
    ];

    // Dates ko handle karne ke liye (Optional but recommended)
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}