<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <h3 class="mb-4 fw-bold text-dark">Leave Management Panel</h3>

    <div class="card shadow-sm border-0 mb-5">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-clock me-2"></i> Current Pending Requests</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Staff Name</th>
                            <th>Leave Type</th>
                            <th>Dates</th>
                            <th>Reason</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($requests)): ?>
                            <?php foreach($requests as $r): ?>
                            <tr>
                                <td class="ps-3"><strong><?= esc($r['username']) ?></strong></td>
                                <td><span class="badge bg-info text-dark"><?= esc($r['leave_name']) ?></span></td>
                                <td>
                                    <small class="d-block text-nowrap"><b>From:</b> <?= $r['from_date'] ?></small>
                                    <small class="d-block text-nowrap"><b>To:</b> <?= $r['to_date'] ?></small>
                                </td>
                                <td><p class="small mb-0 text-muted" style="max-width: 250px;"><?= esc($r['reason']) ?></p></td>
                                <td>
                                    <form action="<?= base_url('admin/approve-reject') ?>" method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                        <div class="input-group input-group-sm mb-1 shadow-sm">
                                            <input type="text" name="admin_remark" class="form-control" placeholder="Remark">
                                        </div>
                                        <div class="d-flex gap-1">
                                            <button type="submit" name="status" value="approved" class="btn btn-sm btn-success flex-grow-1">Approve</button>
                                            <button type="submit" name="status" value="rejected" class="btn btn-sm btn-danger flex-grow-1">Reject</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted">No pending requests found. 🎉</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark py-3 text-white">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-history me-2"></i> Recent Action History</h5>
                </div>
                <div class="col-md-9">
                    <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                        <div class="input-group input-group-sm shadow-sm" style="width: 200px;">
                            <span class="input-group-text bg-light text-dark border-0"><i class="fas fa-calendar-alt"></i></span>
                            <input type="date" id="dateSearch" class="form-control border-0">
                        </div>
                        <div class="input-group input-group-sm shadow-sm" style="width: 200px;">
                            <span class="input-group-text bg-light text-dark border-0"><i class="fas fa-search"></i></span>
                            <input type="text" id="nameInputSearch" class="form-control border-0" placeholder="Search by name...">
                        </div>
                        <select id="staffNameFilter" class="form-select form-select-sm border-0 shadow-sm" style="width: 180px;">
                            <option value="">-- All Staff Members --</option>
                            <?php 
                                $uniqueNames = array_unique(array_column($processed, 'username'));
                                sort($uniqueNames); 
                                foreach($uniqueNames as $name): 
                            ?>
                                <option value="<?= esc($name) ?>"><?= esc($name) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button id="resetFilters" class="btn btn-sm btn-light border shadow-sm">Reset</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="historyTable">
                    <thead class="table-light text-uppercase" style="font-size: 0.8rem;">
                        <tr>
                            <th class="ps-3">Staff Name</th>
                            <th>Last Status</th>
                            <th>Action Date</th>
                            <th>Staff Reason</th>
                            <th>Admin Remark</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody id="historyBody">
                        <?php if(!empty($processed)): ?>
                            <?php foreach($processed as $p): ?>
                            <tr class="history-row" data-staff="<?= esc($p['username']) ?>" data-date="<?= $p['from_date'] ?>">
                                <td class="ps-3 fw-bold"><?= esc($p['username']) ?></td>
                                <td>
                                    <span class="badge <?= ($p['status'] == 'approved') ? 'bg-success' : 'bg-danger' ?> shadow-sm text-uppercase" style="font-size: 0.7rem;">
                                        <?= esc($p['status']) ?>
                                    </span>
                                </td>
                                <td><small><?= date('d M Y', strtotime($p['from_date'])) ?></small></td>
                                <td><p class="small mb-0 text-muted text-truncate" style="max-width: 150px;"><?= esc($p['reason']) ?></p></td>
                                <td class="small text-muted italic"><?= esc($p['admin_remark']) ?: '-' ?></td>
                                <td class="text-end pe-3">
                                  <a href="<?= base_url('admin/leaves/leave-details/'.$p['user_id']) ?>" class="btn btn-sm btn-primary shadow-sm">
                                    <i class="fas fa-file-alt me-1"></i> View Summary
                                  </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr id="noResults"><td colspan="6" class="text-center py-3 text-muted">No history found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    function applyHistoryFilters() {
        let nameFilter = $('#staffNameFilter').val().toLowerCase();
        let textSearch = $('#nameInputSearch').val().toLowerCase();
        let dateFilter = $('#dateSearch').val();
        let visibleRows = 0;

        $('.history-row').each(function() {
            let rowStaff = $(this).data('staff').toLowerCase();
            let rowDate = $(this).data('date'); 
            let matchDropdown = (nameFilter === "" || rowStaff === nameFilter);
            let matchText = (textSearch === "" || rowStaff.includes(textSearch));
            let matchDate = (dateFilter === "" || rowDate === dateFilter);

            if(matchDropdown && matchText && matchDate) {
                $(this).show();
                visibleRows++;
            } else {
                $(this).hide();
            }
        });

        if(visibleRows === 0) {
            if ($('#noResults').length === 0) {
                $('#historyBody').append('<tr id="noResults"><td colspan="6" class="text-center py-3">No matching records found.</td></tr>');
            }
        } else {
            $('#noResults').remove();
        }
    }

    $('#staffNameFilter, #dateSearch').on('change', applyHistoryFilters);
    $('#nameInputSearch').on('keyup', applyHistoryFilters);

    $('#resetFilters').on('click', function() {
        $('#staffNameFilter, #dateSearch, #nameInputSearch').val('');
        $('.history-row').show();
        $('#noResults').remove();
    });
});
</script>

<style>
    .history-row { transition: all 0.2s; }
    .history-row:hover { background-color: #f8f9fa !important; }
    .italic { font-style: italic; }
    .form-control-sm, .form-select-sm { height: 35px; border-radius: 5px; }
</style>

<?= $this->endSection() ?>