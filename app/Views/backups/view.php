<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Backup Details</h3>
            <p class="text-muted small mb-0">Detailed analysis for <b><?= $backup['client_name'] ?></b></p>
        </div>
        <div>
            <a href="<?= base_url('backups') ?>" class="btn btn-light border shadow-sm px-3" style="border-radius: 10px;">
                <i class="fas fa-arrow-left me-2"></i> Back to List
            </a>
            <a href="<?= base_url('backups/edit/'.$backup['id']) ?>" class="btn btn-indigo text-white px-4 ms-2" style="background: #6366f1; border-radius: 10px;">
                <i class="fas fa-edit me-2"></i> Edit This Log
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px;">
                <div class="text-center mb-4">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-user-tie fs-1 text-indigo"></i>
                    </div>
                    <h5 class="fw-bold mb-0"><?= $backup['client_name'] ?></h5>
                    <span class="text-muted small">Client ID: #<?= $backup['client_id'] ?></span>
                </div>
                
                <hr class="opacity-10">
                
                <div class="mb-3">
                    <label class="small text-muted text-uppercase fw-bold d-block">Contact Email</label>
                    <span class="text-dark"><?= $backup['email'] ?? 'Not Provided' ?></span>
                </div>
                
                <div class="mb-3">
                    <label class="small text-muted text-uppercase fw-bold d-block">Status</label>
                    <?php 
                        $next = (new DateTime($backup['last_backup_date']))->modify("+{$backup['backup_interval']} month");
                        $is_overdue = $next <= new DateTime();
                        echo $is_overdue ? '<span class="badge bg-danger">Maintenance Required</span>' : '<span class="badge bg-success">Healthy</span>';
                    ?>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px;">
                <h5 class="fw-bold mb-4">Maintenance Overview</h5>
                
                <div class="row g-4">
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded-4">
                            <small class="text-muted d-block mb-1">Last Backup Performed</small>
                            <h5 class="fw-bold mb-0 text-dark"><?= date('d M, Y', strtotime($backup['last_backup_date'])) ?></h5>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded-4">
                            <small class="text-muted d-block mb-1">Next Scheduled Date</small>
                            <h5 class="fw-bold mb-0 <?= $is_overdue ? 'text-danger' : 'text-indigo' ?>">
                                <?= $next->format('d M, Y') ?>
                            </h5>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded-4">
                            <small class="text-muted d-block mb-1">Service Interval</small>
                            <h5 class="fw-bold mb-0 text-dark">Every <?= $backup['backup_interval'] ?> Months</h5>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded-4">
                            <small class="text-muted d-block mb-1">Time Remaining</small>
                            <h5 class="fw-bold mb-0">
                                <?php 
                                    $diff = (new DateTime())->diff($next);
                                    echo $is_overdue ? 'Lapsed' : $diff->m . 'm ' . $diff->d . 'd';
                                ?>
                            </h5>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="fw-bold small text-muted text-uppercase mb-2">Technical Notes</label>
                    <div class="p-3 bg-light border-start border-4 border-indigo rounded-3">
                        <p class="mb-0 text-dark italic small">
                            <?= !empty($backup['notes']) ? nl2br($backup['notes']) : 'No specific technical notes recorded for this backup cycle.' ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>