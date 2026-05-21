<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-2 px-md-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <h4 class="fw-bold text-dark mb-0">
            <i class="fas fa-users-cog me-2 text-primary"></i>Staff Management
        </h4>
        <button class="btn btn-primary rounded-pill px-4 w-100 w-md-auto shadow-sm" data-bs-toggle="modal" data-bs-target="#addStaffModal">
            <i class="fas fa-plus-circle me-2"></i>Add New Staff
        </button>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="min-width: 200px;">Staff Name</th>
                            <th style="min-width: 180px;">Email</th>
                            <th style="min-width: 120px;">Status</th>
                            <th style="min-width: 150px;">Created At</th>
                            <th class="text-end pe-4" style="min-width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($staffs)): foreach($staffs as $s): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3 flex-shrink-0" style="width:35px; height:35px; font-size:0.8rem; background: #e9ecef; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                        <?= strtoupper(substr($s['name'], 0, 1)) ?>
                                    </div>
                                    <span class="fw-bold text-truncate" style="max-width: 150px;"><?= $s['name'] ?></span>
                                </div>
                            </td>
                            <td><span class="text-muted small"><?= $s['email'] ?></span></td>
                            <td>
                                <span class="badge rounded-pill <?= $s['status'] == 1 ? 'bg-light text-success border-success' : 'bg-light text-danger border-danger' ?> border px-3">
                                    <?= $s['status'] == 1 ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td class="small text-muted">
                                <i class="far fa-calendar-alt me-1"></i><?= date('d M, Y', strtotime($s['created_at'])) ?>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-light border edit-staff-btn me-1" data-id="<?= $s['id'] ?>" title="Edit">
                                        <i class="fas fa-edit text-primary"></i>
                                    </button>
                                    <a href="<?= base_url('admin/delete-staff/'.$s['id']) ?>" class="btn btn-sm btn-light border" onclick="return confirm('Pakka delete karna hai?')" title="Delete">
                                        <i class="fas fa-trash text-danger"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-user-slash fa-2x mb-3 d-block"></i> No staff found
                            </td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered px-3">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <form action="<?= base_url('admin/save-staff') ?>" method="POST">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold"><i class="fas fa-user-plus me-2 text-primary"></i>Create Staff Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Full Name</label>
                        <input type="text" name="name" class="form-control rounded-pill border-light bg-light" placeholder="Enter name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" name="email" class="form-control rounded-pill border-light bg-light" placeholder="Enter email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password</label>
                        <input type="password" name="password" class="form-control rounded-pill border-light bg-light" placeholder="Min 6 characters" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold py-2 shadow-sm">Save Staff Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered px-3">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <form action="<?= base_url('admin/update-staff') ?>" method="POST">
                <input type="hidden" name="id" id="edit_staff_id">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold"><i class="fas fa-user-edit me-2 text-dark"></i>Edit Staff Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Full Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control rounded-pill border-light bg-light" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control rounded-pill border-light bg-light" required>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label small fw-bold">Status</label>
                            <select name="status" id="edit_status" class="form-select rounded-pill border-light bg-light">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="p-2 bg-light rounded-3 mb-3">
                        <p class="mb-1 fw-bold" style="font-size: 0.75rem;"><i class="fas fa-info-circle me-1"></i>Security Tip:</p>
                        <p class="extra-small text-muted mb-0" style="font-size: 0.7rem;">Password khali chhodein agar change nahi karna hai.</p>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">New Password (Optional)</label>
                        <input type="password" name="password" class="form-control rounded-pill border-light bg-light" placeholder="Enter new password">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-dark rounded-pill w-100 fw-bold py-2 shadow-sm">Update Details</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // AJAX to get staff data for edit
    document.querySelectorAll('.edit-staff-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            // Fetch use karke data laate hain
            fetch('<?= base_url('admin/get-staff/') ?>' + id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('edit_staff_id').value = data.id;
                    document.getElementById('edit_name').value = data.name;
                    document.getElementById('edit_email').value = data.email;
                    document.getElementById('edit_status').value = data.status;
                    // Modal show karte hain
                    var editModal = new bootstrap.Modal(document.getElementById('editStaffModal'));
                    editModal.show();
                })
                .catch(error => console.error('Error:', error));
        });
    });
</script>

<style>
    @media (max-width: 768px) {
        .table td, .table th { padding: 12px 10px !important; font-size: 0.85rem; }
        .fw-bold { font-size: 0.9rem; }
        .btn-sm { padding: 0.25rem 0.5rem; }
    }
    .text-truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
</style>

<?= $this->endSection() ?>