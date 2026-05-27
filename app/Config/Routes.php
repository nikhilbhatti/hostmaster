<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --- 1. Authentication (Public Routes) ---
$routes->get('/', 'Auth::login'); 
$routes->get('login', 'Auth::login');
$routes->post('auth/authenticate', 'Auth::authenticate');
$routes->get('logout', 'Auth::logout');

// --- PROTECTED ROUTES (Sirf Login ke baad chalenge) ---
$routes->group('', ['filter' => 'auth'], function($routes) {

  // --- 2. Dashboard (Home) ---
    $routes->get('dashboard', 'Home::index');
    $routes->post('home/submitReport', 'Home::submitReport'); // Staff daily report ke liye
    $routes->get('mark-all-read', 'Home::markAllRead'); // Layout ke "Mark All Read" link ke liye
    
    // --- DAILY REPORTS ROUTES (Added Fix) ---
    $routes->get('reports/all', 'Home::allReports'); 
    $routes->get('reports/delete/(:num)', 'Home::deleteReport/$1'); // Delete report route
    $routes->post('reports/update/(:num)', 'Home::updateReport/$1'); // Update report route
    
    // --- FIXED: 404 ERROR SOLUTIONS (Screenshot Fixes) ---
    // Screenshot 2 & 4 ke mutabik ye missing routes add kiye hain:
    $routes->post('home/updateReport/(:num)', 'Home::updateReport/$1'); 
    $routes->get('home/deleteReport/(:num)', 'Home::deleteReport/$1'); // Naya add kiya image_7b4166.png ke liye
    
    // --- FIXED: Activity Logs Route ---
    $routes->get('activity_logs', 'Home::activity_logs'); 
    
    // --- Notification Helper Route ---
    $routes->get('notifications/go/(:num)', 'Home::notificationGo/$1');

    // --- 3. Staff & Admin Management (UPDATED & FIXED) ---
    $routes->group('admin', function($routes) {
        // Staff List
        $routes->get('staff', 'Admin\Staff::index');
        
        // --- Permissions System (ACTIVE) ---
        $routes->get('staff/permissions/(:num)', 'Admin\Staff::permissions/$1');
        $routes->post('staff/updatePermissions/(:num)', 'Admin\Staff::updatePermissions/$1');
        
        // Profile
        $routes->get('profile', 'ProfileController::index');
        $routes->post('profile/update', 'ProfileController::update');
        $routes->get('status', 'Home::index');
    });

    // --- 4. Notifications Management (FIXED: Added 'view' route to fix 404) ---
    $routes->group('notifications', function($routes) {
        $routes->get('/', 'NotificationController::index'); 
        $routes->get('markAllRead', 'NotificationController::markAllRead'); 
        $routes->get('read/(:num)', 'NotificationController::markAsRead/$1');
        $routes->get('markAsRead/(:num)', 'NotificationController::markAsRead/$1');
        $routes->get('markRead/(:num)', 'NotificationController::markAsRead/$1'); 
        
        // --- FIX FOR 404: Adding missing 'view' route ---
        $routes->get('view/(:num)', 'NotificationController::go/$1'); 
        
        // Pointing to Home/Notification controller appropriately
        $routes->get('go/(:num)', 'NotificationController::go/$1'); 
    });

    // --- 5. Clients Management ---
    $routes->group('clients', function($routes) {
        $routes->get('/', 'ClientController::index');
        $routes->get('add', 'ClientController::add');
        $routes->post('store', 'ClientController::store');
        $routes->get('view/(:num)', 'ClientController::view/$1');
        $routes->get('edit/(:num)', 'ClientController::edit/$1');
        $routes->post('update/(:num)', 'ClientController::update/$1'); 
        $routes->get('delete/(:num)', 'ClientController::delete/$1');
        
        $routes->get('expiring', 'Home::index'); 
        $routes->get('settings', 'Orders::settings'); 
        $routes->get('getClientDetails/(:num)', 'ClientController::getClientDetails/$1');
        $routes->get('getClientOrders/(:num)', 'ClientController::getClientOrders/$1');
        $routes->post('send-notice', 'Home::sendNotice');
    });

    // --- 6. Orders & Service Management ---
    $routes->group('orders', function($routes) {
        $routes->get('/', 'Orders::index');
        $routes->get('add', 'Orders::add');
        $routes->post('store', 'Orders::store');
        $routes->get('view/(:num)', 'Orders::view/$1'); 
        $routes->get('edit/(:num)', 'Orders::edit/$1');
        $routes->post('update/(:num)', 'Orders::update/$1');
        $routes->get('delete/(:num)', 'Orders::delete/$1');
        
        $routes->get('types', 'Orders::types');
        $routes->post('save_type', 'Orders::save_type');
        $routes->post('save_provider', 'Orders::save_provider');
        $routes->get('delete_type/(:num)', 'Orders::delete_type/$1');
        $routes->get('delete_provider/(:num)', 'Orders::delete_provider/$1');
        
        $routes->get('settings', 'Orders::settings');
        $routes->post('updateSettings', 'Orders::updateSettings'); 
        $routes->post('send-notice', 'Orders::sendNotice');
    });

    // --- 7. Backup Management (FIXED PATHS) ---
    // --- 7. Backup Management (FIXED & CLEAN) ---
    $routes->group('backups', function($routes) {
        $routes->get('/', 'Backups::index');
        $routes->get('overdue', 'Home::index');
        $routes->get('add', 'Backups::add');
        $routes->post('store', 'Backups::store');
        $routes->get('view/(:num)', 'Backups::view/$1');
        $routes->get('edit/(:num)', 'Backups::edit/$1');
        $routes->post('update/(:num)', 'Backups::update/$1'); 
        
        $routes->get('mark-done/(:num)', 'Backups::markDone/$1'); 
        $routes->get('delete/(:num)', 'Backups::delete/$1');

        // FIXED LINE: Yahan se 'backups/' hata diya hai kyunki ye group ke andar hai
        $routes->get('get-client-orders/(:num)', 'Backups::getClientOrders/$1');
    });

    // --- 8. Profile & General ---
    $routes->get('profile', 'ProfileController::index');
    $routes->post('profile/update', 'ProfileController::update');
    $routes->post('home/sendNotice', 'Home::sendNotice');
    
    // Fallback for missing backup route
    $routes->get('home/markBackupDone/(:num)', 'Backups::markDone/$1');
});
//lms_shift route hostmaster

