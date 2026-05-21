<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<style>
    /* Desktop & General Styles */
    .glass-card { background: #fff; border-radius: 20px; border: 1px solid #f1f5f9; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
    .btn-action { width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px !important; transition: all 0.2s ease; border: none; font-size: 0.9rem; }
    .btn-action:hover { transform: translateY(-3px); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .text-indigo { color: #6366f1; }
    .bg-indigo-light { background: rgba(99, 102, 241, 0.1); color: #6366f1; }
    .avatar-circle { width: 45px; height: 45px; background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); color: white; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-weight: bold; flex-shrink: 0; }
    
    /* Status Badges */
    .badge-soft-success { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }
    .badge-soft-danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }
    .badge-soft-warning { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); }
    
    .btn-indigo { background: #6366f1; color: white; border: none; transition: 0.3s; border-radius: 12px; }
    .btn-indigo:hover { background: #4f46e5; color: white; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3); }

    /* Custom Search Input */
    .search-input { border-radius: 12px; height: 48px; border: 1px solid #e2e8f0; padding-left: 45px; }
    .search-icon { position: absolute; left: 18px; top: 15px; color: #94a3b8; }

    /* BLUE ROUND PAGINATION */
    .dataTables_wrapper .dataTables_paginate {
        margin-top: 25px !important;
        display: flex !important;
        justify-content: center !important;
        gap: 8px !important;
        padding-bottom: 20px;
    }

    .dataTables_wrapper .paginate_button {
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

    .dataTables_wrapper .paginate_button.current {
        background: #3b82f6 !important;
        color: white !important;
        border: none !important;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
    }

    .dataTables_filter { display: none !important; }
    .dataTables_info { text-align: center; margin-top: 10px; color: #94a3b8; font-size: 0.8rem; }

    @media (max-width: 767.98px) {
        .btn-action { width: 38px; height: 38px; }
    }
</style>

<div class="container-fluid py-3">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">Client Directory</h3>
            <p class="text-muted small mb-0">Managing <strong><?= count($all_clients) ?></strong> active business profiles.</p>
        </div>
        <a href="<?= base_url('clients/add') ?>" class="btn btn-indigo shadow-sm px-4 py-2">
            <i class="fas fa-plus-circle me-2"></i> Add New Client
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-3" style="border-radius: 20px;">
                <div class="d-flex align-items-center">
                    <div class="p-3 rounded-3 me-3 bg-indigo-light">
                        <i class="fas fa-users fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.6rem;">Total Clients</small>
                        <h4 class="mb-0 fw-bold text-dark"><?= count($all_clients) ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card h-100 border-0 shadow-sm p-2" style="border-radius: 20px;">
                <div class="position-relative">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="clientSearch" class="form-control search-input border-0" placeholder="Quick search by name, email or location...">
                </div>
            </div>
        </div>
    </div>

    <div class="card glass-card border-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover" id="clientTable" style="width: 100%;">
                <thead class="bg-light">
                    <tr class="text-muted small text-uppercase">
                        <th class="ps-4 py-3">Client Profile</th>
                        <th>Contact Info</th>
                        <th>Location</th>
                        <th class="text-center px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($all_clients)): foreach($all_clients as $row): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3">
                                        <?= strtoupper(substr($row['client_name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0"><?= esc($row['client_name']) ?></div>
                                        <?php if(!empty($row['website_url'])): ?>
                                            <a href="<?= esc($row['website_url']) ?>" target="_blank" class="text-decoration-none extra-small text-indigo d-block" style="font-size: 0.75rem;">
                                                <i class="fas fa-link me-1"></i><?= str_replace(['https://', 'http://', 'www.'], '', $row['website_url']) ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small mb-1 text-dark text-truncate" style="max-width: 180px;">
                                    <i class="fas fa-envelope me-2 text-muted"></i><?= esc($row['email_1']) ?>
                                </div>
                                <div class="small text-muted">
                                    <i class="fab fa-whatsapp me-2 text-success"></i><?= esc($row['phone']) ?>
                                </div>
                            </td>
                            <td>
                                <div class="small fw-bold text-dark"><?= $row['state'] ?: 'N/A' ?></div>
                                <div class="small text-muted text-truncate" style="max-width: 150px;"><?= $row['address'] ?: 'No address' ?></div>
                            </td>
                            <td class="text-center px-4">
                                <div class="d-flex justify-content-center gap-2">
                                    <button type="button" class="btn-action bg-indigo-light text-indigo" 
                                            onclick="viewClientOrders(<?= $row['id'] ?>, '<?= esc($row['client_name']) ?>')" title="Orders">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                    <a href="<?= base_url('clients/view/'.$row['id']) ?>" class="btn-action bg-light text-primary" title="View"><i class="fas fa-eye"></i></a>
                                    <button type="button" class="btn-action bg-light text-indigo" onclick="openClientMailModal('<?= $row['email_1'] ?>', '<?= esc($row['client_name']) ?>')" title="Email"><i class="fas fa-paper-plane"></i></button>
                                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $row['phone']) ?>" target="_blank" class="btn-action bg-light text-success" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                                    <a href="<?= base_url('clients/edit/'.$row['id']) ?>" class="btn-action bg-light text-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="<?= base_url('clients/delete/'.$row['id']) ?>" onclick="return confirm('Delete client?')" class="btn-action bg-light text-danger" title="Delete"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="ordersModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered px-3">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0"><i class="fas fa-box-open me-2 text-indigo"></i> <span id="modalClientName"></span>'s Orders</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" style="min-width: 600px;">
                        <thead class="bg-light">
                            <tr class="small text-muted text-uppercase" style="font-size: 0.7rem;">
                                <th class="ps-4 py-3">Domain/Service</th>
                                <th>Type & Provider</th>
                                <th>Expiry Date</th>
                                <th class="pe-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody id="ordersTableBody"></tbody>
                    </table>
                </div>
                <div id="noOrdersMsg" class="text-center py-5 d-none">
                    <i class="fas fa-folder-open fs-2 text-muted mb-2 d-block opacity-25"></i>
                    <p class="text-muted">No active orders found for this client.</p>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius: 10px;">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="clientMailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered px-3">
    <div class="modal-content" style="border-radius: 20px; border: none;">
      <form action="<?= base_url('home/sendNotice') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="modal-header border-0 pt-4 px-4">
            <h5 class="fw-bold"><i class="fas fa-paper-plane me-2 text-indigo"></i> Quick Connect</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body px-4">
            <input type="hidden" name="email" id="m_to_email">
            <div class="mb-3">
                <label class="small fw-bold text-muted mb-1 text-uppercase">To</label>
                <input type="text" id="display_email" class="form-control border-0 bg-light py-2" disabled style="border-radius: 10px;">
            </div>
            <div class="mb-3">
                <label class="small fw-bold text-muted mb-1 text-uppercase">Message</label>
                <textarea name="message" id="m_message" rows="4" class="form-control border-0 bg-light py-2" required style="border-radius: 10px;"></textarea>
            </div>
        </div>
        <div class="modal-footer border-0 pb-4 px-4">
            <button type="submit" class="btn btn-indigo w-100 py-2 fw-bold" style="border-radius: 12px;">Send Email Now</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// "DataTable is not a function" fix using NoConflict
var $j = jQuery.noConflict();

$j(document).ready(function() {
    // Check if DataTable plugin is available
    if ($j.isFunction($j.fn.DataTable)) {
        var table = $j('#clientTable').DataTable({
            "pageLength": 10, // SET TO 1 AS REQUESTED
            "dom": 'rtip',
            "ordering": true,
            "language": {
                "paginate": { 
                    "next": '<i class="fas fa-chevron-right"></i>', 
                    "previous": '<i class="fas fa-chevron-left"></i>' 
                }
            }
        });

        // Bind Custom Search
        $j('#clientSearch').on('keyup', function() {
            table.search(this.value).draw();
        });
    }

    // Flash Messages
    <?php if (session()->getFlashdata('status')): ?>
        Swal.fire({ title: 'Success', text: "<?= session()->getFlashdata('status') ?>", icon: 'success', timer: 2000, showConfirmButton: false });
    <?php endif; ?>
});

// Original Functions
function viewClientOrders(clientId, clientName) {
    const tableBody = document.getElementById('ordersTableBody');
    const noOrdersMsg = document.getElementById('noOrdersMsg');
    const nameLabel = document.getElementById('modalClientName');
    
    nameLabel.innerText = clientName;
    tableBody.innerHTML = '<tr><td colspan="4" class="text-center py-5"><div class="spinner-border spinner-border-sm text-indigo"></div> Loading orders...</td></tr>';
    noOrdersMsg.classList.add('d-none');
    
    var myModal = new bootstrap.Modal(document.getElementById('ordersModal'));
    myModal.show();

    fetch('<?= base_url('clients/getClientOrders') ?>/' + clientId)
        .then(response => response.json())
        .then(data => {
            tableBody.innerHTML = '';
            if (data && data.length > 0) {
                data.forEach(order => {
                    const expiryStr = order.domain_expiry_date || order.hosting_expiry_date;
                    let statusBadge = '';
                    if (expiryStr) {
                        const expiryDate = new Date(expiryStr);
                        const today = new Date();
                        const diffDays = Math.ceil((expiryDate - today) / (1000 * 60 * 60 * 24));
                        if (diffDays < 0) statusBadge = `<span class="badge badge-soft-danger rounded-pill px-3 py-1">Expired</span>`;
                        else if (diffDays <= 15) statusBadge = `<span class="badge badge-soft-warning rounded-pill px-3 py-1">Soon (${diffDays}d)</span>`;
                        else statusBadge = `<span class="badge badge-soft-success rounded-pill px-3 py-1">Active</span>`;
                    } else {
                        statusBadge = `<span class="badge bg-light text-muted rounded-pill px-3 py-1">No Date</span>`;
                    }
                    tableBody.innerHTML += `<tr><td class="ps-4"><div class="fw-bold text-dark">${order.domain_name}</div></td><td><div class="small text-dark">${order.type_name || 'Service'}</div></td><td><div class="small fw-bold">${expiryStr || 'N/A'}</div></td><td class="text-center">${statusBadge}</td></tr>`;
                });
            } else {
                noOrdersMsg.classList.remove('d-none');
                tableBody.innerHTML = '';
            }
        });
}

function openClientMailModal(email, name) {
    if(!email || email === "null") { Swal.fire('Error', 'No email found.', 'warning'); return; }
    document.getElementById('m_to_email').value = email;
    document.getElementById('display_email').value = email;
    document.getElementById('m_message').value = `Hi ${name},\n\nWe are checking in regarding your services.`;
    new bootstrap.Modal(document.getElementById('clientMailModal')).show();
}
</script>

<?= $this->endSection() ?>