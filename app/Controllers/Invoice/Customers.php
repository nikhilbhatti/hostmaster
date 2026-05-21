<?php

namespace App\Controllers\Invoice;

use App\Controllers\BaseController;
use App\Models\CustomerModel;

class Customers extends BaseController
{
    private $m;

    public function __construct()
    {
        $this->m = new CustomerModel();
    }

    public function index()
    {
        $s = $this->request->getGet('search');

        $q = $this->m->orderBy('display_name');

        if ($s) {
            $q = $q->groupStart()
                ->like('display_name', $s)
                ->orLike('email', $s)
                ->orLike('company_name', $s)
                ->groupEnd();
        }

        return view('invoice/customers/index', [
            'customers' => $q->findAll(),
            'search'    => $s
        ]);
    }

    public function create()
    {
        return view('invoice/customers/form', [
            'c'     => null,
            'title' => 'New Customer'
        ]);
    }

    public function store()
    {
        $d = $this->request->getPost();

        $d['shipping_same'] = isset($d['shipping_same']) ? 1 : 0;

        $this->m->insert($d);

        return redirect()
            ->to(base_url('invoice/customers'))
            ->with('success', 'Customer added successfully');
    }

    public function show($id)
    {
        $db = \Config\Database::connect();

        $invoices = $db->query(
            "SELECT * FROM invoices WHERE customer_id = ? ORDER BY id DESC",
            [$id]
        )->getResultArray();

        return view('invoice/customers/show', [
            'c'        => $this->m->find($id),
            'invoices' => $invoices
        ]);
    }

    public function edit($id)
    {
        return view('invoice/customers/form', [
            'c'     => $this->m->find($id),
            'title' => 'Edit Customer'
        ]);
    }

    public function update($id)
    {
        $d = $this->request->getPost();

        $d['shipping_same'] = isset($d['shipping_same']) ? 1 : 0;

        $this->m->update($id, $d);

        return redirect()
            ->to(base_url('invoice/customers'))
            ->with('success', 'Customer updated');
    }

    public function delete($id)
    {
        $this->m->delete($id);

        return redirect()
            ->to(base_url('invoice/customers'))
            ->with('success', 'Customer deleted');
    }
}