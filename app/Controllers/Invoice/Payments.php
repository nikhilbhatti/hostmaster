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

    public function index()
    {
        $db = \Config\Database::connect();

        $payments = $db->query("
            SELECT 
                p.*, 
                c.display_name AS cname, 
                i.invoice_number 
            FROM payments p
            LEFT JOIN customers c ON p.customer_id = c.id
            LEFT JOIN invoices i ON p.invoice_id = i.id
            ORDER BY p.id DESC
        ")->getResultArray();

        return view('invoice/payments/show', [
            'payments' => $payments
        ]);
    }

    public function indexpage()
    {
        $db = \Config\Database::connect();

        $payments = $db->query("
            SELECT 
                p.*, 
                c.display_name AS cname, 
                i.invoice_number 
            FROM payments p
            LEFT JOIN customers c ON p.customer_id = c.id
            LEFT JOIN invoices i ON p.invoice_id = i.id
            ORDER BY p.id DESC
        ")->getResultArray();

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
            LEFT JOIN customers c 
            ON i.customer_id = c.id 
            WHERE i.id = ?
        ", [$inv_id])->getRowArray();

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
        $im = new InvoiceModel();

        $customer_id        = $this->request->getPost('customer_id');
        $total_amt_received = floatval($this->request->getPost('amount'));
        $inv_id             = $this->request->getPost('invoice_id');

        $last = $this->m
            ->orderBy('id', 'DESC')
            ->first();

        $n = $last
            ? intval(substr($last['payment_number'], 4)) + 1
            : 1;

        $pn = 'PAY-' . str_pad($n, 4, '0', STR_PAD_LEFT);

        $allocations = $this->request
            ->getPost('allocated_amount') ?? [];

        if (!empty($allocations) && is_array($allocations)) {

            foreach ($allocations as $current_inv_id => $allocated_val) {

                $allocated_val = floatval($allocated_val);

                if ($allocated_val <= 0) {
                    continue;
                }

                $this->m->insert([

                    'payment_number' => $pn,
                    'customer_id'    => $customer_id,
                    'invoice_id'     => $current_inv_id,
                    'payment_date'   => $this->request->getPost('payment_date'),
                    'amount'         => $allocated_val,
                    'payment_mode'   => $this->request->getPost('payment_mode'),
                    'reference'      => $this->request->getPost('reference'),
                    'notes'          => $this->request->getPost('notes')

                ]);

                $inv = $im->find($current_inv_id);

                if ($inv) {

                    $new_paid = floatval($inv['paid_amount']) + $allocated_val;

                    $new_bal = floatval($inv['total']) - $new_paid;

                    $new_status = ($new_bal <= 0)
                        ? 'paid'
                        : 'partially_paid';

                    $im->update($current_inv_id, [

                        'paid_amount' => $new_paid,
                        'balance_due' => max(0, $new_bal),
                        'status'      => $new_status

                    ]);
                }
            }

        } elseif (!empty($inv_id)) {

            $this->m->insert([

                'payment_number' => $pn,
                'customer_id'    => $customer_id,
                'invoice_id'     => $inv_id,
                'payment_date'   => $this->request->getPost('payment_date'),
                'amount'         => $total_amt_received,
                'payment_mode'   => $this->request->getPost('payment_mode'),
                'reference'      => $this->request->getPost('reference'),
                'notes'          => $this->request->getPost('notes')

            ]);

            $inv = $im->find($inv_id);

            if ($inv) {

                $new_paid = floatval($inv['paid_amount']) + $total_amt_received;

                $new_bal = floatval($inv['total']) - $new_paid;

                $new_status = ($new_bal <= 0)
                    ? 'paid'
                    : 'partially_paid';

                $im->update($inv_id, [

                    'paid_amount' => $new_paid,
                    'balance_due' => max(0, $new_bal),
                    'status'      => $new_status

                ]);
            }

        } else {

            $this->m->insert([

                'payment_number' => $pn,
                'customer_id'    => $customer_id,
                'invoice_id'     => null,
                'payment_date'   => $this->request->getPost('payment_date'),
                'amount'         => $total_amt_received,
                'payment_mode'   => $this->request->getPost('payment_mode'),
                'reference'      => $this->request->getPost('reference'),
                'notes'          => $this->request->getPost('notes')

            ]);
        }

        return redirect()
            ->to(base_url('invoice/payments'))
            ->with(
                'success',
                'Payment ' . $pn . ' recorded successfully.'
            );
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

                'email' => $customer['email'] ?? 'N/A',

                'phone' => $customer['phone']
                    ?? $customer['mobile']
                    ?? 'N/A',

                'company_name' => $customer['company_name']
                    ?? 'N/A'

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
                i.invoice_number 
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
        $this->m->delete($id);

        return redirect()
            ->to(base_url('invoice/payments'))
            ->with('success', 'Payment deleted');
    }

    public function edit($id = null)
    {
        $paymentModel  = new PaymentModel();
        $customerModel = new CustomerModel();
        $invoiceModel  = new InvoiceModel();

        $data['payment'] = $paymentModel->find($id);

        if (empty($data['payment'])) {

            throw \CodeIgniter\Exceptions\PageNotFoundException
                ::forPageNotFound("Payment not found");
        }

        $data['customers'] = $customerModel->findAll();
        $data['invoices']  = $invoiceModel->findAll();

        return view('invoice/payments/edit', $data);
    }

    public function update($id = null)
    {
        $paymentModel = new PaymentModel();

        $rules = [

            'customer_id'  => 'required',
            'amount'       => 'required|numeric',
            'payment_date' => 'required|valid_date'

        ];

        if (!$this->validate($rules)) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'errors',
                    $this->validator->getErrors()
                );
        }

        $updateData = [

            'customer_id'  => $this->request->getPost('customer_id'),
            'invoice_id'   => $this->request->getPost('invoice_id'),
            'amount'       => $this->request->getPost('amount'),
            'payment_date' => $this->request->getPost('payment_date'),
            'payment_mode' => $this->request->getPost('payment_mode'),
            'reference'    => $this->request->getPost('reference'),
            'notes'        => $this->request->getPost('notes'),

        ];

        if ($paymentModel->update($id, $updateData)) {

            return redirect()
                ->to(base_url('invoice/payments'))
                ->with(
                    'success',
                    'Payment updated successfully!'
                );
        }

        return redirect()
            ->back()
            ->withInput()
            ->with(
                'error',
                'Failed to update payment.'
            );
    }
}