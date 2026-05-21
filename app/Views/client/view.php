<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="fw-bold text-dark mb-1">Client Profile</h3>
            <p class="text-muted small mb-0">Full overview of business contact and location details.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('clients') ?>" class="btn btn-light border-0 shadow-sm px-3 text-muted" style="border-radius: 10px;">
                <i class="fas fa-arrow-left me-2"></i> Dashboard
            </a>
            <a href="<?= base_url('clients/edit/'.$client['id']) ?>" class="btn btn-primary shadow-sm px-3" style="background: #6366f1; border: none; border-radius: 10px;">
                <i class="fas fa-edit me-2"></i> Edit Profile
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm text-center p-4" style="border-radius: 20px;">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 80px; height: 80px; background: #f3f4f6; border-radius: 50%; color: #6366f1; font-size: 2rem; font-weight: bold;">
                    <?= strtoupper(substr($client['client_name'] ?? 'C', 0, 1)) ?>
                </div>
                <h4 class="fw-bold text-dark mb-1"><?= $client['client_name'] ?></h4>
                <p class="text-muted small mb-3"><?= str_replace(['https://', 'http://'], '', $client['website_url'] ?? '') ?: 'No Website' ?></p>
                
                <div class="badge bg-success py-2 px-3 rounded-pill mb-4" style="font-size: 0.85rem;">
                    <i class="fas fa-check-circle me-1"></i> Active Client
                </div>

                <div class="d-grid gap-2 mt-2">
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $client['phone'] ?? '') ?>" target="_blank" class="btn btn-outline-success border-2 fw-bold" style="border-radius: 10px;">
                        <i class="fab fa-whatsapp me-2"></i> WhatsApp Message
                    </a>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4 p-4" style="border-radius: 20px;">
                <h6 class="fw-bold mb-3 small text-uppercase text-muted">Quick Actions</h6>
                <div class="list-group list-group-flush small">
                    <?php if(!empty($client['website_url'])): ?>
                    <a href="<?= $client['website_url'] ?>" target="_blank" class="list-group-item list-group-item-action border-0 px-0 d-flex justify-content-between align-items-center">
                        Visit Website <i class="fas fa-external-link-alt text-muted"></i>
                    </a>
                    <?php endif; ?>
                    <a href="mailto:<?= $client['email_1'] ?>" class="list-group-item list-group-item-action border-0 px-0 d-flex justify-content-between align-items-center">
                        Send Official Mail <i class="fas fa-envelope text-muted"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-body p-4 p-md-5">
                    
                    <div class="row mb-5">
                        <div class="col-12 mb-3">
                            <h6 class="text-primary fw-bold text-uppercase small"><i class="fas fa-address-book me-2"></i> Contact Information</h6>
                            <hr class="opacity-10 mt-1">
                        </div>
                        
                        <div class="col-md-12 mb-4">
                            <label class="small text-muted d-block">Contact Person</label>
                            <span class="fw-bold text-dark fs-5">
                                <i class="fas fa-user-tie me-2 text-indigo"></i>
                                <?= !empty($client['contact_person']) ? $client['contact_person'] : 'Not Specified' ?>
                            </span>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="small text-muted d-block">Primary Phone</label>
                            <span class="fw-bold text-dark"><?= !empty($client['phone']) ? $client['phone'] : 'N/A' ?></span>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="small text-muted d-block">Secondary Phone</label>
                            <span class="fw-bold text-dark"><?= !empty($client['phone_2']) ? $client['phone_2'] : '--' ?></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted d-block">Primary Email</label>
                            <span class="fw-bold text-dark"><?= !empty($client['email_1']) ? $client['email_1'] : 'N/A' ?></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted d-block">Secondary Email</label>
                            <span class="fw-bold text-dark"><?= !empty($client['email_2']) ? $client['email_2'] : '--' ?></span>
                        </div>
                    </div>

                    <div class="row mb-5">
                        <div class="col-12 mb-3">
                            <h6 class="text-success fw-bold text-uppercase small"><i class="fas fa-map-marker-alt me-2"></i> Location & Address</h6>
                            <hr class="opacity-10 mt-1">
                        </div>
                        <div class="col-md-12 mb-4">
                            <label class="small text-muted d-block">Full Address</label>
                            <span class="fw-bold text-dark"><?= !empty($client['address']) ? $client['address'] : 'No address provided' ?></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted d-block">State</label>
                            <span class="fw-bold text-dark fs-5 text-indigo">
                                <?= (isset($client['state']) && !empty($client['state'])) ? $client['state'] : '<span class="text-danger small">State not found in DB</span>' ?>
                            </span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted d-block">Country</label>
                            <span class="fw-bold text-dark"><?= !empty($client['country']) ? $client['country'] : 'India' ?></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-warning fw-bold text-uppercase small"><i class="fas fa-sticky-note me-2"></i> Business Details & Notes</h6>
                            <hr class="opacity-10 mt-1">
                            <div class="p-3 bg-light rounded" style="white-space: pre-line; min-height: 100px; border-left: 4px solid #6366f1;">
                                <?= !empty($client['business_details']) ? $client['business_details'] : 'No additional notes provided for this client.' ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-indigo { color: #6366f1; }
    .bg-light { background-color: #f8fafc !important; }
    .list-group-item-action:hover { color: #6366f1 !important; background: transparent; }
</style>
<?= $this->endSection() ?>