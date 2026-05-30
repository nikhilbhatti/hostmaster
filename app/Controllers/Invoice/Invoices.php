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
        WHERE i.status != 'trashed'
    ";

    $params = [];

    if ($status) {
        $sql .= " AND i.status = ?";
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
        return view('invoice/invoices/form', $this->formData(null));
    }

    private function generateInvoiceNumber()
    {
        $db = \Config\Database::connect();

        $last = $db->query("
            SELECT invoice_number
            FROM invoices
            WHERE invoice_number LIKE 'INV-%'
            ORDER BY id DESC
            LIMIT 1
        ")->getRowArray();

        if ($last && !empty($last['invoice_number'])) {
            $lastNum = preg_replace('/[^0-9]/', '', $last['invoice_number']);
            $num = !empty($lastNum) ? ((int)$lastNum + 1) : 1;
        } else {
            $num = 1;
        }

        return 'INV-' . str_pad($num, 6, '0', STR_PAD_LEFT);
    }

    public function store()
    {
        $total = (float)$this->request->getPost('total');

        $invoiceNumber = trim((string)$this->request->getPost('invoice_number'));

        if ($invoiceNumber === '') {
            $invoiceNumber = $this->generateInvoiceNumber();
        }

        $action = $this->request->getPost('inv_action');
        $status = ($action === 'sent') ? 'sent' : 'draft';

        $id = $this->m->insert([

            'invoice_number'  => $invoiceNumber,
            'customer_id'     => $this->request->getPost('customer_id'),
            'reference'       => $this->request->getPost('reference'),
            'invoice_date'    => $this->request->getPost('invoice_date'),
            'due_date'        => $this->request->getPost('due_date'),
            'payment_terms'   => $this->request->getPost('payment_terms'),
            'subject'         => $this->request->getPost('subject'),
            'status'          => $status,
            'sub_total'       => $this->request->getPost('sub_total'),
            'discount_type'   => $this->request->getPost('discount_type'),
            'discount_value'  => $this->request->getPost('discount_value'),
            'discount_amount' => $this->request->getPost('discount_amount'),
            'tax_total'       => $this->request->getPost('tax_total'),
            'total'           => $total,
            'paid_amount'     => 0,
            'balance_due'     => $total,
            'customer_notes'  => $this->request->getPost('customer_notes'),
            'terms'           => $this->request->getPost('terms')

        ], true);

        $this->saveLineItems($id);

        return redirect()
            ->to(base_url('invoice/invoices/show/' . $id))
            ->with('success', 'Invoice ' . $invoiceNumber . ' created successfully.');
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
            LEFT JOIN customers c ON i.customer_id = c.id 
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
    $total = (float)$this->request->getPost('total');

    $oldInvoice = $this->m->find($id);

    if (!$oldInvoice) {
        return redirect()
            ->to(base_url('invoice/invoices'))
            ->with('error', 'Invoice not found.');
    }

    $invoiceNumber = trim((string)$this->request->getPost('invoice_number'));

    if ($invoiceNumber === '') {
        $invoiceNumber = $oldInvoice['invoice_number'] ?? $this->generateInvoiceNumber();
    }

    $action = $this->request->getPost('inv_action');
    $status = ($action === 'sent') ? 'sent' : 'draft';

    $db = \Config\Database::connect();

    $paidRow = $db->table('payments')
        ->selectSum('amount')
        ->where('invoice_id', $id)
        ->get()
        ->getRowArray();

    $paidAmount = (float)($paidRow['amount'] ?? 0);
    $balanceDue = max(0, $total - $paidAmount);

    if ($paidAmount > 0 && $balanceDue <= 0) {
        $status = 'paid';
    } elseif ($paidAmount > 0 && $balanceDue > 0) {
        $status = 'partially_paid';
    }

    $this->m->update($id, [
        'invoice_number'  => $invoiceNumber,
        'customer_id'     => $this->request->getPost('customer_id'),
        'reference'       => $this->request->getPost('reference'),
        'invoice_date'    => $this->request->getPost('invoice_date'),
        'due_date'        => $this->request->getPost('due_date'),
        'payment_terms'   => $this->request->getPost('payment_terms'),
        'subject'         => $this->request->getPost('subject'),
        'status'          => $status,
        'sub_total'       => $this->request->getPost('sub_total'),
        'discount_type'   => $this->request->getPost('discount_type'),
        'discount_value'  => $this->request->getPost('discount_value'),
        'discount_amount' => $this->request->getPost('discount_amount'),
        'tax_total'       => $this->request->getPost('tax_total'),
        'total'           => $total,
        'paid_amount'     => $paidAmount,
        'balance_due'     => $balanceDue,
        'customer_notes'  => $this->request->getPost('customer_notes'),
        'terms'           => $this->request->getPost('terms')
    ]);

    $this->im
        ->where('invoice_id', $id)
        ->delete();

    $this->saveLineItems($id);

    return redirect()
        ->to(base_url('invoice/invoices/show/' . $id))
        ->with('success', 'Invoice updated successfully.');
}

   public function delete($id)
{
    $invoice = $this->m->find($id);

    if (!$invoice) {
        return redirect()
            ->to(base_url('invoice/invoices'))
            ->with('error', 'Invoice not found.');
    }

    // Invoice ko Trash me bhejo
    $this->m->update($id, [
        'status'       => 'trashed',
        'trashed_at'   => date('Y-m-d H:i:s'),
        'trash_reason' => 'Moved to Trash'
    ]);

    return redirect()
        ->to(base_url('invoice/invoices'))
        ->with('success', 'Invoice moved to Trash successfully.');
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
            ->findAll(),

        'descriptions' => $this->im
            ->select('description')
            ->where('description IS NOT NULL')
            ->where('description !=', '')
            ->groupBy('description')
            ->orderBy('description', 'ASC')
            ->findAll(),

        'terms_suggestions' => $this->m
            ->select('terms')
            ->where('terms IS NOT NULL')
            ->where('terms !=', '')
            ->groupBy('terms')
            ->orderBy('id', 'DESC')
            ->findAll()
    ];
}
    private function saveLineItems($iid)
    {
        $names = $this->request->getPost('item_name');

        if (!$names) {
            return;
        }

        $itemIds       = $this->request->getPost('item_id') ?? [];
        $descriptions  = $this->request->getPost('item_desc') ?? [];
        $hsnSac        = $this->request->getPost('hsn_sac') ?? [];
        $qtys          = $this->request->getPost('qty') ?? [];
        $units         = $this->request->getPost('unit') ?? [];
        $rates         = $this->request->getPost('rate') ?? [];
        $discounts     = $this->request->getPost('item_discount') ?? [];
        $taxIds        = $this->request->getPost('tax_id') ?? [];
        $taxRates      = $this->request->getPost('tax_rate') ?? [];

        foreach ($names as $k => $name) {

            if (trim((string)$name) === '') {
                continue;
            }

            $qty  = (float)($qtys[$k] ?? 1);
            $rate = (float)($rates[$k] ?? 0);
            $disc = (float)($discounts[$k] ?? 0);
            $tr   = (float)($taxRates[$k] ?? 0);

            $base = $qty * $rate * (1 - $disc / 100);
            $ta   = $base * $tr / 100;
            $amt  = $base + $ta;

            $this->im->insert([

                'invoice_id'  => $iid,
                'item_id'     => $itemIds[$k] ?? null,
                'item_name'   => $name,
                'description' => $descriptions[$k] ?? '',
                'hsn_sac'     => $hsnSac[$k] ?? '',
                'qty'         => $qty,
                'unit'        => $units[$k] ?? 'pcs',
                'rate'        => $rate,
                'discount'    => $disc,
                'tax_id'      => $taxIds[$k] ?? null,
                'tax_rate'    => $tr,
                'tax_amount'  => $ta,
                'amount'      => $amt

            ]);
        }
    }
    public function trash()
{
    $db = \Config\Database::connect();

    $invoices = $db->query("
        SELECT i.*, c.display_name AS cname
        FROM invoices i
        LEFT JOIN customers c ON i.customer_id = c.id
        WHERE i.status = 'trashed'
        ORDER BY i.id DESC
    ")->getResultArray();

    return view('invoice/invoices/trash', [
        'invoices' => $invoices
    ]);
}

public function restore($id)
{
    $this->m->update($id, [
        'status' => 'draft',
        'trashed_at' => null,
        'trash_reason' => null
    ]);

    return redirect()
        ->to(base_url('invoice/invoices/trash'))
        ->with('success', 'Invoice restored successfully.');
}

public function permanentDelete($id)
{
    $this->im->where('invoice_id', $id)->delete();

    (new PaymentModel())
        ->where('invoice_id', $id)
        ->delete();

    $this->m->delete($id);

    return redirect()
        ->to(base_url('invoice/invoices/trash'))
        ->with('success', 'Invoice permanently deleted.');
}
}