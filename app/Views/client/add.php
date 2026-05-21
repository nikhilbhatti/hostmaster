<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .bg-indigo { background: #6366f1; }
    .btn-indigo { background: #6366f1; color: white; transition: 0.3s; }
    .btn-indigo:hover { background: #4f46e5; color: white; transform: translateY(-2px); }
    
    .form-control:focus {
        background-color: #fff !important;
        border: 1px solid #6366f1 !important;
        box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.1);
    }

    /* Mobile Responsive Tweaks */
    @media (max-width: 991.98px) {
        .col-lg-4.bg-indigo {
            padding: 30px !important;
            text-align: center;
        }
        .col-lg-4.bg-indigo ul {
            display: none; /* Mobile par list hide kardi space bachane ke liye */
        }
        .col-lg-4.bg-indigo h4 { font-size: 1.2rem; margin-bottom: 5px !important; }
        .col-lg-8.p-5 { padding: 25px !important; }
        .container-fluid { padding-top: 10px !important; }
    }
</style>

<div class="container-fluid py-3">
    <div class="mb-3 px-2">
        <a href="<?= base_url('clients') ?>" class="text-decoration-none text-muted small fw-bold">
            <i class="fas fa-chevron-left me-1"></i> Back to Client Dashboard
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-11 col-xl-10">
            <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                <div class="row g-0">
                    <div class="col-lg-4 bg-indigo p-lg-5 p-4 text-white">
                        <div class="mb-lg-5 mb-0">
                            <h4 class="fw-bold mb-3"><i class="fas fa-id-card me-2"></i> Client Profiling</h4>
                            <p class="opacity-75 small">Register full business details and location data for better management.</p>
                        </div>
                        
                        <ul class="list-unstyled mt-5 d-none d-lg-block">
                            <li class="mb-4 d-flex align-items-center">
                                <i class="fas fa-check-circle me-3 text-warning fs-5"></i>
                                <span>Complete Address Tracking</span>
                            </li>
                            <li class="mb-4 d-flex align-items-center">
                                <i class="fas fa-check-circle me-3 text-warning fs-5"></i>
                                <span>Multi-Contact Management</span>
                            </li>
                            <li class="mb-4 d-flex align-items-center">
                                <i class="fas fa-check-circle me-3 text-warning fs-5"></i>
                                <span>State-wise Classification</span>
                            </li>
                        </ul>
                    </div>

                    <div class="col-lg-8 p-lg-5 p-4 bg-white">
                        <h4 class="fw-bold text-dark mb-4">Business Registration</h4>
                        
                        <form action="<?= base_url('clients/store') ?>" method="POST" autocomplete="off">
                            <?= csrf_field() ?>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Business Name *</label>
                                    <input type="text" name="client_name" class="form-control border-0 bg-light py-2" style="border-radius: 10px;" required placeholder="e.g. Sharma Enterprise">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Contact Person Name *</label>
                                    <input type="text" name="contact_person" class="form-control border-0 bg-light py-2" style="border-radius: 10px;" required placeholder="e.g. Rahul Sharma">
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Website URL</label>
                                    <input type="url" name="website_url" class="form-control border-0 bg-light py-2" style="border-radius: 10px;" placeholder="https://...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Primary Phone (WA) *</label>
                                    <input type="text" name="phone" class="form-control border-0 bg-light py-2" style="border-radius: 10px;" required placeholder="91...">
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Secondary Phone</label>
                                    <input type="text" name="phone_2" class="form-control border-0 bg-light py-2" style="border-radius: 10px;" placeholder="Alternative number">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Email 1 *</label>
                                    <input type="email" name="email_1" class="form-control border-0 bg-light py-2" style="border-radius: 10px;" required placeholder="primary@email.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Email 2</label>
                                    <input type="email" name="email_2" class="form-control border-0 bg-light py-2" style="border-radius: 10px;" placeholder="secondary@email.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">State *</label>
                                    <input list="stateOptions" name="state" class="form-control border-0 bg-light py-2" style="border-radius: 10px;" placeholder="Select or Type State" required>
                                    <datalist id="stateOptions">
                                        <option value="Andhra Pradesh">
                                        <option value="Bihar">
                                        <option value="Delhi">
                                        <option value="Gujarat">
                                        <option value="Haryana">
                                        <option value="Maharashtra">
                                        <option value="Punjab">
                                        <option value="Rajasthan">
                                        <option value="Uttar Pradesh">
                                        <option value="West Bengal">
                                    </datalist>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Full Address</label>
                                    <textarea name="address" class="form-control border-0 bg-light" rows="2" style="border-radius: 10px;" placeholder="Building, Street, Area..."></textarea>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Country</label>
                                    <input type="text" name="country" class="form-control border-0 bg-light py-2" style="border-radius: 10px;" value="India">
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Business Details / Notes</label>
                                    <textarea name="business_details" class="form-control border-0 bg-light p-3" rows="3" style="border-radius: 15px;" placeholder="Add notes here..."></textarea>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-indigo w-100 py-3 fw-bold shadow-sm" style="border-radius: 15px; border: none;">
                                <i class="fas fa-user-plus me-2"></i> Register Client Profile
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>