<?php

namespace App\Controllers\Invoice;

use App\Controllers\BaseController;
use App\Models\InvoiceModel;
use App\Models\CustomerModel;
use App\Models\QuoteModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $periods = [
            'Today'        => 'DATE(created_at) = CURDATE()',
            'This Week'    => 'YEARWEEK(created_at) = YEARWEEK(NOW())',
            'This Month'   => 'MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())',
            'This Quarter' => 'QUARTER(created_at) = QUARTER(NOW()) AND YEAR(created_at) = YEAR(NOW())',
            'This Year'    => 'YEAR(created_at) = YEAR(NOW())',
        ];

        $stats = [];

        foreach ($periods as $label => $w) {
            $stats[$label] = $db->query("
                SELECT 
                    COUNT(*) cnt,
                    COALESCE(SUM(total), 0) amt,
                    COALESCE(SUM(paid_amount), 0) recv,
                    COALESCE(SUM(balance_due), 0) bal
                FROM invoices 
                WHERE $w
            ")->getRowArray();
        }

        $totalCustomers = (new CustomerModel())->countAll();
        $totalInvoices  = (new InvoiceModel())->countAll();
        $totalQuotes    = (new QuoteModel())->countAll();

        $overdueInvoices = (new InvoiceModel())
            ->where('status', 'overdue')
            ->countAllResults();

        $recentInvoices = $db->query("
            SELECT 
                i.*, 
                c.display_name AS cname
            FROM invoices i
            LEFT JOIN customers c ON i.customer_id = c.id
            ORDER BY i.id DESC
            LIMIT 5
        ")->getResultArray();

        return view('invoice/dashboard/index', compact(
            'stats',
            'totalCustomers',
            'totalInvoices',
            'totalQuotes',
            'overdueInvoices',
            'recentInvoices'
        ));
    }
}