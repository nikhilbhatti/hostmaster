<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="section-header d-flex justify-content-between align-items-center">
    <div>
        <h3 class="mb-1">Trashed Invoices</h3>
        <p class="text-muted mb-0">Deleted invoices can be restored anytime</p>
    </div>

    <a href="<?= base_url('invoice/invoices') ?>" class="btn btn-primary">
        <i class="fas fa-arrow-left"></i> Back to Invoices
    </a>
</div>

<div class="card">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Invoice No</th>
                        <th>Customer</th>
                        <th>Invoice Date</th>
                        <th>Due Date</th>
                        <th>Total</th>
                        <th width="220">Action</th>
                    </tr>
                </thead>

                <tbody>

                <?php if(!empty($invoices)): ?>
                    <?php $i=1; foreach($invoices as $inv): ?>

                    <tr>
                        <td><?= $i++ ?></td>

                        <td>
                            <strong><?= esc($inv['invoice_number']) ?></strong>
                        </td>

                        <td>
                            <?= esc($inv['cname'] ?? 'N/A') ?>
                        </td>

                        <td>
                            <?= date('d M Y', strtotime($inv['invoice_date'])) ?>
                        </td>

                        <td>
                            <?= !empty($inv['due_date'])
                                ? date('d M Y', strtotime($inv['due_date']))
                                : '-' ?>
                        </td>

                        <td>
                            ₹<?= number_format($inv['total'],2) ?>
                        </td>

                        <td>

                            <a href="<?= base_url('invoice/invoices/restore/'.$inv['id']) ?>"
                               class="btn btn-success btn-sm">
                                Restore
                            </a>

                            <a href="<?= base_url('invoice/invoices/permanent-delete/'.$inv['id']) ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Permanent delete?')">
                                Delete
                            </a>

                        </td>
                    </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            No trashed invoices found
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>
            </table>
        </div>

    </div>
</div>

<?= $this->endSection() ?>