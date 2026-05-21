<?= $this->extend('layout/main') ?>

<?php $page_title = 'Items'; ?>

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
    .btn-edit-custom {
        background: #f0f2fe;
        color: #5065e8;
        border: none;
    }
    .btn-edit-custom:hover {
        background: #5065e8;
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
    <h4>Items</h4>
    <a href="<?= base_url('invoice/items/create') ?>" class="btn btn-primary" style="border-radius: 8px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        New Item
    </a>
</div>

<div class="modern-card">
    <div class="table-wrap" style="overflow-x: auto;">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>SKU</th>
                    <th>Unit</th>
                    <th>Selling Price</th>
                    <th>Tax</th>
                    <th style="text-align: right; padding-right: 24px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): ?>
                <tr>
                    <td style="font-weight: 600; color: #111827;"><?= esc($item['name']) ?></td>
                    <td>
                        <span style="background: #f3f4f6; padding: 4px 8px; border-radius: 6px; font-size: 13px; font-weight: 500;">
                            <?= ucfirst($item['item_type']) ?>
                        </span>
                    </td>
                    <td style="font-family: monospace; color: #4b5563;"><?= esc($item['sku'] ?: '-') ?></td>
                    <td><?= esc($item['unit'] ?: '-') ?></td>
                    <td style="font-weight: 500; font-variant-numeric: tabular-nums;">₹<?= number_format($item['selling_price'], 2) ?></td>
                    <td>
                        <?php if($item['tax_id']): ?>
                            <span style="color: #16a34a; font-weight: 500; display: inline-flex; align-items: center; gap: 4px;">
                                <span style="width: 6px; height: 6px; background: #16a34a; border-radius: 50%;"></span>
                                Applied
                            </span>
                        <?php else: ?>
                            <span style="color: #9ca3af;">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display:flex; gap:6px; justify-content: flex-end; padding-right: 8px;">
                            <a href="<?= base_url('invoice/items/edit/' . $item['id']) ?>" class="action-badge-btn btn-edit-custom" title="Edit">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                            </a>
                            
                            <a href="<?= base_url('invoice/items/delete/' . $item['id']) ?>" class="action-badge-btn btn-delete-custom" onclick="return confirm('Delete item?')" title="Delete">
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

                <?php if(empty($items)): ?>
                <tr>
                    <td colspan="7" style="text-align:center; color:#9ca3af; padding:32px">
                        No items yet
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>