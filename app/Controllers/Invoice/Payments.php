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
            WHERE 
                i.id IN (
                    SELECT DISTINCT invoice_id 
                    FROM payments 
                    WHERE invoice_id IS NOT NULL
                )
                OR i.status != 'draft'
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
            ORDER BY i.id DESC
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
                ->to(base_url('invoice/payments/indexpage'))
                ->with('error', 'Invoice not found.');
        }

        return view('invoice/payments/form', [
            'payment' => null,
            'customers' => (new CustomerModel())
                ->where('status', 1)
                ->orderBy('display_name')
                ->findAll(),
            'invoice' => $inv
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

                $this->m->insert([
                    'payment_number' => $pn,
                    'customer_id'    => $customer_id,
                    'invoice_id'     => $current_inv_id,
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

            $this->m->insert([
                'payment_number' => $pn,
                'customer_id'    => $customer_id,
                'invoice_id'     => $inv_id,
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

        return redirect()
            ->to(base_url('invoice/payments/indexpage'))
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

    if (!$invoice) {

        return redirect()
            ->to(base_url('invoice/payments/indexpage'))
            ->with('error', 'Invoice not found.');
    }

    $payments = $db->query("
        SELECT 
            p.*,
            i.invoice_number,
            c.display_name AS cname
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

    return view('invoice/payments/history', [

        'invoice'  => $invoice,

        'payments' => $payments

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
                ->to(base_url('invoice/payments/indexpage'))
                ->with('error', 'Payment not found.');
        }

        $invoice_id = $payment['invoice_id'] ?? null;

        $this->m->delete($id);

        if (!empty($invoice_id)) {
            $this->recalculateInvoice($invoice_id);
        }

        return redirect()
            ->to(base_url('invoice/payments/indexpage'))
            ->with('success', 'Payment deleted and invoice balance updated.');
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

        $data['customers'] = $customerModel->findAll();
        $data['invoices']  = $invoiceModel->findAll();

        return view('invoice/payments/edit', $data);
    }

    public function update($id = null)
    {
        $paymentModel = new PaymentModel();

        $oldPayment = $paymentModel->find($id);

        if (!$oldPayment) {
            return redirect()
                ->to(base_url('invoice/payments/indexpage'))
                ->with('error', 'Payment not found.');
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

        $updateData = [
            'customer_id'  => $this->request->getPost('customer_id'),
            'invoice_id'   => $this->request->getPost('invoice_id'),
            'amount'       => $this->request->getPost('amount'),
            'payment_date' => $this->request->getPost('payment_date'),
            'payment_mode' => $this->request->getPost('payment_mode'),
            'reference'    => $this->request->getPost('reference'),
            'notes'        => $this->request->getPost('notes')
        ];

        if ($paymentModel->update($id, $updateData)) {

            if (!empty($oldInvoiceId)) {
                $this->recalculateInvoice($oldInvoiceId);
            }

            if (!empty($updateData['invoice_id'])) {
                $this->recalculateInvoice($updateData['invoice_id']);
            }

            return redirect()
                ->to(base_url('invoice/payments/indexpage'))
                ->with('success', 'Payment updated successfully!');
        }

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Failed to update payment.');
    }
}