<?= $this->extend('layout/main') ?>

<?php $page_title = 'Quotes'; ?>

<?= $this->section('content') ?>

<style>
    .custom-header-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 4px;
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
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.03);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    .modern-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 14px;
    }
    .modern-table th {
        background: #f9fafb;
        padding: 14px 16px;
        font-weight: 600;
        color: #4b5563;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #e5e7eb;
    }
    .modern-table td {
        padding: 14px 16px;
        color: #374151;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
    }
    .modern-table tr:last-child td {
        border-bottom: none;
    }
    .modern-table tr:hover td {
        background-color: #f9fafb;
    }
    .quote-link {
        color: #5065e8;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.15s ease;
    }
    .quote-link:hover {
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
    /* Button Colors */
    .btn-view-custom {
        background: #f3f4f6;
        color: #4b5563;
        border: none;
    }
    .btn-view-custom:hover {
        background: #4b5563;
        color: #ffffff;
    }
    .btn-edit-custom {
        background: #f0f2fe;
        color: #5065e8;
        border: none;
    }
    .btn-edit-custom:hover {
        background: #5065e8;
        color: #ffffff;
    }
    .btn-convert-custom {
        background: #f0fdf4;
        color: #16a34a;
        border: none;
    }
    .btn-convert-custom:hover {
        background: #16a34a;
        color: #ffffff;
    }
    .btn-delete-custom {
        background: #fef2f2;
        color: #ef4444;
        border: none;
    }
    .btn-delete-custom:hover {
        background: #ef4444;
        color: #ffffff;
    }
</style>

<div class="custom-header-wrapper">
    <h4>Quotes</h4>
    <a href="<?= base_url('invoice/quotes/create') ?>" class="btn btn-primary" style="border-radius: 8px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        New Quote
    </a>
</div>

<div class="modern-card">
    <div class="table-wrap" style="overflow-x: auto;">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Quote#</th>
                    <th>Reference#</th>
                    <th>Customer Name</th>
                    <th>Status</th>
                    <th>Amount</th>
                    <th style="text-align: right; padding-right: 24px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($quotes as $q): ?>
                <tr>
                    <td style="color: #6b7280; font-variant-numeric: tabular-nums;"><?= $q['quote_date'] ?></td>
                    <td>
                        <a href="<?= base_url('invoice/quotes/show/' . $q['id']) ?>" class="quote-link">
                            <?= $q['quote_number'] ?>
                        </a>
                    </td>
                    <td><?= esc($q['reference'] ?: '-') ?></td>
                    <td style="font-weight: 500;"><?= esc($q['cname']) ?></td>
                    <td>
                        <span class="badge badge-<?= $q['status'] ?>">
                            <?= $q['status'] ?>
                        </span>
                    </td>
                    <td style="font-weight: 600; font-variant-numeric: tabular-nums;">₹<?= number_format($q['total'], 2) ?></td>
                    <td>
                        <div style="display:flex; gap:6px; justify-content: flex-end; padding-right: 8px;">
                            <a href="<?= base_url('invoice/quotes/show/' . $q['id']) ?>" class="action-badge-btn btn-view-custom" title="View">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </a>
                            
                            <a href="<?= base_url('invoice/quotes/edit/' . $q['id']) ?>" class="action-badge-btn btn-edit-custom" title="Edit">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                            </a>
                            
                            <a href="<?= base_url('invoice/quotes/convert/' . $q['id']) ?>" class="action-badge-btn btn-convert-custom" onclick="return confirm('Convert to Invoice?')" title="Convert to Invoice">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </a>
                            
                            <a href="<?= base_url('invoice/quotes/delete/' . $q['id']) ?>" class="action-badge-btn btn-delete-custom" onclick="return confirm('Delete?')" title="Delete">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>

                <?php if(empty($quotes)): ?>
                <tr>
                    <td colspan="7" style="text-align:center; color:#9ca3af; padding:32px">
                        No quotes yet
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>