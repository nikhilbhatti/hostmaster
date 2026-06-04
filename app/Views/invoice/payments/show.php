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
    color:#fff;
}

.payments-table-wrap{
    overflow-x:auto;
}

.payments-table{
    width:100%;
    border-collapse:collapse;
    min-width:1500px;
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
    font-size:14px;
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

.status-badge{
    padding:6px 10px;
    border-radius:20px;
    font-size:11px;
    font-weight:700;
    display:inline-block;
    text-transform:uppercase;
    letter-spacing:.5px;
}

.status-paid{
    background:#dcfce7;
    color:#166534;
}

.status-partial{
    background:#fef3c7;
    color:#92400e;
}

.status-unpaid{
    background:#fee2e2;
    color:#991b1b;
}

.amount{
    font-weight:700;
    text-align:right;
    white-space:nowrap;
}

.amount-green{
    color:#16a34a;
}

.amount-red{
    color:#dc2626;
}

.history-btn{
    background:#111827;
    color:#fff;
    padding:8px 12px;
    border-radius:5px;
    text-decoration:none;
    font-size:12px;
    font-weight:600;
    display:inline-block;
}

.history-btn:hover{
    background:#000;
    color:#fff;
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

.payments-bulk-actions{
    display:flex;
    align-items:center;
    justify-content:flex-end;
    gap:12px;
    padding:14px 28px;
    background:#f8fafc;
    border:1px solid #e5e7eb;
    border-radius:8px;
    margin:0 28px 18px;
}

.btn-delete-selected{
    background:#dc2626;
    color:#fff;
    border:none;
    padding:10px 18px;
    border-radius:6px;
    font-size:14px;
    font-weight:700;
    cursor:pointer;
}

.btn-delete-selected:hover{
    background:#b91c1c;
}
</style>

<div class="payments-page">

    <div class="payments-top">

        <div class="payments-title">
            Invoice Payment Ledger <span>⌄</span>
        </div>

        <div class="payments-actions">

            <a href="<?= base_url('invoice/payments/create') ?>"
               class="btn-new-payment">
                + Receive Payment
            </a>

        </div>

    </div>

    <form id="bulkDeleteForm" method="POST" action="<?= base_url('invoice/payments/bulk-delete') ?>">
        <?= csrf_field() ?>

        <div id="bulkActions" class="payments-bulk-actions" style="display:none;">
            <div style="margin-right:16px; color:#b91c1c; font-weight:600; font-size:13px;">
                Warning: This deletes payment records permanently and updates invoice balances. Delete payments here first, then delete invoices from the invoice screen.
            </div>
            <span id="bulkSelectedCount">0 selected</span>
            <button type="submit" class="btn-delete-selected" onclick="return confirm('Warning: deleting selected payment records is permanent. This will update invoice balances and remove payment history.')">
                Delete Selected
            </button>
        </div>

        <div class="payments-table-wrap">

        <table class="payments-table">

            <thead>

                <tr>

                    <th class="checkbox-col">
                        <input type="checkbox" id="selectAllInvoices" onchange="toggleSelectAll(this)">
                    </th>

                    <th>Invoice #</th>

                    <th>Customer</th>

                    <th>Invoice Date</th>

                    <th>Last Payment</th>

                    <th style="text-align:right;">Invoice Total</th>

                    <th style="text-align:right;">Paid Amount</th>

                    <th style="text-align:right;">Balance Due</th>

                    <th>Total Payments</th>

                    <th>Status</th>

                    <th>History</th>

                </tr>

            </thead>

            <tbody>

            <?php if(!empty($payments)): ?>

                <?php foreach($payments as $p): ?>

                    <?php
                        $invoiceId      = $p['invoice_id'] ?? 0;
                        $invoiceTotal   = (float)($p['total'] ?? 0);
                        $paidAmount     = (float)($p['paid_amount'] ?? 0);
                        $balanceDue     = (float)($p['balance_due'] ?? ($invoiceTotal - $paidAmount));
                        $status         = strtolower($p['status'] ?? 'unpaid');

                        $statusClass = 'status-unpaid';

                        if($status == 'paid'){
                            $statusClass = 'status-paid';
                        }
                        elseif($status == 'partially_paid'){
                            $statusClass = 'status-partial';
                        }
                    ?>

                    <?php $targetUrl = base_url('invoice/payments/history/' . $invoiceId); ?>
                    <tr onclick="window.location.href='<?= $targetUrl ?>'">

                        <td class="checkbox-col"
                            onclick="event.stopPropagation();">

                            <input type="checkbox" name="selected_invoices[]" value="<?= esc($invoiceId) ?>" onchange="updateBulkActions();" onclick="event.stopPropagation();">

                        </td>

                        <td>

                            <a href="<?= $targetUrl ?>"
                               class="payment-link"
                               onclick="event.stopPropagation();">

                                <?= esc($p['invoice_number'] ?? '-') ?>

                            </a>

                        </td>

                        <td>
                            <?= esc($p['cname'] ?? '-') ?>
                        </td>

                        <td>
                            <?=
                                !empty($p['invoice_date'])
                                ? date('d/m/Y', strtotime($p['invoice_date']))
                                : '-'
                            ?>
                        </td>

                        <td>
                            <?=
                                !empty($p['last_payment_date'])
                                ? date('d/m/Y', strtotime($p['last_payment_date']))
                                : '-'
                            ?>
                        </td>

                        <td class="amount">
                            ₹<?= number_format($invoiceTotal, 2) ?>
                        </td>

                        <td class="amount amount-green">
                            ₹<?= number_format($paidAmount, 2) ?>
                        </td>

                        <td class="amount amount-red">
                            ₹<?= number_format($balanceDue, 2) ?>
                        </td>

                        <td>

                            <?= $p['payment_count'] ?? 0 ?>

                        </td>

                        <td>

                            <span class="status-badge <?= $statusClass ?>">

                                <?= strtoupper(str_replace('_', ' ', $status)) ?>

                            </span>

                        </td>

                        <td onclick="event.stopPropagation();">

                            <a href="<?= base_url('invoice/payments/history/' . $invoiceId) ?>"
                               class="history-btn">

                                View History

                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>

                    <td colspan="11">

                        <div class="empty-state">

                            No invoice payments found.

                        </div>

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>
    </form>

</div>

<script>
    function updateBulkActions() {
        const checkboxes = Array.from(document.querySelectorAll('input[name="selected_invoices[]"]'));
        const checked = checkboxes.filter(cb => cb.checked);
        const bulkActions = document.getElementById('bulkActions');
        const bulkSelectedCount = document.getElementById('bulkSelectedCount');
        const selectAll = document.getElementById('selectAllInvoices');

        if (checked.length > 0) {
            bulkActions.style.display = 'flex';
        } else {
            bulkActions.style.display = 'none';
        }

        bulkSelectedCount.textContent = checked.length + ' selected';

        if (selectAll) {
            selectAll.checked = checked.length > 0 && checked.length === checkboxes.length;
        }
    }

    function toggleSelectAll(master) {
        const checkboxes = document.querySelectorAll('input[name="selected_invoices[]"]');
        checkboxes.forEach(cb => { cb.checked = master.checked; });
        updateBulkActions();
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateBulkActions();
    });
</script>

<?= $this->endSection() ?>