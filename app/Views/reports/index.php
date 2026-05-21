<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Modern UI Enhancements */
    .glass-card { 
        background: #fff; 
        border-radius: 15px; 
        border: 1px solid #f1f5f9; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.05); 
    }
    
    .search-container { position: relative; }
    .search-input { 
        border-radius: 12px; 
        padding-left: 45px; 
        height: 50px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    .search-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .search-icon { 
        position: absolute; 
        left: 18px; 
        top: 16px; 
        color: #94a3b8; 
    }

    /* Action Buttons Styling */
    .btn-action {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s;
        border: none;
        text-decoration: none;
    }
    .btn-edit { background: #eff6ff; color: #3b82f6; }
    .btn-edit:hover { background: #3b82f6; color: #fff; }
    .btn-delete { background: #fff1f2; color: #f43f5e; margin-left: 5px; }
    .btn-delete:hover { background: #f43f5e; color: #fff; }

    /* BLUE ROUND PAGINATION */
    .dataTables_wrapper .dataTables_paginate {
        margin-top: 25px !important;
        display: flex !important;
        justify-content: center !important;
        gap: 8px !important;
        padding-bottom: 20px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        width: 40px !important;
        height: 40px !important;
        border-radius: 50% !important;
        border: 1px solid #dee2e6 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        background: white !important;
        transition: 0.3s;
        padding: 0 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #3b82f6 !important;
        color: white !important;
        border: none !important;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current) {
        background: #f8fafc !important;
        border-color: #3b82f6 !important;
    }

    .dataTables_filter { display: none !important; } 
    .dataTables_info { text-align: center; margin-top: 10px; color: #94a3b8; font-size: 0.85rem; }
    .report-text-box { line-height: 1.6; word-wrap: break-word; font-size: 0.9rem; }
    .avatar-circle {
        width: 38px; height: 38px; 
        background: #eff6ff; color: #3b82f6; 
        font-weight: 700; border: 1px solid #dbeafe;
    }

    @media print {
        .btn, .search-container, .dataTables_paginate, .action-cell, .btn-action { display: none !important; }
        .glass-card { border: none; box-shadow: none; }
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Daily Work History</h3>
            <p class="text-muted small mb-0">Review all staff activity logs</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-white shadow-sm border px-3" style="border-radius: 10px;">
                <i class="fas fa-print me-2"></i> Print
            </button>
            <button onclick="location.reload()" class="btn btn-primary shadow-sm px-3" style="border-radius: 10px;">
                <i class="fas fa-sync-alt me-2"></i> Refresh
            </button>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <i class="fas fa-exclamation-circle me-2"></i> <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="card glass-card border-0 mb-4 p-3">
        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="finalSearch" class="form-control search-input" placeholder="Search by staff name or work keywords...">
        </div>
    </div>

    <div class="card glass-card border-0">
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table class="table table-hover align-middle mb-0" id="forceTable" style="width:100%">
                    <thead class="bg-light">
                        <tr class="small text-muted text-uppercase">
                            <th class="ps-3">Staff Member</th>
                            <th>Work Report Details</th>
                            <th>Date & Time</th>
                            <th class="text-end pe-3 action-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($daily_reports)): foreach($daily_reports as $report): ?>
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle d-flex align-items-center justify-content-center rounded-circle">
                                        <?= strtoupper(substr($report['staff_name'] ?? 'U', 0, 1)) ?>
                                    </div>
                                    <span class="fw-bold ms-3 text-dark"><?= esc($report['staff_name']) ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="p-3 bg-light rounded-3 text-dark border-start border-primary border-3 report-text-box">
                                    <?= nl2br(esc($report['report_text'])) ?>
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    <i class="far fa-calendar-alt text-muted me-1"></i> <?= date('d M, Y', strtotime($report['created_at'])) ?><br>
                                    <i class="far fa-clock text-primary me-1"></i> <?= date('h:i A', strtotime($report['created_at'])) ?>
                                </div>
                            </td>
                            <td class="text-end pe-3 action-cell">
                                
                                <?php if (session()->get('user_role') !== 'admin' || $report['staff_id'] == session()->get('admin_id')): ?>
                                    <button type="button" onclick="openEditModal(<?= $report['id'] ?>, '<?= esc($report['report_text'], 'js') ?>')" class="btn-action btn-edit" title="Edit Report">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                <?php endif; ?>
                                
                                <?php if (session()->get('user_role') === 'admin'): ?>
                                    <button onclick="deleteReport(<?= $report['id'] ?>)" class="btn-action btn-delete" title="Delete Report">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php endif; ?>

                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Edit Work Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editReportForm" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Update your work details</label>
                        <textarea name="report_text" id="modal_report_text" class="form-control" rows="5" style="border-radius: 10px;" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius: 10px;">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 10px;">Update Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Open Edit Modal
function openEditModal(id, text) {
    $('#modal_report_text').val(text);
    $('#editReportForm').attr('action', "<?= base_url('reports/update/') ?>/" + id);
    new bootstrap.Modal(document.getElementById('editReportModal')).show();
}

// Delete Confirmation
function deleteReport(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This report will be permanently deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#f43f5e',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "<?= base_url('reports/delete/') ?>/" + id;
        }
    })
}

$(document).ready(function() {
    var table = $('#forceTable').DataTable({
        "pageLength": 10, 
        "dom": 'rtip',
        "ordering": true,
        "order": [[2, "desc"]], 
        "columnDefs": [
            { "orderable": false, "targets": 3 } 
        ],
        "language": {
            "paginate": {
                "next": '<i class="fas fa-chevron-right"></i>',
                "previous": '<i class="fas fa-chevron-left"></i>'
            },
            "emptyTable": "No work reports found."
        }
    });

    $('#finalSearch').on('keyup', function() {
        table.search(this.value).draw();
    });
});
</script>

<?= $this->endSection() ?>