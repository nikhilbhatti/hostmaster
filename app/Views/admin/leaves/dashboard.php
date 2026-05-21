<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<style>
    /* Hover effects for dynamic cards */
    .stat-card {
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
        text-decoration: none !important;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h3 class="mb-4 fw-bold text-dark">Leave Management Overview</h3>
        </div>
    </div> 

    <div class="row mb-4">
        <div class="col-md-6">
            <a href="<?= base_url('admin/manage-staff') ?>" class="card stat-card shadow-sm border-0 bg-primary text-white mb-3">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-25 p-3 me-3">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 opacity-75">Total Staff Members</h6>
                        <h2 class="mb-0 fw-bold"><?= number_format($total_staff) ?></h2>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-6">
            <a href="<?= base_url('admin/leave-requests') ?>" class="card stat-card shadow-sm border-0 bg-warning text-dark mb-3">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-dark bg-opacity-10 p-3 me-3">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 opacity-75">Pending Leave Requests</h6>
                        <h2 class="mb-0 fw-bold"><?= number_format($pending_leaves) ?></h2>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-history me-2"></i>Recent Staff Leave Activity</h5>
            <a href="<?= base_url('admin/leave-requests') ?>" class="btn btn-sm btn-link text-decoration-none fw-bold">
                View All Requests <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Staff Member</th>
                            <th>Leave Type</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($recent_leaves)): ?>
                            <?php foreach($recent_leaves as $leave): ?>
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex flex-column">
                                        <b class="text-dark"><?= esc($leave['username']) ?></b>
                                        <small class="text-muted" style="font-size: 0.75rem;">
                                            Applied: <?= date('d M Y', strtotime($leave['applied_on'])) ?>
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-light text-dark border px-3">
                                        <?= esc($leave['leave_name']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="small fw-bold text-secondary">
                                        <i class="far fa-calendar-check me-1"></i>
                                        <?= date('d M', strtotime($leave['from_date'])) ?> - <?= date('d M', strtotime($leave['to_date'])) ?>
                                    </div>
                                </td>
                                <td>
                                    <?php 
                                        $statusClass = 'info';
                                        if($leave['status'] == 'approved') $statusClass = 'success';
                                        if($leave['status'] == 'rejected') $statusClass = 'danger';
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?> px-3">
                                        <?= ucfirst($leave['status']) ?>
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                 <a href="<?= base_url('admin/leaves/leave-details/'.$leave['user_id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">
    <i class="fas fa-chart-pie me-1"></i> Summary
</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                    <p>No recent leave activity found.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>