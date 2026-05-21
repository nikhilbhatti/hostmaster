<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\OrderModel; 
use CodeIgniter\Controller;

class ClientController extends BaseController
{
    protected $clientModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
    }

    // 1. All Clients List
    public function index()
    {
        $data = [
            'all_clients' => $this->clientModel->select('*, email_1 as email')->orderBy('id', 'DESC')->findAll(),
            'title'       => 'Client Directory'
        ];
        
        return view('client/dashboard', $data);
    }

    // 2. Add New Client Page
    public function add()
    {
        $data = [
            'title' => 'Register New Client'
        ];
        return view('client/add', $data);
    }

    // 3. Save New Client
    public function store()
    {
        $clientName = $this->request->getPost('client_name'); 
        
        $data = [
            'client_name'      => $clientName,
            'contact_person'   => $this->request->getPost('contact_person'), // Added field
            'website_url'      => $this->request->getPost('website_url'),
            'business_details' => $this->request->getPost('business_details'),
            'phone'            => $this->request->getPost('phone'),
            'phone_2'          => $this->request->getPost('phone_2'),
            'email_1'          => $this->request->getPost('email_1'),
            'email_2'          => $this->request->getPost('email_2'),
            'address'          => $this->request->getPost('address'),
            'state'            => $this->request->getPost('state'),
            'country'          => $this->request->getPost('country') ?: 'India',
        ];

        if ($this->clientModel->insert($data)) {
            // --- ACTIVITY LOG ---
            log_activity("Registered a new client: " . $clientName);
            
            return redirect()->to(base_url('clients'))->with('status', 'Client successfully registered!');
        } else {
            $errors = $this->clientModel->errors();
            return redirect()->back()->withInput()->with('error', 'Validation Failed: ' . implode(', ', $errors));
        }
    }

    // 4. View Single Client Profile
    public function view($id)
    {
        // Select all fields including contact_person
        $client = $this->clientModel->select('*, email_1 as email')->find($id);
        
        if (!$client) return redirect()->to(base_url('clients'))->with('error', 'Client not found!');

        $data = [
            'client' => $client,
            'title'  => 'Profile: ' . $client['client_name']
        ];
        return view('client/view', $data);
    }

    // --- AJAX Function ---
    public function getClientOrders($clientId)
    {
        $orderModel = new OrderModel();
        
        $orders = $orderModel->select('orders.*, order_types.type_name, service_providers.provider_name')
                            ->join('order_types', 'order_types.id = orders.order_type_id', 'left')
                            ->join('service_providers', 'service_providers.id = orders.provider_id', 'left')
                            ->where('client_id', $clientId)
                            ->findAll();

        return $this->response->setJSON($orders);
    }

    // 5. Edit Client Page
    public function edit($id)
    {
        $client = $this->clientModel->find($id);
        if (!$client) return redirect()->to(base_url('clients'))->with('error', 'Client not found!');

        $data = [
            'client' => $client,
            'title'  => 'Edit Client: ' . $client['client_name']
        ];
        return view('client/edit', $data);
    }

    // 6. Update Client Details
    public function update($id)
    {
        $client = $this->clientModel->find($id);
        if (!$client) {
            return redirect()->to(base_url('clients'))->with('error', 'Record not found.');
        }

        $clientName = $this->request->getPost('client_name');

        $data = [
            'client_name'      => $clientName,
            'contact_person'   => $this->request->getPost('contact_person'), // Added field
            'website_url'      => $this->request->getPost('website_url'),
            'business_details' => $this->request->getPost('business_details'),
            'phone'            => $this->request->getPost('phone'),
            'phone_2'          => $this->request->getPost('phone_2'),
            'email_1'          => $this->request->getPost('email_1'),
            'email_2'          => $this->request->getPost('email_2'),
            'address'          => $this->request->getPost('address'),
            'state'            => $this->request->getPost('state'),
            'country'          => $this->request->getPost('country'),
        ];

        if ($this->clientModel->update($id, $data)) {
            // --- ACTIVITY LOG ---
            log_activity("Updated profile for client: " . $clientName);
            
            return redirect()->to(base_url('clients'))->with('status', 'Client profile updated!');
        } else {
            $errors = $this->clientModel->errors();
            return redirect()->back()->withInput()->with('error', 'Update failed: ' . implode(', ', $errors));
        }
    }

    // 7. Delete Client
    public function delete($id)
    {
        $client = $this->clientModel->find($id);
        
        if ($client) {
            $this->clientModel->delete($id);
            
            // --- ACTIVITY LOG ---
            log_activity("Deleted client record: " . $client['client_name']);
            
            return redirect()->to(base_url('clients'))->with('status', 'Client record deleted!');
        }
        return redirect()->to(base_url('clients'))->with('error', 'Could not find client.');
    }
}