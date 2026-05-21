<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<style>
    /* Responsive Table Design */
    @media (max-width: 991.98px) {
        .res-table tr { display: block; border: 1px solid #e2e8f0; margin-bottom: 15px; border-radius: 12px; background: #fff; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .res-table td { display: flex; justify-content: space-between; align-items: center; border: none !important; padding: 12px 15px; border-bottom: 1px dashed #f1f5f9; }
        .res-table thead { display: none; }
        .mod-header { background: #f8fafc; font-weight: bold; color: #4f46e5; border-bottom: 1px solid #e2e8f0 !important; }
        .res-table td::before { content: attr(data-label); font-weight: 600; color: #64748b; font-size: 0.85rem; }
        .res-table td.mod-header::before { content: ""; }
    }
    
    .form-check-input { width: 2.6em; height: 1.3em; cursor: pointer; }
    .form-check-input:checked { background-color: #198754; border-color: #198754; } /* Green color for consistency */
    .breadcrumb-item a { text-decoration: none; color: #64748b; }
</style>

<div class="container-fluid py-4 px-2 px-md-4">
    
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="<?= base_url('admin/staff') ?>">Staff Control</a></li>
                <li class="breadcrumb-item active">Permissions</li>
            </ol>
            <h4 class="fw-bold text-dark">
                <i class="fas fa-user-shield me-2 text-primary"></i>
                Set Permissions: <span class="text-primary"><?= esc($staff['username']) ?></span>
            </h4>
        </nav>
        <a href="<?= base_url('admin/staff') ?>" class="btn btn-light border rounded-pill d-none d-md-inline-block shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <form action="<?= base_url('admin/staff/updatePermissions/'.$staff['id']) ?>" method="POST">
        <?= csrf_field() ?>

        <?php 
            // FIXED: Sirf CRM Modules, Leave yahan se delete kar diya gaya hai
            $all_modules = [
                'clients'       => 'fas fa-box',
                'orders'        => 'fas fa-shopping-cart',
                'backups'       => 'fas fa-database',
                'notifications' => 'fas fa-bell',
                'daily_reports' => 'fas fa-file-alt'
            ];
        ?>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table align-middle mb-0 res-table">
                    <thead class="bg-light text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-4 py-3">Module Name</th>
                            <th class="text-center">View</th>
                            <th class="text-center">Add</th>
                            <th class="text-center">Edit</th>
                            <th class="text-center">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_modules as $mod => $icon): ?>
                        <tr>
                            <td class="ps-4 mod-header">
                                <div class="d-flex align-items-center">
                                    <div class="bg-white p-2 rounded-3 shadow-sm me-3 d-none d-md-block" style="width: 40px; text-align: center;">
                                        <i class="<?= $icon ?> text-primary"></i>
                                    </div>
                                    <span class="fw-bold text-dark"><?= ucwords(str_replace('_', ' ', $mod)) ?></span>
                                </div>
                            </td>
                            <?php 
                                foreach (['can_view', 'can_add', 'can_edit', 'can_delete'] as $action): 
                                    $isChecked = (isset($current_permissions[$mod][$action]) && $current_permissions[$mod][$action] == 1);
                                    $label = ucwords(str_replace(['can_', '_'], ['', ' '], $action));
                            ?>
                            <td class="text-center" data-label="<?= $label ?>">
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input" type="checkbox" 
                                           name="permissions[<?= $mod ?>][<?= $action ?>]" 
                                           value="1" <?= $isChecked ? 'checked' : '' ?>>
                                </div>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 p-3 bg-white rounded-4 shadow-sm d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <p class="text-muted small mb-0">
                <i class="fas fa-info-circle me-1 text-primary"></i> 
                Changes save karte hi staff ko naye permissions mil jayenge.
            </p>
            <button type="submit" class="btn btn-success btn-lg rounded-pill px-5 fw-bold shadow-sm w-100 w-md-auto">
                <i class="fas fa-check-circle me-2"></i> Update Permissions
            </button>
        </div>

    </form>
</div>

<?= $this->endSection() ?>