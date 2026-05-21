<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .text-indigo { color: #6366f1; }
    .form-label { font-size: 12px; letter-spacing: 0.5px; margin-bottom: 8px; }
    .form-control, .form-select { 
        border-radius: 12px !important; 
        padding: 12px 15px; 
        font-size: 14px;
        transition: 0.3s;
    }
    .form-control:focus, .form-select:focus {
        background-color: #fff !important;
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1) !important;
    }
    .bg-light-custom { background-color: #f8fafc !important; }
    .btn-update { background: #6366f1; border: none; border-radius: 12px; padding: 12px 25px; font-weight: 600; }
    .btn-update:hover { background: #4f46e5; }
    .btn-cancel { border-radius: 12px; padding: 12px 25px; background: #f1f5f9; color: #64748b; border: none; }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .container-fluid { padding: 15px !important; }
        .header-box { flex-direction: column; align-items: flex-start !important; gap: 15px; }
        .view-btn { width: 100%; text-align: center; }
        .action-btns { flex-direction: column; width: 100%; gap: 10px; }
        .btn-update, .btn-cancel { width: 100%; margin: 0 !important; }
    }
</style>

<div class="container-fluid py-4">

    <div class="mb-4 d-flex align-items-center justify-content-between header-box">
        <div>
            <h3 class="fw-bold text-dark mb-1">Edit Order</h3>
            <p class="text-muted small mb-0">Updating subscription for <span class="text-indigo fw-bold"><?= esc($order['domain_name']) ?></span></p>
        </div>
        <a href="<?= base_url('orders') ?>" class="btn btn-outline-primary view-btn shadow-sm px-3 py-2" style="border-radius: 10px; border-color: #6366f1; color: #6366f1;">
            <i class="fas fa-list me-2"></i> View All Orders
        </a>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="card-body p-4 p-md-5">
            
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger border-0 small mb-4 shadow-sm" style="border-radius: 12px;">
                    <i class="fas fa-exclamation-circle me-2"></i> <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('orders/update/' . $order['id']) ?>" method="POST">
                <?= csrf_field() ?>

                <div class="row g-4">
                    <div class="col-md-6 col-12">
                        <label class="form-label fw-bold text-muted text-uppercase">Select Client</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light-custom border-0" style="border-radius: 12px 0 0 12px;"><i class="fas fa-user text-muted"></i></span>
                            <select name="client_id" class="form-select bg-light-custom border-0 shadow-none" required style="border-radius: 0 12px 12px 0;">
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?= $client['id'] ?>" <?= ($client['id'] == $order['client_id']) ? 'selected' : '' ?>>
                                        <?= esc($client['client_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label fw-bold text-muted text-uppercase">Product Type</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light-custom border-0" style="border-radius: 12px 0 0 12px;"><i class="fas fa-tag text-muted"></i></span>
                            <select name="order_type_id" class="form-select bg-light-custom border-0 shadow-none" required style="border-radius: 0 12px 12px 0;">
                                <option value="">-- Select Type --</option>
                                <?php foreach ($order_types as $type): ?>
                                    <option value="<?= $type['id'] ?>" <?= ($type['id'] == $order['order_type_id']) ? 'selected' : '' ?>>
                                        <?= esc($type['type_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label fw-bold text-muted text-uppercase">Service Provider</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light-custom border-0" style="border-radius: 12px 0 0 12px;"><i class="fas fa-server text-muted"></i></span>
                            <select name="provider_id" class="form-select bg-light-custom border-0 shadow-none" required style="border-radius: 0 12px 12px 0;">
                                <option value="">-- Select Provider --</option>
                                <?php foreach ($providers as $provider): ?>
                                    <option value="<?= $provider['id'] ?>" <?= ($provider['id'] == $order['provider_id']) ? 'selected' : '' ?>>
                                        <?= esc($provider['provider_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label fw-bold text-muted text-uppercase">Domain Name</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light-custom border-0" style="border-radius: 12px 0 0 12px;"><i class="fas fa-globe text-muted"></i></span>
                            <input type="text" name="domain_name" class="form-control bg-light-custom border-0 shadow-none" placeholder="example.com" value="<?= esc($order['domain_name']) ?>" style="border-radius: 0 12px 12px 0;" required>
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label fw-bold text-danger text-uppercase">Expiry Date</label>
                        <div class="input-group">
                            <span class="input-group-text bg-danger bg-opacity-10 border-0" style="border-radius: 12px 0 0 12px;"><i class="fas fa-calendar-alt text-danger"></i></span>
                            <input type="date" name="domain_expiry_date" class="form-control bg-danger bg-opacity-10 border-0 shadow-none text-danger fw-bold" value="<?= $order['domain_expiry_date'] ?>" style="border-radius: 0 12px 12px 0;">
                        </div>
                    </div>

                    <div class="col-12 mt-4 pt-2">
                        <div class="d-flex align-items-center action-btns">
                            <button type="submit" class="btn btn-primary btn-update shadow-sm me-3">
                                <i class="fas fa-sync-alt me-2"></i> Update Order
                            </button>
                            <a href="<?= base_url('orders') ?>" class="btn btn-cancel">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>