<?= $this->extend('layout/main') ?>

<?php $page_title = 'Invoices'; ?>

<?= $this->section('content') ?>

<style>
.custom-header-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 4px;
    flex-wrap: wrap;
    gap: 16px;
}

.custom-header-wrapper h4 {
    font-size: 22px;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.modern-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.03);
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.table-wrap {
    width: 100%;
    overflow-x: auto;
}

.modern-table {
    width: 100%;
    min-width: 1150px;
    border-collapse: collapse;
    text-align: left;
    font-size: 14px;
}

.modern-table th {
    background: #f9fafb;
    padding: 14px 16px;
    font-weight: 700;
    color: #4b5563;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #e5e7eb;
    white-space: nowrap;
}

.modern-table td {
    padding: 14px 16px;
    color: #374151;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
    white-space: nowrap;
}

.modern-table tr:last-child td {
    border-bottom: none;
}

.modern-table tr:hover td {
    background-color: #f9fafb;
}

.invoice-link {
    color: #5065e8;
    font-weight: 600;
    text-decoration: none;
}

.invoice-link:hover {
    color: #3b4ec2;
    text-decoration: underline;
}

.action-badge-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    transition: all 0.15s ease;
    text-decoration: none;
}

.btn-view-custom {
    background: #f3f4f6;
    color: #4b5563;
}

.btn-view-custom:hover {
    background: #4b5563;
    color: #ffffff;
}

.btn-edit-custom {
    background: #f0f2fe;
    color: #5065e8;
}

.btn-edit-custom:hover {
    background: #5065e8;
    color: #ffffff;
}

.btn-pay-custom {
    background: #f0fdf4;
    color: #16a34a;
}

.btn-pay-custom:hover {
    background: #16a34a;
    color: #ffffff;
}

.btn-delete-custom {
    background: #fef2f2;
    color: #ef4444;
}

.btn-delete-custom:hover {
    background: #ef4444;
    color: #ffffff;
}

.btn-disabled-custom {
    background: #f3f4f6;
    color: #9ca3af;
    cursor: not-allowed;
}

/* STATUS BADGES */
.status-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 72px;
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    text-transform: capitalize;
    line-height: 1;
}

.status-draft {
    background: #f3f4f6;
    color: #6b7280;
}

.status-sent {
    background: #dbeafe;
    color: #2563eb;
}

.status-partial {
    background: #fef3c7;
    color: #d97706;
}

.status-paid {
    background: #dcfce7;
    color: #16a34a;
}

.status-overdue {
    background: #fee2e2;
    color: #dc2626;
}

.status-default {
    background: #e5e7eb;
    color: #374151;
}
</style>

<div class="custom-header-wrapper">
    <h4>Invoices</h4>

    <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
        <div style="display:flex; gap:6px; background:#f3f4f6; padding:4px; border-radius:8px;">
            <?php foreach(['' => 'All', 'draft' => 'Draft', 'sent' => 'Sent', 'partial' => 'Partial', 'paid' => 'Paid', 'overdue' => 'Overdue'] as $k => $v): ?>
                <a href="<?= base_url('invoice/invoices' . ($k ? '?status=' . $k : '')) ?>"
                   class="btn btn-sm <?= ($status_filter ?? '') === $k ? 'btn-primary' : '' ?>"
                   style="border-radius:6px; font-weight:500; border:none; padding:6px 12px; font-size:13px; <?= ($status_filter ?? '') === $k ? '' : 'background:transparent; color:#4b5563;' ?>">
                    <?= esc($v) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <a href="<?= base_url('invoice/invoices/create') ?>" class="btn btn-primary" style="border-radius:8px; font-weight:500; display:inline-flex; align-items:center; gap:6px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            New Invoice
        </a>
    </div>
</div>

