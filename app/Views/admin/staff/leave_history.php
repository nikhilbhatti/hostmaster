<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>My Leave Applications</h3>
        <a href="<?= base_url('staff/apply-leave') ?>" class="btn btn-primary btn-sm">Apply New</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Leave Type</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Admin Remark</th>
                            <th>Applied On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($my_leaves)): ?>
                            <?php foreach($my_leaves as $l): ?>
                            <tr>
                                <td><strong><?= $l['leave_name'] ?></strong></td>
                                <td>
                                    <?= date('d M', strtotime($l['from_date'])) ?> to <?= date('d M', strtotime($l['to_date'])) ?>
                                </td>
                                <td>
                                    <?php if($l['status'] == 'pending'): ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php elseif($l['status'] == 'approved'): ?>
                                        <span class="badge bg-success">Approved</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Rejected</span>
                                    <?php endif; ?>
                                </td>
                                <td><small class="text-muted"><?= $l['admin_remark'] ?: '--' ?></small></td>
                                <td><?= date('d M, Y', strtotime($l['applied_on'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center">"You have not applied for any leave yet.".</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>