<?= $this->extend('layout/main') ?>
<?php $page_title = 'Payments Received'; ?>
<?= $this->section('content') ?>

<style>
.payments-page{
    background:#fff;
    min-height:calc(100vh - 80px);
    font-family:Inter,system-ui,-apple-system,sans-serif;
}

.payments-top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:32px 28px 24px;
    border-bottom:1px solid #e5e7eb;
}

.payments-title{
    font-size:24px;
    font-weight:700;
    color:#111827;
}

.payments-title span{
    color:#2563eb;
    font-size:18px;
}

.payments-actions{
    display:flex;
    gap:12px;
    align-items:center;
}

.btn-new-payment{
    background:#4087f5;
    color:#fff;
    padding:11px 20px;
    border-radius:6px;
    text-decoration:none;
    font-size:15px;
    font-weight:500;
}

.btn-new-payment:hover{
    background:#2f76e5;
}

.btn-more{
    border:1px solid #d1d5db;
    background:#fff;
    padding:10px 14px;
    border-radius:6px;
    color:#111827;
    cursor:pointer;
}

.payments-table-wrap{
    overflow-x:auto;
}

.payments-table{
    width:100%;
    border-collapse:collapse;
    min-width:1200px;
}

.payments-table thead{
    background:#f8f8fb;
    border-bottom:1px solid #e5e7eb;
}

.payments-table th{
    padding:15px 18px;
    text-align:left;
    font-size:13px;
    color:#667085;
    font-weight:700;
    text-transform:uppercase;
    white-space:nowrap;
}

.payments-table td{
    padding:16px 18px;
    border-bottom:1px solid #edf0f5;
    font-size:15px;
    color:#111827;
    vertical-align:middle;
}

.payments-table tbody tr{
    cursor:pointer;
    transition:all .15s ease;
}

.payments-table tbody tr:hover{
    background:#f8fbff;
}

.checkbox-col{
    width:45px;
    text-align:center;
}

.payment-link{
    color:#2563eb;
    font-weight:700;
    text-decoration:none;
    font-size:15px;
}

.payment-link:hover{
    text-decoration:underline;
}

.status-paid{
    color:#16a34a;
    font-weight:700;
}

.amount{
    font-weight:700;
    text-align:right;
    white-space:nowrap;
}

.empty-state{
    text-align:center;
    padding:60px;
    color:#94a3b8;
}

input[type="checkbox"]{
    width:16px;
    height:16px;
    cursor:pointer;
}
</style>

<div class="payments-page">

    <div class="payments-top">

        <div class="payments-title">
            All Received Payments <span>⌄</span>
        </div>

        <div class="payments-actions">

            <a href="<?= base_url('invoice/payments/create') ?>"
               class="btn-new-payment">
                + New
            </a>

            <button type="button" class="btn-more">
                ⋯
            </button>

        </div>

    </div>

    <div class="payments-table-wrap">

        <table class="payments-table">

            <thead>

                <tr>

                    <th class="checkbox-col">
                        <input type="checkbox">
                    </th>

                    <th>Date</th>

                    <th>Payment #</th>

                    <th>Reference Number</th>

                    <th>Customer Name</th>

                    <th>Invoice#</th>

                    <th>Mode</th>

                    <th style="text-align:right;">
                        Amount
                    </th>

                    <th style="text-align:right;">
                        Unused Amount
                    </th>

                    <th>Status</th>

                </tr>

            </thead>

            <tbody>

            <?php if(!empty($payments)): ?>

                <?php foreach($payments as $p): ?>

                    <tr onclick="window.location.href='<?= base_url('invoice/payments/indexpage') ?>'">

                        <td class="checkbox-col"
                            onclick="event.stopPropagation();">

                            <input type="checkbox">

                        </td>

                        <td>
                            <?= date('d/m/Y', strtotime($p['payment_date'])) ?>
                        </td>

                        <td>

                            <a href="<?= base_url('invoice/payments/indexpage') ?>"
                               class="payment-link"
                               onclick="event.stopPropagation();">

                                <?= esc($p['payment_number']) ?>

                            </a>

                        </td>

                        <td>
                            <?= esc($p['reference'] ?? '-') ?>
                        </td>

                        <td>
                            <?= esc($p['cname'] ?? '-') ?>
                        </td>

                        <td>
                            <?= esc($p['invoice_number'] ?? '-') ?>
                        </td>

                        <td>
                            <?= esc(ucfirst($p['payment_mode'] ?? '-')) ?>
                        </td>

                        <td class="amount">
                            ₹<?= number_format($p['amount'], 2) ?>
                        </td>

                        <td class="amount">
                            ₹<?= number_format($p['unused_amount'] ?? $p['amount'], 2) ?>
                        </td>

                        <td>

                            <span class="status-paid">

                                <?= strtoupper($p['status'] ?? 'PAID') ?>

                            </span>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>

                    <td colspan="10">

                        <div class="empty-state">

                            No payments received yet.

                        </div>

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<?= $this->endSection() ?>