<div class="modern-card">
    <div class="table-wrap">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Invoice#</th>
                    <th>Reference#</th>
                    <th>Customer Name</th>
                    <th>Due Date</th>
                    <th>Amount</th>
                    <th>Balance Due</th>
                    <th>Status</th>
                    <th style="text-align:right; padding-right:24px;">Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if(!empty($invoices)): ?>
                    <?php foreach($invoices as $inv): ?>

                        <?php
                            $statusRaw = trim($inv['status'] ?? 'draft');
                            $statusKey = strtolower(str_replace([' ', '_'], '-', $statusRaw));

                            if ($statusKey === 'partially-paid') {
                                $statusKey = 'partial';
                            }

                            $allowedStatuses = ['draft', 'sent', 'partial', 'paid', 'overdue'];
                            $statusClass = in_array($statusKey, $allowedStatuses) ? 'status-' . $statusKey : 'status-default';

                            $statusLabel = ucwords(str_replace(['-', '_'], ' ', $statusKey));
                        ?>

                        <tr>
                            <td style="color:#6b7280; font-variant-numeric:tabular-nums;">
                                <?= !empty($inv['invoice_date']) ? date('d/m/Y', strtotime($inv['invoice_date'])) : '-' ?>
                            </td>

                            <td>
                                <a href="<?= base_url('invoice/invoices/show/' . $inv['id']) ?>" class="invoice-link">
                                    <?= esc($inv['invoice_number'] ?? '-') ?>
                                </a>
                            </td>

                            <td>
                                <?= esc($inv['reference'] ?? '-') ?: '-' ?>
                            </td>

                            <td style="font-weight:500;">
                                <?= esc($inv['cname'] ?? '-') ?>
                            </td>

                            <td style="color:#6b7280; font-variant-numeric:tabular-nums;">
                                <?= !empty($inv['due_date']) ? date('d/m/Y', strtotime($inv['due_date'])) : '-' ?>
                            </td>

                            <td style="font-weight:500; font-variant-numeric:tabular-nums;">
                                ₹<?= number_format((float)($inv['total'] ?? 0), 2) ?>
                            </td>

                            <td style="font-variant-numeric:tabular-nums; <?= ((float)($inv['balance_due'] ?? 0) > 0) ? 'color:#dc2626; font-weight:600;' : 'color:#10b981; font-weight:600;' ?>">
                                ₹<?= number_format((float)($inv['balance_due'] ?? 0), 2) ?>
                            </td>

                            <td>
                                <span class="status-badge <?= esc($statusClass) ?>">
                                    <?= esc($statusLabel) ?>
                                </span>
                            </td>

                            <td>
                                <div style="display:flex; gap:6px; justify-content:flex-end; padding-right:8px;">
                                    <!-- View Button: Yeh hamesha dikhega -->
                                    <a href="<?= base_url('invoice/invoices/show/' . $inv['id']) ?>" class="action-badge-btn btn-view-custom" title="View">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                    </a>

                                    <?php if ($statusKey !== 'paid'): ?>
                                        <!-- Edit Button -->
                                        <a href="<?= base_url('invoice/invoices/edit/' . $inv['id']) ?>" class="action-badge-btn btn-edit-custom" title="Edit">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M12 20h9"></path>
                                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                            </svg>
                                        </a>

                                        <!-- Record Payment Button -->
                                        <a href="<?= base_url('invoice/payments/create/' . $inv['id']) ?>" class="action-badge-btn btn-pay-custom" title="Record Payment">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                                <line x1="1" y1="10" x2="23" y2="10"></line>
                                            </svg>
                                        </a>

                                        <!-- Delete Button -->
                                        <a href="<?= base_url('invoice/invoices/delete/' . $inv['id']) ?>" class="action-badge-btn btn-delete-custom" onclick="return confirm('Delete?')" title="Delete">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                        </a>
                                    <?php else: ?>
                                        <!-- Paid hone par Locked Disabled Icon dikhega -->
                                        <span class="action-badge-btn btn-disabled-custom" title="Paid Invoices Cannot Be Modified">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                            </svg>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="9" style="text-align:center; color:#9ca3af; padding:32px;">
                            No invoices found
                        </td>
                    </tr>

                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>