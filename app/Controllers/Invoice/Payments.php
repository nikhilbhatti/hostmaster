<?php

namespace App\Controllers\Invoice;

use App\Controllers\BaseController;
use App\Models\PaymentModel;
use App\Models\InvoiceModel;
use App\Models\CustomerModel;

class Payments extends BaseController
{
    private $m;

    public function __construct()
    {
        $this->m = new PaymentModel();
    }

    private function generatePaymentNumber()
    {
        $last = $this->m->orderBy('id', 'DESC')->first();

        if ($last && !empty($last['payment_number'])) {
            $num = preg_replace('/[^0-9]/', '', $last['payment_number']);
            $n = !empty($num) ? ((int)$num + 1) : 1;
        } else {
            $n = 1;
        }

        return 'PAY-' . str_pad($n, 4, '0', STR_PAD_LEFT);
    }

    private function recalculateInvoice($invoice_id)
    {
        if (empty($invoice_id)) {
            return;
        }

        $db = \Config\Database::connect();
        $im = new InvoiceModel();

        $invoice = $im->find($invoice_id);

        if (!$invoice) {
            return;
        }

        $row = $db->table('payments')
            ->selectSum('amount')
            ->where('invoice_id', $invoice_id)
            ->get()
            ->getRowArray();

        $paidAmount = (float)($row['amount'] ?? 0);
        $total      = (float)($invoice['total'] ?? 0);
        $balance    = max(0, $total - $paidAmount);

        if ($paidAmount <= 0) {
            $status = 'unpaid';
        } elseif ($balance <= 0) {
            $status = 'paid';
        } else {
            $status = 'partially_paid';
        }

        $im->update($invoice_id, [
            'paid_amount' => $paidAmount,
            'balance_due' => $balance,
            'status'      => $status
        ]);
    }

    private function isInvoicePaymentLocked(array $invoice = null): bool
    {
        if (empty($invoice)) {
            return false;
        }

        $status = strtolower(trim($invoice['status'] ?? ''));

        return in_array($status, ['paid', 'partial', 'partially_paid'], true)
            || (float)($invoice['paid_amount'] ?? 0) > 0;
    }

    private function isInvoiceDeletedForDirectPayment(array $payment = null): bool
    {
        if (empty($payment) || empty($payment['invoice_id'])) {
            return false;
        }

        $invoice = (new InvoiceModel())->find($payment['invoice_id']);

        if (empty($invoice)) {
            return false;
        }

        $status = strtolower(trim($invoice['status'] ?? ''));

        return $status === 'trashed';
    }

