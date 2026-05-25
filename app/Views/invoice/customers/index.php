<?= $this->extend('layout/main') ?>

<?php $page_title = 'Customers'; ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark m-0">Customers</h4>
    <div class="d-flex gap-2">
        <form method="GET" class="d-flex">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Search..." value="<?= esc($search ?? '') ?>">
            </div>
        </form>
        <a href="<?= base_url('invoice/customers/create') ?>" class="btn btn-primary px-3">
            <i class="bi bi-plus-lg"></i> Add New
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Name</th>
                    <th>Company</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>GSTIN</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($customers)): ?>
                    <?php foreach($customers as $c): ?>
                    <tr>
                        <td class="ps-4 fw-semibold text-primary">
                            <a href="<?= base_url('invoice/customers/show/' . $c['id']) ?>" class="text-decoration-none">
                                <?= esc($c['display_name']) ?>
                            </a>
                        </td>
                        <td class="text-muted"><?= esc($c['company_name'] ?: '-') ?></td>
                        <td><?= esc($c['email'] ?: '-') ?></td>
                        <td><?= esc($c['work_phone'] ?: '-') ?></td>
                        <td>
                            <?php if(!empty($c['gstin'])): ?>
                                <span class="badge bg-light text-dark font-monospace border"><?= esc($c['gstin']) ?></span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="<?= base_url('invoice/customers/edit/' . $c['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?= base_url('invoice/customers/delete/' . $c['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">No customers found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>