<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.9);
        --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    }

    .log-container { animation: fadeIn 0.5s ease-in-out; }
    .table-card { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: var(--glass-bg); backdrop-filter: blur(10px); }
    
    .table thead th { 
        background: #f1f5f9; 
        color: #475569; 
        font-weight: 700; 
        font-size: 0.75rem; 
        text-transform: uppercase; 
        padding: 18px;
        border: none;
    }

    .badge-action {
        padding: 5px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.7rem;
    }

    /* Filter Tabs */
    .filter-tab { cursor: pointer; border-radius: 8px; padding: 6px 14px; font-size: 0.8rem; font-weight: 600; border: 2px solid transparent; transition: all 0.2s; }
    .filter-tab.active { background: #1e293b; color: #fff; }
    .filter-tab:not(.active) { background: #f1f5f9; color: #64748b; }
    .filter-tab:not(.active):hover { border-color: #cbd5e1; }

    @media (max-width: 767px) {
        .desktop-view { display: none; }
        .mobile-card {
            background: #fff;
            border-radius: 18px;
            padding: 20px;
            margin-bottom: 15px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
            position: relative;
        }
        .time-stamp {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 0.7rem;
            color: #94a3b8;
        }
    }

    @media (min-width: 768px) {
        .mobile-view { display: none; }
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .search-box {
        background: #fff;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding-left: 40px;
        transition: 0.3s;
    }
    .search-box:focus { border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); }
</style>

<div class="container-fluid py-4 px-3 log-container">
    
    <div class="row align-items-center mb-4 g-3">
        <div class="col-12 col-md-6">
            <h3 class="fw-extrabold text-dark mb-1">Activity Audit</h3>
            <p class="text-muted small mb-0"><i class="fas fa-shield-check text-success me-1"></i> Real-time system monitoring active</p>
        </div>
        <div class="col-12 col-md-6 d-flex gap-2 justify-content-md-end flex-wrap">
            <div class="position-relative flex-grow-1 flex-md-grow-0">
                <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" id="logSearch" class="form-control search-box py-2" placeholder="Search logs...">
            </div>
            <a href="<?= base_url('dashboard') ?>" class="btn btn-dark rounded-3 px-4 shadow-sm">
                <i class="fas fa-home me-2"></i><span class="d-none d-md-inline">Home</span>
            </a>
        </div>
    </div>

    <!-- ✅ FILTER TABS - All + Leave Related -->
    <div class="d-flex gap-2 mb-4 flex-wrap">
        <span class="filter-tab active" onclick="filterLogs('all', this)">
            <i class="fas fa-list me-1"></i> All Logs
        </span>
        <span class="filter-tab" onclick="filterLogs('Leave Allocation', this)">
            <i class="fas fa-plus-circle me-1"></i> Allocations
        </span>
        <span class="filter-tab" onclick="filterLogs('Leave Edit', this)">
            <i class="fas fa-edit me-1"></i> Edits
        </span>
        <span class="filter-tab" onclick="filterLogs('Leave Delete', this)">
            <i class="fas fa-trash-alt me-1"></i> Deletions
        </span>
        <span class="filter-tab" onclick="filterLogs('Holiday Create', this)">
            <i class="fas fa-calendar-check me-1"></i> Holidays
        </span>
    </div>

    <?php if(!empty($logs)): ?>
        
        <div class="card table-card desktop-view">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Staff Member</th>
                            <th>Event Details</th>
                            <th>Network Info</th>
                            <th class="text-end pe-4">Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($logs as $log): ?>
                        <tr class="log-row" data-action="<?= esc($log['action'] ?? '') ?>">
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle p-2 me-3" style="width:35px; height:35px; display:flex; align-items:center; justify-content:center; background: rgba(99,102,241,0.1)">
                                        <i class="fas fa-user-shield text-primary"></i>
                                    </div>
                                    <span class="fw-bold text-dark"><?= esc($log['staff_name'] ?? 'System') ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="small text-secondary lh-sm py-1" style="max-width: 400px;">
                                    <?php 
                                        $msg    = $log['activity_message'] ?? $log['action'] ?? 'Logged activity';
                                        $action = $log['action'] ?? '';

                                        // Action badge - Leave actions ke liye special colors
                                        if ($action === 'Leave Allocation') {
                                            echo "<span class='badge bg-primary-subtle text-primary mb-1 badge-action'><i class='fas fa-plus-circle me-1'></i>ALLOCATION</span><br>";
                                        } elseif ($action === 'Leave Edit') {
                                            echo "<span class='badge bg-warning-subtle text-warning mb-1 badge-action'><i class='fas fa-edit me-1'></i>EDITED</span><br>";
                                        } elseif ($action === 'Leave Delete') {
                                            echo "<span class='badge bg-danger-subtle text-danger mb-1 badge-action'><i class='fas fa-trash-alt me-1'></i>DELETED</span><br>";
                                        } elseif ($action === 'Holiday Create') {
                                            echo "<span class='badge bg-success-subtle text-success mb-1 badge-action'><i class='fas fa-calendar-check me-1'></i>HOLIDAY</span><br>";
                                        } elseif (stripos($msg, 'delete') !== false) {
                                            echo "<span class='badge bg-danger-subtle text-danger mb-1 badge-action'>DELETED</span><br>";
                                        } elseif (stripos($msg, 'update') !== false) {
                                            echo "<span class='badge bg-warning-subtle text-warning mb-1 badge-action'>UPDATED</span><br>";
                                        } elseif (stripos($msg, 'create') !== false || stripos($msg, 'add') !== false) {
                                            echo "<span class='badge bg-success-subtle text-success mb-1 badge-action'>CREATED</span><br>";
                                        }
                                    ?>
                                    <?= esc($msg) ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge border text-muted fw-normal" style="font-family: monospace;"><?= esc($log['ip_address'] ?? '::1') ?></span>
                            </td>
                            <td class="text-end pe-4">
                                <span class="d-block fw-bold text-dark small"><?= date('h:i A', strtotime($log['created_at'])) ?></span>
                                <span class="text-muted" style="font-size: 0.7rem;"><?= date('d-M-Y', strtotime($log['created_at'])) ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mobile-view">
            <?php foreach($logs as $log): ?>
            <div class="mobile-card log-row" data-action="<?= esc($log['action'] ?? '') ?>">
                <span class="time-stamp fw-bold"><?= date('h:i A', strtotime($log['created_at'])) ?></span>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-light p-2 text-primary me-2">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <h6 class="mb-0 fw-bold"><?= esc($log['staff_name'] ?? 'System') ?></h6>
                </div>

                <div class="p-3 rounded-4 bg-light mb-2">
                    <?php
                        $action = $log['action'] ?? '';
                        if ($action === 'Leave Allocation')  echo "<span class='badge bg-primary text-white mb-2' style='font-size:10px;'>ALLOCATION</span><br>";
                        elseif ($action === 'Leave Edit')    echo "<span class='badge bg-warning text-dark mb-2' style='font-size:10px;'>EDITED</span><br>";
                        elseif ($action === 'Leave Delete')  echo "<span class='badge bg-danger text-white mb-2' style='font-size:10px;'>DELETED</span><br>";
                        elseif ($action === 'Holiday Create') echo "<span class='badge bg-success text-white mb-2' style='font-size:10px;'>HOLIDAY</span><br>";
                    ?>
                    <p class="mb-0 small text-dark fw-medium lh-sm">
                        <?= esc($log['activity_message'] ?? $log['action'] ?? 'System Process') ?>
                    </p>
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small" style="font-size: 10px;"><?= date('d M Y', strtotime($log['created_at'])) ?></span>
                    <code class="text-primary small" style="font-size: 10px;"><?= esc($log['ip_address'] ?? '::1') ?></code>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <div class="card text-center py-5 border-0 shadow-sm rounded-4">
            <div class="card-body">
                <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="80" class="opacity-50 mb-3">
                <h5 class="text-muted">No Logs Found</h5>
                <p class="small text-muted">The activity history is currently empty.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    // Live Search
    document.getElementById('logSearch').addEventListener('keyup', function() {
        let value = this.value.toLowerCase();
        document.querySelectorAll('.log-row').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
        });
    });

    // ✅ Filter Tabs - Leave actions filter
    function filterLogs(action, tab) {
        // Active tab update
        document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');

        // Rows filter
        document.querySelectorAll('.log-row').forEach(row => {
            if (action === 'all') {
                row.style.display = '';
            } else {
                row.style.display = (row.getAttribute('data-action') === action) ? '' : 'none';
            }
        });
    }
</script>

<?= $this->endSection() ?>