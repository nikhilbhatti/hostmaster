<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .bg-indigo-soft { background-color: #eef2ff; }
    .text-indigo { color: #6366f1; }
    .btn-indigo { background: #6366f1; color: white; border: none; }
    .btn-indigo:hover { background: #4f46e5; color: white; }
    .config-card { border-radius: 20px; transition: transform 0.2s; }
    .config-card:hover { transform: translateY(-5px); }
    .table thead th { border: none; letter-spacing: 0.5px; }
    .form-control:focus { box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2); border: 1px solid #6366f1 !important; }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .container-fluid { padding: 15px !important; }
        .config-card { padding: 20px !important; }
        .d-flex.gap-2 { flex-direction: column; } /* Form input and button stack on mobile */
        .btn-indigo, .btn-primary { width: 100%; }
    }
</style>

<div class="container-fluid py-4">
    
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1">Configuration</h3>
        <p class="text-muted small mb-0">Manage your service types and providers</p>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-12">
            <div class="card border-0 shadow-sm p-4 config-card">
                <h5 class="fw-bold mb-3 text-dark d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-2">
                        <i class="fas fa-layer-group text-primary"></i>
                    </div>
                    Service Types
                </h5>
                
                <form action="<?= base_url('orders/save_type') ?>" method="POST" class="d-flex gap-2 mb-4">
                    <input type="text" name="type_name" class="form-control bg-light border-0 py-2" placeholder="e.g. SEO Service" required>
                    <button type="submit" class="btn btn-primary px-4 rounded-3 shadow-sm">
                        <i class="fas fa-plus me-1 small"></i> Add
                    </button>
                </form>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light text-muted small">
                            <tr>
                                <th class="ps-3">SERVICE NAME</th>
                                <th class="text-end pe-3">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($order_types)): ?>
                                <?php foreach($order_types as $ot): ?>
                                <tr>
                                    <td class="ps-3 fw-bold text-dark small"><?= esc($ot['type_name']) ?></td>
                                    <td class="text-end pe-3">
                                        <a href="<?= base_url('orders/delete_type/'.$ot['id']) ?>" 
                                           class="btn btn-light btn-sm rounded-circle text-danger shadow-sm" 
                                           onclick="return confirm('Delete Service ')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center py-4 text-muted small">No services added yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-12">
            <div class="card border-0 shadow-sm p-4 config-card">
                <h5 class="fw-bold mb-3 text-dark d-flex align-items-center">
                    <div class="bg-indigo-soft p-2 rounded-3 me-2">
                        <i class="fas fa-server text-indigo"></i>
                    </div>
                    Service Providers
                </h5>
                
                <form action="<?= base_url('orders/save_provider') ?>" method="POST" class="d-flex gap-2 mb-4">
                    <input type="text" name="provider_name" class="form-control bg-light border-0 py-2" placeholder="e.g. Hostinger" required>
                    <button type="submit" class="btn btn-indigo px-4 rounded-3 shadow-sm">
                        <i class="fas fa-plus me-1 small"></i> Add
                    </button>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light text-muted small">
                            <tr>
                                <th class="ps-3">PROVIDER NAME</th>
                                <th class="text-end pe-3">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($providers)): ?>
                                <?php foreach($providers as $p): ?>
                                <tr>
                                    <td class="ps-3 fw-bold text-indigo small"><?= esc($p['provider_name']) ?></td>
                                    <td class="text-end pe-3">
                                        <a href="<?= base_url('orders/delete_provider/'.$p['id']) ?>" 
                                           class="btn btn-light btn-sm rounded-circle text-danger shadow-sm" 
                                           onclick="return confirm('Delete Service Provider?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center py-4 text-muted small">No providers added yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>