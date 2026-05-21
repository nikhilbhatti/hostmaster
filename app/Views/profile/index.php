<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        --glass-bg: rgba(255, 255, 255, 0.95);
    }

    /* Header Background */
    .profile-header {
        background: var(--primary-gradient);
        border-radius: 0 0 40px 40px;
        padding: 60px 20px 100px;
        color: white;
        text-align: center;
    }

    /* Overlapping Card Effect */
    .overlap-container {
        margin-top: -80px;
        padding-bottom: 50px;
    }

    .profile-card { 
        border-radius: 24px; 
        border: none; 
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    /* Avatar Box */
    .avatar-wrapper {
        position: relative;
        width: 120px;
        height: 120px;
        margin: -60px auto 20px;
    }

    .avatar-box {
        width: 100%;
        height: 100%;
        border-radius: 35px;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.5rem;
        font-weight: 800;
        color: #6366f1;
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        border: 5px solid #fff;
    }

    .status-badge {
        position: absolute;
        bottom: 5px;
        right: 5px;
        width: 28px;
        height: 28px;
        background: #10b981;
        border: 4px solid #fff;
        border-radius: 50%;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    /* Form Styling */
    .custom-input { 
        border-radius: 12px; 
        border: 1px solid #e2e8f0; 
        padding: 14px 18px; 
        background: #f8fafc;
        font-size: 0.95rem;
        transition: all 0.2s ease-in-out;
    }

    .custom-input:focus { 
        background: #fff; 
        border-color: #6366f1; 
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); 
    }

    .label-small { 
        font-size: 0.75rem; 
        font-weight: 700; 
        color: #64748b; 
        margin-bottom: 8px; 
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Buttons */
    .btn-update {
        background: var(--primary-gradient);
        border: none;
        padding: 14px 35px;
        border-radius: 15px;
        font-weight: 700;
        color: white;
        transition: all 0.3s;
    }

    .btn-update:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
        color: white;
    }

    .info-tile {
        background: #f1f5f9;
        border-radius: 18px;
        padding: 15px;
        margin-bottom: 12px;
        transition: 0.2s;
    }
    
    .info-tile:hover { background: #e2e8f0; }

    .truncate-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
    }

    @media (max-width: 768px) {
        .profile-header { padding: 40px 15px 80px; }
        .overlap-container { margin-top: -60px; }
        .btn-update { width: 100%; }
    }
</style>

<div class="container-fluid p-0">
    <div class="profile-header shadow-sm">
        <h2 class="fw-bold mb-1">Account Settings</h2>
        <p class="opacity-75 mb-0">Customize your profile and secure your credentials</p>
    </div>

    <div class="container overlap-container">
        <div class="row g-4">
            
            <div class="col-lg-4">
                <div class="card profile-card shadow-lg p-4 text-center h-100">
                    <div class="avatar-wrapper">
                        <div class="avatar-box">
                            <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div class="status-badge" title="Active Account"></div>
                    </div>
                    
                    <h4 class="fw-bold text-dark mb-0 mt-2"><?= esc($user['name']) ?></h4>
                    <p class="text-muted small mb-3">@<?= esc($user['username']) ?></p>
                    
                    <div class="mb-4">
                        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-4 py-2 fw-bold">
                            <i class="bi bi-shield-lock me-1"></i> <?= esc($user['role'] ?? 'Administrator') ?>
                        </span>
                    </div>

                    <hr class="opacity-50 mb-4">

                    <div class="text-start">
                        <div class="info-tile d-flex align-items-center">
                            <div class="bg-white rounded-3 p-2 me-3 shadow-sm text-primary">
                                <i class="bi bi-envelope fs-5"></i>
                            </div>
                            <div>
                                <label class="label-small mb-0">Email Address</label>
                                <div class="fw-bold text-dark small truncate-text" title="<?= esc($user['email']) ?>">
                                    <?= esc($user['email']) ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="info-tile d-flex align-items-center">
                            <div class="bg-white rounded-3 p-2 me-3 shadow-sm text-primary">
                                <i class="bi bi-calendar-check fs-5"></i>
                            </div>
                            <div>
                                <label class="label-small mb-0">Member Since</label>
                                <div class="fw-bold text-dark small">
                                    <?= date('d M, Y', strtotime($user['created_at'])) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card profile-card shadow-lg p-4 p-md-5">
                    <form action="<?= base_url('profile/update') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-4 p-2 me-3">
                                <i class="bi bi-person-bounding-box fs-4"></i>
                            </div>
                            <h5 class="fw-bold m-0 text-dark">Personal Details</h5>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="label-small"><i class="bi bi-person text-primary"></i> Full Name</label>
                                <input type="text" name="name" class="form-control custom-input" value="<?= esc($user['name']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="label-small"><i class="bi bi-at text-primary"></i> Username</label>
                                <input type="text" name="username" class="form-control custom-input" value="<?= esc($user['username']) ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="label-small"><i class="bi bi-envelope-at text-primary"></i> Email Address</label>
                                <input type="email" name="email" class="form-control custom-input" value="<?= esc($user['email']) ?>" required>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-4 mt-5">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-4 p-2 me-3">
                                <i class="bi bi-shield-lock fs-4"></i>
                            </div>
                            <h5 class="fw-bold m-0 text-dark">Security & Password</h5>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <label class="label-small"><i class="bi bi-key text-warning"></i> Change Password</label>
                                <input type="password" name="password" class="form-control custom-input" placeholder="Leave blank to keep current password">
                                <div class="mt-2 text-muted" style="font-size: 11px;">
                                    <i class="bi bi-info-circle me-1"></i> Use at least 8 characters if changing.
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 pt-4 border-top d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                            <p class="text-muted small m-0"><i class="bi bi-clock-history me-1"></i> Profile changes track in activity logs</p>
                            <button type="submit" class="btn btn-update">
                                Update My Profile <i class="bi bi-check-circle ms-2 small"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (session()->getFlashdata('status')): ?>
            Swal.fire({ 
                icon: 'success', 
                title: 'Success!', 
                text: "<?= session()->getFlashdata('status') ?>", 
                confirmButtonColor: '#6366f1',
                timer: 3000
            });
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
            Swal.fire({ 
                icon: 'error', 
                title: 'Opps!', 
                text: "<?= session()->getFlashdata('error') ?>",
                confirmButtonColor: '#ef4444'
            });
        <?php endif; ?>
    });
</script>
<?= $this->endSection() ?>