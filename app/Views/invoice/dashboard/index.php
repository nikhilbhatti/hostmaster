<?= $this->extend('layout/main') ?>

<?php $page_title = 'Dashboard'; ?>

<?= $this->section('content') ?>

<style>
.invoice-dashboard {
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    color: #334155;
    background-color: #f8fafc;
    padding: 16px;
}

.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 28px;
}

.section-header h4 {
    margin: 0;
    font-size: 26px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.02em;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 10px;
    text-decoration: none;
    border: 1px solid transparent;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    line-height: 1.2;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn-primary {
    background: #2563eb;
    color: #fff;
    border-color: #2563eb;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
}

.btn-primary:hover {
    background: #1d4ed8;
    border-color: #1d4ed8;
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25);
}

.btn-outline {
    background: #fff;
    color: #1e293b;
    border-color: #cbd5e1;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.btn-outline:hover {
    background: #f8fafc;
    border-color: #94a3b8;
    color: #0f172a;
    transform: translateY(-1px);
}

.btn-sm {
    padding: 6px 12px;
    font-size: 13px;
    border-radius: 8px;
}

/* 📊 Premium Stats Cards Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 32px;
}

.stat-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 22px 24px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 20px -8px rgba(15, 23, 42, 0.08);
}

/* Distinct color-coded left borders for each dynamic metric */
.stat-card.cust-card { border-left: 5px solid #3b82f6; }
.stat-card.inv-card { border-left: 5px solid #10b981; }
.stat-card.quo-card { border-left: 5px solid #8b5cf6; }
.stat-card.overdue-card { 
    border-left: 5px solid #ef4444; 
    background-color: #fff5f5; 
}

.stat-label {
    color: #64748b;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 8px;
}

.stat-value {
    color: #0f172a;
    font-size: 30px;
    font-weight: 800;
    line-height: 1;
}

/* 🏢 Standard Container Cards */
.card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
    overflow: hidden;
}

.card-header {
    padding: 18px 24px;
    border-bottom: 1px solid #f1f5f9;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.card-header h5 {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
}

.table-wrap {
    width: 100%;
    overflow-x: auto;
}

.ztable {
    width: 100%;
    border-collapse: collapse;
    min-width: 760px;
}

.ztable thead {
    background: #f8fafc;
}

.ztable th {
    padding: 14px 24px;
    text-align: left;
    color: #64748b;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    border-bottom: 1px solid #e2e8f0;
    white-space: nowrap;
}

.ztable td {
    padding: 16px 24px;
    color: #334155;
    font-size: 14px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    white-space: nowrap;
}

.ztable tbody tr:hover {
    background: #f8fafc;
}

/* 🏷️ Better Status Badges styling */
.badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    text-transform: capitalize;
}

.badge-paid {
    background: #dc262612; /* Fallback dynamic color map */
    color: #15803d;
    background-color: #dcfce7;
}

.badge-draft {
    background-color: #f1f5f9;
    color: #475569;
}

.badge-overdue, .badge-unpaid {
    background-color: #fee2e2;
    color: #dc2626;
}

.badge-partially_paid {
    background-color: #fef3c7;
    color: #b45309;
}

.badge-sent {
    background-color: #dbeafe;
    color: #1d4ed8;
}

@media(max-width:1100px){
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media(max-width:650px){
    .section-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .section-header h4 {
        font-size: 22px;
    }
}
</style>

<div class="invoice-dashboard">

    <div class="section-header">
        <h4>Dashboard</h4>

        <div style="display:flex;gap:10px;flex-wrap:wrap">
            <a href="<?= base_url('invoice/quotes/create') ?>" class="btn btn-outline">
                <i class="bi bi-file-earmark-plus"></i> New Quote
            </a>

            <a href="<?= base_url('invoice/invoices/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus"></i> New Invoice
            </a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card cust-card">
            <div class="stat-label">Total Customers</div>
            <div class="stat-value"><?= $totalCustomers ?></div>
        </div>

        <div class="stat-card inv-card">
            <div class="stat-label">Total Invoices</div>
            <div class="stat-value"><?= $totalInvoices ?></div>
        </div>

        <div class="stat-card quo-card">
            <div class="stat-label">Total Quotes</div>
            <div class="stat-value"><?= $totalQuotes ?></div>
        </div>

        <div class="stat-card overdue-card">
            <div class="stat-label" style="color:#dc2626">Overdue</div>
            <div class="stat-value" style="color:#dc2626"><?= $overdueInvoices ?></div>
        </div>
    </div>

    <div class="card" style="margin-bottom:32px">
        <div class="card-header">
            <h5>
                <i class="bi bi-bar-chart-line-fill" style="color:#2563eb"></i>
                Invoice Overview
            </h5>
        </div>

        <div class="table-wrap">
            <table class="ztable">
                <thead>
                    <tr>
                        <th style="padding-left: 24px;">Status Category</th>
                        <th>Invoices</th>
                        <th>Invoice Amount</th>
                        <th>Amount Received</th>
                        <th>Balance Due</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($stats as $label => $row): ?>
                        <tr>
                            <td style="font-weight:700; color:#1e293b; padding-left: 24px;"><?= $label ?></td>
                            <td style="font-weight: 600;"><?= $row['cnt'] ?></td>
                            <td style="font-weight: 600; color: #0f172a;">₹<?= number_format($row['amt'], 2) ?></td>
                            <td style="color:#16a34a; font-weight: 600;">₹<?= number_format($row['recv'], 2) ?></td>
                            <td style="color:#dc2626; font-weight: 600;">₹<?= number_format($row['bal'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table> 
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5><i class="bi bi-list-task" style="color:#64748b"></i> Recent Invoices</h5>

            <a href="<?= base_url('invoice/invoices') ?>" class="btn btn-outline btn-sm">
                View All
            </a>
        </div>

        <div class="table-wrap">
            <table class="ztable">
                <thead>
                    <tr>
                        <th style="padding-left: 24px;">Invoice#</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Balance</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($recentInvoices as $inv): ?>
                        <tr>
                            <td style="padding-left: 24px;">
                                <a href="<?= base_url('invoice/invoices/show/' . $inv['id']) ?>" style="color:#2563eb; font-weight:700; text-decoration:none;">
                                    <?= esc($inv['invoice_number']) ?>
                                </a>
                            </td>

                            <td style="font-weight: 600; color: #1e293b;"><?= esc($inv['cname']) ?></td>
                            <td style="color: #64748b; font-size: 13px;"><?= esc($inv['invoice_date']) ?></td>
                            <td style="font-weight: 700; color: #0f172a;">₹<?= number_format($inv['total'], 2) ?></td>
                            <td style="color:#dc2626; font-weight:700;">₹<?= number_format($inv['balance_due'], 2) ?></td>

                            <td>
                                <span class="badge badge-<?= esc(strtolower($inv['status'])) ?>">
                                    <?= esc($inv['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if(empty($recentInvoices)): ?>
                        <tr>
                            <td colspan="6" style="text-align:center; color:#94a3b8; padding:48px; font-weight: 500;">
                                <i class="bi bi-inbox" style="font-size: 22px; display:block; margin-bottom: 4px;"></i> No invoices available yet
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?= $this->endSection() ?>