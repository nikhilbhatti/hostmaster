<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .text-indigo { color: #6366f1; }
    .bg-light-custom { background-color: #f8fafc; border: 1px solid #edf2f7; }
    .detail-card { border-radius: 20px; border: none; }
    .info-label { font-size: 11px; letter-spacing: 0.5px; margin-bottom: 6px; display: block; }
    .info-value { font-size: 15px; padding: 12px 15px; border-radius: 12px; display: flex; align-items: center; }
    
    /* Mobile Optimization */
    @media (max-width: 767.98px) {
        .container-fluid { padding: 15px !important; }
        .mb-4.d-flex { flex-direction: column; align-items: flex-start !important; gap: 15px; }
        .d-flex.gap-2 { width: 100%; }
        .btn { flex: 1; justify-content: center; display: flex; align-items: center; padding: 10px !important; font-size: 14px; }
        .info-value { font-size: 14px; }
    }
</style>

<div class="container-fluid py-4">

    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="fw-bold text-dark mb-1">Order Details</h3>
            <p class="text-muted small mb-0">Subscription for <span class="text-indigo fw-bold"><?= esc($order['domain_name']) ?></span></p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('orders/edit/'.$order['id']) ?>" class="btn btn-primary shadow-sm px-4" style="border-radius: 10px; background: #6366f1; border:none;">
                <i class="fas fa-edit me-2"></i> Edit
            </a>
            <a href="<?= base_url('orders') ?>" class="btn btn-outline-secondary shadow-sm px-3" style="border-radius: 10px; background: white;">
                <i class="fas fa-arrow-left me-2"></i> Back
            </a>
        </div>
    </div>

    <div class="card detail-card shadow-sm">
        <div class="card-body p-4">
            <div class="row g-3">
                
                <div class="col-md-6 col-12">
                    <label class="info-label fw-bold text-muted text-uppercase">Client Name</label>
                    <div class="info-value bg-light-custom text-dark fw-bold">
                        <i class="fas fa-user-circle me-3 text-indigo fs-5"></i> <?= esc($order['client_name']) ?>
                    </div>
                </div>

                <div class="col-md-6 col-12">
                    <label class="info-label fw-bold text-muted text-uppercase">Product Type</label>
                    <div class="info-value bg-light-custom text-dark">
                        <i class="fas fa-layer-group me-3 text-indigo fs-5"></i> <?= esc($order['type_name'] ?? 'N/A') ?>
                    </div>
                </div>

                <div class="col-md-6 col-12">
                    <label class="info-label fw-bold text-muted text-uppercase">Service Provider</label>
                    <div class="info-value bg-light-custom text-dark">
                        <i class="fas fa-server me-3 text-indigo fs-5"></i> <?= esc($order['provider_name'] ?? 'Manual') ?>
                    </div>
                </div>

                <div class="col-md-6 col-12">
                    <label class="info-label fw-bold text-muted text-uppercase">Domain Name</label>
                    <div class="info-value bg-light-custom text-primary fw-bold">
                        <i class="fas fa-globe me-3 fs-5"></i> <?= esc($order['domain_name']) ?>
                    </div>
                </div>

                <div class="col-md-6 col-12">
                    <label class="info-label fw-bold text-danger text-uppercase">Domain Expiry Date</label>
                    <div class="info-value bg-danger bg-opacity-10 text-danger fw-bold border border-danger border-opacity-10">
                        <i class="fas fa-calendar-alt me-3 fs-5"></i> 
                        <?= date('d M, Y', strtotime($order['domain_expiry_date'])) ?>
                    </div>
                </div>

                <div class="col-md-6 col-12">
                    <label class="info-label fw-bold text-danger text-uppercase">Hosting Expiry Date</label>
                    <div class="info-value bg-danger bg-opacity-10 text-danger fw-bold border border-danger border-opacity-10">
                        <i class="fas fa-calendar-check me-3 fs-5"></i> 
                        <?= date('d M, Y', strtotime($order['hosting_expiry_date'])) ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <div class="mt-3 text-center">
        <?php 
            $days_left = round((strtotime($order['domain_expiry_date']) - time()) / 86400);
            if($days_left < 0): 
        ?>
            <span class="badge bg-danger px-3 py-2 rounded-pill">Status: Expired</span>
        <?php else: ?>
            <span class="badge bg-success px-3 py-2 rounded-pill">Status: Active (<?= $days_left ?> days remaining)</span>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>