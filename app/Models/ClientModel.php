<?php 

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model {
    protected $table      = 'clients';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'client_name', 
        'contact_person', // <--- Naya field yahan add kar diya
        'website_url', 
        'business_details', 
        'phone', 
        'phone_2', 
        'email_1', 
        'email_2', 
        'address', 
        'state', 
        'country'
    ];

    protected $useTimestamps = true; 
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation rules table structure ke hisaab se
    protected $validationRules = [
        'client_name'    => 'required|min_length[3]|max_length[255]',
        'contact_person' => 'required|min_length[2]|max_length[255]', // <--- Validation bhi add kar di
        'phone'          => 'required|min_length[10]',
        'email_1'        => 'required|valid_email'
    ];
}