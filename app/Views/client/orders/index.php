<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<style>
    .bg-indigo-soft { background-color: #eef2ff; }
    .text-indigo { color: #6366f1; }
    .table tbody tr:hover { background-color: #f8fafc; transition: 0.2s; }
    .action-btn { width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; transition: 0.3s; border-radius: 50% !important; }
    .action-btn:hover { background-color: #6366f1 !important; color: white !important; }
    .action-btn:hover i { color: white !important; }

    /* Cursor logic: Only show pointer for Expiry Date */
    th { cursor: default !important; }
    th.sorting_expiry { cursor: pointer !important; }

    /* BLUE ROUND PAGINATION STYLES */
    .dataTables_wrapper .dataTables_paginate {
        margin-top: 25px !important;
        display: flex !important;
        justify-content: center !important;
        gap: 8px !important;
        padding-bottom: 20px;
    }

    .dataTables_wrapper .paginate_button {
        width: 35px !important;
        height: 35px !important;
        border-radius: 50% !important;
        border: 1px solid #dee2e6 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        background: white !important;
        transition: 0.3s;
        padding: 0 !important;
        font-size: 0.85rem;
    }

    .dataTables_wrapper .paginate_button.current {
        background: #6366f1 !important;
        color: white !important;
        border: none !important;
        box-shadow: 0 4px 10px rgba(99, 102, 241, 0.3);
    }
    
    .dataTables_filter { display: none !important; } 
    .dataTables_info { text-align: center; margin-top: 10px; color: #94a3b8; font-size: 0.75rem; }

    /* --- MOBILE OPTIMIZATION --- */
    @media (max-width: 767.98px) {
        .container-fluid { padding: 12px !important; }
        .mb-4.d-flex { flex-direction: column; align-items: flex-start !important; gap: 15px; }
        .btn-primary { width: 100%; justify-content: center; display: flex; align-items: center; }
        .table-responsive { border: none !important; }
        .table thead { display: none; }
        .table tbody tr { 
            display: block; 
            border: 1px solid #e2e8f0; 
            border-radius: 15px; 
            margin-bottom: 15px; 
            padding: 10px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .table tbody td { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border: none; 
            padding: 8px 10px !important; 
            text-align: right;
            font-size: 14px;
        }
        .table tbody td::before { 
            content: attr(data-label); 
            font-weight: 700; 
            text-transform: uppercase; 
            font-size: 11px; 
            color: #64748b;
            flex: 1;
            text-align: left;
        }
        .table tbody td:first-child { 
            background: #f8fafc; 
            border-radius: 10px; 
            margin-bottom: 5px;
            text-align: left;
            flex-direction: row-reverse;
        }
        .table tbody td:first-child::before { display: none; }
        .avatar-sm { width: 32px !important; height: 32px !important; }
    }
</style>

<div class="container-fluid py-4">

    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="fw-bold text-dark mb-1">Orders Management</h3>
            <p class="text-muted small mb-0">Track domain and service subscriptions</p>
        </div>
        <a href="<?= base_url('orders/add') ?>" class="btn btn-primary shadow-sm px-4 py-2" style="border-radius: 12px; background: #6366f1; border:none;">
            <i class="fas fa-plus-circle me-2"></i> Create New Order
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 15px; border-left: 5px solid #ef4444 !important;">
                <div class="d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 p-3 rounded-circle me-3">
                        <i class="fas fa-exclamation-triangle text-danger"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Expiry Alerts</h6>
                        <small class="text-danger fw-bold">Check items marked in red</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8 col-sm-6">
            <div class="card border-0 shadow-sm p-2" style="border-radius: 15px;">
                <div class="input-group">
                    <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="orderSearch" class="form-control border-0" placeholder="Search orders, clients, or domains...">
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden; background: transparent;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="orderTable" style="background: white; width: 100%;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Website & Client</th>
                            <th class="py-3 text-uppercase small fw-bold text-muted">Type & Provider</th>
                            <th class="py-3 text-uppercase small fw-bold text-muted sorting_expiry">Expiry Date</th>
                            <th class="py-3 text-uppercase small fw-bold text-muted">Status</th>
                            <th class="pe-4 py-3 text-center text-uppercase small fw-bold text-muted">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($orders)): ?>
                            <?php 
                            $today = strtotime(date('Y-m-d')); 
                            foreach($orders as $order): 
                                $expiry_raw = $order['domain_expiry_date']; 
                                $expiry_ts = strtotime($expiry_raw);
                                $diff = round(($expiry_ts - $today) / 86400);
                                $color = ($diff < 15) ? 'text-danger' : 'text-success';
                            ?>
                            <tr>
                                <td class="ps-4" data-label="Client">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3 bg-indigo-soft rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                                            <i class="fas fa-globe text-indigo"></i>
                                        </div>
                                        <div class="text-start">
                                            <div class="fw-bold text-dark"><?= esc($order['domain_name']) ?></div>
                                            <small class="text-muted d-block"><?= esc($order['client_name']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Type">
                                    <div class="fw-bold small text-dark"><?= esc($order['type_name'] ?? 'N/A') ?></div>
                                    <span class="badge bg-light text-indigo border shadow-sm mt-1" style="font-size: 10px;">
                                        <?= esc($order['provider_name'] ?? 'Manual') ?>
                                    </span>
                                </td>
                                <td data-label="Expiry" data-order="<?= $expiry_raw ?>">
                                    <div class="fw-bold small"><?= date('d M, Y', $expiry_ts) ?></div>
                                    <small class="<?= $color ?> fw-bold" style="font-size: 11px;">
                                        <?= ($diff < 0) ? 'Expired' : $diff . ' days left' ?>
                                    </small>
                                </td>
                                <td data-label="Status">
                                    <span class="badge rounded-pill px-3 py-2" style="background: <?= ($diff < 0) ? '#fee2e2' : '#dcfce7' ?>; color: <?= ($diff < 0) ? '#ef4444' : '#22c55e' ?>;">
                                        <?= ($diff < 0) ? 'Expired' : 'Active' ?>
                                    </span>
                                </td>
                                <td class="pe-4 text-center" data-label="Control">
                                    <button class="btn btn-light btn-sm action-btn mx-md-auto ms-auto" 
                                            onclick="openActionPopup('<?= $order['id'] ?>', '<?= esc($order['domain_name']) ?>')"
                                            type="button">
                                        <i class="fas fa-cog text-muted"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="actionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold" id="modalTitle">Manage Order</h6>
                <button type="button" class="btn-close small" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="avatar-md mx-auto bg-indigo-soft rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 55px; height: 55px;">
                        <i class="fas fa-file-invoice text-indigo fs-4"></i>
                    </div>
                    <p class="small text-muted mb-0 fw-bold text-break" id="modalDomainName"></p>
                </div>
                <div class="d-grid gap-2">
                    <a id="editLink" href="#" class="btn btn-light text-start py-2 px-3 border-0 rounded-3">
                        <i class="fas fa-edit me-2 text-primary"></i> Edit Order
                    </a>
                    <a id="viewLink" href="#" class="btn btn-light text-start py-2 px-3 border-0 rounded-3">
                        <i class="fas fa-eye me-2 text-info"></i> View Details
                    </a>
                    <hr class="my-1 opacity-25">
                    <a id="deleteLink" href="#" class="btn btn-light text-start py-2 px-3 border-0 rounded-3 text-danger" onclick="return confirm('Delete order?')">
                        <i class="fas fa-trash-alt me-2"></i> Delete Order
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
var $j = jQuery.noConflict();

$j(document).ready(function() {
    if ($j.isFunction($j.fn.DataTable)) {
        var table = $j('#orderTable').DataTable({
            "pageLength": 10,
            "dom": 'rtip',
            "ordering": true,
            "order": [[ 2, "asc" ]], // Initial sorting by Expiry Date
            "columnDefs": [
                { "orderable": true, "targets": 2 },    // Sirf Expiry column sortable hai
                { "orderable": false, "targets": [0, 1, 3, 4] } // Baaki sab disabled
            ],
            "language": {
                "paginate": { 
                    "next": '<i class="fas fa-chevron-right"></i>', 
                    "previous": '<i class="fas fa-chevron-left"></i>' 
                },
                "emptyTable": "No orders available"
            }
        });

        $j('#orderSearch').on('keyup', function() {
            table.search(this.value).draw();
        });
    }
});

function openActionPopup(id, domain) {
    document.getElementById('modalDomainName').innerText = domain;
    document.getElementById('editLink').href = "<?= base_url('orders/edit') ?>/" + id;
    document.getElementById('viewLink').href = "<?= base_url('orders/view') ?>/" + id;
    document.getElementById('deleteLink').href = "<?= base_url('orders/delete') ?>/" + id;
    new bootstrap.Modal(document.getElementById('actionModal')).show();
}
</script>

<?= $this->endSection() ?>