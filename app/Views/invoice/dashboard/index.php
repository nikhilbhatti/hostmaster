<?= $this->extend('layout/main') ?>
<?php $page_title = 'Dashboard'; ?>

<?= $this->section('content') ?>

<style>
    .invoice-dashboard { font-family: 'Inter', sans-serif; background-color: #f1f5f9; padding: 24px; min-height: 100vh; }
    .section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
    .section-header h4 { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0; }

    /* Buttons */
    .btn { padding: 10px 16px; border-radius: 12px; font-weight: 600; font-size: 14px; transition: 0.3s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
    .btn-primary { background: #4f46e5; color: #fff; }
    .btn-primary:hover { background: #4338ca; color: #fff; }
    .btn-outline { background: #fff; color: #374151; border: 1px solid #d1d5db; }
    .btn-outline:hover { background: #f9fafb; }

    /* Stats Grid */
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 28px; }
    .stat-card { background: #fff; padding: 20px; border-radius: 16px; border: 1px solid #e5e7eb; transition: 0.3s; }
    .stat-card:hover { border-color: #4f46e5; }
    .stat-label { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 8px; }
    .stat-value { font-size: 28px; font-weight: 800; color: #111827; }

    /* Card & Table */
    .card { background: #fff; border-radius: 16px; border: 1px solid #e5e7eb; margin-bottom: 24px; overflow: hidden; }
    .card-header { padding: 20px; border-bottom: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center; }
    .card-header h5 { font-size: 16px; font-weight: 700; color: #111827; margin: 0; }
    .ztable { width: 100%; border-collapse: collapse; }
    .ztable th { padding: 14px 20px; text-align: left; background: #f9fafb; color: #6b7280; font-size: 12px; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #e5e7eb; }
    .ztable td { padding: 16px 20px; border-bottom: 1px solid #f3f4f6; color: #374151; font-size: 14px; }
    
    /* Badges */
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .badge-paid { background: #dcfce7; color: #166534; }
    .badge-sent { background: #dbeafe; color: #1e40af; }
    .badge-overdue { background: #fee2e2; color: #991b1b; }
    .badge-draft { background: #f3f4f6; color: #374151; }
    .badge-partially_paid { background: #fef3c7; color: #92400e; }
</style>

<div class="invoice-dashboard">
    <div class="section-header">
        <h4>Dashboard Overview</h4>
        <div style="display:flex; gap:10px;">
            <a href="<?= base_url('invoice/quotes/create') ?>" class="btn btn-outline">New Quote</a>
            <a href="<?= base_url('invoice/invoices/create') ?>" class="btn btn-primary">+ New Invoice</a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Customers</div>
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
        <div class="stat-card">
            <div class="stat-label" style="color:#dc2626;">Overdue</div>
            <div class="stat-value" style="color:#dc2626;"><?= $overdueInvoices ?></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5>Invoice Overview</h5></div>
        <div class="table-wrap"><table class="ztable">
            <thead><tr><th>Status</th><th>Count</th><th>Total Amount</th><th>Received</th><th>Balance</th></tr></thead>
            <tbody>
                <?php foreach($stats as $label => $row): ?>
                <tr>
                    <td style="font-weight:700; color:#111827;"><?= $label ?></td>
                    <td><?= $row['cnt'] ?></td>
                    <td>₹<?= number_format($row['amt'], 2) ?></td>
                    <td style="color:#16a34a; font-weight:600;">₹<?= number_format($row['recv'], 2) ?></td>
                    <td style="color:#dc2626; font-weight:600;">₹<?= number_format($row['bal'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div>
    </div>

    <div class="card">
        <div class="card-header"><h5>Recent Invoices</h5><a href="<?= base_url('invoice/invoices') ?>" class="btn btn-outline" style="padding: 6px 12px; font-size: 12px;">View All</a></div>
        <div class="table-wrap"><table class="ztable">
            <thead><tr><th>Invoice#</th><th>Customer</th><th>Date</th><th>Total</th><th>Balance</th><th>Status</th></tr></thead>
            <tbody>
                <?php foreach($recentInvoices as $inv): ?>
                <tr>
                    <td style="font-weight:700; color:#4f46e5;"><?= esc($inv['invoice_number']) ?></td>
                    <td><?= esc($inv['cname']) ?></td>
                    <td><?= esc($inv['invoice_date']) ?></td>
                    <td>₹<?= number_format($inv['total'], 2) ?></td>
                    <td style="color:#dc2626; font-weight:600;">₹<?= number_format($inv['balance_due'], 2) ?></td>
                    <td><span class="badge badge-<?= esc(strtolower($inv['status'])) ?>"><?= esc($inv['status']) ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div>
    </div>
</div>

<?= $this->endSection() ?>