$routes->get('admin/dashboard', '\App\Controllers\Home::index', ['filter' => 'auth']);
$routes->get('admin', '\App\Controllers\Home::index', ['filter' => 'auth']);

// ==========================================================
// --- 2. HOLIDAY MANAGEMENT & MISC ---
// ==========================================================
$routes->match(['GET', 'POST'], 'admin/delete-holiday/(:num)', '\App\Controllers\Admin\HolidayController::deleteHoliday/$1');
$routes->post('admin/save-holiday', '\App\Controllers\Admin\HolidayController::saveHoliday');
$routes->post('admin/store-holiday', '\App\Controllers\Admin\HolidayController::saveHoliday');
$routes->get('admin/get-holidays', '\App\Controllers\Admin\HolidayController::getHolidays');

// --- User & Leave Management (Direct) ---
$routes->get('admin/toggle-user/(:num)/(:num)', '\App\Controllers\Admin\LeaveController::toggleUser/$1/$2');
$routes->get('admin/delete-leave-type/(:num)', '\App\Controllers\Admin\LeaveController::deleteLeaveType/$1');
$routes->post('admin/approve-reject', '\App\Controllers\Admin\LeaveController::updateLeaveStatus');

// --- Leave Permissions ---
$routes->get('admin/staff/leave_permissions/(:num)', '\App\Controllers\Admin\LeavePermission::index/$1');
$routes->post('admin/staff/leave_permissions/update/(:num)', '\App\Controllers\Admin\LeavePermission::update/$1');


// ==========================================================
// --- 3. STAFF SIDE ROUTES ---
// ==========================================================
$routes->group('staff', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'Home::index');
    $routes->get('leave-dashboard', 'StaffController::leaveDashboard');
    
    // --- Leave Actions ---
    $routes->get('apply-leave', 'StaffController::applyLeave');
    $routes->post('store-leave', 'StaffController::storeLeave');
    $routes->post('submit-leave', 'StaffController::storeLeave'); 
    $routes->get('leave-history', 'StaffController::leaveHistory');
    $routes->get('get-official-holidays', 'StaffController::getOfficialHolidays');

    // --- DAILY WORK REPORT ROUTES (NAYA ADD KIYA) ---
    $routes->get('add-report', 'StaffController::add_report');
    $routes->post('save-report', 'StaffController::save_report');
});


