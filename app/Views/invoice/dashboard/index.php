<?= $this->extend('layout/main') ?>

<?php $page_title = 'Dashboard'; ?>

<?= $this->section('content') ?>

<style>
.invoice-dashboard{
    font-family:Inter,system-ui,-apple-system,sans-serif;
    color:#111827;
}

.section-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    margin-bottom:22px;
}

.section-header h4{
    margin:0;
    font-size:26px;
    font-weight:800;
    color:#111827;
}

.btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:6px;
    padding:10px 16px;
    border-radius:8px;
    text-decoration:none;
    border:1px solid transparent;
    font-size:14px;
    font-weight:600;
    cursor:pointer;
    line-height:1.2;
}

.btn-primary{
    background:#2563eb;
    color:#fff;
    border-color:#2563eb;
}

.btn-primary:hover{
    background:#1d4ed8;
    color:#fff;
}

.btn-outline{
    background:#fff;
    color:#111827;
    border-color:#d1d5db;
}

.btn-outline:hover{
    background:#f8fafc;
    color:#111827;
}

.btn-sm{
    padding:7px 12px;
    font-size:13px;
}

.stats-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:18px;
    margin-bottom:26px;
}

.stat-card{
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:12px;
    padding:20px 22px;
    box-shadow:0 4px 14px rgba(15,23,42,.04);
}

.stat-label{
    color:#64748b;
    font-size:14px;
    font-weight:600;
    margin-bottom:8px;
}

.stat-value{
    color:#111827;
    font-size:28px;
    font-weight:800;
    line-height:1;
}

.card{
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:12px;
    box-shadow:0 4px 14px rgba(15,23,42,.04);
    overflow:hidden;
}

.card-header{
    padding:16px 20px;
    border-bottom:1px solid #e5e7eb;
    background:#fff;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
}

.card-header h5{
    margin:0;
    font-size:18px;
    font-weight:800;
    color:#111827;
    display:flex;
    align-items:center;
    gap:8px;
}

.table-wrap{
    width:100%;
    overflow-x:auto;
}

.ztable{
    width:100%;
    border-collapse:collapse;
    min-width:760px;
}

.ztable thead{
    background:#f8fafc;
}

.ztable th{
    padding:13px 16px;
    text-align:left;
    color:#64748b;
    font-size:12px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.03em;
    border-bottom:1px solid #e5e7eb;
    white-space:nowrap;
}

.ztable td{
    padding:14px 16px;
    color:#111827;
    font-size:14px;
    border-bottom:1px solid #eef2f7;
    vertical-align:middle;
    white-space:nowrap;
}

.ztable tbody tr:hover{
    background:#f8fbff;
}

.badge{
    display:inline-flex;
    align-items:center;
    padding:4px 9px;
    border-radius:999px;
    font-size:11px;
    font-weight:800;
    text-transform:uppercase;
}

.badge-paid{
    background:#dcfce7;
    color:#15803d;
}

.badge-draft{
    background:#f1f5f9;
    color:#475569;
}

.badge-overdue{
    background:#fee2e2;
    color:#dc2626;
}

.badge-partially_paid{
    background:#fef3c7;
    color:#b45309;
}

.badge-sent{
    background:#dbeafe;
    color:#1d4ed8;
}

@media(max-width:1100px){
    .stats-grid{
        grid-template-columns:repeat(2,1fr);
    }
}

@media(max-width:650px){
    .section-header{
        flex-direction:column;
        align-items:flex-start;
    }

    .stats-grid{
        grid-template-columns:1fr;
    }

    .section-header h4{
        font-size:22px;
    }
}
</style>

<div class="invoice-dashboard">

    <div class="section-header">
        <h4>Dashboard</h4>

        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <a href="<?= base_url('invoice/quotes/create') ?>" class="btn btn-outline">
                <i class="bi bi-file-earmark-plus"></i> New Quote
            </a>

            <a href="<?= base_url('invoice/invoices/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus"></i> New Invoice
            </a>
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
            <div class="stat-label" style="color:#dc2626">Overdue</div>
            <div class="stat-value" style="color:#dc2626"><?= $overdueInvoices ?></div>
        </div>
    </div>

    <div class="card" style="margin-bottom:24px">
        <div class="card-header">
            <h5>
                <i class="bi bi-bar-chart-line" style="color:#5065e8"></i>
                Invoice Overview
            </h5>
        </div>

        <div class="table-wrap">
            <table class="ztable">
                <thead>
                    <tr>
                        <th></th>
                        <th>Invoices</th>
                        <th>Invoice Amount</th>
                        <th>Amount Received</th>
                        <th>Balance Due</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($stats as $label => $row): ?>
                        <tr>
                            <td style="font-weight:700;color:#374151"><?= $label ?></td>
                            <td><?= $row['cnt'] ?></td>
                            <td>₹<?= number_format($row['amt'], 2) ?></td>
                            <td style="color:#16a34a">₹<?= number_format($row['recv'], 2) ?></td>
                            <td style="color:#dc2626">₹<?= number_format($row['bal'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table> 
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>Recent Invoices</h5>

            <a href="<?= base_url('invoice/invoices') ?>" class="btn btn-outline btn-sm">
                View All
            </a>
        </div>

        <div class="table-wrap">
            <table class="ztable">
                <thead>
                    <tr>
                        <th>Invoice#</th>
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
                            <td>
                                <a href="<?= base_url('invoice/invoices/show/' . $inv['id']) ?>" style="color:#5065e8;font-weight:700;text-decoration:none">
                                    <?= esc($inv['invoice_number']) ?>
                                </a>
                            </td>

                            <td><?= esc($inv['cname']) ?></td>
                            <td><?= esc($inv['invoice_date']) ?></td>
                            <td>₹<?= number_format($inv['total'], 2) ?></td>
                            <td>₹<?= number_format($inv['balance_due'], 2) ?></td>

                            <td>
                                <span class="badge badge-<?= esc($inv['status']) ?>">
                                    <?= esc($inv['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if(empty($recentInvoices)): ?>
                        <tr>
                            <td colspan="6" style="text-align:center;color:#9ca3af;padding:32px">
                                No invoices yet
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?= $this->endSection() ?>