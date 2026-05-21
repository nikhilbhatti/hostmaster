<?= $this->extend('layout/main') ?>

<?php $page_title = 'Customers'; ?>

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
    .modern-search-bar {
        display: flex;
        align-items: center;
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 6px 12px;
        transition: all 0.2s ease;
    }
    .modern-search-bar:focus-within {
        background: #fff;
        border-color: #5065e8;
        box-shadow: 0 0 0 3px rgba(80, 101, 232, 0.15);
    }
    .modern-search-bar i {
        color: #9ca3af;
        margin-right: 8px;
        font-size: 14px;
    }
    .modern-search-bar input {
        border: none;
        background: transparent;
        outline: none;
        font-size: 14px;
        color: #374151;
        width: 220px;
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
    .customer-link {
        color: #5065e8;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.15s ease;
    }
    .customer-link:hover {
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
    <h4>Customers</h4>
    <div style="display:flex; gap:12px; align-items:center">
        <form method="GET" style="display:flex">
            <div class="modern-search-bar">
                <i class="bi bi-search"></i>
                <input type="text" name="search" placeholder="Search customers..." value="<?= esc($search ?? '') ?>">
            </div>
        </form>
        <a href="<?= base_url('invoice/customers/create') ?>" class="btn btn-primary" style="border-radius: 8px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">
            <i class="bi bi-plus-lg"></i> New Customer
        </a>
    </div>
</div>

<div class="modern-card">
    <div class="table-wrap" style="overflow-x: auto;">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Company</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>GSTIN</th>
                    <th style="text-align: right; padding-right: 24px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($customers as $c): ?>
                <tr>
                    <td>
                        <a href="<?= base_url('invoice/customers/show/' . $c['id']) ?>" class="customer-link">
                            <?= esc($c['display_name']) ?>
                        </a>
                    </td>
                    <td style="color: #6b7280; font-weight: 500;"><?= esc($c['company_name'] ?: '-') ?></td>
                    <td><?= esc($c['email'] ?: '-') ?></td>
                    <td style="font-variant-numeric: tabular-nums;"><?= esc($c['work_phone'] ?: '-') ?></td>
                    <td>
                        <?php if(!empty($c['gstin'])): ?>
                            <span style="background: #f3f4f6; padding: 4px 8px; border-radius: 4px; font-family: monospace; font-size: 13px; color: #374151;">
                                <?= esc($c['gstin']) ?>
                            </span>
                        <?php else: ?>
                            <span style="color: #9ca3af;">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display:flex; gap:8px; justify-content: flex-end; padding-right: 8px;">
                            <a href="<?= base_url('invoice/customers/edit/' . $c['id']) ?>" class="action-badge-btn btn-edit-custom" title="Edit">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                            </a>
                            <a href="<?= base_url('invoice/customers/delete/' . $c['id']) ?>" class="action-badge-btn btn-delete-custom" onclick="return confirm('Delete customer?')" title="Delete">
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

                <?php if(empty($customers)): ?>
                <tr>
                    <td colspan="6" style="text-align:center; color:#9ca3af; padding:32px">
                        No customers found
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>