// ==========================================================
// --- 4. LMS ADMIN ROUTES (Group: admin/leaves) ---
// ==========================================================
$routes->group('admin/leaves', ['namespace' => 'App\Controllers\Admin', 'filter' => 'auth'], function($routes) {
    
    // Dashboard & Requests
    $routes->get('/', 'LeaveController::index'); 
    $routes->get('leave-requests', 'LeaveController::pendingLeaves');
    $routes->get('requests', 'LeaveController::pendingLeaves');

    // --- Summary button ke liye ---
    $routes->get('summary/(:num)', 'LeaveController::leaveDetails/$1');

    // Staff Management
    $routes->get('staff-manage', 'LeaveController::manageStaff');
    $routes->get('manage-staff', 'LeaveController::manageStaff');
     
    // Settings & History
    $routes->get('allocate', 'LeaveController::allocateLeaves'); 
    $routes->get('types', 'LeaveController::leaveTypes'); 
    $routes->get('history', 'LeaveController::leaveHistory'); 
    
    // --- LEAVE REPORTING ROUTE ---
    $routes->GET('reporting', 'LeaveReportController::index');
    $routes->GET('get-monthly-data', 'LeaveReportController::getMonthlyData');

    // --- NEW ROUTES FOR ALLOCATION DELETE & UPDATE ---
    $routes->post('delete-allocation/(:num)', 'LeaveController::deleteAllocation/$1');
    $routes->post('update-allocation-single', 'LeaveController::updateAllocationSingle');

    // --- DELETE ACTION KE LIYE ---
    $routes->post('delete-request/(:num)', 'LeaveController::deleteLeave/$1');

    // View Details
    $routes->get('view/(:num)', 'LeaveController::leaveDetails/$1');
    $routes->get('leave-details/(:num)', 'LeaveController::leaveDetails/$1');

    // Post Actions
    $routes->post('store-type', 'LeaveController::storeLeaveType');
    $routes->post('store-allocation', 'LeaveController::storeAllocation');
    $routes->post('store-user', 'LeaveController::storeUser');
    $routes->post('update-status', 'LeaveController::updateLeaveStatus');
    $routes->post('approve-reject', 'LeaveController::updateLeaveStatus');
    
    // Delete & Toggle Actions
    $routes->get('delete-type/(:num)', 'LeaveController::deleteLeaveType/$1');
    $routes->get('toggle-user/(:num)/(:num)', 'LeaveController::toggleUser/$1/$2');

    $routes->get('leave_permissions/(:num)', 'LeavePermission::index/$1');
    $routes->post('leave_permissions/update/(:num)', 'LeavePermission::update/$1');
});

// ==========================================================
// --- 5. EXTRA ADMIN CRUD & STAFF ACTIONS ---
// ==========================================================
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'auth'], function($routes) {
    
    $routes->post('store-leave-type', 'LeaveController::storeLeaveType');
    $routes->post('store-allocation', 'LeaveController::storeAllocation');
    $routes->post('store-user', 'LeaveController::storeUser');
    $routes->get('manage-staff', 'LeaveController::manageStaff');
    $routes->get('leave-requests', 'LeaveController::pendingLeaves');

    // Staff Actions
    $routes->get('edit-staff/(:num)', 'LeaveController::editStaff/$1');
    $routes->post('update-staff/(:num)', 'LeaveController::updateStaff/$1');
    $routes->get('delete-staff/(:num)', 'LeaveController::deleteStaff/$1');
});
// invoice system routes 

/*
|--------------------------------------------------------------------------
| INVOICE / BILLING SYSTEM ROUTES
|--------------------------------------------------------------------------
| Add ONLY this block in HostMaster Routes.php
|--------------------------------------------------------------------------
*/

