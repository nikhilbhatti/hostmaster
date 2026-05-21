<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .table-hover tbody tr:hover { background-color: #f8fafc; transition: 0.2s; }
    .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 6px; }
    .dot-overdue { background-color: #ef4444; box-shadow: 0 0 8px rgba(239, 68, 68, 0.5); animation: pulse-red 2s infinite; }
    .dot-uptodate { background-color: #10b981; }
    
    @keyframes pulse-red {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }

    .btn-action {
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: all 0.2s;
        border: none;
    }
    .btn-action:hover { transform: translateY(-2px); }
    
    .table thead th {
        font-size: 11px;
        letter-spacing: 0.05em;
        padding-top: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f1f5f9;
    }
</style>

<div class="container-fluid py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">Backup Management</h3>
            <p class="text-muted small mb-0">Monitor data safety cycles and upcoming maintenance tasks.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('backups/add') ?>" class="btn btn-primary shadow-sm px-4 py-2" style="background: #6366f1; border-radius: 12px; border: none;">
                <i class="fas fa-plus-circle me-2"></i> New Backup Log
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-white">
                <small class="text-muted d-block mb-1">Total Logs</small>
                <h4 class="fw-bold mb-0"><?= count($backups) ?></h4>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-white">
                <small class="text-muted d-block mb-1 text-danger">Overdue</small>
                <h4 class="fw-bold mb-0 text-danger">
                    <?php 
                        $overdueCount = 0;
                        foreach($backups as $b) {
                            $next = (new DateTime($b['last_backup_date']))->modify("+{$b['backup_interval']} month");
                            if($next <= new DateTime()) $overdueCount++;
                        }
                        echo $overdueCount;
                    ?>
                </h4>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-muted text-uppercase">
                        <th class="ps-4">Client Details</th>
                        <th>Last Activity</th>
                        <th>Frequency</th>
                        <th>Next Due Date</th>
                        <th>Health Status</th>
                        <th class="text-end pe-4">Manage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($backups)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" class="opacity-25 mb-3" alt="No data">
                                <h6 class="text-muted fw-normal">No backup logs found. Your safety list is empty.</h6>
                                <a href="<?= base_url('backups/add') ?>" class="btn btn-sm btn-link text-indigo mt-2">Create your first log</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($backups as $b): 
                            $last = new DateTime($b['last_backup_date']);
                            $next = clone $last;
                            $next->modify("+{$b['backup_interval']} month");
                            $today = new DateTime();
                            $is_overdue = $next <= $today;
                        ?>
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-indigo-subtle text-indigo rounded-3 p-2 me-3 d-none d-sm-block" style="background: #eef2ff;">
                                        <i class="fas fa-user-shield"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0"><?= esc($b['client_name']) ?></div>
                                        <small class="text-muted">ID: #BKP-<?= $b['id'] ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-dark small fw-medium"><?= date('d M, Y', strtotime($b['last_backup_date'])) ?></div>
                                <div class="extra-small text-muted" style="font-size: 10px;">Recorded</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal py-2 px-3 rounded-3">
                                    <i class="fas fa-calendar-alt me-1 text-indigo"></i> <?= $b['backup_interval'] ?> Mo
                                </span>
                            </td>
                            <td>
                                <div class="small fw-bold <?= $is_overdue ? 'text-danger' : 'text-dark' ?>">
                                    <?= $next->format('d M, Y') ?>
                                </div>
                                <small class="extra-small text-muted" style="font-size: 10px;">
                                    <?= $is_overdue ? 'Action Required' : 'Scheduled' ?>
                                </small>
                            </td>
                            <td>
                                <?php if($is_overdue): ?>
                                    <span class="badge rounded-pill text-danger px-3 py-2 d-inline-flex align-items-center" style="background: rgba(239, 68, 68, 0.1);">
                                        <span class="status-dot dot-overdue"></span> Overdue
                                    </span>
                                <?php else: ?>
                                    <span class="badge rounded-pill text-success px-3 py-2 d-inline-flex align-items-center" style="background: rgba(16, 185, 129, 0.1);">
                                        <span class="status-dot dot-uptodate"></span> Safe
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <button onclick="confirmTask('<?= base_url('backups/mark-done/'.$b['id']) ?>', 'Mark this client as backed up today?')" 
                                            class="btn-action bg-success bg-opacity-10 text-success" title="Mark Done Today">
                                        <i class="fas fa-check-circle"></i>
                                    </button>

                                    <a href="<?= base_url('backups/edit/'.$b['id']) ?>" class="btn-action bg-warning bg-opacity-10 text-warning" title="Edit Settings">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <button onclick="confirmDelete('<?= base_url('backups/delete/'.$b['id']) ?>')" 
                                            class="btn-action bg-danger bg-opacity-10 text-danger" title="Remove Log">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // General Task Confirmation (Mark Done)
    function confirmTask(url, message) {
        Swal.fire({
            title: 'Are you sure?',
            text: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Mark it Done!',
            customClass: { popup: 'rounded-4' }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }

    // Delete Confirmation
    function confirmDelete(url) {
        Swal.fire({
            title: 'Delete this log?',
            text: "This backup record will be permanently removed...",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Delete it!',
            customClass: { popup: 'rounded-4' }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }

    // Success Flashdata
    <?php if(session()->getFlashdata('status')): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '<?= session()->getFlashdata('status') ?>',
            timer: 2000,
            showConfirmButton: false,
            customClass: { popup: 'rounded-4' }
        });
    <?php endif; ?>
</script>

<?= $this->endSection() ?>