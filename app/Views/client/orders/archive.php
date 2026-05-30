<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<style>
.archive-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:18px;
}
.archive-card{
    background:#fff;
    border-radius:18px;
    border:0;
    box-shadow:0 8px 24px rgba(15,23,42,.06);
    padding:22px;
}
.archive-table{
    width:100%;
    border-collapse:separate;
    border-spacing:0 10px;
}
.archive-table thead th{
    background:#f8fafc;
    color:#64748b;
    font-size:12px;
    text-transform:uppercase;
    padding:14px 16px;
    border:none;
}
.archive-table tbody tr{
    background:#fff;
    box-shadow:0 2px 10px rgba(15,23,42,.05);
}
.archive-table tbody td{
    padding:16px;
    vertical-align:middle;
    border-top:1px solid #eef2f7;
    border-bottom:1px solid #eef2f7;
}
.archive-table tbody td:first-child{
    border-left:1px solid #eef2f7;
    border-radius:12px 0 0 12px;
}
.archive-table tbody td:last-child{
    border-right:1px solid #eef2f7;
    border-radius:0 12px 12px 0;
}
.domain-text{
    font-weight:700;
    color:#111827;
}
.muted-text{
    color:#64748b;
    font-size:13px;
}
.action-wrap{
    display:flex;
    gap:8px;
    flex-wrap:nowrap;
}
.action-wrap .btn{
    border-radius:10px;
    font-size:13px;
    padding:7px 11px;
}
</style>

<div class="archive-header">
    <div>
        <h3 class="fw-bold mb-1">Archived Orders</h3>
        <p class="text-muted small mb-0">Restore or replace old archived orders</p>
    </div>

    <a href="<?= base_url('orders') ?>" class="btn btn-primary px-4" style="border-radius:12px;">
        <i class="fas fa-arrow-left me-1"></i> Back to Orders
    </a>
</div>

<div class="archive-card">

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="archive-table">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>Client</th>
                    <th>Domain</th>
                    <th>Service Type</th>
                    <th>Provider</th>
                    <th>Expiry Date</th>
                    <th width="300">Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php if(!empty($orders)): ?>
                <?php $i = 1; foreach($orders as $o): ?>
                    <tr>
                        <td><?= $i++ ?></td>

                        <td>
                            <strong><?= esc($o['client_name'] ?? 'N/A') ?></strong>
                        </td>

                        <td>
                            <div class="domain-text"><?= esc($o['domain_name'] ?? 'N/A') ?></div>
                        </td>

                        <td>
                            <?= esc($o['type_name'] ?? 'N/A') ?>
                        </td>

                        <td>
                            <span class="badge bg-light text-dark border">
                                <?= esc($o['provider_name'] ?? 'N/A') ?>
                            </span>
                        </td>

                        <td>
                            <?= !empty($o['domain_expiry_date'])
                                ? date('d M Y', strtotime($o['domain_expiry_date']))
                                : 'N/A' ?>
                        </td>

                        <td>
                            <div class="action-wrap">
                                <a href="<?= base_url('orders/restore/'.$o['id']) ?>"
                                   class="btn btn-success btn-sm"
                                   onclick="return confirm('Restore this order?')">
                                    <i class="fas fa-undo me-1"></i> Restore
                                </a>

                                <a href="<?= base_url('orders/replace/'.$o['id']) ?>"
                                   class="btn btn-primary btn-sm"
                                   onclick="return confirm('Create new active order from this archived order?')">
                                    <i class="fas fa-copy me-1"></i> Replace
                                </a>

                                <a href="<?= base_url('orders/permanent-delete/'.$o['id']) ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Permanent delete? This cannot be undone.')">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        No Archived Orders Found
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>