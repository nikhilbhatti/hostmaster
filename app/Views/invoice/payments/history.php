<?= $this->extend('layout/main') ?>
<?php $page_title = 'Payment History'; ?>
<?= $this->section('content') ?>

<style>
.history-page{background:#fff;min-height:calc(100vh - 80px);font-family:Inter,system-ui,-apple-system,sans-serif;color:#111827}
.history-top{padding:28px;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center;gap:15px}
.history-title{font-size:24px;font-weight:700}
.history-sub{font-size:14px;color:#64748b;margin-top:5px}
.btn-back{background:#fff;border:1px solid #d1d5db;color:#111827;padding:9px 14px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600}
.btn-back:hover{background:#f8fafc;color:#111827}
.summary-wrap{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;padding:24px 28px;background:#f8fafc;border-bottom:1px solid #e5e7eb}
.summary-card{background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:18px}
.summary-label{font-size:12px;color:#64748b;text-transform:uppercase;font-weight:700;letter-spacing:.5px;margin-bottom:8px}
.summary-value{font-size:20px;font-weight:800;color:#111827}
.green{color:#16a34a!important}
.red{color:#dc2626!important}
.blue{color:#2563eb!important}
.status-badge{display:inline-block;padding:7px 12px;border-radius:20px;font-size:12px;font-weight:800;text-transform:uppercase}
.status-paid{background:#dcfce7;color:#166534}
.status-partially_paid{background:#fef3c7;color:#92400e}
.status-unpaid{background:#fee2e2;color:#991b1b}
.customer-box{padding:22px 28px;border-bottom:1px solid #e5e7eb}
.customer-name{font-size:18px;font-weight:700;color:#2563eb}
.customer-meta{font-size:14px;color:#64748b;margin-top:5px}
.table-wrap{padding:24px 28px;overflow-x:auto}
.history-table{width:100%;border-collapse:collapse;min-width:900px}
.history-table thead{background:#f8fafc}
.history-table th{padding:14px 16px;text-align:left;font-size:12px;color:#64748b;text-transform:uppercase;font-weight:800;border-bottom:1px solid #e5e7eb}
.history-table td{padding:15px 16px;border-bottom:1px solid #edf0f5;font-size:14px;color:#111827}
.amount{text-align:right;font-weight:800;white-space:nowrap}
.empty-state{text-align:center;padding:60px;color:#94a3b8}
.timeline-dot{width:10px;height:10px;background:#2563eb;border-radius:50%;display:inline-block;margin-right:8px}
.footer-actions{padding:0 28px 28px;display:flex;gap:12px}
.btn-add{background:#2563eb;color:#fff;padding:10px 15px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:700}
.btn-add:hover{background:#1d4ed8;color:#fff}
.btn-print{background:#111827;color:#fff;border:0;padding:10px 15px;border-radius:6px;font-size:14px;font-weight:700;cursor:pointer}
.disabled-action{color:#9ca3af!important;cursor:not-allowed;text-decoration:none!important;font-weight:700}
.btn-disabled{background:#9ca3af!important;color:#fff!important;cursor:not-allowed;pointer-events:none}
@media(max-width:900px){.summary-wrap{grid-template-columns:1fr 1fr}.history-top{flex-direction:column;align-items:flex-start}}
@media(max-width:600px){.summary-wrap{grid-template-columns:1fr}}
@media print{.btn-back,.footer-actions{display:none!important}.history-page{background:#fff}.summary-wrap{background:#fff}}
</style>

<?php
$total        = (float)($invoice['total'] ?? 0);
$paid         = (float)($invoice['paid_amount'] ?? 0);
$balance      = (float)($invoice['balance_due'] ?? max(0, $total - $paid));
$status       = strtolower($invoice['status'] ?? 'unpaid');
$statusClass  = 'status-' . $status;
$isPaid       = ($status === 'paid');
$sourceInvoice = isset($source) && strtolower(trim($source)) === 'invoice';
$returnQuery = $sourceInvoice ? '?source=invoice&return=history' : '?return=history';
$isDirectRestricted = !$sourceInvoice && $status === 'trashed';
$historyBackUrl = $sourceInvoice
    ? base_url('invoice/invoices/show/' . $invoice['id'] . '?source=invoice')
    : base_url('invoice/payments');
?>

<div class="history-page">

    <div class="history-top">
        <div>
            <div class="history-title">
                Payment History - <?= esc($invoice['invoice_number'] ?? '-') ?>
            </div>
            <div class="history-sub">
                Complete payment ledger for this invoice
            </div>
        </div>

        <a href="<?= esc($historyBackUrl) ?>" class="btn-back">
            ← Back to Payments
        </a>
    </div>

    <div class="summary-wrap">

        <div class="summary-card">
            <div class="summary-label">Invoice Total</div>
            <div class="summary-value">
                ₹<?= number_format($total, 2) ?>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-label">Paid Amount</div>
            <div class="summary-value green">
                ₹<?= number_format($paid, 2) ?>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-label">Pending Balance</div>
            <div class="summary-value red">
                ₹<?= number_format($balance, 2) ?>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-label">Status</div>
            <span class="status-badge <?= esc($statusClass) ?>">
                <?= strtoupper(str_replace('_', ' ', $status)) ?>
            </span>
        </div>

    </div>

    <div class="customer-box">
        <div class="customer-name">
            <?= esc($invoice['cname'] ?? '-') ?>
        </div>

        <div class="customer-meta">
            <?= esc($invoice['company_name'] ?? 'No Company') ?>
            <?php if(!empty($invoice['email'])): ?>
                | <?= esc($invoice['email']) ?>
            <?php endif; ?>
            <?php if(!empty($invoice['phone'])): ?>
                | <?= esc($invoice['phone']) ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($isDirectRestricted): ?>
        <div style="margin:0 28px 24px;padding:16px;border:1px solid #f1c40f;background:#fffbeb;border-radius:10px;color:#92400e;font-size:14px;">
            ⚠️ This invoice is deleted/trashed. Payment edit/delete is blocked on this screen.
        </div>
    <?php endif; ?>

    <div class="table-wrap">

        <table class="history-table">

            <thead>
                <tr>
                    <th>#</th>
                    <th>Payment Date</th>
                    <th>Payment Number</th>
                    <th>Mode</th>
                    <th>Reference</th>
                    <th>Notes</th>
                    <th style="text-align:right;">Amount Received</th>
                    <th style="text-align:right;">Running Balance</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php if(!empty($payments)): ?>

                <?php
                    $runningPaid = 0;
                    $i = 1;
                ?>

                <?php foreach($payments as $p): ?>

                    <?php
                        $amount = (float)($p['amount'] ?? 0);
                        $runningPaid += $amount;
                        $runningBalance = max(0, $total - $runningPaid);
                    ?>

                    <tr>
                        <td>
                            <span class="timeline-dot"></span><?= $i++ ?>
                        </td>

                        <td>
                            <?= !empty($p['payment_date']) ? date('d/m/Y', strtotime($p['payment_date'])) : '-' ?>
                        </td>

                        <td class="blue" style="font-weight:700;">
                            <?= esc($p['payment_number'] ?? '-') ?>
                        </td>

                        <td>
                            <?= esc(ucwords(str_replace('_', ' ', $p['payment_mode'] ?? '-'))) ?>
                        </td>

                        <td>
                            <?= !empty($p['reference']) ? esc($p['reference']) : '-' ?>
                        </td>

                        <td>
                            <?= !empty($p['notes']) ? esc($p['notes']) : '-' ?>
                        </td>

                        <td class="amount green">
                            ₹<?= number_format($amount, 2) ?>
                        </td>

                        <td class="amount red">
                            ₹<?= number_format($runningBalance, 2) ?>
                        </td>

                        <td>
                            <?php if($isDirectRestricted): ?>
                                <span class="disabled-action">Edit</span>
                                |
                                <span class="disabled-action">Delete</span>
                            <?php else: ?>
                                <a href="<?= base_url('invoice/payments/edit/' . $p['id']) . $returnQuery ?>" style="color:#2563eb;font-weight:700;text-decoration:none;">Edit</a>
                                |
                                <a href="<?= base_url('invoice/payments/delete/' . $p['id']) . $returnQuery ?>" onclick="return confirm('Are you sure you want to delete this payment? Invoice balance will be updated.')" style="color:#dc2626;font-weight:700;text-decoration:none;">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            No payment history found for this invoice.
                        </div>
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

    <div class="footer-actions">
        <?php if($isPaid): ?>
            <a href="javascript:void(0)" class="btn-add btn-disabled">
                + Receive More Payment
            </a>
        <?php else: ?>
            <a href="<?= base_url('invoice/payments/create-for-invoice/' . ($invoice['id'] ?? 0)) ?>" class="btn-add">
                + Receive More Payment
            </a>
        <?php endif; ?>

        <button type="button" class="btn-print" onclick="window.print()">
            Print History
        </button>
    </div>

</div>

<?= $this->endSection() ?>