<?= $this->extend('layout/main') ?>

<?php $page_title = 'Dashboard'; ?>

<?= $this->section('content') ?>

<style>
    /* Modern Dashboard Styling */
    .invoice-dashboard {
        font-family: 'Inter', system-ui, sans-serif;
        color: #1e293b;
        padding: 24px;
        background: #f1f5f9;
        min-height: 100vh;
    }

    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 32px;
    }

    .section-header h4 {
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
    }

    /* Premium Buttons */
    .btn {
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    .btn-primary { background: #4f46e5; color: #fff; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2); }
    .btn-primary:hover { background: #4338ca; transform: translateY(-2px); }
    .btn-outline { background: #fff; border: 1px solid #e2e8f0; color: #475569; }
    .btn-outline:hover { background: #f8fafc; border-color: #cbd5e1; }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: #fff;
        padding: 24px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        transition: 0.3s;
    }
    .stat-card:hover { border-color: #4f46e5; }
    .stat-label { font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px; }
    .stat-value { font-size: 28px; font-weight: 800; color: #0f172a; }

    /* Modern Tables */
    .card { background: #fff; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 32px; }
    .card-header { padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
    
    .ztable { width: 100%; border-collapse: separate; border-spacing: 0; }
    .ztable th { padding: 16px 24px; font-size: 12px; color: #64748b; background: #f8fafc; }
    .ztable td { padding: 18px 24px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
    
    /* Modern Status Badges */
    .badge { padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .badge-paid { background: #dcfce7; color: #166534; }
    .badge-unpaid, .badge-overdue { background: #fee2e2; color: #991b1b; }
    .badge-draft { background: #f1f5f9; color: #475569; }
    .badge-partially_paid { background: #fef3c7; color: #92400e; }
    .badge-sent { background: #dbeafe; color: #1e40af; }

    @media(max-width:992px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
</style>

<div class="invoice-dashboard">
    <div class="section-header">
        <h4>Dashboard Overview</h4>
        <div style="display:flex; gap:12px">
            <a href="<?= base_url('invoice/quotes/create') ?>" class="btn btn-outline"><i class="bi bi-file-earmark-plus"></i> New Quote</a>
            <a href="<?= base_url('invoice/invoices/create') ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Invoice</a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Customers</div>
            <div class="stat-value"><?= $totalCustomers ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Invoices</div>
            <div class="stat-value"><?= $totalInvoices ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Quotes</div>
            <div class="stat-value"><?= $totalQuotes ?></div>
        </div>
        <div class="stat-card" style="border: 1px solid #fee2e2;">
            <div class="stat-label" style="color:#dc2626">Overdue</div>
            <div class="stat-value" style="color:#dc2626"><?= $overdueInvoices ?></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5><i class="bi bi-graph-up-arrow" style="color:#4f46e5"></i> Invoice Overview</h5></div>
        <div class="table-wrap">
            <table class="ztable">
                <thead>
                    <tr><th>Category</th><th>Count</th><th>Total Amount</th><th>Received</th><th>Balance</th></tr>
                </thead>
                <tbody>
                    <?php foreach($stats as $label => $row): ?>
                        <tr>
                            <td style="font-weight:700"><?= $label ?></td>
                            <td><?= $row['cnt'] ?></td>
                            <td style="font-weight:700">₹<?= number_format($row['amt'], 2) ?></td>
                            <td style="color:#16a34a; font-weight:700">₹<?= number_format($row['recv'], 2) ?></td>
                            <td style="color:#dc2626; font-weight:700">₹<?= number_format($row['bal'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5><i class="bi bi-clock-history" style="color:#64748b"></i> Recent Invoices</h5>
            <a href="<?= base_url('invoice/invoices') ?>" class="btn btn-outline" style="padding: 6px 14px; font-size: 12px;">View All</a>
        </div>
        <div class="table-wrap">
            <table class="ztable">
                <thead>
                    <tr><th>Invoice#</th><th>Customer</th><th>Date</th><th>Total</th><th>Balance</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php foreach($recentInvoices as $inv): ?>
                        <tr>
                            <td style="font-weight:700; color:#4f46e5;"><a href="<?= base_url('invoice/invoices/show/' . $inv['id']) ?>" style="text-decoration:none; color:inherit;"><?= esc($inv['invoice_number']) ?></a></td>
                            <td style="font-weight:600"><?= esc($inv['cname']) ?></td>
                            <td><?= esc($inv['invoice_date']) ?></td>
                            <td style="font-weight:700">₹<?= number_format($inv['total'], 2) ?></td>
                            <td style="font-weight:700; color:#dc2626">₹<?= number_format($inv['balance_due'], 2) ?></td>
                            <td><span class="badge badge-<?= esc(strtolower($inv['status'])) ?>"><?= esc($inv['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>