<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    :root {
        --bg-sidebar: #0b0f1a; 
        --card-bg: rgba(255, 255, 255, 0.03);
        --accent-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        --accent-color: #818cf8;
        --text-main: #f1f5f9;
        --text-dim: #94a3b8;
        --nav-font: 'Inter', sans-serif;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    #sidebar {
        min-width: 280px;
        max-width: 280px;
        background: var(--bg-sidebar);
        height: 100vh;
        height: 100dvh;
        display: flex;
        flex-direction: column;
        transition: var(--transition);
        position: sticky;
        top: 0;
        border-right: 1px solid rgba(255, 255, 255, 0.05);
        z-index: 1050; 
        font-family: var(--nav-font);
        overflow: hidden;
    }

    .sidebar-content-wrapper {
        flex: 1 1 auto; 
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
        scrollbar-width: thin;
        scrollbar-color: var(--accent-color) transparent;
    }

    .sidebar-content-wrapper::-webkit-scrollbar { width: 5px; }
    .sidebar-content-wrapper::-webkit-scrollbar-thumb { background: rgba(99, 102, 241, 0.2); border-radius: 10px; }

    @media (max-width: 768px) {
        #sidebar {
            position: fixed;
            left: -280px;
            top: 0;
            height: 100vh;
            height: 100dvh;
        }

        .wrapper.active #sidebar {
            left: 0;
            box-shadow: 15px 0 40px rgba(0,0,0,0.6);
        }

        .sidebar-footer {
            position: sticky;
            bottom: 0;
            z-index: 99;
        }
    }

    .sidebar-header { padding: 30px 25px 20px; display: flex; align-items: center; text-decoration: none !important; flex-shrink: 0; }
    .logo-box { width: 42px; height: 42px; background: var(--accent-gradient); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 16px rgba(99, 102, 241, 0.3); margin-right: 12px; }
    .brand-name { font-size: 1.4rem; font-weight: 800; color: #fff; letter-spacing: -0.5px; }

    .nav-label { padding: 20px 25px 10px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: #475569; letter-spacing: 1.2px; }
    .nav-list { list-style: none; padding: 0 15px; margin: 0; }
    .nav-item { margin-bottom: 2px; }

    .nav-link { display: flex; align-items: center; padding: 11px 16px; color: var(--text-dim); text-decoration: none !important; border-radius: 10px; font-weight: 500; font-size: 0.9rem; transition: var(--transition); }
    .nav-link i:first-child { font-size: 1.1rem; width: 25px; margin-right: 10px; }
    .nav-link:hover, .nav-link.active { background: rgba(255, 255, 255, 0.05); color: #fff; }
    .nav-link.active { background: rgba(99, 102, 241, 0.12); color: var(--accent-color); font-weight: 600; }

    .collapse-wrapper { background: rgba(0, 0, 0, 0.2); border-radius: 10px; margin: 4px 8px; padding: 5px 0; }
    .sub-nav-list { list-style: none; padding-left: 38px; margin: 0; }
    .sub-nav-link { display: block; padding: 7px 12px; color: var(--text-dim); text-decoration: none !important; font-size: 0.85rem; border-radius: 6px; transition: 0.2s; }
    .sub-nav-link:hover, .sub-nav-link.active-sub { color: #fff; background: rgba(255,255,255,0.03); padding-left: 15px; }

    .fa-chevron-down { font-size: 0.7rem; transition: transform 0.3s; opacity: 0.4; }
    .nav-link[aria-expanded="true"] .fa-chevron-down { transform: rotate(180deg); opacity: 1; }

    .sidebar-footer { 
        padding: 15px; 
        background: var(--bg-sidebar); 
        border-top: 1px solid rgba(255,255,255,0.05); 
        flex-shrink: 0;
        margin-bottom: env(safe-area-inset-bottom);
    }
    
    .logout-action { 
        height: 48px;
        display: flex; 
        align-items: center; 
        justify-content: center; 
        border-radius: 12px; 
        color: #fff; 
        transition: 0.3s; 
        text-decoration: none !important; 
        background: rgba(239, 68, 68, 0.1); 
        border: 1px solid rgba(239, 68, 68, 0.2);
    }
    .logout-action:hover { background: #ef4444; border-color: #ef4444; transform: translateY(-2px); }
    .logout-action span { font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
</style>

<?php
$db = \Config\Database::connect();
$current_session_user_id = session()->get('admin_id'); 
$user_role = session()->get('user_role');

$checkPerm = function($module) use ($db, $current_session_user_id, $user_role) {
    if ($user_role === 'admin') return true; 
    return $db->table('permissions')
              ->where(['user_id' => $current_session_user_id, 'module_name' => $module, 'can_view' => 1])
              ->get()->getRow() ? true : false;
};

$user_row = $db->table('admins')->where('id', $current_session_user_id)->get()->getRowArray();
$lms_permissions = json_decode($user_row['lms_permissions'] ?? '{}', true);

$hasLms = function($mod, $act = 'view') use ($lms_permissions, $user_role) {
    if ($user_role === 'admin') return true;
    return (isset($lms_permissions[$mod][$act]) && $lms_permissions[$mod][$act] == 1);
};
?>

<nav id="sidebar">
    <a href="<?= base_url('admin/dashboard') ?>" class="sidebar-header">
        <div class="logo-box"><i class="fas fa-server text-white"></i></div>
        <div class="brand-name">Host<span style="color: var(--accent-color)">Master</span></div>
    </a>

    <div class="sidebar-content-wrapper">
        <div class="nav-label">General</div>

        <ul class="nav-list" id="sidebarMenu">
            <li class="nav-item">
                <a href="<?= base_url('/') ?>" class="nav-link <?= (uri_string() == '' || uri_string() == 'dashboard') ? 'active' : '' ?>">
                    <i class="fas fa-chart-pie"></i> Dashboard
                </a>
            </li>

            <?php if ($user_role == 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center justify-content-between <?= (strpos(uri_string(), 'invoice') !== false) ? 'active' : 'collapsed' ?>" 
                   data-bs-toggle="collapse" href="#billingSystemCollapse" role="button"
                   aria-expanded="<?= (strpos(uri_string(), 'invoice') !== false) ? 'true' : 'false' ?>">
                    <span><i class="fas fa-file-invoice-dollar"></i> Billing System</span>
                    <i class="fas fa-chevron-down"></i>
                </a>

                <div class="collapse <?= (strpos(uri_string(), 'invoice') !== false) ? 'show' : '' ?>" id="billingSystemCollapse" data-bs-parent="#sidebarMenu">
                    <div class="collapse-wrapper">
                        <ul class="sub-nav-list">
                            <li><a href="<?= base_url('invoice/dashboard') ?>" class="sub-nav-link <?= (uri_string() == 'invoice/dashboard' || uri_string() == 'invoice') ? 'active-sub' : '' ?>">Dashboard</a></li>
                            <li><a href="<?= base_url('invoice/customers') ?>" class="sub-nav-link <?= (strpos(uri_string(), 'invoice/customers') !== false) ? 'active-sub' : '' ?>">Customers</a></li>
                            <li><a href="<?= base_url('invoice/quotes') ?>" class="sub-nav-link <?= (strpos(uri_string(), 'invoice/quotes') !== false) ? 'active-sub' : '' ?>">Quotes</a></li>
                            <li><a href="<?= base_url('invoice/invoices') ?>" class="sub-nav-link <?= (strpos(uri_string(), 'invoice/invoices') !== false) ? 'active-sub' : '' ?>">Invoices</a></li>
                            <li><a href="<?= base_url('invoice/payments') ?>" class="sub-nav-link <?= (strpos(uri_string(), 'invoice/payments') !== false) ? 'active-sub' : '' ?>">Payments Received</a></li>
                            <li><a href="<?= base_url('invoice/items') ?>" class="sub-nav-link <?= (strpos(uri_string(), 'invoice/items') !== false) ? 'active-sub' : '' ?>">Items</a></li>
                            <li><a href="<?= base_url('invoice/taxes') ?>" class="sub-nav-link <?= (strpos(uri_string(), 'invoice/taxes') !== false) ? 'active-sub' : '' ?>">Taxes</a></li>
                        </ul>
                    </div>
                </div>
            </li>
            <?php endif; ?>

            <?php if ($user_role !== 'admin'): ?>
            <li class="nav-item">
                <a href="<?= base_url('staff/add-report') ?>" class="nav-link <?= (uri_string() == 'staff/add-report') ? 'active' : '' ?>">
                    <i class="fas fa-plus-circle text-success"></i> Add Report
                </a>
            </li>
            <?php endif; ?>

            <?php if ($checkPerm('orders')): ?>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center justify-content-between <?= (strpos(uri_string(), 'orders') !== false) ? 'active' : 'collapsed' ?>" 
                   data-bs-toggle="collapse" href="#orderCollapse" role="button">
                    <span><i class="fas fa-shopping-cart"></i> Orders</span>
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="collapse <?= (strpos(uri_string(), 'orders') !== false) ? 'show' : '' ?>" id="orderCollapse" data-bs-parent="#sidebarMenu">
                    <div class="collapse-wrapper">
                        <ul class="sub-nav-list">
                            <li><a href="<?= base_url('orders') ?>" class="sub-nav-link <?= (uri_string() == 'orders') ? 'active-sub' : '' ?>">Orders List</a></li>
                            <li><a href="<?= base_url('orders/add') ?>" class="sub-nav-link <?= (uri_string() == 'orders/add') ? 'active-sub' : '' ?>">New Order</a></li>
                            <li><a href="<?= base_url('orders/types') ?>" class="sub-nav-link <?= (uri_string() == 'orders/types') ? 'active-sub' : '' ?>">Service Plans</a></li>
                        </ul>
                    </div>
                </div>
            </li>
            <?php endif; ?>

            <?php if ($checkPerm('clients')): ?>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center justify-content-between <?= (strpos(uri_string(), 'clients') !== false) ? 'active' : 'collapsed' ?>" 
                   data-bs-toggle="collapse" href="#clientCollapse" role="button">
                    <span><i class="fas fa-address-book"></i> Client Base</span>
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="collapse <?= (strpos(uri_string(), 'clients') !== false) ? 'show' : '' ?>" id="clientCollapse" data-bs-parent="#sidebarMenu">
                    <div class="collapse-wrapper">
                        <ul class="sub-nav-list">
                            <li><a href="<?= base_url('clients') ?>" class="sub-nav-link <?= (uri_string() == 'clients') ? 'active-sub' : '' ?>">All Clients</a></li>
                            <li><a href="<?= base_url('clients/add') ?>" class="sub-nav-link <?= (uri_string() == 'clients/add') ? 'active-sub' : '' ?>">Add Client</a></li>
                        </ul>
                    </div>
                </div>
            </li>
            <?php endif; ?>

            <?php if ($checkPerm('daily_reports')): ?>
            <li class="nav-item">
                <a href="<?= base_url('reports/all') ?>" class="nav-link <?= (strpos(uri_string(), 'reports') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-chart-line"></i> Daily Reports
                </a>
            </li>
            <?php endif; ?>
        </ul>

        <?php if ($user_role == 'admin'): ?>
        <div class="nav-label">Master Admin</div>
        <ul class="nav-list">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center justify-content-between <?= (strpos(uri_string(), 'admin/staff') !== false) ? 'active' : 'collapsed' ?>" 
                   data-bs-toggle="collapse" href="#staffCollapse" role="button">
                    <span><i class="fas fa-user-shield"></i> Staff Control</span>
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="collapse <?= (strpos(uri_string(), 'admin/staff') !== false) ? 'show' : '' ?>" id="staffCollapse" data-bs-parent="#sidebarMenu">
                    <div class="collapse-wrapper">
                        <ul class="sub-nav-list">
                            <li><a href="<?= base_url('admin/staff') ?>" class="sub-nav-link <?= (uri_string() == 'admin/staff') ? 'active-sub' : '' ?>">Staff Permission</a></li>
                            <li><a href="<?= base_url('admin/leaves/staff-manage') ?>" class="sub-nav-link <?= (uri_string() == 'admin/leaves/staff-manage') ? 'active-sub' : '' ?>">Staff Manage</a></li>
                        </ul>
                    </div>
                </div>
            </li>
        </ul>
        <?php endif; ?>

        <div class="nav-label">Management & Utilities</div>
        <ul class="nav-list">
            <?php if ($user_role == 'admin' && $checkPerm('leave_management')): ?>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center justify-content-between <?= (strpos(uri_string(), 'admin/leaves') !== false) ? 'active' : 'collapsed' ?>" 
                   data-bs-toggle="collapse" href="#leaveCollapse" role="button">
                    <span><i class="fas fa-calendar-check"></i> Leave System</span>
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="collapse <?= (strpos(uri_string(), 'admin/leaves') !== false) ? 'show' : '' ?>" id="leaveCollapse" data-bs-parent="#sidebarMenu">
                    <div class="collapse-wrapper">
                        <ul class="sub-nav-list">
                            <li><a href="<?= base_url('admin/leaves') ?>" class="sub-nav-link <?= (uri_string() == 'admin/leaves') ? 'active-sub' : '' ?>">Overview</a></li>
                            <li><a href="<?= base_url('admin/leaves/types') ?>" class="sub-nav-link <?= (uri_string() == 'admin/leaves/types') ? 'active-sub' : '' ?>">Leave Types</a></li>
                            <li><a href="<?= base_url('admin/leaves/allocate') ?>" class="sub-nav-link <?= (uri_string() == 'admin/leaves/allocate') ? 'active-sub' : '' ?>">Allocate Leaves</a></li>
                            <li><a href="<?= base_url('admin/leaves/requests') ?>" class="sub-nav-link <?= (uri_string() == 'admin/leaves/requests') ? 'active-sub' : '' ?>">Leave Requests</a></li>
                            <li><a href="<?= base_url('admin/leaves/reporting') ?>" class="sub-nav-link <?= (uri_string() == 'admin/leaves/reporting') ? 'active-sub' : '' ?>">Leave Reporting</a></li>
                        </ul>
                    </div>
                </div>
            </li>
            <?php endif; ?>

            <?php if ($user_role !== 'admin' && ($hasLms('leave_manage', 'view') || $hasLms('leave_req', 'view'))): ?>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center justify-content-between <?= (strpos(uri_string(), 'staff') !== false) ? 'active' : 'collapsed' ?>" 
                   data-bs-toggle="collapse" href="#staffLeaveCollapse" role="button">
                    <span><i class="fas fa-user-clock me-2"></i> My Leaves</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <div class="collapse <?= (strpos(uri_string(), 'staff') !== false) ? 'show' : '' ?>" id="staffLeaveCollapse" data-bs-parent="#sidebarMenu">
                    <div class="collapse-wrapper">
                        <ul class="sub-nav-list nav flex-column">
                            <li class="nav-item"><a href="<?= base_url('staff/leave-dashboard') ?>" class="sub-nav-link p-2 <?= (uri_string() == 'staff/leave-dashboard') ? 'active-sub fw-bold text-primary' : '' ?>"><i class="fas fa-th-large me-2"></i> My Dashboard</a></li>
                            <?php if ($hasLms('leave_manage', 'add')): ?>
                            <li class="nav-item"><a href="<?= base_url('staff/apply-leave') ?>" class="sub-nav-link p-2 <?= (uri_string() == 'staff/apply-leave') ? 'active-sub fw-bold text-primary' : '' ?>"><i class="fas fa-paper-plane me-2"></i> Apply Leave</a></li>
                            <?php endif; ?>
                            <?php if ($hasLms('leave_req', 'view')): ?>
                            <li class="nav-item"><a href="<?= base_url('staff/leave-history') ?>" class="sub-nav-link p-2 <?= (uri_string() == 'staff/leave-history') ? 'active-sub fw-bold text-primary' : '' ?>"><i class="fas fa-history me-2"></i> Leave History</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </li>
            <?php endif; ?>

            <?php if ($checkPerm('backups')): ?>
            <li class="nav-item">
                <a href="<?= base_url('backups') ?>" class="nav-link <?= (strpos(uri_string(), 'backups') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-database"></i> Database Backup
                </a>
            </li>
            <?php endif; ?>
             
            <?php if ($checkPerm('activity_logs')): ?>
            <li class="nav-item">
                <a href="<?= base_url('activity_logs') ?>" class="nav-link <?= (uri_string() == 'activity_logs') ? 'active' : '' ?>">
                    <i class="fas fa-history"></i> Activity Logs
                </a>
            </li>
            <?php endif; ?>

            <?php if ($user_role == 'admin'): ?>
            <li class="nav-item mt-2">
                <a href="<?= base_url('clients/settings') ?>" class="nav-link <?= (uri_string() == 'clients/settings') ? 'active' : '' ?>">
                    <i class="fas fa-cog me-2"></i> SMTP Config
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="sidebar-footer">
        <a href="<?= base_url('logout') ?>" class="logout-action w-100">
            <i class="fas fa-power-off me-2"></i>
            <span>Logout</span>
        </a>
    </div>
</nav>