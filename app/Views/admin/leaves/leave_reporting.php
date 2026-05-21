<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>

<style>
    .report-card { background: #fff; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 2rem; }
    .report-header { background: #0b0f1a; color: #fff; padding: 15px 20px; border-radius: 10px 10px 0 0; }
    .filter-section { background: #fdfdfd; border-bottom: 1px solid #eee; padding: 20px; }
    .export-buttons { margin-bottom: 15px; display: flex; gap: 10px; }
    .btn-ex { border: none; padding: 6px 15px; border-radius: 4px; color: #fff; font-size: 13px; font-weight: 600; cursor: pointer; }
    .btn-excel { background: #10b981; }
    .btn-pdf { background: #ef4444; }
    .badge-soft { background: #e0f2fe; color: #0369a1; padding: 4px 8px; border-radius: 4px; font-size: 11px; }
</style>

<div class="container-fluid py-4">
    <div class="report-card">
        <div class="report-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-file-invoice mr-2 text-primary"></i> Staff Leave Summary</h5>
            <small id="statusMsg" class="text-success" style="display:none;"><i class="fas fa-check-circle"></i> Updated</small>
        </div>

        <div class="filter-section">
            <div class="row">
                <div class="col-md-6">
                    <label class="small font-weight-bold">STAFF MEMBER</label>
                    <select id="staff_id" class="form-control" onchange="fetchReport()">
                        <option value="">All Staff</option>
                        <?php foreach($staff as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= $s['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="small font-weight-bold">MONTH</label>
                    <input type="month" id="month_picker" class="form-control" value="<?= date('Y-m') ?>" onchange="fetchReport()">
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <div class="export-buttons">
                <button onclick="downloadExcel()" class="btn-ex btn-excel"><i class="fas fa-file-excel"></i> Excel Export</button>
                <button onclick="downloadPDF()" class="btn-ex btn-pdf"><i class="fas fa-file-pdf"></i> PDF Export</button>
            </div>

            <div class="table-responsive">
                <table id="leaveTable" class="table table-hover w-100">
                    <thead class="bg-light">
                        <tr>
                            <th>Date</th>
                            <th>Staff Details</th>
                            <th>Leave Type</th>
                            <th>Duration</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr><td colspan="5" class="text-center py-4 text-muted">Loading your report...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function fetchReport() {
    var sid = $('#staff_id').val();
    var mon = $('#month_picker').val();
    var apiUrl = "<?= base_url('admin/leaves/get-monthly-data') ?>";
    
    $('#statusMsg').hide();
    
    $.ajax({
        url: apiUrl,
        type: "GET",
        data: { staff_id: sid, month: mon },
        dataType: "json",
        success: function(data) {
            var html = '';
            if(data && data.length > 0) {
                data.forEach(function(row) {
                    var name = row.staff_name || 'User';
                    var eid = row.employee_id || 'N/A';
                    // Database mapping
                    // 1. Pehle value nikalein
var days = parseFloat(row.total_days > 0 ? row.total_days : row.calculated_days) || 0;

// 2. Condition check karein (Agar 1 hai toh 'Day', warna 'Days')
var durationText = days + (days === 1 ? ' Day' : ' Days');

// 3. HTML mein variable use karein
html += `<tr>
    <td>${row.from_date}</td>
    <td><b>${name}</b><br><small class="text-muted">ID: ${eid}</small></td>
    <td><span class="badge-soft">${row.leave_name}</span></td>
    <td><b>${durationText}</b></td>
    <td><small>${row.reason || '---'}</small></td>
</tr>`;
                });
            } else {
                html = '<tr><td colspan="5" class="text-center py-4">No records found for this selection.</td></tr>';
            }
            $('#tableBody').html(html);
            $('#statusMsg').fadeIn().delay(2000).fadeOut();
        },
        error: function() {
            $('#tableBody').html('<tr><td colspan="5" class="text-center text-danger">Error loading data.</td></tr>');
        }
    });
}

// Excel Export Logic
function downloadExcel() {
    var mon = $('#month_picker').val();
    var workbook = XLSX.utils.book_new();
    
    // 1. Table ka data array mein convert karein (Formatting saaf karne ke liye)
    var data = [];
    var headers = ["Date", "Staff Name", "Staff ID", "Leave Type", "Duration", "Reason"];
    data.push(headers);

    $("#tableBody tr").each(function() {
        var row = [];
        $(this).find('td').each(function(index) {
            var text = $(this).text().trim();
            
            // "Staff Details" wale column ko Name aur ID mein todne ke liye
            if (index === 1) { 
                var details = text.split("ID:"); 
                row.push(details[0] ? details[0].trim() : ""); // Name
                row.push(details[1] ? details[1].trim() : ""); // ID
            } else {
                row.push(text);
            }
        });
        data.push(row);
    });

    // 2. Worksheet banayein
    var worksheet = XLSX.utils.aoa_to_sheet(data);

    // 3. Auto-Column Width (Taaki ####### na dikhe)
    var wscols = [
        { wch: 15 }, // Date
        { wch: 20 }, // Staff Name
        { wch: 12 }, // Staff ID
        { wch: 20 }, // Leave Type
        { wch: 12 }, // Duration
        { wch: 35 }  // Reason
    ];
    worksheet['!cols'] = wscols;

    // 4. Workbook mein sheet add karke download karein
    XLSX.utils.book_append_sheet(workbook, worksheet, "Leave Report");
    XLSX.writeFile(workbook, "Leave_Report_" + mon + ".xlsx");
}
// PDF Export Logic
function downloadPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    doc.text("Staff Leave Report - " + $('#month_picker').val(), 14, 15);
    doc.autoTable({ html: '#leaveTable', startY: 25, theme: 'grid' });
    doc.save("Leave_Report_" + $('#month_picker').val() + ".pdf");
}

// Page load initialization
$(document).ready(function() {
    fetchReport();
});
</script>

<?= $this->endSection() ?>