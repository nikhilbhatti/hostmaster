<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .text-indigo { color: #6366f1; }
    .form-label { font-size: 11px; letter-spacing: 0.8px; margin-bottom: 8px; color: #64748b !important; }
    .custom-input, .custom-select { 
        border-radius: 12px !important; 
        padding: 14px 16px; 
        font-size: 14px;
        transition: 0.3s;
        border: 1px solid #edf2f7 !important;
        background-color: #f8fafc !important;
    }
    .custom-input:focus, .custom-select:focus {
        background-color: #fff !important;
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1) !important;
    }
    .readonly-input {
        background-color: #e2e8f0 !important;
        cursor: not-allowed;
        font-weight: 600;
        color: #475569;
    }
    .btn-update { 
        background: #6366f1; 
        border: none; 
        border-radius: 12px; 
        padding: 12px 35px; 
        font-weight: 600; 
        transition: 0.3s; 
    }
    .btn-update:hover { 
        background: #4f46e5; 
        transform: translateY(-2px); 
        box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3); 
    }
    
    @media (max-width: 768px) {
        .container-fluid { padding: 15px !important; }
        .btn-update { width: 100%; }
    }
</style>

<div class="container-fluid py-4">
    <div class="mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">Edit Backup Log</h3>
            <p class="text-muted small mb-0">Modifying backup record for <span class="text-indigo fw-bold"><?= esc($backup['client_name']) ?></span></p>
        </div>
        <a href="<?= base_url('backups') ?>" class="btn btn-sm btn-outline-secondary px-3 py-2" style="border-radius: 10px;">
            <i class="fas fa-times me-2"></i> Discard Changes
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 24px;">
                <div class="card-body p-4 p-md-5">
                    <form action="<?= base_url('backups/update/'.$backup['id']) ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label fw-bold text-uppercase">Client Account</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-secondary bg-opacity-10 border-0" style="border-radius: 12px 0 0 12px;"><i class="fas fa-lock text-muted"></i></span>
                                    <input type="text" class="form-control custom-input readonly-input border-0 shadow-none" 
                                           value="<?= esc($backup['client_name']) ?>" readonly disabled style="border-radius: 0 12px 12px 0;">
                                    <input type="hidden" name="client_id" value="<?= $backup['client_id'] ?>">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-uppercase">Last Backup Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0" style="border-radius: 12px 0 0 12px;"><i class="fas fa-calendar-alt text-muted"></i></span>
                                    <input type="date" name="last_backup_date" class="form-control custom-input border-0 shadow-none" 
                                           value="<?= $backup['last_backup_date'] ?>" required style="border-radius: 0 12px 12px 0;">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-uppercase">Reminder Interval</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0" style="border-radius: 12px 0 0 12px;"><i class="fas fa-redo-alt text-muted"></i></span>
                                    <select name="backup_interval" class="form-select custom-select border-0 shadow-none" required style="border-radius: 0 12px 12px 0;">
                                        <option value="1" <?= ($backup['backup_interval'] == 1) ? 'selected' : '' ?>>Monthly (Critical)</option>
                                        <option value="3" <?= ($backup['backup_interval'] == 3) ? 'selected' : '' ?>>Every 3 Months (Standard)</option>
                                        <option value="6" <?= ($backup['backup_interval'] == 6) ? 'selected' : '' ?>>Every 6 Months (Recommended)</option>
                                        <option value="12" <?= ($backup['backup_interval'] == 12) ? 'selected' : '' ?>>Yearly (Maintenance)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold text-uppercase">Maintenance Notes</label>
                                <textarea name="notes" class="form-control custom-input border-0" rows="4" 
                                          placeholder="Update notes regarding this specific backup session..."><?= esc($backup['notes']) ?></textarea>
                            </div>

                            <div class="col-12 mt-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <div class="text-muted small">
                                    <i class="fas fa-info-circle me-1"></i> Changes will reflect on Dashboard.
                                </div>
                                <div class="d-flex gap-2 w-sm-100">
                                    <a href="<?= base_url('backups') ?>" class="btn btn-light px-4 py-2" style="border-radius: 12px;">Cancel</a>
                                    <button type="submit" class="btn btn-primary btn-update shadow-sm text-white">
                                        <i class="fas fa-sync-alt me-2"></i> Update Record
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 bg-light shadow-sm p-4" style="border-radius: 24px;">
                <h6 class="fw-bold text-dark mb-3">Backup Meta</h6>
                <div class="mb-3">
                    <label class="extra-small text-uppercase text-muted d-block mb-1">Entry Created</label>
                    <span class="small fw-bold"><?= date('d M, Y', strtotime($backup['created_at'])) ?></span>
                </div>
                <hr class="opacity-10">
                <p class="small text-muted mb-0">
                    <i class="fas fa-lightbulb text-warning me-1"></i> 
                    Agar aapne backup interval change kiya hai, toh system automatic <b>Next Due Date</b> recalculate kar dega.
                </p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>