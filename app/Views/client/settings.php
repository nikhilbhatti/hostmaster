<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h4 class="fw-bold text-dark">
                        <i class="fas fa-server text-indigo me-2" style="color: #6366f1;"></i> 
                        SMTP Configuration
                    </h4>
                    <p class="text-muted small">Manage your Brevo SMTP details here and notification settings.</p>
                </div>
                
                <div class="card-body p-4">
                    <?php if(session()->getFlashdata('status')): ?>
                        <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center" style="border-radius: 12px; background-color: #ecfdf5; color: #065f46;">
                            <i class="fas fa-check-circle me-3"></i> 
                            <div><strong>Success!</strong> <?= session()->getFlashdata('status') ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex align-items-center" style="border-radius: 12px; background-color: #fef2f2; color: #991b1b;">
                            <i class="fas fa-exclamation-circle me-3"></i> 
                            <div><strong>Error!</strong> <?= session()->getFlashdata('error') ?></div>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('orders/updateSettings') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold text-muted mb-2">Brevo SMTP Login (User)</label>
                                <input type="text" name="smtp_user" class="form-control border-0 bg-light py-2" 
                                       style="border-radius: 10px;" 
                                       value="<?= esc($settings['smtp_user'] ?? '') ?>" 
                                       placeholder="example@smtp-brevo.com" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold text-muted mb-2">Brevo SMTP Key (Password)</label>
                                <input type="password" name="smtp_pass" class="form-control border-0 bg-light py-2" 
                                       style="border-radius: 10px;" 
                                       value="<?= esc($settings['smtp_pass'] ?? '') ?>" 
                                       placeholder="Enter your SMTP Key here" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold text-muted mb-2">Verified Sender Email (Admin)</label>
                                <input type="email" name="from_email" class="form-control border-0 bg-light py-2" 
                                       style="border-radius: 10px;" 
                                       value="<?= esc($settings['from_email'] ?? '') ?>" 
                                       placeholder="sender@yourdomain.com" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold text-muted mb-2" style="color: #6366f1;">HR Email (For Leave Alerts)</label>
                                <input type="email" name="hr_email" class="form-control border-0 bg-light py-2" 
                                       style="border-radius: 10px; border: 1px dashed #6366f1 !important;" 
                                       value="<?= esc($settings['hr_email'] ?? '') ?>" 
                                       placeholder="hr@yourdomain.com">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold text-muted mb-2">Display Name (Sender Name)</label>
                                <input type="text" name="from_name" class="form-control border-0 bg-light py-2" 
                                       style="border-radius: 10px;" 
                                       value="<?= esc($settings['from_name'] ?? '') ?>" 
                                       placeholder="e.g. HostMaster Support" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold text-muted mb-2">SMTP Host</label>
                                <input type="text" name="smtp_host" class="form-control border-0 bg-light py-2" 
                                       style="border-radius: 10px; opacity: 0.7;" value="smtp-relay.brevo.com" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold text-muted mb-2">SMTP Port</label>
                                <input type="text" name="smtp_port" class="form-control border-0 bg-light py-2" 
                                       style="border-radius: 10px; opacity: 0.7;" value="587" readonly>
                            </div>
                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <p class="extra-small text-muted mb-2">HR mail is optional. If empty, only Admin gets alerts.</p>
                            </div>
                        </div>

                        <hr class="my-4 opacity-25">
                        
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="small text-muted">
                                <i class="fas fa-lock me-1 text-success"></i> Settings Securely Stored
                            </span>
                            <button type="submit" class="btn btn-indigo text-white px-5 shadow-sm py-2" 
                                    style="background: #6366f1; border-radius: 12px; font-weight: 500;">
                                <i class="fas fa-save me-2"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius: 20px; background: #f8fafc;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 text-dark"><i class="fas fa-lightbulb text-warning me-2"></i> Connection Guide</h5>
                    <div class="row g-4 text-muted">
                        <div class="col-md-4">
                            <p class="small mb-0 fw-bold text-dark">1. Verified Sender</p>
                            <p class="extra-small">Use the email verified on Brevo. This is your primary Admin contact.</p>
                        </div>
                        <div class="col-md-4">
                            <p class="small mb-0 fw-bold text-dark">2. HR Notification</p>
                            <p class="extra-small">Adding an HR email allows the Leave system to notify two people at once.</p>
                        </div>
                        <div class="col-md-4">
                            <p class="small mb-0 fw-bold text-dark">3. SMTP Key</p>
                            <p class="extra-small">Always generate a new API Key if authentication fails.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .extra-small { font-size: 0.75rem; }
    .text-indigo { color: #6366f1; }
    .btn-indigo { transition: all 0.3s ease; }
    .btn-indigo:hover { 
        background: #4f46e5 !important; 
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4) !important;
    }
    .form-control:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.1);
        border: 1px solid #6366f1 !important;
    }
</style>
<?= $this->endSection() ?>