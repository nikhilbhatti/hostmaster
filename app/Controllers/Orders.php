<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\ClientModel;
use App\Models\SmtpSettingsModel;

class Orders extends BaseController
{
    protected $db;
    protected $orderModel;
    protected $clientModel;
    protected $smtpModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->orderModel = new OrderModel();
        $this->clientModel = new ClientModel();
        $this->smtpModel = new SmtpSettingsModel();
    }

    public function index()
    {
        $data['orders'] = $this->orderModel->select('orders.*, clients.client_name, order_types.type_name, service_providers.provider_name')
                                    ->join('clients', 'clients.id = orders.client_id', 'left')
                                    ->join('order_types', 'order_types.id = orders.order_type_id', 'left')
                                    ->join('service_providers', 'service_providers.id = orders.provider_id', 'left')
                                    ->orderBy('orders.id', 'DESC')
                                    ->findAll();

        return view('client/orders/index', $data); 
    }

    public function view($id)
    {
        $data['order'] = $this->orderModel->select('orders.*, clients.client_name, order_types.type_name, service_providers.provider_name')
                                    ->join('clients', 'clients.id = orders.client_id', 'left')
                                    ->join('order_types', 'order_types.id = orders.order_type_id', 'left')
                                    ->join('service_providers', 'service_providers.id = orders.provider_id', 'left')
                                    ->find($id);

        if (!$data['order']) {
            // English Fix: Order not found
            return redirect()->to(base_url('orders'))->with('error', 'Order not found!');
        }

        return view('client/orders/view', $data); 
    }

    public function add()
    {
        $data['clients'] = $this->clientModel->findAll();
        $data['order_types'] = $this->db->table('order_types')->get()->getResultArray();
        $data['providers'] = $this->db->table('service_providers')->get()->getResultArray();

        if (empty($data['clients'])) {
            // English Fix: Please add a client first
            return redirect()->to(base_url('clients/add'))->with('error', 'Please add a client first!');
        }

        return view('client/orders/add', $data);
    }

    public function store()
    {
        $rules = [
            'client_id'     => 'required',
            'order_type_id' => 'required',
            'provider_id'   => 'required',
            'domain_name'   => 'required',
        ];

        if (!$this->validate($rules)) {
            // English Fix: Required fields missing
            return redirect()->back()->withInput()->with('error', 'Please fill in all required fields.');
        }

        $domainName = $this->request->getPost('domain_name');

        $data = [
            'client_id'           => $this->request->getPost('client_id'),
            'order_type_id'       => $this->request->getPost('order_type_id'),
            'provider_id'         => $this->request->getPost('provider_id'),
            'domain_name'         => $domainName,
            'domain_expiry_date'  => $this->request->getPost('domain_expiry_date'),
            'hosting_plan'        => $this->request->getPost('hosting_plan'),
            'hosting_expiry_date' => $this->request->getPost('hosting_expiry_date'),
            'total_amount'        => $this->request->getPost('amount') ?: 0,
            'status'              => 'active'
        ];

        if ($this->orderModel->save($data)) {
            $newOrderId = $this->orderModel->getInsertID(); 
            
            $this->sendAutoExpiryEmails($newOrderId); 

            $this->log_activity("Created a new order for domain: " . $domainName);
            
            // English Fix: Order successfully saved
            return redirect()->to(base_url('orders'))->with('success', 'Order saved successfully!');
        } else {
            // English Fix: Error while saving
            return redirect()->back()->withInput()->with('error', 'Something went wrong while saving the order.');
        }
    }

    public function settings()
    {
        $settings = $this->smtpModel->find(1);
        $data = [
            'title'    => 'SMTP System Settings',
            'settings' => $settings 
        ];
        return view('client/settings', $data);
    }

   public function updateSettings()
{
    $data = [
        'smtp_user'  => $this->request->getPost('smtp_user'),
        'smtp_pass'  => $this->request->getPost('smtp_pass'),
        'from_email' => $this->request->getPost('from_email'),
        'from_name'  => $this->request->getPost('from_name'),
        'hr_email'   => $this->request->getPost('hr_email'), // <--- Ye line add kar di hai
        'smtp_host'  => 'smtp-relay.brevo.com',
        'smtp_port'  => 587
    ];

    if ($this->smtpModel->find(1)) {
        $this->smtpModel->update(1, $data);
    } else {
        $data['id'] = 1;
        $this->smtpModel->insert($data);
    }

    // Helper check
    if (function_exists('log_activity')) {
        log_activity("Updated System SMTP Settings");
    }

    return redirect()->back()->with('status', 'SMTP Settings Updated Successfully!');
}

    public function types()
    {
        $data['order_types'] = $this->db->table('order_types')->get()->getResultArray();
        $data['providers'] = $this->db->table('service_providers')->get()->getResultArray();
        return view('client/orders/types', $data);
    }

    public function save_type()
    {
        $name = $this->request->getPost('type_name');
        if(!empty($name)){
            $this->db->table('order_types')->insert(['type_name' => $name]);
            log_activity("Added a new service type: " . $name);
            // English Fix: Service type added
            return redirect()->to(base_url('orders/types'))->with('success', 'Service type added successfully!');
        }
        // English Fix: Name is required
        return redirect()->back()->with('error', 'Service type name is required.');
    }

    public function save_provider()
    {
        $name = $this->request->getPost('provider_name');
        if(!empty($name)){
            $this->db->table('service_providers')->insert(['provider_name' => $name]);
            log_activity("Added a new service provider: " . $name);
            // English Fix: Provider added
            return redirect()->to(base_url('orders/types'))->with('success', 'Service provider added successfully!');
        }
        // English Fix: Provider name is required
        return redirect()->back()->with('error', 'Provider name is required.');
    }

    public function delete_type($id)
    {
        $this->db->table('order_types')->where('id', $id)->delete();
        log_activity("Deleted a service type (ID: $id)");
        // English Fix: Type deleted
        return redirect()->to(base_url('orders/types'))->with('success', 'Service type deleted successfully.');
    }

    public function delete_provider($id)
    {
        $this->db->table('service_providers')->where('id', $id)->delete();
        log_activity("Deleted a service provider (ID: $id)");
        // English Fix: Provider deleted
        return redirect()->to(base_url('orders/types'))->with('success', 'Provider deleted successfully.');
    }

    public function edit($id)
    {
        $data['order'] = $this->orderModel->find($id);
        
        if (!$data['order']) {
            // English Fix: Order not found
            return redirect()->to(base_url('orders'))->with('error', 'Order not found!');
        }

        $data['clients'] = $this->clientModel->findAll();
        $data['order_types'] = $this->db->table('order_types')->get()->getResultArray();
        $data['providers'] = $this->db->table('service_providers')->get()->getResultArray();

        return view('client/orders/edit', $data);
    }

    public function update($id)
    {
        $domainName = $this->request->getPost('domain_name');
        $data = [
            'client_id'           => $this->request->getPost('client_id'),
            'order_type_id'       => $this->request->getPost('order_type_id'),
            'provider_id'         => $this->request->getPost('provider_id'),
            'domain_name'         => $domainName,
            'domain_expiry_date'  => $this->request->getPost('domain_expiry_date'),
            'hosting_plan'        => $this->request->getPost('hosting_plan'),
            'hosting_expiry_date' => $this->request->getPost('hosting_expiry_date'),
            'total_amount'        => $this->request->getPost('amount'),
        ];

        if ($this->orderModel->update($id, $data)) {
            
            $this->sendAutoExpiryEmails($id); 

            $this->log_activity("Updated order details for: " . $domainName);

            // English Fix: Order updated
            return redirect()->to(base_url('orders'))->with('success', 'Order updated successfully!');
        } else {
            // English Fix: Update failed
            return redirect()->back()->with('error', 'Failed to update order.');
        }
    }

    public function delete($id)
    {
        $order = $this->orderModel->find($id);
        if ($this->orderModel->delete($id)) {
            $domain = isset($order['domain_name']) ? $order['domain_name'] : $id;
            log_activity("Deleted order record for: " . $domain);
            // English Fix: Order deleted
            return redirect()->to(base_url('orders'))->with('success', 'Order deleted successfully.');
        }
        // English Fix: Delete failed
        return redirect()->to(base_url('orders'))->with('error', 'Unable to delete order.');
    }
}