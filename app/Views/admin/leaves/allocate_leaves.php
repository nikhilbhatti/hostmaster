<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<style>
    :root { --admin-primary: #4e73df; --admin-success: #1cc88a; --admin-info: #36b9cc; --admin-warning: #f6c23e; --admin-danger: #e74a3b; }
    .main-card { border-radius: 15px; border: none; transition: transform 0.2s; }
    .main-card:hover { transform: translateY(-5px); }
    .avatar-icon { width: 40px; height: 40px; line-height: 40px; background: linear-gradient(135deg, #4e73df, #224abe); color: white; border-radius: 10px; text-align: center; font-size: 15px; font-weight: bold; box-shadow: 0 4px 10px rgba(78,115,223,0.2); }
    .table-divider { border-top: 3px solid #f8f9fc !important; }
    .status-badge { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; padding: 0.4em 0.8em; border-radius: 50rem; }
    /* --- Holiday Calendar Container Fix --- */
#holidayCalendar { 
    background: #fff; 
    padding: 15px; 
    border-radius: 12px; 
    width: 100%; 
    overflow: hidden; /* Bahar nikalne se rokne ke liye */
}

/* FullCalendar Header Alignment */
.fc-header-toolbar { 
    margin-bottom: 1.5rem !important; 
    display: flex;
    flex-wrap: wrap; /* Mobile par buttons niche aa jayenge */
    gap: 10px;
}

.fc-button-primary { 
    background-color: var(--admin-primary) !important; 
    border-color: var(--admin-primary) !important; 
    text-transform: capitalize;
}

/* Holiday Event Styling - Improved Text Wrapping */
.fc-event.holiday-event { 
    background: linear-gradient(45deg, #e74a3b, #be2617) !important; 
    border: none !important; 
    padding: 4px 8px; 
    cursor: pointer; 
    font-size: 0.8em; 
    box-shadow: 0 3px 6px rgba(231,74,59,0.3);
    white-space: normal !important; /* Lamba naam wrap hoga */
    display: block;
    border-radius: 4px;
}

/* --- Global Fixes for Long Text --- */
.text-wrap {
    white-space: normal !important;
    word-break: break-word !important;
}

/* Stat Box Fix (Removed extra 'px' typo) */
.stat-box { 
    border-left: 4px solid; 
    background: #fff; 
    padding: 20px; 
    border-radius: 10px; 
    height: 100%; 
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
}

/* Glass Input Fix */
.glass-input { 
    border: 2px solid #eee; 
    border-radius: 8px; 
    padding: 10px 15px; 
    transition: all 0.3s; 
    width: 100%; /* Input container ke bahar na jaye */
}

.glass-input:focus { 
    border-color: var(--admin-primary); 
    box-shadow: 0 0 0 0.2rem rgba(78,115,223,0.1); 
    outline: none;
}

/* Scroll Area Fix */
.scroll-area { 
    max-height: 600px; 
    overflow-y: auto; 
    scrollbar-width: thin; 
    padding-right: 5px;
}

.scroll-area::-webkit-scrollbar { width: 6px; }
.scroll-area::-webkit-scrollbar-thumb { background: #dddddd; border-radius: 10px; }

/* Table and Cell Adjustment */
#allocationTable td { 
    padding-top: 12px; 
    padding-bottom: 12px; 
    vertical-align: middle;
}
    /* Table Fix */
    #allocationTable td { padding-top: 12px; padding-bottom: 12px; }
</style>

<div class="container-fluid py-4 bg-light">
    <div class="row mb-4 animate__animated animate__fadeIn">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between bg-white p-4 rounded-4 shadow-sm">
                <div>
                    <h3 class="mb-1 fw-black text-dark">LMS Control Center</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Admin</a></li>
                            <li class="breadcrumb-item active">Leave & Holidays</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-3">
                    <div class="text-end d-none d-md-block">
                        <p class="text-muted small mb-0">Server Time</p>
                        <h6 class="mb-0 fw-bold"><?= date('h:i A') ?></h6>
                    </div>
                    <div class="vr"></div>
                    <div class="bg-primary-subtle p-2 px-3 rounded-3 text-primary fw-bold text-center">
                        <div class="small">FY</div>
                        <div>2025-26</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-xl-4 col-md-6">
            <div class="stat-box border-primary shadow-sm">
                <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Staff</div>
                <div class="h5 mb-0 fw-bold text-gray-800"><?= count($users) ?> Members</div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="stat-box border-success shadow-sm">
                <div class="text-xs fw-bold text-success text-uppercase mb-1">Active Leave Types</div>
                <div class="h5 mb-0 fw-bold text-gray-800"><?= count($leave_types) ?> Categories</div>
            </div>
        </div>
        <div class="col-xl-4 col-md-12">
            <div class="stat-box border-warning shadow-sm">
                <div class="text-xs fw-bold text-warning text-uppercase mb-1">Allocated Records</div>
                <div class="h5 mb-0 fw-bold text-gray-800"><?= count($allocations) ?> Entries</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card main-card shadow-sm mb-4">
                <div class="card-header bg-dark py-3 d-flex align-items-center">
                    <div class="bg-warning rounded-circle p-2 me-3">
                        <i class="fas fa-plus text-dark"></i>
                    </div>
                    <h6 class="mb-0 fw-bold text-white">Create New Allocation</h6>
                </div>
                <div class="card-body p-4">
                    <form action="<?= base_url('admin/store-allocation') ?>" method="POST" id="mainAllocationForm">
                        <?= csrf_field() ?>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">TARGET EMPLOYEE</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-2 border-end-0"><i class="fas fa-user text-primary"></i></span>
                                <select name="user_id" id="user_select" class="form-select form-select-lg glass-input border-start-0" required style="font-size: 1rem;">
                                    <option value="">Search Member...</option>
                                    <?php if(!empty($users)): foreach($users as $u): ?>
                                        <option value="<?= $u['id'] ?>"><?= esc($u['username']) ?></option>
                                    <?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <label class="fw-bold small text-muted">QUOTA SETTINGS</label>
                            <span class="badge bg-secondary-subtle text-secondary" style="font-size: 10px;">RESET ANNUAL</span>
                        </div>

                        <div class="rounded-4 border overflow-hidden">
                            <table class="table table-borderless align-middle mb-0">
                                <thead class="bg-light border-bottom">
                                    <tr>
                                        <th class="ps-3 py-2 small fw-bold">CATEGORY</th>
                                        <th class="text-end pe-3 py-2 small fw-bold">DAYS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php if(!empty($leave_types)): foreach($leave_types as $lt): 
    $lname = strtolower($lt['leave_name']);
    
    // Yahan 'earned' add kar diya gaya hai logic mein
    $isYearly = (strpos($lname, 'sick') !== false || 
                 strpos($lname, 'casual') !== false || 
                 strpos($lname, 'earned') !== false); 
    
    // Agar yearly hai toh admin khud fill karega (empty value), 
    // warna monthly ke liye default 1 rahega
    $defaultVal = ($isYearly) ? "" : "1"; 
?>                
<tr class="border-bottom">
    <td class="ps-3">
        <div class="fw-bold text-dark mb-0" style="font-size: 0.9rem;"><?= esc($lt['leave_name']) ?></div>
        <span class="badge <?= $isYearly ? 'bg-info-subtle text-info' : 'bg-warning-subtle text-warning' ?>" style="font-size: 8px; letter-spacing: 1px;">
            <?= $isYearly ? 'ANNUAL' : 'MONTHLY' ?>
        </span>
    </td>
    <td class="pe-3">
        <input type="number" name="limits[<?= $lt['id'] ?>]" 
               class="form-control form-control-sm text-center fw-bold border-2" 
               value="<?= $defaultVal ?>" 
               placeholder="0" 
               min="0" 
               required 
               style="width: 70px; margin-left: auto; border-radius: 6px;">
    </td>
</tr>
<?php endforeach; else: ?>
                                        <tr><td colspan="2" class="text-center p-4">No categories found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info border-0 mt-4 p-3 rounded-4 shadow-sm">
                            <div class="d-flex">
                                <i class="fas fa-info-circle mt-1 me-3"></i>
                                <div class="small">
                                    <strong>Auto-Logic:</strong> Monthly leaves will auto-reset on the 1st of every month. Annual leaves carry forward until March 31st.
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg shadow-lg border-0 py-3 fw-bold mt-2">
                            <i class="fas fa-check-double me-2"></i> Confirm Allocation
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
    <div class="card main-card shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold"><i class="fas fa-users-cog me-2 text-primary"></i> Staff Allocation List</h6>
            <div class="input-group w-50">
                <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="tableSearch" class="form-control form-control-sm border-start-0 shadow-none" placeholder="Search staff...">
            </div>
        </div>
        <div class="card-body p-0">
            <div class="scroll-area">
                <table class="table table-hover align-middle mb-0" id="allocationTable">
                    <thead class="bg-light sticky-top">
                        <tr>
                            <th class="ps-4 py-3">STAFF NAME</th>
                            <th class="text-center">TOTAL ALLOCATIONS</th>
                            <th class="text-end pe-4">DETAILS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $groupedData = [];
                        if(!empty($allocations)) {
                            foreach($allocations as $a) {
                                $groupedData[$a['username']][] = $a;
                            }
                        }

                        if(!empty($groupedData)):
                            foreach($groupedData as $username => $leaves): 
                        ?>
                        <tr class="allocation-row">
                            <td class="ps-4">
                                <div class="d-flex align-items-center py-2">
                                    <div class="avatar-icon me-3 shadow-sm"><?= strtoupper(substr($username, 0, 1)) ?></div>
                                    <div class="fw-bold text-dark"><?= esc($username) ?></div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-soft-primary text-primary border border-primary px-3">
                                    <?= count($leaves) ?> Leave Categories
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <!-- View Details Button -->
                                <button class="btn btn-sm btn-primary rounded-pill px-3 view-staff-leaves" 
                                        data-user="<?= esc($username) ?>" 
                                        data-leaves='<?= json_encode($leaves) ?>'>
                                    <i class="fas fa-eye me-1"></i> View & Manage
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="3" class="text-center py-5 text-muted">No records found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal for Viewing Leave Details -->
<div class="modal fade" id="leaveDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i> <span id="modalStaffName"></span>'s Allocations</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="leaveItemsContainer">
                    <!-- Leaves will be injected here via JS -->
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Company Holiday Planner Section -->
<div class="row mt-5">
    <div class="col-12">
        <div class="card main-card shadow-lg overflow-hidden border-0">
            <!-- Header Fix: Added flex-wrap and gap for mobile -->
            <div class="card-header bg-danger text-white py-4 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div style="flex: 1; min-width: 200px;">
                    <h4 class="mb-1 fw-black d-flex align-items-center flex-wrap">
                        <i class="fas fa-calendar-check me-2"></i> 
                        <span class="text-wrap">Company Holiday Planner</span>
                    </h4>
                    <p class="mb-0 small opacity-75 text-wrap">Visual calendar to manage official off-days and events.</p>
                </div>
                <!-- Buttons Container -->
                <div class="d-flex gap-2 flex-shrink-0">
                    <button class="btn btn-light btn-sm fw-bold px-3 shadow-sm" onclick="calendar.today()">Today</button>
                    <div class="btn-group shadow-sm">
                        <button type="button" class="btn btn-sm btn-dark" onclick="calendar.changeView('dayGridMonth')">Month</button>
                        <button type="button" class="btn btn-sm btn-dark" onclick="calendar.changeView('dayGridWeek')">Week</button>
                    </div>
                </div>
            </div>
            
            <div class="card-body bg-white p-lg-5">
                <div class="row g-5">
                    <div class="col-xl-8 border-end">
                        <div id='holidayCalendar' class="shadow-sm border rounded-4"></div>
                    </div>
                    <div class="col-xl-4">
                        <div class="sticky-top" style="top: 100px;">
                            <div class="p-4 bg-light rounded-4 border-2 border border-white shadow">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="bg-danger text-white rounded-3 p-2 me-3 shadow-sm flex-shrink-0">
                                        <i class="fas fa-calendar-plus"></i>
                                    </div>
                                    <h5 class="fw-bold mb-0 text-wrap">Add New Holiday</h5>
                                </div>
                                <form id="holidayForm">
                                    <?= csrf_field() ?>
                                    <div class="mb-3">
                                        <label class="small fw-bold text-muted mb-2">PICK DATE</label>
                                        <input type="date" id="h_date" class="form-control glass-input" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="small fw-bold text-muted mb-2">EVENT TITLE</label>
                                        <input type="text" id="h_name" class="form-control glass-input" placeholder="e.g. Diwali Festival" required>
                                    </div>
                                    <div class="mb-4">
                                        <label class="small fw-bold text-muted mb-2">ADDITIONAL NOTES</label>
                                        <textarea id="h_desc" class="form-control glass-input" rows="3" placeholder="Description for employees..."></textarea>
                                    </div>
                                    <button type="submit" id="saveBtn" class="btn btn-danger btn-lg w-100 fw-bold shadow-lg py-3">
                                        <i class="fas fa-thumbtack me-2"></i> Save Holiday
                                    </button>
                                </form>
                            </div>
                            <div class="mt-4 p-4 rounded-4 bg-primary text-white shadow-lg position-relative overflow-hidden">
                                <i class="fas fa-lightbulb position-absolute opacity-25" style="font-size: 100px; right: -20px; bottom: -20px;"></i>
                                <h6 class="fw-bold mb-2">Pro Tip!</h6>
                                <p class="small mb-0 position-relative" style="z-index: 1;">These holidays will be automatically excluded from employee leave balance calculations.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modify Quota Modal Section -->
<div class="modal fade animate__animated animate__zoomIn" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-primary text-white border-0 py-4 px-4" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-black"><i class="fas fa-user-edit me-2"></i> Modify Quota</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editAllocationForm">
                <div class="modal-body p-4 px-5">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-4 text-center">
                        <div class="avatar-icon mx-auto mb-2 shadow-sm" style="width: 60px; height: 60px; line-height: 60px; font-size: 24px; background: #f0f2f5; border-radius: 50%; color: #0d6efd;" id="modal_avatar">?</div>
                        
                        <!-- Name Fix: Added text-wrap and word-break -->
                        <h6 class="fw-bold text-dark mb-1 px-2 text-wrap" id="display_user_name" style="word-break: break-word; line-height: 1.4;">User Name</h6>
                        
                        <!-- Badge Fix: Added text-wrap -->
                        <div class="d-flex justify-content-center">
                            <span class="badge bg-light text-muted border text-wrap lh-base px-3 py-2" id="display_leave_type" style="max-width: 100%; white-space: normal;">Leave Type</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-2 uppercase">Adjust Limit (In Days)</label>
                        <div class="input-group input-group-lg shadow-sm">
                            <input type="number" name="leave_limit" id="edit_limit" class="form-control glass-input text-center fw-bold border-primary" required min="0">
                            <span class="input-group-text bg-primary text-white border-primary fw-bold px-4">DAYS</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-4" style="border-radius: 0 0 20px 20px;">
                    <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none" data-bs-dismiss="modal">Keep Original</button>
                    <button type="submit" class="btn btn-primary px-5 fw-bold rounded-pill shadow">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    var calendar;
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. Table Search ---
        $("#tableSearch").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#allocationTable tbody tr.allocation-row").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // --- 2. Holiday Calendar ---
        var calendarEl = document.getElementById('holidayCalendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 650,
            themeSystem: 'bootstrap5',
            headerToolbar: { left: 'prev,next', center: 'title', right: '' },
            events: '<?= site_url('admin/get-holidays') ?>',
            eventClassNames: 'holiday-event',
            displayEventTime: false,
            eventClick: function(info) {
                if(confirm("Action: Delete Holiday '" + info.event.title + "'?")) {
                    $.post('<?= site_url('admin/delete-holiday') ?>/' + info.event.id, 
                    {<?= csrf_token() ?>: '<?= csrf_hash() ?>'}, function(res) {
                        if(res.status === 'success') {
                            info.event.remove();
                        }
                    });
                }
            }
        });
        calendar.render();

        // --- NEW: View Details Popup Logic ---
      // --- Updated: View Details Popup Logic ---
$('.view-staff-leaves').on('click', function() {
    const username = $(this).data('user');
    const leaves = $(this).data('leaves');
    
    $('#modalStaffName').text(username);
    let html = '<div class="list-group list-group-flush">';
    
    leaves.forEach(l => {
        const lname = l.leave_name.toLowerCase();
        // Added 'earned' to the yearly check
        const isYearly = lname.includes('sick') || lname.includes('casual') || lname.includes('earned');
        const cycleClass = isYearly ? 'bg-info text-white' : 'bg-warning text-dark';
        const cycleText = isYearly ? 'Yearly' : 'Monthly';

        html += `
            <div class="list-group-item p-3 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 fw-bold text-dark text-uppercase">${l.leave_name}</h6>
                        <span class="badge ${cycleClass} py-1" style="font-size: 10px;">${cycleText} Cycle</span>
                    </div>
                    <div class="text-end">
                        <div class="h5 mb-2 text-primary fw-bold">${l.leave_limit} <small class="text-muted" style="font-size: 12px;">Days</small></div>
                        <div class="btn-group shadow-sm">
                            <button type="button" class="btn btn-sm btn-white border edit-allocation-btn" 
                                data-id="${l.id}" data-user="${username}" data-type="${l.leave_name}" data-limit="${l.leave_limit}">
                                <i class="fas fa-edit text-primary"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-white border delete-allocation-btn" data-id="${l.id}">
                                <i class="fas fa-trash-alt text-danger"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>`;
    });
    $('#leaveItemsContainer').html(html);
    $('#leaveDetailsModal').modal('show');
});
        // --- 3. Edit Allocation (UPDATED: Now works inside Popup too) ---
        $(document).on('click', '.edit-allocation-btn', function() {
            const d = $(this).data();
            // Agar popup khula hai toh band kar do
            $('#leaveDetailsModal').modal('hide');
            
            $('#edit_id').val(d.id);
            $('#display_user_name').text(d.user);
            $('#display_leave_type').text(d.type);
            $('#edit_limit').val(d.limit);
            $('#modal_avatar').text(d.user.charAt(0).toUpperCase());
            $('#editModal').modal('show');
        });

        // --- 4. Update Allocation (AJAX) ---
        $('#editAllocationForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: '<?= site_url('admin/leaves/update-allocation-single') ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    if(res.status === 'success') {
                        location.reload();
                    } else {
                        alert(res.message || "Update failed");
                    }
                },
                error: function() {
                    alert("Server Error: Could not update record.");
                }
            });
        });

        // --- 5. Delete Allocation (UPDATED: Now works inside Popup too) ---
        $(document).on('click', '.delete-allocation-btn', function() {
            const id = $(this).data('id');
            if(confirm("Warning: This will remove this specific leave quota for this employee. Continue?")) {
                $.post('<?= site_url('admin/leaves/delete-allocation') ?>/' + id, 
                {<?= csrf_token() ?>: '<?= csrf_hash() ?>'}, function(res) {
                    if(res.status === 'success') {
                        location.reload();
                    } else {
                        alert(res.message || "Delete failed");
                    }
                }, 'json');
            }
        });

        // --- 6. Holiday Form ---
        $('#holidayForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#saveBtn');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
            
            $.post('<?= site_url('admin/save-holiday') ?>', {
                <?= csrf_token() ?>: '<?= csrf_hash() ?>',
                holiday_date: $('#h_date').val(),
                holiday_name: $('#h_name').val(),
                description: $('#h_desc').val()
            }, function(res) {
                if(res.status === 'success') {
                    calendar.refetchEvents();
                    $('#holidayForm')[0].reset();
                } else {
                    alert(res.message);
                }
                btn.prop('disabled', false).html('<i class="fas fa-thumbtack me-2"></i> Save Holiday');
            }, 'json');
        });
    });
</script>
<?= $this->endSection() ?>