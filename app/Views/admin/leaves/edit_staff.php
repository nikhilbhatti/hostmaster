<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-warning text-white fw-bold py-3 d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-user-edit me-2"></i>Edit Staff Member</span>
                    <a href="<?= base_url('admin/manage-staff') ?>" class="btn btn-sm btn-light fw-bold">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
                <div class="card-body p-4">
                    <form action="<?= base_url('admin/update-staff/'.$user['id']) ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Employee ID</label>
                            <input type="text" name="employee_id" class="form-control bg-light border-2" 
                                   value="<?= esc($user['employee_id']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Username (Login ID)</label>
                            <input type="text" name="username" class="form-control bg-light border-2" 
                                   value="<?= esc($user['username']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Full Name</label>
                            <input type="text" name="full_name" class="form-control bg-light border-2" 
                                   value="<?= esc($user['name']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Employment Type</label>
                            <select name="employment_type" class="form-select bg-light border-2" required>
                                <option value="Fresher" <?= ($user['employment_type'] == 'Fresher') ? 'selected' : '' ?>>
                                    Fresher (EL after 6 Months)
                                </option>
                                <option value="Experienced" <?= ($user['employment_type'] == 'Experienced') ? 'selected' : '' ?>>
                                    Experienced (EL Starts Immediately)
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Email Address</label>
                            <input type="email" name="email" class="form-control bg-light border-2" 
                                   value="<?= esc($user['email']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Joining Date</label>
                            <input type="date" name="joining_date" class="form-control bg-light border-2" 
                                   value="<?= esc($user['joining_date']) ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">New Password (Leave blank to keep old)</label>
                            <input type="password" name="password" class="form-control bg-light border-2" 
                                   placeholder="Enter new password if you want to change">
                        </div>

                        <button type="submit" class="btn btn-warning w-100 shadow-sm fw-bold py-2 text-white">
                            <i class="fas fa-save me-1"></i> Update Staff Details
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-2 { border-width: 2px !important; }
    .form-control:focus, .form-select:focus {
        border-color: #ffc107;
        box-shadow: none;
    }
    .card { border-radius: 12px; }
    .card-header { border-radius: 12px 12px 0 0 !important; }
</style>
<?= $this->endSection() ?>