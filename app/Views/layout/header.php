<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'HostMaster Pro' ?> | Admin CRM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* 1. Body Setup */
        html, body { 
            height: 100%; 
            margin: 0; 
            padding: 0; 
            overflow: hidden; 
            font-family: 'Inter', sans-serif; 
            background-color: #f4f7f6; 
        }

        .wrapper { 
            display: flex; 
            height: 100vh; 
            width: 100%; 
            overflow: hidden;
            position: relative;
        }

        /* 2. Content Area */
        #content { 
            display: flex;
            flex-direction: column; 
            flex: 1;
            min-width: 0;
            height: 100vh;
            position: relative;
            transition: all 0.3s ease;
        }

        /* 3. Header Styling */
        .navbar-custom { 
            background: #fff; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); 
            padding: 10px 25px; 
            border-radius: 15px;
            margin: 15px 15px 10px 15px; 
            flex-shrink: 0; 
        }

        /* 4. Scrollable Main Area */
        main {
            flex: 1; 
            overflow-y: auto !important; 
            overflow-x: hidden;
            padding: 10px 25px;
            display: block; 
            -webkit-overflow-scrolling: touch;
        }

        .content-body {
            min-height: 100%; 
            padding-bottom: 20px;
        }

        /* 5. Footer Area - Fixed single placement */
        .fixed-footer-container {
            flex-shrink: 0; 
            background: #fff;
            border-top: 1px solid #e2e8f0;
            width: 100%;
            z-index: 100;
        }

        /* Notification Dropdown Styling */
        .dropdown-menu-notif { 
            width: 320px; 
            max-width: 90vw;
            border-radius: 18px; 
            border: none; 
            box-shadow: 0 15px 30px rgba(0,0,0,0.1); 
            margin-top: 15px;
            overflow: hidden;
        }

        /* Mobile Adjustments */
        @media (max-width: 768px) {
            .navbar-custom { margin: 10px; padding: 10px 15px; }
            main { padding: 10px 15px; }
            .avatar-circle { width: 35px; height: 35px; }
        }

        .notif-item { transition: 0.2s; border-bottom: 1px solid #f1f5f9; text-decoration: none !important; }
        .notif-item:hover { background-color: #f8fafc; }
        .notif-unread { background-color: #f0f7ff; border-left: 4px solid #6366f1; }
        .extra-small { font-size: 0.7rem; }
        
        .avatar-circle {
            width: 40px; height: 40px; 
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            color: white; font-weight: 600;
            display: flex; align-items: center; justify-content: center;
            border-radius: 12px; text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(99, 102, 241, 0.2);
        }
        .avatar-circle:hover { transform: translateY(-2px); color: white; }

        .pulse-danger { animation: pulse-red 2s infinite; }
        @keyframes pulse-red { 
            0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
            100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
        }

        main::-webkit-scrollbar { width: 5px; }
        main::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body>

<?php
    $db = \Config\Database::connect();
    $role = session()->get('role') ?? 'staff';
    
    // Notifications Fetching
    if(!isset($notifications) || empty($notifications)){
        $builder = $db->table('notifications')
                      ->select('notifications.*, admins.name as creator_name')
                      ->join('admins', 'admins.id = notifications.user_id', 'left');

        if ($role !== 'admin') {
            $builder->where('is_admin_only', 0);
        }

        $notifications = $builder->orderBy('notifications.created_at', 'DESC')
                                 ->limit(10)
                                 ->get()
                                 ->getResult();
    }
    
    // Unread Count
    if(!isset($unread_count)){
        $countBuilder = $db->table('notifications')->where('is_read', 0);
        if ($role !== 'admin') {
            $countBuilder->where('is_admin_only', 0);
        }
        $unread_count = $countBuilder->countAllResults();
    }
?>

<div class="wrapper">
    <?= view('layout/sidebar') ?>

    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container-fluid">
                <div class="d-flex align-items-center">
                    <i class="fas fa-bars me-3 text-muted" id="sidebarCollapse" style="cursor:pointer;"></i>
                    <h5 class="mb-0 fw-bold text-dark d-none d-sm-block"><?= $title ?? 'System Overview' ?></h5>
                </div>
                
                <div class="ms-auto d-flex align-items-center">
                    <div class="dropdown me-2 me-md-3">
                        <button class="btn btn-light position-relative rounded-circle p-2" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell text-muted"></i>
                            <?php if($unread_count > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger pulse-danger" style="font-size: 0.6rem;">
                                    <?= $unread_count ?>
                                </span>
                            <?php endif; ?>
                        </button>
                        
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-notif p-0">
                            <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                                <h6 class="mb-0 fw-bold" style="font-size: 0.85rem;">Notifications</h6>
                                <?php if($unread_count > 0): ?>
                                    <a href="<?= base_url('notifications/markAllRead') ?>" class="extra-small text-primary fw-bold text-decoration-none">Mark all read</a>
                                <?php endif; ?>
                            </div>
                            
                            <div style="max-height: 350px; overflow-y: auto;">
                                <?php if(!empty($notifications)): foreach($notifications as $n): 
                                    $notif = (object)$n; 
                                    $is_unread = (isset($notif->is_read) && $notif->is_read == 0);
                                    $iconClass = 'fa-bell'; $bgClass = 'bg-primary';
                                    if(isset($notif->type)){
                                        if($notif->type == 'expiry') { $iconClass = 'fa-clock'; $bgClass = 'bg-warning'; }
                                        elseif($notif->type == 'critical') { $iconClass = 'fa-exclamation-triangle'; $bgClass = 'bg-danger'; }
                                    }
                                ?>
                                    <a href="<?= base_url('notifications/view/' . ($notif->id ?? 0)) ?>" 
                                       class="dropdown-item p-3 notif-item d-flex align-items-start gap-3 <?= $is_unread ? 'notif-unread' : 'opacity-75' ?>">
                                       
                                        <div class="p-2 rounded-circle <?= $bgClass ?> text-white d-flex align-items-center justify-content-center" 
                                            style="font-size: 0.7rem; width: 30px; height: 30px; flex-shrink: 0;">
                                            <i class="fas <?= $iconClass ?>"></i>
                                        </div>

                                        <div class="text-wrap">
                                            <div class="small fw-bold text-dark"><?= esc($notif->title ?? 'Notification') ?></div>
                                            <div class="extra-small text-muted mb-1"><?= esc($notif->message ?? '') ?></div>
                                            <div class="extra-small opacity-50">
                                                <i class="far fa-clock me-1"></i><?= date('h:i A', strtotime($notif->created_at)) ?>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; else: ?>
                                    <div class="p-4 text-center text-muted small">No notifications</div>
                                <?php endif; ?>
                            </div>
                            <a href="<?= base_url('notifications') ?>" class="dropdown-item text-center p-2 border-top extra-small fw-bold text-primary">View All</a>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center border-start ps-3">
                        <div class="text-end me-2 d-none d-md-block">
                            <p class="mb-0 small fw-bold text-dark"><?= session()->get('name') ?: 'Admin' ?></p>
                            <p class="mb-0 text-success fw-bold" style="font-size: 0.6rem;"><i class="fas fa-circle me-1"></i>Online</p>
                        </div>
                        <a href="<?= base_url('profile') ?>" class="avatar-circle">
                            <?= strtoupper(substr(session()->get('name') ?: 'A', 0, 1)) ?>
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <main id="main-scroll-area">
            <div class="content-body">
                <?= $this->renderSection('content') ?>
            </div>
        </main>

        <div class="fixed-footer-container">
            <?= view('layout/footer') ?>
        </div>
        
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Sidebar Toggle
    document.getElementById('sidebarCollapse')?.addEventListener('click', function() {
        document.querySelector('.wrapper').classList.toggle('active');
    });
</script>

</body>
</html>