<?php

namespace App\Controllers\Invoice;

use App\Controllers\BaseController;

use App\Models\QuoteModel;
use App\Models\QuoteItemModel;
use App\Models\CustomerModel;
use App\Models\ItemModel;
use App\Models\TaxModel;
use App\Models\InvoiceModel;
use App\Models\InvoiceItemModel;

class Quotes extends BaseController
{
    private $m, $im;

    public function __construct()
    {
        $this->m  = new QuoteModel();
        $this->im = new QuoteItemModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();

        $quotes = $db->query("
            SELECT 
                q.*, 
                c.display_name AS cname 
            FROM quotes q 
            LEFT JOIN customers c 
                ON q.customer_id = c.id 
            ORDER BY q.id DESC
        ")->getResultArray();

        return view('invoice/quotes/index', [
            'quotes' => $quotes
        ]);
    }

    public function create()
    {
        return view(
            'invoice/quotes/form',
            $this->formData(null)
        );
    }

    public function store()
    {
        $last = $this->m->orderBy('id', 'DESC')->first();

        $n = $last
            ? intval(substr($last['quote_number'], 4)) + 1
            : 1;

        $qn = 'QUO-' . str_pad($n, 4, '0', STR_PAD_LEFT);

        $id = $this->m->insert([
            'quote_number'    => $qn,
            'customer_id'     => $this->request->getPost('customer_id'),
            'reference'       => $this->request->getPost('reference'),
            'quote_date'      => $this->request->getPost('quote_date'),
            'expiry_date'     => $this->request->getPost('expiry_date'),
            'subject'         => $this->request->getPost('subject'),
            'status'          => 'draft',
            'sub_total'       => $this->request->getPost('sub_total'),
            'discount_type'   => $this->request->getPost('discount_type'),
            'discount_value'  => $this->request->getPost('discount_value'),
            'discount_amount' => $this->request->getPost('discount_amount'),
            'tax_total'       => $this->request->getPost('tax_total'),
            'total'           => $this->request->getPost('total'),
            'customer_notes'  => $this->request->getPost('customer_notes'),
            'terms'           => $this->request->getPost('terms')
        ], true);

        $this->saveLineItems($id);

        return redirect()
            ->to(base_url('invoice/quotes/show/' . $id))
            ->with('success', 'Quote ' . $qn . ' created');
    }

    public function show($id)
    {
        $db = \Config\Database::connect();

        $q = $db->query("
            SELECT 
                q.*,
                c.display_name AS cname,
                c.email AS cemail,
                c.work_phone AS cphone,
                c.gstin AS cgstin,
                c.b_address1,
                c.b_city,
                c.b_state,
                c.b_zip
            FROM quotes q
            LEFT JOIN customers c
                ON q.customer_id = c.id
            WHERE q.id = ?
        ", [$id])->getRowArray();

        return view('invoice/quotes/show', [
            'q'     => $q,
            'items' => $this->im
                ->where('quote_id', $id)
                ->findAll()
        ]);
    }

    public function edit($id)
    {
        return view(
            'invoice/quotes/form',
            $this->formData($this->m->find($id))
        );
    }

    public function update($id)
    {
        $this->m->update($id, [

            'customer_id'     => $this->request->getPost('customer_id'),
            'reference'       => $this->request->getPost('reference'),
            'quote_date'      => $this->request->getPost('quote_date'),
            'expiry_date'     => $this->request->getPost('expiry_date'),
            'subject'         => $this->request->getPost('subject'),
            'sub_total'       => $this->request->getPost('sub_total'),
            'discount_type'   => $this->request->getPost('discount_type'),
            'discount_value'  => $this->request->getPost('discount_value'),
            'discount_amount' => $this->request->getPost('discount_amount'),
            'tax_total'       => $this->request->getPost('tax_total'),
            'total'           => $this->request->getPost('total'),
            'customer_notes'  => $this->request->getPost('customer_notes'),
            'terms'           => $this->request->getPost('terms')

        ]);

        $this->im->where('quote_id', $id)->delete();

        $this->saveLineItems($id);

        return redirect()
            ->to(base_url('invoice/quotes/show/' . $id))
            ->with('success', 'Quote updated');
    }

    public function delete($id)
    {
        $this->im->where('quote_id', $id)->delete();

        $this->m->delete($id);

        return redirect()
            ->to(base_url('invoice/quotes'))
            ->with('success', 'Quote deleted');
    }

    public function convert($id)
    {
        $q = $this->m->find($id);

        $inv     = new InvoiceModel();
        $invItem = new InvoiceItemModel();

        $last = $inv->orderBy('id', 'DESC')->first();

        $n = $last
            ? intval(substr($last['invoice_number'], 4)) + 1
            : 1;

        $in = 'INV-' . str_pad($n, 4, '0', STR_PAD_LEFT);

        $iid = $inv->insert([

            'invoice_number'  => $in,
            'quote_id'        => $id,
            'customer_id'     => $q['customer_id'],
            'invoice_date'    => date('Y-m-d'),
            'due_date'        => date('Y-m-d', strtotime('+30 days')),
            'payment_terms'   => 'net30',
            'subject'         => $q['subject'],
            'status'          => 'draft',
            'sub_total'       => $q['sub_total'],
            'discount_type'   => $q['discount_type'],
            'discount_value'  => $q['discount_value'],
            'discount_amount' => $q['discount_amount'],
            'tax_total'       => $q['tax_total'],
            'total'           => $q['total'],
            'balance_due'     => $q['total'],
            'customer_notes'  => $q['customer_notes'],
            'terms'           => $q['terms']

        ], true);

        foreach (
            $this->im->where('quote_id', $id)->findAll()
            as $qi
        ) {

            $invItem->insert([

                'invoice_id'  => $iid,
                'item_id'     => $qi['item_id'],
                'item_name'   => $qi['item_name'],
                'description' => $qi['description'],
                'qty'         => $qi['qty'],
                'unit'        => $qi['unit'],
                'rate'        => $qi['rate'],
                'discount'    => $qi['discount'],
                'tax_id'      => $qi['tax_id'],
                'tax_rate'    => $qi['tax_rate'],
                'tax_amount'  => $qi['tax_amount'],
                'amount'      => $qi['amount']

            ]);
        }

        $this->m->update($id, [
            'status' => 'accepted'
        ]);

        return redirect()
            ->to(base_url('invoice/invoices/show/' . $iid))
            ->with(
                'success',
                'Invoice ' . $in . ' created from Quote'
            );
    }

    private function formData($quote)
    {
        return [

            'quote' => $quote,

            'quote_items' => $quote
                ? $this->im
                    ->where('quote_id', $quote['id'])
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

    private function saveLineItems($pid)
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

                'quote_id'    => $pid,
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