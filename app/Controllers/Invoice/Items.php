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
    public function ajaxStore()
{
    $data = [

        'item_type' => $this->request->getPost('item_type'),

        'name' => $this->request->getPost('name'),

        'sku' => $this->request->getPost('sku'),

        'hsn_sac' => $this->request->getPost('hsn_sac'),

        'unit' => $this->request->getPost('unit'),

        'description' => $this->request->getPost('description'),

        'selling_price' => $this->request->getPost('selling_price'),

        'purchase_price' => 0,

        'tax_id' => $this->request->getPost('tax_id'),

        'status' => 1
    ];

    /* AUTO SKU */
    if (empty($data['sku'])) {

        $last = $this->m
            ->orderBy('id', 'DESC')
            ->first();

        $num = $last ? ($last['id'] + 1) : 1;

        $data['sku'] = 'SKU-' . str_pad($num, 5, '0', STR_PAD_LEFT);
    }

    $this->m->insert($data);

    $id = $this->m->getInsertID();

    $item = $this->m->find($id);

    return $this->response->setJSON([

        'success' => true,

        'item' => $item
    ]);
}
}