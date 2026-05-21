<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-plus-circle me-2"></i>Add Leave Type</h6>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/store-leave-type') ?>" method="POST">
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">Leave Name (e.g. Sick Leave)</label>
                            <input type="text" name="leave_name" class="form-control form-control-lg shadow-none border-2" placeholder="Enter name..." required>
                            <div class="form-text mt-2">Sirf leave ka category name daalein (e.g. Casual Leave).</div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 btn-lg fw-bold shadow-sm">
                            <i class="fas fa-save me-2"></i>Create Type
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-list me-2 text-primary"></i>Active Leave Categories
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 py-3">Leave Name</th>
                                    <th class="text-center py-3">Status</th>
                                    <th class="text-end pe-4 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($types)): foreach($types as $t): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-3">
                                                <i class="fas fa-folder text-primary"></i>
                                            </div>
                                            <span class="fw-bold text-dark"><?= $t['leave_name'] ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success-light text-success border border-success px-3">Active</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="<?= base_url('admin/delete-leave-type/' . $t['id']) ?>" 
                                           class="btn btn-sm btn-outline-danger border-0" 
                                           onclick="return confirm('Kya aap is category ko delete karna chahte hain?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted">
                                            No leave types found. Please add one.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-success-light { background-color: #e8fadf; }
    .table th { font-size: 12px; letter-spacing: 0.5px; text-transform: uppercase; }
    .form-control:focus { border-color: #4e73df; }
</style>
<?= $this->endSection() ?>