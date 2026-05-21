<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    /* Modern Glassmorphism & UI Enhancements */
    .glass-card { 
        background: rgba(255, 255, 255, 0.95); 
        backdrop-filter: blur(10px); 
        border-radius: 20px; 
        transition: all 0.3s ease; 
        border: 1px solid rgba(0,0,0,0.05);
        text-decoration: none !important;
        display: flex;
        flex-direction: column;
    }

    /* Hover Effects */
    .stat-card-interactive:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 15px 30px rgba(0,0,0,0.08); 
        cursor: pointer;
    }
    
    .btn-indigo { 
        background: #6366f1; 
        color: white; 
        border-radius: 12px; 
        transition: 0.3s; 
        border: none; 
        font-weight: 500;
    }
    .btn-indigo:hover { 
        background: #4f46e5; 
        color: white; 
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4); 
    }
    
    /* Critical Alert Animation */
    .animate-pulse-red { 
        animation: pulse-red 2s infinite; 
        border-left: 5px solid #ef4444 !important; 
    }
    @keyframes pulse-red { 
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }
    
    .status-dot { height: 10px; width: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; }
    .extra-small { font-size: 0.75rem; }
    
    .activity-scroll { 
        max-height: 400px; 
        overflow-y: auto; 
        scrollbar-width: thin;
    }
    
    .table-responsive {
        border-radius: 0 0 20px 20px;
        overflow-x: auto;
    }
    .table { min-width: 600px; margin-bottom: 0; }
    .table thead th { border-top: none; font-size: 0.7rem; letter-spacing: 0.5px; }

    .provider-badge {
        font-size: 0.65rem;
        padding: 4px 10px;
        font-weight: 600;
        text-transform: uppercase;
    }

    @media (max-width: 576px) {
        .container-fluid { padding: 10px !important; }
        h3 { font-size: 1.2rem; }
    }

    body { overflow-x: hidden; background-color: #f8fafc; }
</style>

<div class="container-fluid py-3">
    
    <!-- Flash Messages -->
    <?php if(session()->getFlashdata('status') || session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 15px;">
            <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('status') ?: session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 15px;">
            <i class="fas fa-exclamation-triangle me-2"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Header -->
    <div class="row align-items-center mb-4 g-2">
        <div class="col-sm-6">
            <h3 class="fw-bold text-dark mb-1">Command Center</h3>
            <p class="text-muted small mb-0">
                <span class="status-dot bg-success"></span>
                Logged in as: <strong><?= esc(session()->get('name')) ?></strong> (<?= ucfirst(esc(session()->get('user_role'))) ?>)
            </p>
        </div>
        <div class="col-sm-6 d-flex justify-content-sm-end gap-2">
            <button onclick="window.print()" class="btn btn-white shadow-sm border-0 d-none d-md-block" style="border-radius: 10px;">
                <i class="fas fa-print text-muted me-1"></i> Print
            </button>
            <?php if(session()->get('user_role') == 'admin'): ?>
                <a href="<?= base_url('clients/add') ?>" class="btn btn-indigo shadow-sm px-3">
                    <i class="fas fa-plus-circle me-1"></i> Add Client
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <a href="<?= base_url('orders') ?>" class="card border-0 shadow-sm p-3 glass-card stat-card-interactive h-100" style="border-left: 5px solid #6366f1;">
                <small class="text-muted fw-bold text-uppercase extra-small">Total Orders</small>
                <div class="d-flex justify-content-between align-items-end mt-2">
                    <h2 class="fw-bold mb-0 text-dark"><?= $total_orders ?? 0 ?></h2>
                    <i class="fas fa-file-invoice text-primary fs-4 opacity-25"></i>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 glass-card h-100" style="border-left: 5px solid #ef4444;">
                <small class="text-muted fw-bold text-uppercase extra-small">Backup Overdue</small>
                <div class="d-flex justify-content-between align-items-end mt-2">
                    <h2 class="fw-bold mb-0 text-danger"><?= count($backup_alerts ?? []) ?></h2>
                    <i class="fas fa-database text-danger fs-4 opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <a href="<?= base_url('clients') ?>" class="card border-0 shadow-sm p-3 glass-card stat-card-interactive h-100" style="border-left: 5px solid #6366f1;">
                <small class="text-muted fw-bold text-uppercase extra-small">Active Clients</small>
                <div class="d-flex justify-content-between align-items-end mt-2">
                    <h2 class="fw-bold mb-0 text-indigo"><?= count($all_clients ?? []) ?></h2>
                    <i class="fas fa-users text-indigo fs-4 opacity-25"></i>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 glass-card h-100" style="border-left: 5px solid #22c55e;">
                <small class="text-muted fw-bold text-uppercase extra-small">System Status</small>
                <div class="d-flex justify-content-between align-items-end mt-2">
                    <h2 class="fw-bold mb-0 text-success" style="font-size: 1.3rem;">Secure</h2>
                    <i class="fas fa-shield-alt text-success fs-4 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Column -->
        <div class="col-lg-8">
            
            <!-- Service Renewal Alerts (OLD) -->
            <div class="card border-0 shadow-sm glass-card mb-4">
                <div class="card-header bg-transparent border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-bell me-2 text-warning"></i> Service Renewal Alerts</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light text-muted small uppercase">
                            <tr>
                                <th class="ps-4">Service Details</th>
                                <th>Type & Provider</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($expiring_orders)): foreach($expiring_orders as $order): 
                                $expiryDate = !empty($order['domain_expiry_date']) ? $order['domain_expiry_date'] : $order['hosting_expiry_date'];
                                $today_dt = new DateTime('today');
                                $expiry_dt = new DateTime($expiryDate);
                                $interval = $today_dt->diff($expiry_dt);
                                $days = (int)$interval->format('%r%a');
                                if ($days < 0) $days = 0;
                                $isCritical = ($days <= 5);
                                $pName = !empty($order['provider_name']) ? $order['provider_name'] : 'Other';
                                $providerLower = strtolower($pName); 
                                $pBadge = 'bg-primary text-white'; 
                                if(strpos($providerLower, 'hostinger') !== false) { $pBadge = 'bg-info text-white'; }
                                elseif(strpos($providerLower, 'google') !== false || strpos($providerLower, 'cloud') !== false) { $pBadge = 'bg-danger text-white'; }
                                elseif(strpos($providerLower, 'bigrock') !== false) { $pBadge = 'bg-warning text-dark'; }
                                $displayType = !empty($order['hosting_plan']) ? $order['hosting_plan'] : ($order['type_name'] ?? 'Service');
                            ?>
                            <tr class="<?= $isCritical ? 'animate-pulse-red' : '' ?>">
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark mb-0"><?= esc($order['domain_name']) ?></div>
                                    <small class="text-muted"><?= esc($order['client_name']) ?></small>
                                </td>
                                <td>
                                    <div class="extra-small text-muted mb-1"><?= esc($displayType) ?></div>
                                    <span class="badge rounded-pill <?= $pBadge ?> provider-badge"><?= esc(ucfirst($pName)) ?></span>
                                </td>
                                <td><span class="badge rounded-pill <?= $isCritical ? 'bg-danger' : 'bg-warning text-dark' ?>"><?= $days ?> Days Left</span></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button onclick="openEmailPopup('<?= $order['email_1'] ?? '' ?>', '<?= addslashes($order['client_name']) ?>', '<?= $order['domain_name'] ?>', '<?= $days ?>')" class="btn btn-sm btn-light border shadow-sm"><i class="fas fa-envelope text-primary"></i></button>
                                        <?php $waMsg = "Reminder: Hi ".urlencode($order['client_name']).", Your service ".urlencode($order['domain_name'])." is expiring in ".$days." days."; ?>
                                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $order['phone'] ?? '') ?>?text=<?= $waMsg ?>" target="_blank" class="btn btn-sm btn-light border shadow-sm"><i class="fab fa-whatsapp text-success"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="4" class="text-center py-5 text-muted small">No upcoming expiries.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- NEW: Leave Management System Section -->
           <!-- Simple Leave Activity Section -->
