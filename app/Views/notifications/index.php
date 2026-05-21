<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .text-indigo { color: #6366f1; }
    .extra-small { font-size: 11px; }
    .notif-item { transition: all 0.2s ease; border-left: 4px solid transparent; }
    .notif-item:hover { background-color: #f8fafc !important; transform: scale(1.005); }
    .notif-unread { border-left-color: #6366f1; background-color: #f5f7ff; }
    .notif-icon { width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    
    .btn-action { width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 10px; }
    
    @media (max-width: 767.98px) {
        .container-fluid { padding: 15px !important; }
        .notif-item { flex-direction: column; align-items: flex-start !important; }
        .notif-actions { width: 100%; margin-top: 12px; justify-content: flex-end; border-top: 1px solid #eee; padding-top: 8px; }
        .notif-icon { width: 35px; height: 35px; }
    }
</style>

<div class="container-fluid py-4">
    <div class="mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">Notifications</h3>
            <p class="text-muted small mb-0">Stay updated with system activities and alerts</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('notifications/markAllRead') ?>" class="btn btn-primary shadow-sm px-4" style="border-radius: 12px; background: #6366f1; border:none;">
                <i class="fas fa-check-double me-2 small"></i> Mark All Read
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 20px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom border-light">
            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-history me-2 text-indigo"></i>Recent Activity</h6>
        </div>
        
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                <?php if(!empty($all_notifs)): foreach($all_notifs as $n): ?>
                    <?php 
                        $icon = 'fas fa-bell'; 
                        $bg_class = 'bg-primary';
                        
                        $title = $n['title'] ?? 'Alert'; // Title null check
                        if(strpos(strtolower($title), 'expiry') !== false || ($n['type'] ?? '') == 'alert') {
                            $icon = 'fas fa-exclamation-triangle';
                            $bg_class = 'bg-warning';
                        } elseif(($n['type'] ?? '') == 'staff_alert') {
                            $icon = 'fas fa-user-shield';
                            $bg_class = 'bg-danger';
                        }

                        // --- FIX FOR ERROR ---
                        // Agar link khali hai toh dashboard par bhej do, null mat bhejo
                        $finalLink = (!empty($n['link'])) ? base_url($n['link']) : base_url('dashboard');
                    ?>

                    <div class="list-group-item p-3 d-flex justify-content-between align-items-center notif-item <?= !($n['is_read'] ?? 0) ? 'notif-unread' : 'opacity-75' ?>">
                        <div class="d-flex align-items-start gap-3">
                            <div class="notif-icon rounded-circle <?= !($n['is_read'] ?? 0) ? $bg_class : 'bg-secondary' ?> text-white shadow-sm">
                                <i class="<?= $icon ?> small"></i>
                            </div>

                            <div>
                                <h6 class="mb-1 fw-bold <?= !($n['is_read'] ?? 0) ? 'text-dark' : 'text-muted' ?>">
                                    <?= esc($title) ?>
                                    <?php if(!($n['is_read'] ?? 0)): ?>
                                        <span class="badge rounded-pill bg-danger ms-2" style="font-size: 8px; vertical-align: middle;">NEW</span>
                                    <?php endif; ?>
                                </h6>
                                <p class="mb-1 small text-secondary" style="line-height: 1.4;"><?= esc($n['message'] ?? '') ?></p>
                                <div class="extra-small text-indigo fw-medium">
                                    <i class="far fa-clock me-1"></i><?= date('d M, Y • h:i A', strtotime($n['created_at'] ?? 'now')) ?>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 notif-actions">
                            <?php if(!($n['is_read'] ?? 0)): ?>
                                <a href="<?= base_url('notifications/markRead/'.($n['id'] ?? 0)) ?>" class="btn btn-light btn-action border shadow-sm" title="Mark as Read">
                                    <i class="fas fa-check text-success small"></i>
                                </a>
                            <?php endif; ?>
                            
                            <a href="<?= $finalLink ?>" class="btn btn-light btn-action border shadow-sm" title="View Details">
                                <i class="fas fa-chevron-right text-indigo small"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; else: ?>
                    <div class="p-5 text-center text-muted">
                        <div class="mb-3">
                            <i class="fas fa-bell-slash fa-3x opacity-25"></i>
                        </div>
                        <h5 class="fw-bold">No Notifications Yet</h5>
                        <p class="small">When you get alerts, they will appear here in a nice list.</p>
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-sm btn-outline-primary px-4 mt-2" style="border-radius: 10px;">Go Home</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>