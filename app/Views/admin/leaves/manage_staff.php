<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white fw-bold py-3">
                    <i class="fas fa-user-plus me-2"></i>Add New Staff
                </div>
                <div class="card-body p-4">
                    <form action="<?= base_url('admin/store-user') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Employee ID</label>
                            <input type="text" name="emp_id" class="form-control bg-light border-2" placeholder="e.g. SGS001" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Username (Login ID)</label>
                            <input type="text" name="username" class="form-control bg-light border-2" placeholder="e.g. rahul_01" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Full Name</label>
                            <input type="text" name="full_name" class="form-control bg-light border-2" placeholder="e.g. Rahul Sharma" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Employment Type</label>
                            <select name="employment_type" class="form-select bg-light border-2" required>
                                <option value="Fresher">Fresher</option>
                                <option value="Experienced">Experienced</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Email Address</label>
                            <input type="email" name="email" class="form-control bg-light border-2" placeholder="staff@example.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Joining Date</label>
                            <input type="date" name="joining_date" class="form-control bg-light border-2" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Password</label>
                            <input type="password" name="password" class="form-control bg-light border-2" placeholder="Create a strong password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 shadow-sm fw-bold py-2 mt-2">
                            <i class="fas fa-check-circle me-1"></i> Create Account
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center py-3 border-bottom">
                    <span><i class="fas fa-users me-2 text-primary"></i>Active Staff Directory</span>
                    <span class="badge bg-light text-dark border"><?= count($users) ?> Total Members</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Emp ID</th> <th>Staff Info</th>
                                    <th>Type & Status</th> 
                                    <th>Joined On</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($users)): ?>
                                    <?php foreach($users as $u): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <span class="badge bg-secondary-subtle text-secondary border px-2">
    <!-- emp_id ko badal kar employee_id kar diya -->
    <?= esc($u['employee_id'] ?? 'N/A') ?>
</span>
                                        </td>

                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary-subtle rounded-circle p-2 me-3 text-center" style="width: 40px; height: 40px; line-height: 25px; display: flex; align-items: center; justify-content: center; background-color: #eef2ff;">
                                                    <i class="fas fa-user text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark"><?= esc($u['name'] ?? $u['username']) ?></div>
                                                    <small class="text-muted">@<?= esc($u['username']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php 
                                                // --- AUTOMATIC CONVERSION LOGIC ---
                                                $joiningDate = new DateTime($u['joining_date']);
                                                $today = new DateTime();
                                                $diff = $today->diff($joiningDate);
                                                $monthsPassed = ($diff->y * 12) + $diff->m;

                                                $currentType = $u['employment_type'];
                                                $isAutoExp = ($currentType == 'Fresher' && $monthsPassed >= 6);
                                            ?>
                                            
                                            <?php if($isAutoExp): ?>
                                                <span class="badge bg-info-light text-info border border-info px-2 mb-1">
                                                    <i class="fas fa-arrow-up small me-1"></i> Experienced
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-primary border border-primary-subtle px-2 mb-1">
                                                    <?= esc($u['employment_type']) ?>
                                                </span>
                                            <?php endif; ?>

                                            <br>

                                            <?php if($u['is_active'] == 1): ?>
                                                <small class="text-success fw-bold"><i class="fas fa-circle me-1" style="font-size: 8px;"></i> Active</small>
                                            <?php else: ?>
                                                <small class="text-danger fw-bold"><i class="fas fa-circle me-1" style="font-size: 8px;"></i> Inactive</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small class="fw-bold text-secondary"><?= date('d M, Y', strtotime($u['joining_date'])) ?></small>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                                <a href="<?= base_url('admin/staff/leave_permissions/'.$u['id']) ?>" class="btn btn-sm btn-outline-primary" title="Permissions"><i class="fas fa-user-shield"></i></a>
                                                <a href="<?= base_url('admin/edit-staff/'.$u['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                                <a href="<?= base_url('admin/toggle-user/'.$u['id'].'/'.$u['is_active']) ?>" class="btn btn-sm <?= ($u['is_active'] == 1) ? 'btn-outline-secondary' : 'btn-outline-success' ?>"><i class="fas <?= ($u['is_active'] == 1) ? 'fa-user-slash' : 'fa-user-check' ?>"></i></a>
                                                <a href="<?= base_url('admin/delete-staff/'.$u['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Confirm Delete?')"><i class="fas fa-trash-alt"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center py-5 text-muted">No staff records found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-info-light { background-color: #e0f7fa; }
    .bg-success-light { background-color: #e8f5e9; }
    .bg-danger-light { background-color: #ffebee; }
    .table-hover tbody tr:hover { background-color: #f8f9fa; }
    .border-2 { border-width: 2px !important; }
    .bg-secondary-subtle { background-color: #f0f1f2; }
</style>
<?= $this->endSection() ?>