<!-- Session check: Sirf Admin ko dikhane ke liye -->
<?php if (session()->get('role') === 'admin'): ?>
<div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center" style="border-radius: 15px 15px 0 0;">
        <h6 class="mb-0 fw-bold text-primary">
            <i class="fas fa-sync-alt me-2"></i> Recent Staff Leave Activity
        </h6>
        <a href="<?= base_url('admin/leaves/requests') ?>" class="text-primary fw-bold small text-decoration-none">
            View All Requests <i class="fas fa-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="bg-light">
                <tr class="text-dark small">
                    <th class="ps-4 border-0">Staff Member</th>
                    <th class="border-0">Leave Type</th>
                    <th class="border-0">Duration</th>
                    <th class="border-0">Status</th>
                    <th class="text-center border-0">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($recent_leaves)): foreach($recent_leaves as $leave): ?>
                <tr class="border-bottom">
                    <td class="ps-4">
                        <div class="fw-bold text-dark mb-0" style="font-size: 0.9rem;"><?= esc($leave['staff_name']) ?></div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border px-3 py-2" style="border-radius: 20px; font-weight: 500; font-size: 0.75rem;">
                            <?= esc($leave['leave_type']) ?>
                        </span>
                    </td>
                    <td class="small text-muted">
                        <i class="far fa-calendar-alt me-1"></i> 
                        <?= date('d M', strtotime($leave['from_date'])) ?> - <?= date('d M', strtotime($leave['to_date'])) ?>
                    </td>
                    <td>
                        <?php 
                            $lStatus = strtolower($leave['status'] ?? 'pending');
                            $statusClass = ($lStatus == 'approved') ? 'bg-success' : (($lStatus == 'rejected') ? 'bg-danger' : 'bg-warning');
                        ?>
                        <span class="badge <?= $statusClass ?> px-3 py-1" style="border-radius: 6px; font-size: 0.7rem;">
                            <?= ucfirst($lStatus) ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <?php if (isset($leave['id']) && !empty($leave['id'])): ?>
                             <a href="<?= base_url('admin/leaves/leave-details/'.$leave['user_id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">
                                <i class="fas fa-chart-pie me-1"></i> Summary
                            </a>
                        <?php else: ?>
                            <button class="btn btn-sm btn-light disabled" style="border-radius: 20px; font-size: 0.75rem;">No ID</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted small">No recent activity found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>      <!-- Recent Work Reports (OLD) -->
            <div class="card border-0 shadow-sm glass-card mb-4">
                <div class="card-header bg-transparent border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">📝 Recent Work Reports</h5>
                    <?php if(session()->get('user_role') !== 'admin'): ?>
                    <button class="btn btn-sm btn-indigo px-3" data-bs-toggle="modal" data-bs-target="#reportModal">
                        <i class="fas fa-plus me-1"></i> Add Report
                    </button>
                    <?php endif; ?>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light small uppercase">
                            <tr>
                                <th class="ps-4">Staff</th>
                                <th>Report Detail</th>
                                <th>Date/Time</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($daily_reports)): foreach($daily_reports as $report): ?>
                            <tr>
                                <td class="ps-4 fw-bold small text-indigo"><?= esc($report['staff_name']) ?></td>
                                <td class="small text-dark"><?= nl2br(esc($report['report_text'])) ?></td>
                                <td class="extra-small text-muted"><?= date('d M, h:i A', strtotime($report['created_at'])) ?></td>
                                <td class="text-end pe-4">
                                    <?php if(session()->get('user_role') !== 'admin' && $report['staff_id'] == session()->get('admin_id')): ?>
                                        <button onclick="editReport(<?= $report['id'] ?>, '<?= addslashes($report['report_text']) ?>')" class="btn btn-link text-primary p-0 me-2"><i class="fas fa-edit"></i></button>
                                    <?php endif; ?>
                                    <?php if(session()->get('user_role') === 'admin'): ?>
                                        <a href="<?= base_url('home/deleteReport/'.$report['id']) ?>" class="btn btn-link text-danger p-0" onclick="return confirm('Delete this report?')"><i class="fas fa-trash"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted small">No reports found for today.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-4 h-100">
                <?php if(session()->get('user_role') == 'admin'): ?>
                <div class="card border-0 shadow-sm glass-card flex-grow-0">
                    <div class="card-header bg-transparent border-0 py-3">
                        <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-bolt me-2"></i> System Activity</h5>
                    </div>
                    <div class="card-body pt-0 activity-scroll">
                        <?php if(!empty($staff_activities)): foreach($staff_activities as $act): ?>
                            <div class="d-flex align-items-start mb-3 border-bottom pb-2">
                                <div class="flex-grow-1">
                                    <div class="small fw-bold text-dark"><?= esc($act['staff_name']) ?></div>
                                    <div class="extra-small text-muted"><?= esc($act['activity_message'] ?? $act['action'] ?? 'Activity recorded') ?></div>
                                    <div class="extra-small text-indigo mt-1"><i class="far fa-clock"></i> <?= date('h:i A', strtotime($act['created_at'])) ?></div>
                                </div>
                            </div>
                        <?php endforeach; else: ?>
                            <p class="text-center text-muted small py-4">No recent activity.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm glass-card flex-grow-1">
                    <div class="card-header bg-transparent border-0 py-3">
                        <h5 class="mb-0 fw-bold text-danger"><i class="fas fa-database me-2"></i> Backup Alerts</h5>
                    </div>
                    <div class="card-body pt-0">
                        <?php if(!empty($backup_alerts)): foreach($backup_alerts as $ba): ?>
                            <div class="p-3 mb-3 border rounded-3 bg-light shadow-sm">
                                <div class="fw-bold small mb-1 text-dark"><?= esc($ba['client_name']) ?></div>
                                <div class="text-muted extra-small mb-2"><i class="fas fa-calendar-alt me-1"></i> Last: <?= date('d M, Y', strtotime($ba['last_backup_date'])) ?></div>
                                <a href="<?= base_url('home/markBackupDone/'.$ba['id']) ?>" class="btn btn-sm btn-indigo w-100 py-2" style="font-size: 0.75rem;">Mark as Completed</a>
                            </div>
                        <?php endforeach; else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-check-circle text-success fs-1 opacity-25 mb-3"></i>
                                <p class="text-muted small">All backups are up to date!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal & Scripts remain same -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">New Daily Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('home/submitReport') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <textarea name="report_text" class="form-control border-light bg-light" placeholder="What did you work on today?..." style="height: 120px; border-radius: 15px;"></textarea>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-indigo w-100">Submit Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function openEmailPopup(email, clientName, domain, days) {
    const message = `Hi ${clientName},\n\nThis is a reminder that your service for ${domain} is expiring in ${days} days. \n\nPlease process the renewal ASAP.\n\nRegards,\nTeam Admin`;
    Swal.fire({
        title: 'Send Renewal Email',
        html: `<div class="text-start">
                <label class="small fw-bold text-muted mb-1">Recipient Email:</label>
                <input type="email" id="email-to" class="form-control mb-3" value="${email}">
                <label class="small fw-bold text-muted mb-1">Message Body:</label>
                <textarea id="email-msg" class="form-control" style="height: 150px;">${message}</textarea>
               </div>`,
        showCancelButton: true,
        confirmButtonText: 'Send Mail',
        confirmButtonColor: '#6366f1',
        preConfirm: () => {
            const emailValue = document.getElementById('email-to').value;
            const messageValue = document.getElementById('email-msg').value;
            if(!emailValue || !messageValue) { Swal.showValidationMessage('Please fill all fields'); }
            return { email: emailValue, message: messageValue };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= base_url('home/sendNotice') ?>';
            const params = { '<?= csrf_token() ?>': '<?= csrf_hash() ?>', email: result.value.email, message: result.value.message, domain_name: domain };
            for (const key in params) {
                const input = document.createElement('input');
                input.type = 'hidden'; input.name = key; input.value = params[key];
                form.appendChild(input);
            }
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function editReport(id, oldText) {
    Swal.fire({
        title: 'Edit Work Report',
        input: 'textarea',
        inputValue: oldText,
        showCancelButton: true,
        confirmButtonText: 'Update',
        confirmButtonColor: '#6366f1',
        preConfirm: (newText) => {
            if (!newText) { Swal.showValidationMessage('Report cannot be empty'); }
            return newText;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= base_url('home/updateReport/') ?>' + id;
            const input = document.createElement('input');
            input.type = 'hidden'; input.name = 'report_text'; input.value = result.value;
            const csrf = document.createElement('input');
            csrf.type = 'hidden'; csrf.name = '<?= csrf_token() ?>'; csrf.value = '<?= csrf_hash() ?>';
            form.appendChild(input); form.appendChild(csrf);
            document.body.appendChild(form); form.submit();
        }
    });
}
</script>
<?= $this->endSection() ?>