<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    /* Custom Styling for a Modern Look */
    .leave-card { transition: transform 0.2s ease-in-out; border-radius: 12px; overflow: hidden; }
    .leave-card:hover { transform: translateY(-5px); }
    .icon-box { width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 10px; }
    .bg-gradient-primary { background: linear-gradient(45deg, #4e73df, #224abe); color: white; }
    .bg-gradient-danger { background: linear-gradient(45deg, #e74a3b, #be2617); color: white; }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h3 class="fw-bold text-dark">My Leave Board</h3>
            <p class="text-muted mb-0">Apna leave status aur balance yahan track karein.</p>
        </div>
    
    </div>

    <!-- Leave Balances Section -->
    <div class="row mb-4">
        <?php if(!empty($leaveBalances)): ?>
            <?php foreach($leaveBalances as $lb): 
                $limit = $lb['leave_limit'] ?? 0;
                $left  = $lb['available_balance'] ?? 0; 
                
                // Styling based on balance
                $isLow = ($left <= 0);
                $colorClass = $isLow ? 'danger' : 'primary';
                $gradientClass = $isLow ? 'bg-gradient-danger' : 'bg-gradient-primary';
                $icon = $isLow ? 'fa-exclamation-triangle' : 'fa-calendar-check';
            ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card leave-card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="icon-box <?= $gradientClass ?> shadow-sm">
                                <i class="fas <?= $icon ?> fa-lg"></i>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-<?= $colorClass ?>-soft text-<?= $colorClass ?> text-uppercase fw-bold p-2" style="font-size: 0.7rem; background-color: <?= $isLow ? '#f8d7da' : '#cfe2ff' ?>;">
                                    <?= $lb['leave_name'] ?>
                                </span>
                            </div>
                        </div>
                        <h6 class="text-muted small fw-bold mb-1">Available Balance</h6>
                        <div class="d-flex align-items-baseline">
                            <h2 class="fw-bold mb-0 text-dark"><?= $left ?></h2>
                            <span class="ms-2 text-muted small">/ <?= $limit ?> Days</span>
                        </div>
                        <!-- Progress Bar (Optional Visual) -->
                        <div class="progress mt-3" style="height: 6px;">
                            <?php $percent = ($limit > 0) ? ($left / $limit) * 100 : 0; ?>
                            <div class="progress-bar bg-<?= $colorClass ?>" role="progressbar" style="width: <?= $percent ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-light border shadow-sm py-3 text-center text-muted">
                    <i class="fas fa-info-circle me-2"></i> No leave allocations found for <?= date('Y') ?>.
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recent Requests Table -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-history me-2 text-primary"></i> Recent Requests</h5>
                    <a href="<?= base_url('staff/leave-history') ?>" class="btn btn-sm btn-outline-primary rounded-pill">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Leave Type</th>
                                    <th>Dates & Duration</th>
                                    <th>Reason</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($recent_leaves)): ?>
                                    <?php foreach($recent_leaves as $rl): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="p-2 bg-light rounded-circle me-3 text-primary">
                                                    <i class="fas fa-file-alt"></i>
                                                </div>
                                                <span class="fw-bold text-dark"><?= $rl['leave_name'] ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-dark small fw-bold">
                                                <?= date('d M', strtotime($rl['from_date'])) ?> - <?= date('d M, Y', strtotime($rl['to_date'])) ?>
                                            </div>
                                            <span class="text-muted extra-small" style="font-size: 0.75rem;">
                                                <i class="far fa-clock me-1"></i> Requested on <?= date('d M', strtotime($rl['created_at'] ?? '')) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted small">
                                                <?= (isset($rl['reason']) && strlen($rl['reason']) > 40) ? substr($rl['reason'], 0, 40).'...' : ($rl['reason'] ?? '---') ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php 
                                                $statusClass = 'bg-secondary';
                                                if($rl['status'] == 'pending') $statusClass = 'bg-warning text-dark';
                                                elseif($rl['status'] == 'approved') $statusClass = 'bg-success';
                                                elseif($rl['status'] == 'rejected') $statusClass = 'bg-danger';
                                            ?>
                                            <span class="badge <?= $statusClass ?> rounded-pill px-3 py-2 text-capitalize shadow-sm" style="min-width: 90px;">
                                                <?= $rl['status'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center py-5 text-muted">Aapne abhi tak koi request apply nahi ki hai.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>