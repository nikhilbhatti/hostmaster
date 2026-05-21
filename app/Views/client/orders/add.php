<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .text-indigo { color: #6366f1; }
    .form-label { font-size: 12px; letter-spacing: 0.5px; margin-bottom: 8px; text-transform: uppercase; }
    .form-control, .form-select { 
        border-radius: 12px !important; 
        padding: 12px 15px; 
        font-size: 14px;
        transition: 0.3s;
        border: 1px solid #edf2f7 !important;
    }
    .form-control:focus, .form-select:focus {
        background-color: #fff !important;
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1) !important;
    }
    .bg-light-custom { background-color: #f8fafc !important; }
    .btn-save { background: #6366f1; border: none; border-radius: 12px; padding: 12px 30px; font-weight: 600; transition: 0.3s; }
    .btn-save:hover { background: #4f46e5; transform: translateY(-2px); }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .container-fluid { padding: 15px !important; }
        .header-box { flex-direction: column; align-items: flex-start !important; gap: 15px; }
        .view-btn { width: 100%; text-align: center; border-radius: 12px !important; }
        .btn-save { width: 100%; }
    }
</style>

<div class="container-fluid py-4">

    <div class="mb-4 d-flex align-items-center justify-content-between header-box">
        <div>
            <h3 class="fw-bold text-dark mb-1">Create New Order</h3>
            <p class="text-muted small mb-0">Fill in the details to register a new subscription</p>
        </div>
        <a href="<?= base_url('orders') ?>" class="btn btn-outline-primary view-btn shadow-sm px-4 py-2" style="border-radius: 12px; border: 2px solid #6366f1; color: #6366f1; font-weight: 600;">
            <i class="fas fa-list-ul me-2"></i> View All Orders
        </a>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
        <div class="card-body p-4 p-md-5">
            <form action="<?= base_url('orders/store') ?>" method="POST">
                <?= csrf_field() ?>
                
                <div class="row g-4">
                    
                    <div class="col-md-6 col-12">
                        <label class="form-label fw-bold text-muted">Select Client</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light-custom border-0" style="border-radius: 12px 0 0 12px;"><i class="fas fa-user text-muted"></i></span>
                            <select name="client_id" class="form-select bg-light-custom border-0 shadow-none" required style="border-radius: 0 12px 12px 0;">
                                <option value="">-- Choose Client --</option>
                                <?php foreach($clients as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= esc($c['client_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label fw-bold text-muted">Product Type</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light-custom border-0" style="border-radius: 12px 0 0 12px;"><i class="fas fa-layer-group text-muted"></i></span>
                            <select name="order_type_id" class="form-select bg-light-custom border-0 shadow-none" required style="border-radius: 0 12px 12px 0;">
                                <option value="">-- Select Type --</option>
                                <?php foreach($order_types as $ot): ?>
                                    <option value="<?= $ot['id'] ?>"><?= esc($ot['type_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label fw-bold text-muted">Service Provider</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light-custom border-0" style="border-radius: 12px 0 0 12px;"><i class="fas fa-server text-muted"></i></span>
                            <select name="provider_id" class="form-select bg-light-custom border-0 shadow-none" required style="border-radius: 0 12px 12px 0;">
                                <option value="">-- Select Provider --</option>
                                <?php foreach($providers as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= esc($p['provider_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label fw-bold text-muted">Domain Name</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light-custom border-0" style="border-radius: 12px 0 0 12px;"><i class="fas fa-globe text-muted"></i></span>
                            <input type="text" name="domain_name" class="form-control bg-light-custom border-0 shadow-none" placeholder="example.com" required style="border-radius: 0 12px 12px 0;">
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="form-label fw-bold text-danger">Expiry Date</label>
                        <div class="input-group">
                            <span class="input-group-text bg-danger bg-opacity-10 border-0" style="border-radius: 12px 0 0 12px;"><i class="fas fa-calendar-alt text-danger"></i></span>
                            <input type="date" name="domain_expiry_date" class="form-control bg-danger bg-opacity-10 border-0 shadow-none text-danger fw-bold" required style="border-radius: 0 12px 12px 0;">
                        </div>
                    </div>

                </div>

                <div class="mt-5">
                    <button type="submit" class="btn btn-primary btn-save px-5 shadow-sm text-white">
                        <i class="fas fa-check-circle me-2"></i> Save New Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>