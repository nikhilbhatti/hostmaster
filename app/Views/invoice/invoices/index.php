<?= $this->extend('layout/main') ?>
<?php $page_title = 'Invoices'; ?>

<?= $this->section('content') ?>

<style>
    /* Premium Status Badges */
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
    .badge-paid { background: #dcfce7; color: #166534; }
    .badge-draft { background: #f3f4f6; color: #374151; }
    .badge-sent { background: #dbeafe; color: #1e40af; }
    .badge-partial { background: #fef3c7; color: #92400e; }
    .badge-overdue { background: #fee2e2; color: #991b1b; }

    /* Modern Table Enhancements */
    .modern-card { background: #ffffff; border-radius: 16px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
    .modern-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .modern-table th { background: #f8fafc; padding: 16px; font-size: 12px; color: #64748b; border-bottom: 2px solid #f1f5f9; }
    .modern-table td { padding: 16px; border-bottom: 1px solid #f1f5f9; transition: 0.2s; }
    .modern-table tr:hover td { background: #fdfdfd; }
    
    /* Action Buttons */
    .action-badge-btn { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; transition: 0.3s; }
    .btn-view-custom { background: #f1f5f9; color: #475569; }
    .btn-edit-custom { background: #eef2ff; color: #4f46e5; }
    .btn-pay-custom { background: #f0fdf4; color: #15803d; }
    .btn-delete-custom { background: #fef2f2; color: #b91c1c; }
    .action-badge-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
</style>

<div class="custom-header-wrapper" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h4 style="font-size: 24px; font-weight: 700; color: #111827;">Invoices</h4>
        <p style="color: #6b7280; font-size: 14px;">Manage and track all your customer invoices here.</p>
    </div>
    <a href="<?= base_url('invoice/invoices/create') ?>" class="btn btn-primary" style="padding: 10px 20px; border-radius: 10px; font-weight: 600;">
        + Create Invoice
    </a>
</div>

<div class="modern-card">
    <div style="overflow-x: auto;">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Invoice #</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($invoices as $inv): ?>
                <tr>
                    <td style="color: #4b5563; font-weight: 500;"><?= date('d M, Y', strtotime($inv['invoice_date'])) ?></td>
                    <td><a href="<?= base_url('invoice/invoices/show/'.$inv['id']) ?>" class="invoice-link" style="color: #4f46e5; font-weight: 600; text-decoration:none;"><?= $inv['invoice_number'] ?></a></td>
                    <td style="font-weight: 600;"><?= esc($inv['cname']) ?></td>
                    <td style="font-weight: 600;">₹<?= number_format($inv['total'], 2) ?></td>
                    <td style="font-weight: 700; color: <?= $inv['balance_due'] > 0 ? '#b91c1c' : '#059669' ?>;">
                        ₹<?= number_format($inv['balance_due'], 2) ?>
                    </td>
                    <td><span class="badge badge-<?= $inv['status'] ?>"><?= $inv['status'] ?></span></td>
                    <td>
                        <div style="display:flex; gap:8px; justify-content: flex-end;">
                            <a href="<?= base_url('invoice/invoices/show/'.$inv['id']) ?>" class="action-badge-btn btn-view-custom" title="View"><i class="fa fa-eye"></i></a>
                            <a href="<?= base_url('invoice/invoices/edit/'.$inv['id']) ?>" class="action-badge-btn btn-edit-custom" title="Edit"><i class="fa fa-pencil"></i></a>
                            <a href="<?= base_url('invoice/payments/create/'.$inv['id']) ?>" class="action-badge-btn btn-pay-custom" title="Pay"><i class="fa fa-money"></i></a>
                            <a href="<?= base_url('invoice/invoices/delete/'.$inv['id']) ?>" class="action-badge-btn btn-delete-custom" onclick="return confirm('Delete?')" title="Delete"><i class="fa fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>