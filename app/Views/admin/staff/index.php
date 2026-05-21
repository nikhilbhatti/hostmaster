<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<style>
    /* Desktop vs Mobile logic */
    @media (max-width: 767.98px) {
        .desktop-view { display: none; }
        .mobile-card {
            background: #fff;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 5px solid #4f46e5;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
    }

    @media (min-width: 768px) {
        .mobile-view { display: none; }
        .table thead th { 
            background-color: #f8fafc; 
            color: #64748b;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 15px;
        }
    }

    .avatar-icon {
        width: 40px; height: 40px;
        background: #eef2ff;
        color: #4f46e5;
        display: flex; align-items: center; justify-content: center;
        border-radius: 10px; font-weight: bold;
    }
</style>

<div class="container-fluid py-4 px-2 px-md-4">
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1">
            <i class="fas fa-shield-alt me-2 text-primary"></i>Staff Permissions
        </h3>
        <p class="text-muted small mb-0">Control access levels for each staff member</p>
    </div>

    <!-- Variable Check -->
    <?php $list = !empty($staff_members) ? $staff_members : (!empty($staffs) ? $staffs : []); ?>

    <?php if(!empty($list)): ?>
        
        <!-- DESKTOP TABLE: Only Permissions Column -->
        <div class="card border-0 shadow-sm rounded-4 desktop-view overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Staff Member</th>
                            <th>Username</th>
                            <th>Current Status</th>
                            <th class="text-center">Access Control</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($list as $s): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-icon me-3">
                                        <?= strtoupper(substr($s['name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark"><?= esc($s['name']) ?></div>
                                        <div class="text-muted small"><?= esc($s['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border px-2"><?= esc($s['username']) ?></span></td>
                            <td>
                                <span class="badge rounded-pill <?= ($s['status'] ?? 1) == 1 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> px-3 py-2 border">
                                    <?= ($s['status'] ?? 1) == 1 ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="<?= base_url('admin/staff/permissions/'.$s['id']) ?>" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                                    <i class="fas fa-key me-1"></i> Set Permissions
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MOBILE VIEW: Only Permissions Button -->
        <div class="mobile-view">
            <?php foreach($list as $s): ?>
            <div class="mobile-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar-icon me-2">
                            <?= strtoupper(substr($s['name'], 0, 1)) ?>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0"><?= esc($s['name']) ?></h6>
                            <span class="text-muted extra-small">@<?= esc($s['username']) ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid pt-2 border-top">
                    <a href="<?= base_url('admin/staff/permissions/'.$s['id']) ?>" class="btn btn-primary rounded-pill shadow-sm">
                        <i class="fas fa-lock me-1"></i> Manage Access
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <div class="text-center py-5 bg-white rounded-4 shadow-sm mt-3">
            <i class="fas fa-user-shield fa-3x text-light mb-3"></i>
            <h5 class="text-muted">No staff members found to manage.</h5>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>