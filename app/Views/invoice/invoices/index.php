<?= $this->extend('layout/main') ?>

<?php $page_title = 'Invoices'; ?>

<?= $this->section('content') ?>

<style>
    .custom-header-wrapper { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 16px; }
    .custom-header-wrapper h4 { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0; }

    .modern-card { background: #ffffff; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; overflow: hidden; }
    
    .modern-table { width: 100%; border-collapse: collapse; text-align: left; }
    .modern-table th { background: #f8fafc; padding: 16px 20px; font-weight: 700; color: #64748b; text-transform: uppercase; font-size: 11px; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0; }
    .modern-table td { padding: 18px 20px; color: #334155; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
    .modern-table tbody tr:hover { background-color: #f8fafc; }

    /* Badges */
    .badge { padding: 5px 12px; border-radius: 999px; font-size: 11px; font-weight: 700; text-transform: uppercase; display: inline-block; }
    .badge-paid { background: #dcfce7; color: #15803d; }
    .badge-draft { background: #f1f5f9; color: #475569; }
    .badge-sent { background: #dbeafe; color: #1e40af; }
    .badge-partial { background: #fef3c7; color: #92400e; }
    .badge-overdue { background: #fee2e2; color: #b91c1c; }

    /* Action Buttons */
    .action-badge-btn { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 10px; transition: all 0.2s ease; border: none; cursor: pointer; }
    .btn-view { background: #f1f5f9; color: #475569; }
    .btn-view:hover { background: #334155; color: #fff; }
    .btn-edit { background: #eff6ff; color: #2563eb; }
    .btn-edit:hover { background: #2563eb; color: #fff; }
    .btn-pay { background: #f0fdf4; color: #16a34a; }
    .btn-pay:hover { background: #16a34a; color: #fff; }
    .btn-delete { background: #fef2f2; color: #ef4444; }
    .btn-delete:hover { background: #ef4444; color: #fff; }
</style>

<div class="custom-header-wrapper">
    <h4>Invoices</h4>
    <a href="<?= base_url('invoice/invoices/create') ?>" class="btn btn-primary" style="border-radius: 10px; font-weight: 600; padding: 10px 20px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        New Invoice
    </a>
</div>

<div class="modern-card">
    <div style="overflow-x: auto;">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Invoice#</th>
                    <th>Customer</th>
                    <th>Due Date</th>
                    <th>Total</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th style="text-align: right; padding-right: 24px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($invoices as $inv): ?>
                <tr>
                    <td style="color: #64748b;"><?= $inv['invoice_date'] ?></td>
                    <td style="font-weight: 700; color: #0f172a;"><?= $inv['invoice_number'] ?></td>
                    <td style="font-weight: 600;"><?= esc($inv['cname']) ?></td>
                    <td style="color: #64748b;"><?= $inv['due_date'] ?></td>
                    <td style="font-weight: 600;">₹<?= number_format($inv['total'], 2) ?></td>
                    <td style="font-weight: 700; color: <?= $inv['balance_due'] > 0 ? '#b91c1c' : '#10b981' ?>;">
                        ₹<?= number_format($inv['balance_due'], 2) ?>
                    </td>
                    <td><span class="badge badge-<?= $inv['status'] ?>"><?= $inv['status'] ?></span></td>
                    <td>
                        <div style="display:flex; gap:6px; justify-content: flex-end; padding-right: 12px;">
                            <a href="<?= base_url('invoice/invoices/show/'.$inv['id']) ?>" class="action-badge-btn btn-view" title="View"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a>
                            <a href="<?= base_url('invoice/invoices/edit/'.$inv['id']) ?>" class="action-badge-btn btn-edit" title="Edit"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
                            <a href="<?= base_url('invoice/payments/create/'.$inv['id']) ?>" class="action-badge-btn btn-pay" title="Pay"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg></a>
                            <a href="<?= base_url('invoice/invoices/delete/'.$inv['id']) ?>" class="action-badge-btn btn-delete" onclick="return confirm('Delete?')" title="Delete"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>