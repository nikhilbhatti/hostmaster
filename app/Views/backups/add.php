<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .text-indigo { color: #6366f1; }
    .form-label { font-size: 11px; letter-spacing: 0.8px; margin-bottom: 8px; color: #64748b !important; }
    
    .custom-input, .custom-select { 
        border-radius: 12px !important; 
        padding: 14px 16px; 
        font-size: 14px;
        transition: 0.3s;
        border: 1px solid #edf2f7 !important;
        background-color: #f8fafc !important;
    }
    
    .custom-input:focus, .custom-select:focus {
        background-color: #fff !important;
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1) !important;
    }

    .card, .card-body, .input-group { overflow: visible !important; }

    .info-card {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        border-radius: 24px;
        border: none;
        position: relative;
        overflow: hidden;
    }

    .btn-save { background: #6366f1; border: none; border-radius: 12px; padding: 12px 35px; font-weight: 600; transition: 0.3s; }
    .btn-save:hover { background: #4f46e5; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3); }
    
    #orderListArea { display: none; }

    /* Searchable Client Dropdown */
    .client-dd-wrap { position: relative; width: 100%; overflow: visible !important; }
    .client-dd-display {
        width: 100%;
        min-height: 51px;
        border-radius: 0 12px 12px 0 !important;
        padding: 14px 45px 14px 16px;
        font-size: 14px;
        border: 1px solid #edf2f7 !important;
        background: #f8fafc !important;
        cursor: pointer;
        color: #111827;
        outline: none;
        text-align: left;
    }
    .client-dd-display:focus {
        background: #fff !important;
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 4px rgba(99,102,241,.10) !important;
    }
    .client-dd-arrow {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        pointer-events: none;
        z-index: 2;
    }
    .client-dd-menu {
        display: none;
        position: absolute;
        left: 0;
        right: 0;
        top: calc(100% + 6px);
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.18);
        z-index: 99999;
        overflow: hidden;
    }
    .client-dd-menu.open { display: block; }
    .client-dd-search-box {
        padding: 10px;
        background: #fff;
        border-bottom: 1px solid #eef2f7;
        position: sticky;
        top: 0;
        z-index: 2;
    }
    .client-dd-search-box input {
        width: 100%;
        border: 1px solid #dbe3ef;
        border-radius: 10px;
        padding: 10px 12px;
        font-size: 14px;
        outline: none;
    }
    .client-dd-search-box input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.10); }
    .client-dd-options {
        max-height: 260px;
        overflow-y: auto;
        overflow-x: hidden;
    }
    .client-dd-option {
        padding: 12px 15px;
        cursor: pointer;
        border-bottom: 1px solid #f3f4f6;
        background: #fff;
    }
    .client-dd-option:hover, .client-dd-option.active { background: #eef2ff; }
    .client-dd-name { font-size: 14px; font-weight: 700; color: #111827; }
    .client-dd-empty { padding: 14px; font-size: 13px; color: #64748b; text-align: center; }
    
    .order-item { 
        border: 1px solid #edf2f7; 
        padding: 15px; 
        border-radius: 15px; 
        margin-bottom: 10px;
        transition: all 0.3s ease;
        cursor: pointer;
        background: white;
    }

    .order-item:hover { border-color: #6366f1; background: #f5f7ff; }
    
    /* Jab select ho jaye toh ye style apply hoga */
    .order-item.selected { 
        border-color: #6366f1 !important; 
        background: #eef2ff !important; 
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
        transform: scale(1.01);
    }
    
    .radio-custom { width: 18px; height: 18px; cursor: pointer; accent-color: #6366f1; }

    @media (max-width: 768px) {
        .container-fluid { padding: 15px !important; }
        .btn-save { width: 100%; }
    }
</style>

<div class="container-fluid py-4">
    <div class="mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">Log New Backup</h3>
            <p class="text-muted small mb-0">Maintain backup consistency for client safety</p>
        </div>
        <a href="<?= base_url('backups') ?>" class="btn btn-sm btn-outline-secondary px-3 py-2" style="border-radius: 10px;">
            <i class="fas fa-arrow-left me-2"></i> Back to Logs
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 24px;">
                <div class="card-body p-4 p-md-5">
                    <form action="<?= base_url('backups/store') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label fw-bold text-uppercase">Target Client</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0" style="border-radius: 12px 0 0 12px;"><i class="fas fa-user-tie text-muted"></i></span>
                                    <div class="client-dd-wrap" id="clientDropdown">
                                        <input type="hidden" id="clientSelect" name="client_id" required>
                                        <button type="button" class="client-dd-display" id="clientDdDisplay" onclick="toggleClientDropdown()">Select client for this backup...</button>
                                        <i class="fas fa-chevron-down client-dd-arrow"></i>

                                        <div class="client-dd-menu" id="clientDdMenu">
                                            <div class="client-dd-search-box">
                                                <input type="text" id="clientDdSearch" placeholder="Search client..." autocomplete="off" oninput="filterClientDropdown(this.value)">
                                            </div>
                                            <div class="client-dd-options" id="clientDdOptions">
                                                <?php if(!empty($clients)): ?>
                                                    <?php foreach($clients as $client): ?>
                                                        <div class="client-dd-option"
                                                             data-id="<?= $client['id'] ?>"
                                                             data-name="<?= esc($client['client_name']) ?>"
                                                             onclick="selectClientFromDropdown(this)">
                                                            <div class="client-dd-name"><?= esc($client['client_name']) ?></div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                                <div class="client-dd-empty" id="clientDdEmpty" style="display:none;">No client found</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="orderListArea" class="mt-4 p-3 rounded-4" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="small fw-bold text-indigo mb-0 text-uppercase" style="font-size: 10px;">Select Order to Backup:</h6>
                                        <span class="badge bg-white text-muted border small fw-normal text-dark" style="font-size: 10px;">Required</span>
                                    </div>
                                    <div id="ordersTableBody">
                                        </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-uppercase">Completion Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0" style="border-radius: 12px 0 0 12px;"><i class="fas fa-calendar-check text-muted"></i></span>
                                    <input type="date" name="last_backup_date" class="form-control custom-input border-0 shadow-none" value="<?= date('Y-m-d') ?>" required style="border-radius: 0 12px 12px 0;">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-uppercase">Next Interval</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0" style="border-radius: 12px 0 0 12px;"><i class="fas fa-history text-muted"></i></span>
                                    <select name="backup_interval" class="form-select custom-select border-0 shadow-none" required style="border-radius: 0 12px 12px 0;">
                                        <option value="1">Monthly (Critical)</option>
                                        <option value="3" selected>Every 3 Months (Standard)</option>
                                        <option value="6">Every 6 Months (Recommended)</option>
                                        <option value="12">Yearly (Maintenance Only)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold text-uppercase">Backup Description</label>
                                <textarea id="backupNotes" name="notes" class="form-control custom-input border-0" rows="4" placeholder="Description will auto-fill when you select an order above..."></textarea>
                                <small class="text-muted mt-2 d-block" style="font-size: 10px;">
                                    <i class="fas fa-magic me-1"></i> Selection automatically updates this field.
                                </small>
                            </div>

                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-primary btn-save shadow text-white">
                                    <i class="fas fa-cloud-upload-alt me-2"></i> Confirm Backup Entry
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card info-card shadow-lg p-4 text-white">
                <div class="d-flex align-items-center mb-4">
                    <div class="rounded-3 p-2 me-3" style="background: rgba(99, 102, 241, 0.2);">
                        <i class="fas fa-shield-alt text-indigo fs-4"></i>
                    </div>
                    <h5 class="fw-bold mb-0">Backup Guidelines</h5>
                </div>
                <div class="space-y-4">
                    <div class="d-flex gap-3 mb-4">
                        <div class="mt-1"><i class="fas fa-check-circle text-indigo small"></i></div>
                        <p class="small mb-0 opacity-75"><b>Select Service:</b> Linking a backup to a specific service helps in tracking history.</p>
                    </div>
                    <div class="d-flex gap-3 mb-4">
                        <div class="mt-1"><i class="fas fa-check-circle text-indigo small"></i></div>
                        <p class="small mb-0 opacity-75"><b>Automation:</b> Description is auto-generated to save your time.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function toggleClientDropdown() {
    const menu = $('#clientDdMenu');
    menu.toggleClass('open');
    if (menu.hasClass('open')) {
        $('#clientDdSearch').val('');
        filterClientDropdown('');
        setTimeout(function(){ $('#clientDdSearch').focus(); }, 50);
    }
}

function filterClientDropdown(q) {
    q = (q || '').toLowerCase().trim();
    let visibleCount = 0;

    $('#clientDdOptions .client-dd-option').each(function() {
        const name = String($(this).data('name') || '').toLowerCase();
        const isMatch = name.includes(q);
        $(this).toggle(isMatch);
        if (isMatch) visibleCount++;
    });

    $('#clientDdEmpty').toggle(visibleCount === 0);
}

function selectClientFromDropdown(el) {
    const clientId = $(el).data('id');
    const clientName = $(el).data('name');

    $('#clientSelect').val(clientId).trigger('change');
    $('#clientDdDisplay').text(clientName);
    $('#clientDdMenu').removeClass('open');
    $('.client-dd-option').removeClass('active');
    $(el).addClass('active');

    loadClientOrders(clientId);
}

$(document).on('click', function(e) {
    if (!$(e.target).closest('#clientDropdown').length) {
        $('#clientDdMenu').removeClass('open');
    }
});

function loadClientOrders(clientId) {
    if(clientId) {
        $.ajax({
            url: "<?= base_url('backups/get-client-orders') ?>/" + clientId,
            type: "GET",
            dataType: "json",
            beforeSend: function() {
                $('#ordersTableBody').html('<div class="py-3 text-center"><i class="fas fa-spinner fa-spin text-indigo me-2"></i><span class="small text-muted">Fetching client services...</span></div>');
                $('#orderListArea').slideDown();
                $('#backupNotes').val('');
            },
            success: function(data) {
                var html = '';
                if(data && data.length > 0) {
                    $.each(data, function(key, value) {
                        var serviceName = value.service || value.type_name || "General Service";
                        var domainName = value.domain_name || "No Domain";
                        var orderDate = value.created_at ? value.created_at.split(' ')[0] : "N/A";

                        html += `
                        <div class="order-item d-flex align-items-center gap-3" onclick="selectOrder(this, '${domainName}', '${serviceName}')">
                            <input type="radio" name="order_id" value="${value.id}" class="radio-custom" required>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold text-dark small text-uppercase">${serviceName}</span>
                                    <span class="badge bg-success-subtle text-success small" style="font-size: 8px;">ACTIVE</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-indigo small fw-semibold" style="font-size: 11px;">${domainName}</span>
                                    <span class="text-muted" style="font-size: 10px;">Placed: ${orderDate}</span>
                                </div>
                            </div>
                        </div>`;
                    });
                } else {
                    html = '<div class="text-center p-3"><p class="small text-muted mb-0">No active services found for this client.</p></div>';
                }
                $('#ordersTableBody').html(html);
            },
            error: function() {
                $('#ordersTableBody').html('<p class="small text-danger p-2 mb-0 text-center">Error loading data. Check connection.</p>');
            }
        });
    }
}

/**
 * Handles the selection of an order card
 * Highlights the card and auto-fills the textarea
 */
function selectOrder(element, domain, service) {
    // 1. UI Highlight: Remove from all, add to clicked one
    $('.order-item').removeClass('selected');
    $(element).addClass('selected');
    
    // 2. Select the radio button inside the clicked card
    $(element).find('input[type="radio"]').prop('checked', true);
    
    // 3. Logic: Auto-fill the description (Textarea)
    // Humne format thoda professional rakha hai
    var noteText = "Backup completed for " + service + " (" + domain + "). All files and database verified.";
    $('#backupNotes').val(noteText);
}
</script>
<?= $this->endSection() ?>