    private function isPaymentLatest(array $payment = null): bool
    {
        if (empty($payment) || empty($payment['invoice_id'])) {
            return true;
        }

        $latest = $this->m
            ->where('invoice_id', $payment['invoice_id'])
            ->orderBy('payment_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();

        return !empty($latest) && $latest['id'] === $payment['id'];
    }

    private function isPaymentEditAllowed(array $payment = null): bool
    {
        if (empty($payment) || empty($payment['invoice_id'])) {
            return true;
        }

        if ($this->isInvoiceDeletedForDirectPayment($payment)) {
            return false;
        }

        $count = $this->m->where('invoice_id', $payment['invoice_id'])->countAllResults();

        if ($count <= 1) {
            return true;
        }

        return $this->isPaymentLatest($payment);
    }

    private function isPaymentDeleteAllowed(array $payment = null): bool
    {
        if (empty($payment) || empty($payment['invoice_id'])) {
            return true;
        }

        if ($this->isInvoiceDeletedForDirectPayment($payment)) {
            return false;
        }

        return true;
    }

    private function invoiceLedgerQuery()
    {
        $db = \Config\Database::connect();

        return $db->query("
            SELECT 
                i.id AS invoice_id,
                i.invoice_number,
                i.invoice_date,
                i.total,
                COALESCE(i.paid_amount, 0) AS paid_amount,
                COALESCE(i.balance_due, i.total) AS balance_due,
                i.status,
                c.display_name AS cname,
                c.company_name,
                MAX(p.payment_date) AS last_payment_date,
                COUNT(p.id) AS payment_count
            FROM invoices i
            LEFT JOIN customers c ON c.id = i.customer_id
            LEFT JOIN payments p ON p.invoice_id = i.id
            WHERE i.id IN (
                SELECT DISTINCT invoice_id 
                FROM payments 
                WHERE invoice_id IS NOT NULL
            )
            GROUP BY 
                i.id,
                i.invoice_number,
                i.invoice_date,
                i.total,
                i.paid_amount,
                i.balance_due,
                i.status,
                c.display_name,
                c.company_name

            UNION ALL

            SELECT 
                p.invoice_id AS invoice_id,
                COALESCE(p.invoice_number, CONCAT('Deleted Invoice #', p.invoice_id)) AS invoice_number,
                NULL AS invoice_date,
                0 AS total,
                SUM(p.amount) AS paid_amount,
                0 AS balance_due,
                'partially_paid' AS status,
                COALESCE(c.display_name, 'Deleted Customer') AS cname,
                COALESCE(c.company_name, '') AS company_name,
                MAX(p.payment_date) AS last_payment_date,
                COUNT(p.id) AS payment_count
            FROM payments p
            LEFT JOIN invoices i ON i.id = p.invoice_id
            LEFT JOIN customers c ON c.id = p.customer_id
            WHERE p.invoice_id IS NOT NULL
                AND i.id IS NULL
            GROUP BY 
                p.invoice_id,
                COALESCE(p.invoice_number, CONCAT('Deleted Invoice #', p.invoice_id)),
                COALESCE(c.display_name, 'Deleted Customer'),
                COALESCE(c.company_name, '')

            ORDER BY invoice_id DESC
        ")->getResultArray();
    }

    public function index()
    {
        $payments = $this->invoiceLedgerQuery();

        return view('invoice/payments/show', [
            'payments' => $payments
        ]);
    }

    public function indexpage()
    {
        $payments = $this->invoiceLedgerQuery();

        return view('invoice/payments/index', [
            'payments' => $payments
        ]);
    }

    public function create()
    {
        return view('invoice/payments/form', [
            'payment' => null,
            'customers' => (new CustomerModel())
                ->where('status', 1)
                ->orderBy('display_name')
                ->findAll(),
            'invoice' => null
        ]);
    }

    public function createForInvoice($inv_id)
    {
        $db = \Config\Database::connect();

        $inv = $db->query("
            SELECT 
                i.*, 
                c.display_name AS cname 
            FROM invoices i 
            LEFT JOIN customers c ON i.customer_id = c.id 
            WHERE i.id = ?
        ", [$inv_id])->getRowArray();

        if (!$inv) {
            return redirect()
                ->to(base_url('invoice/payments'))
                ->with('error', 'Invoice not found.');
        }

        return view('invoice/payments/form', [
            'payment' => null,
            'customers' => (new CustomerModel())
                ->where('status', 1)
                ->orderBy('display_name')
                ->findAll(),
            'invoice' => $inv,
            'source' => strtolower(trim($this->request->getGet('source') ?? ''))
        ]);
    }

    public function store()
    {
        $db = \Config\Database::connect();

        $customer_id = $this->request->getPost('customer_id');
        $paymentDate = $this->request->getPost('payment_date');
        $paymentMode = $this->request->getPost('payment_mode');
        $reference   = $this->request->getPost('reference');
        $notes       = $this->request->getPost('notes');
        $inv_id      = $this->request->getPost('invoice_id');
        $amount      = (float)$this->request->getPost('amount');

        if (empty($customer_id) || empty($paymentDate)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Customer and payment date are required.');
        }

        $pn = $this->generatePaymentNumber();

        $allocations = $this->request->getPost('allocated_amount') ?? [];
        $saved = false;

        $db->transStart();

        if (!empty($allocations) && is_array($allocations)) {

            foreach ($allocations as $current_inv_id => $allocated_val) {

                $allocated_val = (float)$allocated_val;

                if ($allocated_val <= 0) {
                    continue;
                }

                $invoiceRow = $db->table('invoices')
                    ->select('invoice_number')
                    ->where('id', $current_inv_id)
                    ->get()
                    ->getRowArray();

                $this->m->insert([
                    'payment_number' => $pn,
                    'customer_id'    => $customer_id,
                    'invoice_id'     => $current_inv_id,
                    'invoice_number' => $invoiceRow['invoice_number'] ?? null,
                    'payment_date'   => $paymentDate,
                    'amount'         => $allocated_val,
                    'payment_mode'   => $paymentMode,
                    'reference'      => $reference,
                    'notes'          => $notes
                ]);

                $saved = true;
                $this->recalculateInvoice($current_inv_id);
            }

        } elseif (!empty($inv_id) && $amount > 0) {

            $invoiceRow = $db->table('invoices')
                ->select('invoice_number')
                ->where('id', $inv_id)
                ->get()
                ->getRowArray();

            $this->m->insert([
                'payment_number' => $pn,
                'customer_id'    => $customer_id,
                'invoice_id'     => $inv_id,
                'invoice_number' => $invoiceRow['invoice_number'] ?? null,
                'payment_date'   => $paymentDate,
                'amount'         => $amount,
                'payment_mode'   => $paymentMode,
                'reference'      => $reference,
                'notes'          => $notes
            ]);

            $saved = true;
            $this->recalculateInvoice($inv_id);

        } elseif ($amount > 0) {

            $this->m->insert([
                'payment_number' => $pn,
                'customer_id'    => $customer_id,
                'invoice_id'     => null,
                'invoice_number' => null,
                'payment_date'   => $paymentDate,
                'amount'         => $amount,
                'payment_mode'   => $paymentMode,
                'reference'      => $reference,
                'notes'          => $notes
            ]);

            $saved = true;
        }

        $db->transComplete();

        if ($db->transStatus() === false || !$saved) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Payment could not be recorded. Please enter amount or allocation.');
        }

        if ($this->request->getPost('source') === 'invoice' && !empty($inv_id)) {
            return redirect()
                ->to(base_url('invoice/invoices/show/' . $inv_id))
                ->with('success', 'Payment ' . $pn . ' recorded successfully.');
        }

        return redirect()
            ->to(base_url('invoice/payments'))
            ->with('success', 'Payment ' . $pn . ' recorded successfully.');
    }

public function history($invoice_id)
{
    $db = \Config\Database::connect();

    $invoice = $db->query("
        SELECT 
            i.*,
            c.display_name AS cname,
            c.company_name,
            c.email,
            c.mobile AS phone
        FROM invoices i
        LEFT JOIN customers c 
            ON c.id = i.customer_id
        WHERE i.id = ?
    ", [$invoice_id])->getRowArray();

    $payments = $db->query("
        SELECT 
            p.*,
            COALESCE(i.invoice_number, p.invoice_number) AS invoice_number,
            COALESCE(c.display_name, 'Deleted Customer') AS cname,
            COALESCE(c.company_name, '') AS company_name
        FROM payments p
        LEFT JOIN invoices i 
            ON i.id = p.invoice_id
        LEFT JOIN customers c 
            ON c.id = p.customer_id
        WHERE p.invoice_id = ?
        ORDER BY 
            p.payment_date ASC,
            p.id ASC
    ", [$invoice_id])->getResultArray();

    $latestPaymentId = !empty($payments) ? end($payments)['id'] : null;
    $paymentCount = count($payments);

    if (!$invoice) {
        if (empty($payments)) {
            return redirect()
                ->to(base_url('invoice/payments'))
                ->with('error', 'Invoice not found.');
        }

        $paidAmount = array_sum(array_column($payments, 'amount'));
        $firstPayment = $payments[0];

        $invoice = [
            'id' => $invoice_id,
            'invoice_number' => $firstPayment['invoice_number'] ?? ('Deleted Invoice #' . $invoice_id),
            'total' => 0,
            'paid_amount' => $paidAmount,
            'balance_due' => 0,
            'status' => 'partially_paid',
            'cname' => $firstPayment['cname'] ?? 'Deleted Customer',
            'company_name' => $firstPayment['company_name'] ?? ''
        ];
    }

    return view('invoice/payments/history', [
        'invoice' => $invoice,
        'payments' => $payments,
        'latestPaymentId' => $latestPaymentId,
        'paymentCount' => $paymentCount,
        'source' => strtolower(trim($this->request->getGet('source') ?? ''))
    ]);
}

    public function get_unpaid_invoices($customer_id)
    {
        $db = \Config\Database::connect();

        $invoices = $db->query("
            SELECT 
                id, 
                invoice_number, 
                invoice_date, 
                total, 
                balance_due 
            FROM invoices 
            WHERE customer_id = ? 
            AND status NOT IN ('draft', 'paid') 
            AND balance_due > 0 
            ORDER BY id ASC
        ", [$customer_id])->getResultArray();

        return $this->response->setJSON($invoices);
    }

    public function get_customer_details($customer_id)
    {
        try {
            $db = \Config\Database::connect();

            $customer = $db->table('customers')
                ->where('id', $customer_id)
                ->get()
                ->getRowArray();

            if (!$customer) {
                return $this->response->setJSON([
                    'email'        => 'N/A',
                    'phone'        => 'N/A',
                    'company_name' => 'N/A'
                ]);
            }

            return $this->response->setJSON([
                'email'        => $customer['email'] ?? 'N/A',
                'phone'        => $customer['phone'] ?? $customer['mobile'] ?? 'N/A',
                'company_name' => $customer['company_name'] ?? 'N/A'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'email'        => 'N/A',
                'phone'        => 'N/A',
                'company_name' => 'N/A'
            ]);
        }
    }

    public function show($id)
    {
        $db = \Config\Database::connect();

        $payment = $db->query("
            SELECT 
                p.*, 
                c.display_name AS cname, 
                i.invoice_number,
                i.total,
                i.paid_amount,
                i.balance_due,
                i.status AS invoice_status
            FROM payments p
            LEFT JOIN customers c ON p.customer_id = c.id
            LEFT JOIN invoices i ON p.invoice_id = i.id
            WHERE p.id = ?
        ", [$id])->getRowArray();

        return view('invoice/payments/show_single', [
            'p' => $payment
        ]);
    }

    public function delete($id)
    {
        $payment = $this->m->find($id);

        if (!$payment) {
            return redirect()
                ->to(base_url('invoice/payments'))
                ->with('error', 'Payment not found.');
        }

        if (!$this->isPaymentDeleteAllowed($payment)) {
            return redirect()
                ->back()
                ->with('error', 'Payment delete is blocked on this screen while the invoice is active.');
        }

        $invoice_id = $payment['invoice_id'] ?? null;

        $this->m->delete($id);

        if (!empty($invoice_id)) {
            $this->recalculateInvoice($invoice_id);
        }

        $source = strtolower(trim($this->request->getGet('source') ?? ''));
        $returnTo = strtolower(trim($this->request->getGet('return') ?? ''));

        if (!empty($invoice_id) && $returnTo === 'history') {
            $redirectUrl = base_url('invoice/payments/history/' . $invoice_id)
                . ($source === 'invoice' ? '?source=invoice' : '');
        } elseif ($source === 'invoice' && !empty($invoice_id)) {
            $redirectUrl = base_url('invoice/invoices/show/' . $invoice_id . '?source=invoice');
        } else {
            $redirectUrl = base_url('invoice/payments');
        }

        return redirect()
            ->to($redirectUrl)
            ->with('success', 'Payment deleted and invoice balance updated. If no payments remain, the invoice can be edited or deleted from the invoice screen.');
    }

    public function bulkDelete()
    {
        $selected = $this->request->getPost('selected_invoices');

        if (empty($selected) || !is_array($selected)) {
            return redirect()
                ->back()
                ->with('error', 'Please select at least one invoice row to delete.');
        }

        $selectedIds = array_filter(array_map('intval', $selected), fn($id) => $id > 0);

        if (empty($selectedIds)) {
            return redirect()
                ->back()
                ->with('error', 'Invalid selection. Please choose a valid invoice row.');
        }

        $invoiceModel = new InvoiceModel();
        $existingInvoices = $invoiceModel->whereIn('id', $selectedIds)->findAll();

        foreach ($existingInvoices as $invoice) {
            $status = strtolower(trim($invoice['status'] ?? ''));
            if ($status === 'trashed') {
                return redirect()
                    ->back()
                    ->with('error', 'Payments for trashed invoices cannot be deleted from this screen.');
            }
        }

        $db = \Config\Database::connect();
        $payments = $db->table('payments')
            ->select('id, invoice_id')
            ->whereIn('invoice_id', $selectedIds)
            ->get()
            ->getResultArray();

        if (empty($payments)) {
            return redirect()
                ->back()
                ->with('error', 'No payments found for the selected invoice rows.');
        }

        $paymentIds = array_column($payments, 'id');
        $invoiceIds = array_unique(array_column($payments, 'invoice_id'));

        $db->transStart();
        $this->m->delete($paymentIds);
        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete selected payments.');
        }

        foreach ($invoiceIds as $invoiceId) {
            $this->recalculateInvoice($invoiceId);
        }

        return redirect()
            ->to(base_url('invoice/payments'))
            ->with('success', 'Selected payments deleted and invoice balances updated. If no payments remain, the invoice can be edited or deleted from the invoice screen.');
    }

    public function edit($id = null)
    {
        $paymentModel  = new PaymentModel();
        $customerModel = new CustomerModel();
        $invoiceModel  = new InvoiceModel();

        $data['payment'] = $paymentModel->find($id);

        if (empty($data['payment'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Payment not found");
        }

        if (!$this->isPaymentEditAllowed($data['payment'])) {
            return redirect()
                ->back()
                ->with('error', 'This payment cannot be edited because a newer payment exists. Delete the latest payment first.');
        }

        $data['customers'] = $customerModel->findAll();
        $data['invoices']  = $invoiceModel->findAll();
        $data['source']    = strtolower(trim($this->request->getGet('source') ?? ''));

        return view('invoice/payments/edit', $data);
    }

    public function update($id = null)
    {
        $paymentModel = new PaymentModel();
        $db = \Config\Database::connect();

        $oldPayment = $paymentModel->find($id);

        if (!$oldPayment) {
            return redirect()
                ->to(base_url('invoice/payments'))
                ->with('error', 'Payment not found.');
        }

        if (!$this->isPaymentEditAllowed($oldPayment)) {
            return redirect()
                ->back()
                ->with('error', 'This payment cannot be updated because a newer payment exists. Delete the latest payment first.');
        }

        $rules = [
            'customer_id'  => 'required',
            'amount'       => 'required|numeric',
            'payment_date' => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $oldInvoiceId = $oldPayment['invoice_id'];

        $invoiceIdUpdate = $this->request->getPost('invoice_id');
        $invoiceNumberUpdate = null;

        if (!empty($invoiceIdUpdate)) {
            $invoiceRow = $db->table('invoices')
                ->select('invoice_number')
                ->where('id', $invoiceIdUpdate)
                ->get()
                ->getRowArray();
            $invoiceNumberUpdate = $invoiceRow['invoice_number'] ?? null;
        }

        $updateData = [
            'customer_id'    => $this->request->getPost('customer_id'),
            'invoice_id'     => $invoiceIdUpdate,
            'invoice_number' => $invoiceNumberUpdate,
            'amount'         => $this->request->getPost('amount'),
            'payment_date'   => $this->request->getPost('payment_date'),
            'payment_mode'   => $this->request->getPost('payment_mode'),
            'reference'      => $this->request->getPost('reference'),
            'notes'          => $this->request->getPost('notes')
        ];

        if ($paymentModel->update($id, $updateData)) {

            if (!empty($oldInvoiceId)) {
                $this->recalculateInvoice($oldInvoiceId);
            }

            if (!empty($updateData['invoice_id'])) {
                $this->recalculateInvoice($updateData['invoice_id']);
            }

            $source = strtolower(trim($this->request->getGet('source') ?? ''));
            $returnTo = strtolower(trim($this->request->getGet('return') ?? ''));

            if (!empty($updateData['invoice_id']) && $returnTo === 'history') {
                $redirectUrl = base_url('invoice/payments/history/' . $updateData['invoice_id'])
                    . ($source === 'invoice' ? '?source=invoice' : '');
            } elseif ($source === 'invoice' && !empty($updateData['invoice_id'])) {
                $redirectUrl = base_url('invoice/payments/history/' . $updateData['invoice_id'] . '?source=invoice');
            } else {
                $redirectUrl = base_url('invoice/payments');
            }

            return redirect()
                ->to($redirectUrl)
                ->with('success', 'Payment updated successfully!');
        }

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Failed to update payment.');
    }
}