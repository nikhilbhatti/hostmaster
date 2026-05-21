<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0">Leave Report: <?= esc($user['username']) ?></h3>
            <p class="text-muted small">Detailed history and summary for the selected staff member.</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary d-none d-md-block">
                <i class="fas fa-print me-1"></i> Print Report
            </button>
            <a href="<?= base_url('admin/leaves/leave-requests') ?>" class="btn btn-dark btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-12">
            <h6 class="fw-bold text-muted small text-uppercase mb-3"><i class="fas fa-wallet me-2"></i> Current Leave Balance (<?= date('Y') ?>)</h6>
        </div>
        <?php if(!empty($balances)): ?>
            <?php foreach($balances as $bal): ?>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #4e73df !important; border-radius: 10px;">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-bold text-primary"><?= esc($bal['name']) ?></span>
                            <span class="badge bg-light text-dark border fw-normal" style="font-size: 10px;"><?= $bal['total'] ?> Total</span>
                        </div>
                        <div class="d-flex align-items-baseline">
                            <h3 class="mb-0 fw-bold text-dark"><?= $bal['remaining'] ?></h3>
                            <span class="ms-1 text-muted small">Days Left</span>
                        </div>
                        <div class="progress mt-2" style="height: 5px;">
                            <?php $perc = ($bal['total'] > 0) ? ($bal['used'] / $bal['total']) * 100 : 0; ?>
                            <div class="progress-bar bg-primary" style="width: <?= 100 - $perc ?>%"></div>
                        </div>
                        <div class="mt-2 d-flex justify-content-between" style="font-size: 11px;">
                            <span class="text-muted small">Used: <strong><?= $bal['used'] ?></strong></span>
                            <span class="text-muted small">Year: <?= date('Y') ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-light border border-dashed py-3 text-center small text-muted">
                    <i class="fas fa-exclamation-triangle me-1"></i> No leave limits allotted to this staff for the current year.
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="row mb-4 g-3" id="summaryCards">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-success text-white" style="border-radius: 12px;">
                <div class="card-body d-flex align-items-center justify-content-between py-4">
                    <div>
                        <h6 class="text-uppercase mb-1 small fw-bold">Approved Requests</h6>
                        <h2 class="mb-0 fw-bold" id="count-approved"><?= $summary['approved'] ?></h2>
                    </div>
                    <i class="fas fa-check-circle fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-danger text-white" style="border-radius: 12px;">
                <div class="card-body d-flex align-items-center justify-content-between py-4">
                    <div>
                        <h6 class="text-uppercase mb-1 small fw-bold">Rejected Requests</h6>
                        <h2 class="mb-0 fw-bold" id="count-rejected"><?= $summary['rejected'] ?></h2>
                    </div>
                    <i class="fas fa-times-circle fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-warning text-dark" style="border-radius: 12px;">
                <div class="card-body d-flex align-items-center justify-content-between py-4">
                    <div>
                        <h6 class="text-uppercase mb-1 small fw-bold">Pending Requests</h6>
                        <h2 class="mb-0 fw-bold" id="count-pending"><?= $summary['pending'] ?></h2>
                    </div>
                    <i class="fas fa-hourglass-half fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header bg-white py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-history me-2"></i> Full Leave History</h5>
                </div>
                <div class="col-md-6 text-md-end mt-2 mt-md-0">
                    <div class="d-inline-flex align-items-center bg-light p-1 rounded border">
                        <label class="mx-2 small fw-bold text-muted text-nowrap mb-0">Filter Month:</label>
                        <input type="month" id="monthFilter" class="form-control form-control-sm border-0 bg-transparent" style="width: 160px; box-shadow:none;">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="detailsTable">
                    <thead class="table-light text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-4 py-3">Leave Type</th>
                            <th class="py-3">From - To</th>
                            <th class="py-3">Reason</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Admin Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($all_leaves)): ?>
                            <?php foreach($all_leaves as $l): ?>
                            <tr class="leave-row" data-date="<?= date('Y-m', strtotime($l['from_date'])) ?>" data-status="<?= $l['status'] ?>">
                                <td class="ps-4">
                                    <span class="badge bg-soft-info text-info border border-info px-2 py-1 fw-bold">
                                        <?= esc($l['leave_name']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark mb-0" style="font-size: 0.9rem;"><?= date('d M Y', strtotime($l['from_date'])) ?></div>
                                    <div class="text-muted x-small" style="font-size: 0.8rem;">to <?= date('d M Y', strtotime($l['to_date'])) ?></div>
                                </td>
                                <td>
                                    <p class="small mb-0 text-muted text-truncate" style="max-width: 250px;" title="<?= esc($l['reason']) ?>">
                                        <?= esc($l['reason']) ?>
                                    </p>
                                </td>
                                <td>
                                    <?php 
                                        $statusClass = 'bg-warning text-dark';
                                        if($l['status'] == 'approved') $statusClass = 'bg-success text-white';
                                        if($l['status'] == 'rejected') $statusClass = 'bg-danger text-white';
                                    ?>
                                    <span class="badge <?= $statusClass ?> shadow-none text-uppercase px-3" style="font-size: 0.65rem; border-radius: 50px;">
                                        <?= esc($l['status']) ?>
                                    </span>
                                </td>
                                <td class="small text-muted italic">
                                    <?= esc($l['admin_remark']) ?: '<span class="opacity-25">- No Remark -</span>' ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr id="noData">
                                <td colspan="5" class="text-center py-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" class="opacity-25 mb-3">
                                    <p class="text-muted">No leave history found for this user.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr id="noMatch" style="display:none;">
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-search me-2"></i> No records found for the selected month.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white text-center py-3 border-top">
            <p class="x-small text-muted mb-0">Generated on <?= date('d M Y, h:i A') ?> • LMS Reporting System</p>
        </div>
    </div>
</div>

<style>
    .bg-soft-info { background-color: #f0f9ff; }
    .italic { font-style: italic; }
    .leave-row { transition: all 0.2s ease; border-bottom: 1px solid #f1f1f1; }
    .leave-row:hover { background-color: #f8fbff !important; }
    .border-dashed { border: 2px dashed #dee2e6 !important; }
    .x-small { font-size: 0.75rem; }
    @media print {
        .btn, #monthFilter, .card-header .row .col-md-6:last-child { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #eee !important; }
        body { background: white !important; }
        .container-fluid { padding: 0 !important; }
    }
</style>

<script>
    // JS Filter for Month & Dynamic Card Updates
    document.getElementById('monthFilter').addEventListener('change', function() {
        let selectedMonth = this.value;
        let rows = document.querySelectorAll('.leave-row');
        let matchFound = false;
        
        let approvedCount = 0;
        let rejectedCount = 0;
        let pendingCount = 0;

        rows.forEach(row => {
            let rowDate = row.getAttribute('data-date');
            let rowStatus = row.getAttribute('data-status');

            if (selectedMonth === "" || rowDate === selectedMonth) {
                row.style.display = "";
                matchFound = true;
                
                if(rowStatus === 'approved') approvedCount++;
                else if(rowStatus === 'rejected') rejectedCount++;
                else if(rowStatus === 'pending') pendingCount++;
            } else {
                row.style.display = "none";
            }
        });

        document.getElementById('count-approved').innerText = selectedMonth === "" ? "<?= $summary['approved'] ?>" : approvedCount;
        document.getElementById('count-rejected').innerText = selectedMonth === "" ? "<?= $summary['rejected'] ?>" : rejectedCount;
        document.getElementById('count-pending').innerText = selectedMonth === "" ? "<?= $summary['pending'] ?>" : pendingCount;

        const noMatchRow = document.getElementById('noMatch');
        if(noMatchRow) noMatchRow.style.display = matchFound ? "none" : "";
    });
</script>
<?= $this->endSection() ?>