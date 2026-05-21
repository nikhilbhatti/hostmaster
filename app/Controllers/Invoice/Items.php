<?php

namespace App\Controllers\Invoice;

use App\Controllers\BaseController;
use App\Models\ItemModel;
use App\Models\TaxModel;

class Items extends BaseController
{
    private $m;

    public function __construct()
    {
        $this->m = new ItemModel();
    }

    public function index()
    {
        return view('invoice/items/index', [
            'items' => $this->m->orderBy('name')->findAll()
        ]);
    }

    public function create()
    {
        return view('invoice/items/form', [
            'item'  => null,
            'taxes' => (new TaxModel())->where('status', 1)->findAll()
        ]);
    }

    public function store()
    {
        $this->m->insert($this->request->getPost());

        return redirect()
            ->to(base_url('invoice/items'))
            ->with('success', 'Item added');
    }

    public function edit($id)
    {
        return view('invoice/items/form', [
            'item'  => $this->m->find($id),
            'taxes' => (new TaxModel())->where('status', 1)->findAll()
        ]);
    }

    public function update($id)
    {
        $this->m->update($id, $this->request->getPost());

        return redirect()
            ->to(base_url('invoice/items'))
            ->with('success', 'Item updated');
    }

    public function delete($id)
    {
        $this->m->delete($id);

        return redirect()
            ->to(base_url('invoice/items'))
            ->with('success', 'Item deleted');
    }

    public function getPrice($id)
    {
        return $this->response->setJSON($this->m->find($id));
    }
}