$routes->group('invoice', ['namespace' => 'App\Controllers\Invoice'], function($routes) {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    $routes->get('/', 'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index');


    /*
    |--------------------------------------------------------------------------
    | Taxes
    |--------------------------------------------------------------------------
    */

    $routes->get('taxes', 'Taxes::index');
    $routes->get('taxes/create', 'Taxes::create');
    $routes->post('taxes/store', 'Taxes::store');

    $routes->get('taxes/edit/(:num)', 'Taxes::edit/$1');
    $routes->post('taxes/update/(:num)', 'Taxes::update/$1');

    $routes->get('taxes/delete/(:num)', 'Taxes::delete/$1');


    /*
    |--------------------------------------------------------------------------
    | Customers
    |--------------------------------------------------------------------------
    */

    $routes->get('customers', 'Customers::index');

    $routes->get('customers/create', 'Customers::create');
    $routes->post('customers/store', 'Customers::store');
    $routes->get('customers/prefill', 'Customers::prefill');

    $routes->get('customers/show/(:num)', 'Customers::show/$1');

    $routes->get('customers/edit/(:num)', 'Customers::edit/$1');
    $routes->post('customers/update/(:num)', 'Customers::update/$1');

    $routes->get('customers/delete/(:num)', 'Customers::delete/$1');


    /*
    |--------------------------------------------------------------------------
    | Items
    |--------------------------------------------------------------------------
    */

    $routes->get('items', 'Items::index');

    $routes->get('items/create', 'Items::create');
    $routes->post('items/store', 'Items::store');

    $routes->get('items/edit/(:num)', 'Items::edit/$1');
    $routes->post('items/update/(:num)', 'Items::update/$1');

    $routes->get('items/delete/(:num)', 'Items::delete/$1');

    $routes->get('items/price/(:num)', 'Items::getPrice/$1');


    /*
    |--------------------------------------------------------------------------
    | Quotes
    |--------------------------------------------------------------------------
    */

    $routes->get('quotes', 'Quotes::index');

    $routes->get('quotes/create', 'Quotes::create');
    $routes->post('quotes/store', 'Quotes::store');

    $routes->get('quotes/show/(:num)', 'Quotes::show/$1');

    $routes->get('quotes/edit/(:num)', 'Quotes::edit/$1');
    $routes->post('quotes/update/(:num)', 'Quotes::update/$1');

    $routes->get('quotes/delete/(:num)', 'Quotes::delete/$1');

    $routes->get('quotes/convert/(:num)', 'Quotes::convert/$1');


    /*
    |--------------------------------------------------------------------------
    | Invoices
    |--------------------------------------------------------------------------
    */

    $routes->get('invoices', 'Invoices::index');

    $routes->get('invoices/create', 'Invoices::create');
    $routes->post('invoices/store', 'Invoices::store');

    $routes->get('invoices/show/(:num)', 'Invoices::show/$1');

    $routes->get('invoices/edit/(:num)', 'Invoices::edit/$1');
    $routes->post('invoices/update/(:num)', 'Invoices::update/$1');

    $routes->get('invoices/delete/(:num)', 'Invoices::delete/$1');
$routes->post('items/ajax-store', 'Items::ajaxStore');

    /*
    |--------------------------------------------------------------------------
    | Payments
    |--------------------------------------------------------------------------
    */

    $routes->get('payments', 'Payments::index');

    $routes->get('payments/indexpage', 'Payments::indexpage');

    $routes->get('payments/create', 'Payments::create');

    $routes->get(
        'payments/create/(:num)',
        'Payments::createForInvoice/$1'
    );

    $routes->get(
        'payments/create-for-invoice/(:num)',
        'Payments::createForInvoice/$1'
    );

    $routes->post('payments/store', 'Payments::store');

    $routes->get(
        'payments/history/(:num)',
        'Payments::history/$1'
    );

    $routes->get(
        'payments/show/(:num)',
        'Payments::show/$1'
    );

    $routes->get(
        'payments/edit/(:num)',
        'Payments::edit/$1'
    );

    $routes->post(
        'payments/update/(:num)',
        'Payments::update/$1'
    );

    $routes->get(
        'payments/delete/(:num)',
        'Payments::delete/$1'
    );


    /*
    |--------------------------------------------------------------------------
    | AJAX ROUTES
    |--------------------------------------------------------------------------
    */

    $routes->get(
        'payments/get_customer_details/(:num)',
        'Payments::get_customer_details/$1'
    );

    $routes->get(
        'payments/get_unpaid_invoices/(:num)',
        'Payments::get_unpaid_invoices/$1'
    );

});