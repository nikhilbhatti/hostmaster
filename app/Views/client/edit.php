<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    /* Desktop & General Improvements */
    .input-group-text { border-radius: 12px 0 0 12px !important; min-width: 45px; justify-content: center; }
    .form-control, .form-select { border-radius: 0 12px 12px 0 !important; transition: all 0.3s ease; }
    .form-control:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.1);
        border: 1px solid #6366f1 !important;
    }
    textarea.form-control { border-radius: 12px !important; }
    
    /* Chrome/Safari fix for datalist arrow */
    input::-webkit-calendar-picker-indicator {
        opacity: 0.5;
        cursor: pointer;
    }

    /* Mobile Responsive Custom CSS */
    @media (max-width: 767.98px) {
        .container-fluid { padding: 10px !important; }
        .card-body { padding: 1.5rem !important; }
        .h3 { font-size: 1.25rem; }
        .btn-primary { width: 100%; padding: 12px !important; }
        .btn-light { width: 100%; margin-top: 10px; }
        .d-flex.gap-3 { flex-direction: column; } /* Buttons stack on mobile */
        
        /* Labels small improvement */
        .form-label { font-size: 0.75rem; margin-bottom: 4px; }
    }
</style>

<div class="container-fluid py-4">
    
    <div class="mb-4 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">Edit Client Profile</h3>
            <p class="text-muted small mb-0">Updating information for <strong><?= $client['client_name'] ?></strong></p>
        </div>
        <a href="<?= base_url('clients') ?>" class="btn btn-light border-0 shadow-sm px-3 text-muted w-sm-100" style="border-radius: 10px;">
            <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                <div class="p-1" style="background: linear-gradient(90deg, #6366f1 0%, #a855f7 100%);"></div>
                
                <div class="card-body p-4 p-md-5">
                    <form action="<?= base_url('clients/update/'.$client['id']) ?>" method="POST" autocomplete="off">
                        <?= csrf_field() ?>
                        
                        <input type="text" style="display:none">
                        <input type="password" style="display:none">
                        
                        <div class="row g-4">
                            <div class="col-12">
                                <h6 class="text-primary fw-bold text-uppercase small mb-3">
                                    <i class="fas fa-user-circle me-2"></i> Basic Information
                                </h6>
                                <hr class="opacity-10 mt-0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Client / Business Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-building text-muted"></i></span>
                                    <input type="text" name="client_name" value="<?= $client['client_name'] ?>" class="form-control bg-light border-0 py-2" placeholder="Enter business name" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Contact Person Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-user text-muted"></i></span>
                                    <input type="text" name="contact_person" value="<?= $client['contact_person'] ?? '' ?>" class="form-control bg-light border-0 py-2" placeholder="Enter contact person name" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Website URL</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-globe text-muted"></i></span>
                                    <input type="url" name="website_url" value="<?= $client['website_url'] ?>" class="form-control bg-light border-0 py-2" placeholder="https://example.com">
                                </div>
                            </div>

                            <div class="col-12 mt-4 mt-md-5">
                                <h6 class="text-success fw-bold text-uppercase small mb-3">
                                    <i class="fas fa-address-book me-2"></i> Contact Details
                                </h6>
                                <hr class="opacity-10 mt-0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Primary Phone (WA) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 text-success"><i class="fab fa-whatsapp"></i></span>
                                    <input type="text" name="phone" value="<?= $client['phone'] ?>" class="form-control bg-light border-0 py-2" placeholder="91..." required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Secondary Phone</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-phone-alt text-muted"></i></span>
                                    <input type="text" name="phone_2" value="<?= $client['phone_2'] ?>" class="form-control bg-light border-0 py-2" placeholder="Alternative number">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Email 1 (Primary) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-envelope text-muted"></i></span>
                                    <input type="email" name="email_1" value="<?= $client['email_1'] ?>" class="form-control bg-light border-0 py-2" placeholder="client@example.com" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Email 2 (Secondary)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-envelope-open text-muted"></i></span>
                                    <input type="email" name="email_2" value="<?= $client['email_2'] ?>" class="form-control bg-light border-0 py-2" placeholder="secondary@example.com">
                                </div>
                            </div>

                            <div class="col-12 mt-4 mt-md-5">
                                <h6 class="text-warning fw-bold text-uppercase small mb-3">
                                    <i class="fas fa-map-marker-alt me-2"></i> Address & Location
                                </h6>
                                <hr class="opacity-10 mt-0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Full Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-home text-muted"></i></span>
                                    <input type="text" name="address" value="<?= $client['address'] ?>" class="form-control bg-light border-0 py-2" placeholder="Office/Shop address">
                                </div>
                            </div>

                            <div class="col-md-3 col-6">
                                <label class="form-label small fw-bold text-muted">State</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-map text-muted"></i></span>
                                    <input list="stateOptions" name="state" id="stateInput" value="<?= $client['state'] ?>" class="form-control bg-light border-0 py-2" placeholder="Select" autocomplete="off">
                                    <datalist id="stateOptions">
                                        <option value="Andhra Pradesh"><option value="Arunachal Pradesh"><option value="Assam"><option value="Bihar"><option value="Chhattisgarh"><option value="Delhi"><option value="Goa"><option value="Gujarat"><option value="Haryana"><option value="Himachal Pradesh"><option value="Jharkhand"><option value="Karnataka"><option value="Kerala"><option value="Madhya Pradesh"><option value="Maharashtra"><option value="Manipur"><option value="Meghalaya"><option value="Mizoram"><option value="Nagaland"><option value="Odisha"><option value="Punjab"><option value="Rajasthan"><option value="Sikkim"><option value="Tamil Nadu"><option value="Telangana"><option value="Tripura"><option value="Uttar Pradesh"><option value="Uttarakhand"><option value="West Bengal"><option value="Andaman and Nicobar Islands"><option value="Chandigarh"><option value="Jammu and Kashmir"><option value="Ladakh"><option value="Lakshadweep"><option value="Puducherry">
                                    </datalist>
                                </div>
                            </div>

                            <div class="col-md-3 col-6">
                                <label class="form-label small fw-bold text-muted">Country</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-flag text-muted"></i></span>
                                    <input type="text" name="country" value="<?= $client['country'] ?? 'India' ?>" class="form-control bg-light border-0 py-2">
                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <label class="form-label small fw-bold text-muted">Business Details / Notes</label>
                                <textarea name="business_details" class="form-control bg-light border-0 p-3" rows="4" placeholder="Additional notes about the client..."><?= $client['business_details'] ?></textarea>
                            </div>
                        </div>

                        <div class="mt-5 d-flex gap-3">
                            <button type="submit" class="btn btn-primary px-5 py-3 fw-bold shadow-sm flex-grow-1" style="border-radius: 12px; background: #6366f1; border: none;">
                                <i class="fas fa-save me-2"></i> Save Changes
                            </button>
                            <a href="<?= base_url('clients') ?>" class="btn btn-light px-4 py-3 fw-bold text-muted" style="border-radius: 12px; border: 1px solid #eee;">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('stateInput').addEventListener('focus', function() {
        this.setAttribute('placeholder', 'Type to search state...');
    });
</script>

<?= $this->endSection() ?>