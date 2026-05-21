<?php

namespace App\Controllers\Invoice;

use App\Controllers\BaseController;
use App\Models\InvoiceModel;
use App\Models\InvoiceItemModel;
use App\Models\CustomerModel;
use App\Models\ItemModel;
use App\Models\TaxModel;
use App\Models\PaymentModel;

class Invoices extends BaseController
{
    private $m;
    private $im;

    public function __construct()
    {
        $this->m  = new InvoiceModel();
        $this->im = new InvoiceItemModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();

        $status = $this->request->getGet('status');

        $sql = "
            SELECT 
                i.*, 
                c.display_name AS cname 
            FROM invoices i 
            LEFT JOIN customers c ON i.customer_id = c.id
        ";

        $params = [];

        if ($status) {
            $sql .= " WHERE i.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY i.id DESC";

        $invoices = $db->query($sql, $params)->getResultArray();

        return view('invoice/invoices/index', [
            'invoices'      => $invoices,
            'status_filter' => $status
        ]);
    }

    public function create()
    {
        return view(
            'invoice/invoices/form',
            $this->formData(null)
        );
    }

    public function store()
    {
        $total = $this->request->getPost('total');

        $last = $this->m
            ->orderBy('id', 'DESC')
            ->first();

        $n = $last
            ? intval(substr($last['invoice_number'], 4)) + 1
            : 1;

        $in = 'INV-' . str_pad($n, 4, '0', STR_PAD_LEFT);

        $id = $this->m->insert([

            'invoice_number'  => $in,
            'customer_id'     => $this->request->getPost('customer_id'),
            'reference'       => $this->request->getPost('reference'),
            'invoice_date'    => $this->request->getPost('invoice_date'),
            'due_date'        => $this->request->getPost('due_date'),
            'payment_terms'   => $this->request->getPost('payment_terms'),
            'subject'         => $this->request->getPost('subject'),
            'status'          => 'draft',
            'sub_total'       => $this->request->getPost('sub_total'),
            'discount_type'   => $this->request->getPost('discount_type'),
            'discount_value'  => $this->request->getPost('discount_value'),
            'discount_amount' => $this->request->getPost('discount_amount'),
            'tax_total'       => $this->request->getPost('tax_total'),
            'total'           => $total,
            'balance_due'     => $total,
            'customer_notes'  => $this->request->getPost('customer_notes'),
            'terms'           => $this->request->getPost('terms')

        ], true);

        $this->saveLineItems($id);

        return redirect()
            ->to(base_url('invoice/invoices/show/' . $id))
            ->with('success', 'Invoice ' . $in . ' created');
    }

    public function show($id)
    {
        $db = \Config\Database::connect();

        $inv = $db->query("
            SELECT 
                i.*, 
                c.display_name AS cname, 
                c.email AS cemail, 
                c.work_phone AS cphone, 
                c.gstin AS cgstin, 
                c.b_address1, 
                c.b_address2,
                c.b_city, 
                c.b_state, 
                c.b_zip,
                c.b_country
            FROM invoices i 
            LEFT JOIN customers c 
                ON i.customer_id = c.id 
            WHERE i.id = ?
        ", [$id])->getRowArray();

        if (!$inv) {
            return redirect()
                ->to(base_url('invoice/invoices'))
                ->with('error', 'Invoice not found');
        }

        $payments = $db->query("
            SELECT * 
            FROM payments 
            WHERE invoice_id = ? 
            ORDER BY id DESC
        ", [$id])->getResultArray();

        return view('invoice/invoices/show', [
            'inv'      => $inv,
            'items'    => $this->im
                ->where('invoice_id', $id)
                ->findAll(),
            'payments' => $payments
        ]);
    }

    public function edit($id)
    {
        return view(
            'invoice/invoices/form',
            $this->formData($this->m->find($id))
        );
    }

    public function update($id)
    {
        $total = $this->request->getPost('total');

        $this->m->update($id, [

            'customer_id'     => $this->request->getPost('customer_id'),
            'reference'       => $this->request->getPost('reference'),
            'invoice_date'    => $this->request->getPost('invoice_date'),
            'due_date'        => $this->request->getPost('due_date'),
            'payment_terms'   => $this->request->getPost('payment_terms'),
            'subject'         => $this->request->getPost('subject'),
            'sub_total'       => $this->request->getPost('sub_total'),
            'discount_type'   => $this->request->getPost('discount_type'),
            'discount_value'  => $this->request->getPost('discount_value'),
            'discount_amount' => $this->request->getPost('discount_amount'),
            'tax_total'       => $this->request->getPost('tax_total'),
            'total'           => $total,
            'balance_due'     => $total,
            'customer_notes'  => $this->request->getPost('customer_notes'),
            'terms'           => $this->request->getPost('terms')

        ]);

        $this->im
            ->where('invoice_id', $id)
            ->delete();

        $this->saveLineItems($id);

        return redirect()
            ->to(base_url('invoice/invoices/show/' . $id))
            ->with('success', 'Invoice updated');
    }

    public function delete($id)
    {
        $this->im
            ->where('invoice_id', $id)
            ->delete();

        (new PaymentModel())
            ->where('invoice_id', $id)
            ->delete();

        $this->m->delete($id);

        return redirect()
            ->to(base_url('invoice/invoices'))
            ->with('success', 'Invoice deleted');
    }

    private function formData($inv)
    {
        return [

            'invoice' => $inv,

            'invoice_items' => $inv
                ? $this->im
                    ->where('invoice_id', $inv['id'])
                    ->findAll()
                : [],

            'customers' => (new CustomerModel())
                ->where('status', 1)
                ->orderBy('display_name')
                ->findAll(),

            'items' => (new ItemModel())
                ->where('status', 1)
                ->orderBy('name')
                ->findAll(),

            'taxes' => (new TaxModel())
                ->where('status', 1)
                ->findAll()
        ];
    }

    private function saveLineItems($iid)
    {
        $names = $this->request->getPost('item_name');

        if (!$names) {
            return;
        }

        foreach ($names as $k => $name) {

            if (!$name) {
                continue;
            }

            $qty  = $this->request->getPost('qty')[$k] ?? 1;
            $rate = $this->request->getPost('rate')[$k] ?? 0;
            $disc = $this->request->getPost('item_discount')[$k] ?? 0;
            $tr   = $this->request->getPost('tax_rate')[$k] ?? 0;

            $base = $qty * $rate * (1 - $disc / 100);

            $ta = $base * $tr / 100;

            $amt = $base + $ta;

            $this->im->insert([

                'invoice_id'  => $iid,
                'item_id'     => $this->request->getPost('item_id')[$k] ?? null,
                'item_name'   => $name,
                'description' => $this->request->getPost('item_desc')[$k] ?? '',
                'qty'         => $qty,
                'unit'        => $this->request->getPost('unit')[$k] ?? 'pcs',
                'rate'        => $rate,
                'discount'    => $disc,
                'tax_id'      => $this->request->getPost('tax_id')[$k] ?? null,
                'tax_rate'    => $tr,
                'tax_amount'  => $ta,
                'amount'      => $amt

            ]);
        }
    }
}