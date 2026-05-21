<?php

namespace App\Controllers\Invoice;

use App\Controllers\BaseController;
use App\Models\TaxModel;

class Taxes extends BaseController
{
    private $m;

    public function __construct()
    {
        $this->m = new TaxModel();
    }

    public function index()
    {
        return view('invoice/taxes/index', [
            'taxes' => $this->m->orderBy('name')->findAll()
        ]);
    }

    public function create()
    {
        return view('invoice/taxes/form', [
            'tax' => null
        ]);
    }

    public function store()
    {
        $this->m->insert([
            'name'   => $this->request->getPost('name'),
            'rate'   => $this->request->getPost('rate'),
            'status' => 1
        ]);

        return redirect()
            ->to(base_url('invoice/taxes'))
            ->with('success', 'Tax added successfully');
    }

    public function edit($id)
    {
        return view('invoice/taxes/form', [
            'tax' => $this->m->find($id)
        ]);
    }

    public function update($id)
    {
        $this->m->update($id, [
            'name' => $this->request->getPost('name'),
            'rate' => $this->request->getPost('rate')
        ]);

        return redirect()
            ->to(base_url('invoice/taxes'))
            ->with('success', 'Tax updated');
    }

    public function delete($id)
    {
        $this->m->delete($id);

        return redirect()
            ->to(base_url('invoice/taxes'))
            ->with('success', 'Tax deleted');
    }
}