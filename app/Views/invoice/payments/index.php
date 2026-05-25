<?= $this->extend('layout/main') ?>
<?php $page_title = 'Payments Received'; ?>
<?= $this->section('content') ?>

<style>
.zoho-split-dashboard {
    display: grid;
    grid-template-columns: 350px 1fr;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    min-height: calc(100vh - 100px);
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    margin: 10px 0;
    overflow: hidden;
}
.zoho-sidebar {
    background: #ffffff;
    border-right: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    height: calc(100vh - 100px);
}
.sidebar-header-row {
    padding: 16px 20px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8fafc;
}
.sidebar-main-title { font-size: 15px; font-weight: 600; color: #1e293b; }
.btn-zoho-new {
    background: #2563eb;
    color: #ffffff;
    padding: 5px 12px;
    font-size: 12px;
    font-weight: 500;
    border-radius: 4px;
    text-decoration: none !important;
}
.btn-zoho-new:hover { background: #1d4ed8; color:#fff; }

.sidebar-scroll-stack { overflow-y: auto; flex: 1; }
.payment-master-card {
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    text-decoration: none !important;
    color: inherit !important;
    transition: background 0.1s ease;
}
.payment-master-card:hover { background: #f8fafc; }
.payment-master-card.active-selected {
    background: #f0f6ff;
    border-left: 4px solid #2563eb;
}
.meta-card-left { display: flex; flex-direction: column; gap: 3px; }
.card-cust-name { font-weight: 600; font-size: 13.5px; color: #1e293b; }
.card-sub-hints { font-size: 12px; color: #64748b; }
.card-status-badge {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 2px;
}
.status-paid { color:#16a34a; }
.status-partial { color:#d97706; }
.status-unpaid { color:#dc2626; }
.card-meta-right { text-align: right; font-weight: 600; font-size: 13.5px; color: #0f172a; }
.card-meta-small { display:block; font-size:11px; color:#64748b; font-weight:400; margin-top:3px; }

.zoho-viewer-workspace {
    background: #f8fafc;
    display: flex;
    flex-direction: column;
    height: calc(100vh - 100px);
    overflow-y: auto;
}
.viewer-top-ribbon {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    padding: 12px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 10;
}
.ribbon-doc-title { font-size: 18px; font-weight: 600; color: #0f172a; }
.ribbon-actions-group { display: flex; gap: 8px; flex-wrap:wrap; }
.btn-ribbon-utility {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #334155;
    padding: 6px 14px;
    font-size: 13px;
    font-weight: 500;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none !important;
}
.btn-ribbon-utility:hover { background: #f8fafc; border-color: #94a3b8; }
.btn-ribbon-delete { color: #dc2626; }
.btn-ribbon-delete:hover { background: #fef2f2; border-color: #fca5a5; }

.whats-next-banner {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 16px 20px;
    margin: 20px 24px 0 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.banner-text-side { font-size: 13px; color: #334155; }
.banner-text-side strong {
    color: #0f172a;
    font-weight: 600;
    display: block;
    font-size: 11px;
    margin-bottom: 2px;
    letter-spacing: 0.5px;
}
.btn-action-paid-trigger {
    background: #2563eb;
    color: #fff;
    border: 1px solid #2563eb;
    padding: 5px 12px;
    font-size: 12.5px;
    font-weight: 500;
    border-radius: 4px;
    text-decoration: none !important;
}
.btn-action-paid-trigger:hover { background: #1d4ed8; color:#fff; }

.canvas-scroll-container { padding: 24px; }
.receipt-a4-sheet {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    max-width: 850px;
    width: 100%;
    margin: 0 auto;
    box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    padding: 50px 60px;
    box-sizing: border-box;
    position: relative;
    min-height: 842px;
}
.draft-corner-tag {
    position: absolute;
    top: 24px;
    left: -12px;
    background: #94a3b8;
    color: #ffffff;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 28px;
    transform: rotate(-45deg);
    text-transform: uppercase;
    letter-spacing: 1px;
}
.sheet-header-flex { display: flex; justify-content: space-between; margin-bottom: 45px; }
.brand-main-logo {
    font-size: 26px;
    font-weight: 700;
    color: #0f172a;
    text-transform: lowercase;
    margin-bottom: 4px;
}
.brand-address-text { font-size: 13px; color: #475569; line-height: 1.6; }
.sheet-main-title {
    font-size: 22px;
    font-weight: 400;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 1px;
    text-align: right;
}
.sheet-meta-matrix {
    display: grid;
    grid-template-columns: 1fr 260px;
    gap: 40px;
    margin-top: 35px;
    align-items: flex-start;
}
.matrix-data-table { width: 100%; border-collapse: collapse; }
.matrix-data-table td { padding: 8px 0; font-size: 13px; color: #334155; vertical-align: top; }
.matrix-data-table td:first-child { color: #64748b; width: 140px; }
.matrix-data-table td strong { color: #0f172a; font-weight: 500; }

.zoho-green-amount-box {
    background: #71a062 !important;
    color: #ffffff !important;
    padding: 20px;
    border-radius: 4px;
    text-align: left;
}
.green-box-label {
    font-size: 11px;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
    font-weight: 500;
}
.green-box-sum { font-size: 26px; font-weight: 700; }

.summary-grid {
    display:grid;
    grid-template-columns: repeat(3, 1fr);
    gap:12px;
    margin-top:30px;
}
.summary-box {
    border:1px solid #e2e8f0;
    background:#f8fafc;
    border-radius:6px;
    padding:14px;
}
.summary-label {
    font-size:11px;
    color:#64748b;
    text-transform:uppercase;
    letter-spacing:.5px;
    margin-bottom:5px;
}
.summary-value {
    font-size:17px;
    color:#0f172a;
    font-weight:700;
}

.allocation-header-title {
    font-size: 12px;
    font-weight: 600;
    color: #0f172a;
    margin-top: 50px;
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.allocation-grid-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.allocation-grid-table th {
    border-bottom: 2px solid #e2e8f0;
    padding: 10px 0;
    text-align: left;
    color: #64748b;
    font-weight: 500;
}
.allocation-grid-table td {
    border-bottom: 1px solid #f1f5f9;
    padding: 14px 0;
    color: #334155;
}
.empty-center-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 80px 40px;
    color: #94a3b8;
    text-align: center;
    font-style: italic;
}

@media print {
    html, body {
        background: #ffffff !important;
        margin: 0 !important;
        padding: 0 !important;
        height: auto !important;
    }
    .zoho-sidebar,
    .viewer-top-ribbon,
    .whats-next-banner {
        display: none !important;
    }
    .zoho-split-dashboard,
    .zoho-viewer-workspace,
    .canvas-scroll-container {
        display: block !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
        height:auto !important;
        overflow:visible !important;
    }
    .receipt-a4-sheet {
        margin: 0 auto !important;
        padding: 40px 50px !important;
        box-shadow: none !important;
        border: none !important;
        width: 100% !important;
        max-width: 100% !important;
        min-height: auto !important;
    }
    * {
        -webkit-print-color-adjust: exact !important;
        color-adjust: exact !important;
    }
}
</style>

<?php
    /*
    IMPORTANT:
    Controller se $payments invoice-wise grouped array aana chahiye.
    Recommended fields:
    invoice_id, invoice_number, cname, total, paid_amount, balance_due, status, last_payment_date, payment_count
    */

    $active_id = service('request')->getGet('active_id');

    if (empty($active_id) && !empty($payments)) {
        $active_id = $payments[0]['invoice_id'] ?? $payments[0]['id'] ?? null;
    }

    $current_p = null;

    if (!empty($payments)) {
        foreach ($payments as $pay) {
            $row_id = $pay['invoice_id'] ?? $pay['id'] ?? null;

            if ($row_id == $active_id) {
                $current_p = $pay;
                break;
            }
        }

        if (!$current_p) {
            $current_p = $payments[0];
        }
    }

    $invoiceId      = $current_p['invoice_id'] ?? null;
    $invoiceNumber  = $current_p['invoice_number'] ?? '--';
    $customerName   = $current_p['cname'] ?? 'Unknown Customer';
    $invoiceTotal   = (float)($current_p['total'] ?? 0);
    $paidAmount     = (float)($current_p['paid_amount'] ?? 0);
    $balanceDue     = (float)($current_p['balance_due'] ?? max(0, $invoiceTotal - $paidAmount));
    $status         = strtolower($current_p['status'] ?? 'unpaid');
    $lastPayDate    = $current_p['last_payment_date'] ?? null;
    $paymentCount   = (int)($current_p['payment_count'] ?? 0);

    $statusClass = 'status-unpaid';
    if ($status == 'paid') {
        $statusClass = 'status-paid';
    } elseif ($status == 'partially_paid' || $status == 'partial') {
        $statusClass = 'status-partial';
    }
?>

<div class="zoho-split-dashboard">

    <div class="zoho-sidebar">
        <div class="sidebar-header-row">
            <span class="sidebar-main-title">Invoice Payment</span>
            <a href="<?= base_url('invoice/payments/create') ?>" class="btn-zoho-new">
                <i class="bi bi-plus"></i> New
            </a>
        </div>

        <div class="sidebar-scroll-stack">
            <?php if (!empty($payments)): ?>
                <?php foreach ($payments as $pay): ?>
                    <?php
                        $rowId       = $pay['invoice_id'] ?? $pay['id'] ?? null;
                        $rowTotal    = (float)($pay['total'] ?? 0);
                        $rowPaid     = (float)($pay['paid_amount'] ?? 0);
                        $rowBalance  = (float)($pay['balance_due'] ?? max(0, $rowTotal - $rowPaid));
                        $rowStatus   = strtolower($pay['status'] ?? 'unpaid');

                        $rowStatusClass = 'status-unpaid';
                        if ($rowStatus == 'paid') {
                            $rowStatusClass = 'status-paid';
                        } elseif ($rowStatus == 'partially_paid' || $rowStatus == 'partial') {
                            $rowStatusClass = 'status-partial';
                        }
                    ?>

                    <a href="<?= base_url('invoice/payments?active_id=' . $rowId) ?>"
                       class="payment-master-card <?= ($rowId == $active_id) ? 'active-selected' : '' ?>">

                        <div class="meta-card-left">
                            <span class="card-cust-name"><?= esc($pay['cname'] ?? 'Unknown Customer') ?></span>

                            <span class="card-sub-hints">
                                <?= esc($pay['invoice_number'] ?? '--') ?>
                                <?php if (!empty($pay['last_payment_date'])): ?>
                                    • Last: <?= date('d/m/Y', strtotime($pay['last_payment_date'])) ?>
                                <?php endif; ?>
                            </span>

                            <span class="card-status-badge <?= $rowStatusClass ?>">
                                <?= esc(str_replace('_', ' ', strtoupper($rowStatus))) ?>
                            </span>
                        </div>

                        <div class="card-meta-right">
                            ₹<?= number_format($rowPaid, 2) ?>
                            <span class="card-meta-small">
                                Bal: ₹<?= number_format($rowBalance, 2) ?>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="padding: 30px; text-align: center; color: #94a3b8; font-size: 13px;">
                    No invoice payments found.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="zoho-viewer-workspace">

        <?php if ($current_p): ?>

            <div class="viewer-top-ribbon">
                <div class="ribbon-doc-title">
                    Invoice Payment: <?= esc($invoiceNumber) ?>
                </div>

                <div class="ribbon-actions-group">
                    <?php if (!empty($invoiceId)): ?>
                        <a href="<?= base_url('invoice/payments/create-for-invoice/' . $invoiceId) ?>" class="btn-ribbon-utility">
                            ➕ Add Payment
                        </a>

                        <a href="<?= base_url('invoice/payments/history/' . $invoiceId) ?>" class="btn-ribbon-utility">
                            📜 Payment History
                        </a>
                    <?php endif; ?>

                    <button type="button" class="btn-ribbon-utility" onclick="window.print()">
                        🖨️ PDF / Print
                    </button>
                </div>
            </div>

            <div class="whats-next-banner">
                <div class="banner-text-side">
                    <strong>PAYMENT STATUS</strong>
                    <?= esc($customerName) ?> के invoice
                    <b><?= esc($invoiceNumber) ?></b>
                    में ₹<?= number_format($paidAmount, 2) ?> received और
                    ₹<?= number_format($balanceDue, 2) ?> pending है.
                </div>

                <div>
                    <?php if ($status == 'paid'): ?>
                        <span class="btn-action-paid-trigger" style="background:#16a34a;border-color:#16a34a;">
                            Paid
                        </span>
                    <?php else: ?>
                        <a href="<?= base_url('invoice/payments/create-for-invoice/' . $invoiceId) ?>" class="btn-action-paid-trigger">
                            Receive Payment
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="canvas-scroll-container">
                <div class="receipt-a4-sheet">

                    <div class="draft-corner-tag <?= $statusClass ?>">
                        <?= esc(str_replace('_', ' ', $status)) ?>
                    </div>

                    <div class="sheet-header-flex">
                        <div class="brand-identity-box">
                            <div class="brand-main-logo">slysis</div>
                            <div class="brand-address-text">
                                Himachal Pradesh<br>
                                India<br>
                                91-7876728830<br>
                                bhattinikhil530@gmail.com
                            </div>
                        </div>

                        <div class="sheet-main-title">
                            Invoice Payment Ledger
                        </div>
                    </div>

                    <hr style="border: 0; border-top: 1px solid #f1f5f9; margin-bottom: 20px;">

                    <div class="sheet-meta-matrix">
                        <div>
                            <table class="matrix-data-table">
                                <tr>
                                    <td>Invoice Number</td>
                                    <td><strong><?= esc($invoiceNumber) ?></strong></td>
                                </tr>

                                <tr>
                                    <td>Customer</td>
                                    <td><strong><?= esc($customerName) ?></strong></td>
                                </tr>

                                <tr>
                                    <td>Last Payment</td>
                                    <td>
                                        <strong>
                                            <?= !empty($lastPayDate) ? date('d/m/Y', strtotime($lastPayDate)) : '--' ?>
                                        </strong>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Total Payments</td>
                                    <td><strong><?= $paymentCount ?></strong></td>
                                </tr>

                                <tr>
                                    <td>Status</td>
                                    <td>
                                        <strong class="<?= $statusClass ?>">
                                            <?= esc(str_replace('_', ' ', strtoupper($status))) ?>
                                        </strong>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="zoho-green-amount-box">
                            <div class="green-box-label">Amount Received</div>
                            <div class="green-box-sum">
                                ₹<?= number_format($paidAmount, 2) ?>
                            </div>
                        </div>
                    </div>

                    <div class="summary-grid">
                        <div class="summary-box">
                            <div class="summary-label">Invoice Total</div>
                            <div class="summary-value">₹<?= number_format($invoiceTotal, 2) ?></div>
                        </div>

                        <div class="summary-box">
                            <div class="summary-label">Paid Amount</div>
                            <div class="summary-value" style="color:#16a34a;">
                                ₹<?= number_format($paidAmount, 2) ?>
                            </div>
                        </div>

                        <div class="summary-box">
                            <div class="summary-label">Balance Due</div>
                            <div class="summary-value" style="color:#dc2626;">
                                ₹<?= number_format($balanceDue, 2) ?>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 45px; max-width: 350px;">
                        <div style="font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                            Bill To
                        </div>

                        <div style="font-weight: 600; font-size: 14px; color: #2563eb;">
                            <?= esc($customerName) ?>
                        </div>

                        <div style="font-size: 13px; color: #475569; margin-top: 2px; line-height: 1.4;">
                            Individual Account / Corporate Customer
                        </div>
                    </div>

                    <div class="allocation-header-title">Invoice Payment Summary</div>

                    <table class="allocation-grid-table">
                        <thead>
                            <tr>
                                <th style="width: 35%;">Invoice Number</th>
                                <th style="text-align: right; width: 20%;">Invoice Amount</th>
                                <th style="text-align: right; width: 20%;">Received</th>
                                <th style="text-align: right; width: 20%;">Pending</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td style="font-weight: 600; color: #0f172a;">
                                    <span style="color:#2563eb;">
                                        <?= esc($invoiceNumber) ?>
                                    </span>
                                </td>

                                <td style="text-align: right;">
                                    ₹<?= number_format($invoiceTotal, 2) ?>
                                </td>

                                <td style="text-align: right; font-weight: 600; color: #16a34a;">
                                    ₹<?= number_format($paidAmount, 2) ?>
                                </td>

                                <td style="text-align: right; font-weight: 600; color: #dc2626;">
                                    ₹<?= number_format($balanceDue, 2) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div style="margin-top: 35px;">
                        <?php if (!empty($invoiceId)): ?>
                            <a href="<?= base_url('invoice/payments/history/' . $invoiceId) ?>"
                               style="display:inline-block;background:#0f172a;color:#fff;padding:9px 14px;border-radius:5px;text-decoration:none;font-size:13px;">
                                View Full Payment History
                            </a>
                        <?php endif; ?>
                    </div>

                    <div style="margin-top: 120px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 15px;">
                        Thank you for your business! This is a system generated invoice payment ledger by slysis.
                    </div>

                </div>
            </div>

        <?php else: ?>

            <div class="empty-center-state">
                <i class="bi bi-inbox" style="font-size: 32px; margin-bottom: 10px; color:#cbd5e1;"></i>
                No payment records selected or available to preview.
            </div>

        <?php endif; ?>

    </div>
</div>

<?= $this->endSection() ?>