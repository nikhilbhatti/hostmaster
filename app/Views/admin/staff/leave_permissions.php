<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/leaves/manage-staff') ?>" class="text-decoration-none">Staff List</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Permissions</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="fas fa-user-shield me-2 text-primary"></i> 
                LMS Permissions: <span class="text-primary"><?= esc($staff['name'] ?? $staff['username'] ?? 'User') ?></span>
            </h4>
        </div>
        <a href="<?= base_url('admin/leaves/manage-staff') ?>" class="btn btn-light btn-sm rounded-pill border shadow-sm px-3">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-3 fs-4"></i>
                <div><?= session()->getFlashdata('success') ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('admin/staff/leave_permissions/update/'.($staff['id'] ?? '')) ?>" method="POST">
        <?= csrf_field() ?>
        
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 border-0 text-secondary fw-semibold">LMS Module</th>
                            <th class="text-center border-0 text-secondary fw-semibold">View</th>
                            <!-- <th class="text-center border-0 text-secondary fw-semibold">Apply/Add</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dashboard Section (Separate Row for Apply Leave View) -->
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box text-primary rounded-3 me-3 d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px; background-color: rgba(var(--bs-primary-rgb), 0.1);">
                                        <i class="fas fa-th-large fs-5"></i>
                                    </div>
                                    <div class="fw-bold text-dark">Dashboard</div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input custom-switch" type="checkbox" 
                                           name="lms_permissions[leave_manage][view]" 
                                           value="1" <?= (isset($current_lms['leave_manage']['view']) && $current_lms['leave_manage']['view'] == 1) ? 'checked' : '' ?>>
                                </div>
                            </td>
                            <!-- <td class="text-center text-muted small">N/A</td> -->
                        </tr>

                        <!-- Apply Leave (Add only) -->
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box text-success rounded-3 me-3 d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px; background-color: rgba(var(--bs-success-rgb), 0.1);">
                                        <i class="fas fa-paper-plane fs-5"></i>
                                    </div>
                                    <div class="fw-bold text-dark">Apply Leave</div>
                                </div>
                            </td>
                            <!-- <td class="text-center text-muted small">N/A</td> -->
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input custom-switch" type="checkbox" 
                                           name="lms_permissions[leave_manage][add]" 
                                           value="1" <?= (isset($current_lms['leave_manage']['add']) && $current_lms['leave_manage']['add'] == 1) ? 'checked' : '' ?>>
                                </div>
                            </td>
                        </tr>

                        <!-- Leave History (Renamed from Leave Requests) -->
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box text-info rounded-3 me-3 d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px; background-color: rgba(var(--bs-info-rgb), 0.1);">
                                        <i class="fas fa-history fs-5"></i>
                                    </div>
                                    <div class="fw-bold text-dark">Leave History</div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input custom-switch" type="checkbox" 
                                           name="lms_permissions[leave_req][view]" 
                                           value="1" <?= (isset($current_lms['leave_req']['view']) && $current_lms['leave_req']['view'] == 1) ? 'checked' : '' ?>>
                                </div>
                            </td>
                            <!-- <td class="text-center text-muted small">N/A</td> -->
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="card-footer bg-white border-0 py-3 ps-4">
                <p class="text-muted small mb-0">
                    <i class="fas fa-info-circle me-1"></i> Permissions customized: Apply Leave split into Dashboard (View) and Apply (Add). Requests renamed to Leave History.
                </p>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 shadow-sm fw-bold">
                <i class="fas fa-save me-2"></i> Update Permissions
            </button>
        </div>
    </form>
</div>

<style>
    .custom-switch {
        width: 2.8em !important;
        height: 1.5em !important;
        cursor: pointer;
    }
    .custom-switch:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    .table-hover tbody tr:hover {
        background-color: #fbfbfb;
    }
    .breadcrumb-item + .breadcrumb-item::before {
        content: ">";
    }
</style>

<?= $this->endSection() ?>