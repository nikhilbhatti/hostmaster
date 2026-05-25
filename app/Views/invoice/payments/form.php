<?= $this->extend('layout/main') ?>
<?php $page_title = 'Record Payment'; ?>
<?= $this->section('content') ?>

<style>
body{background:#f7f8fb}
.zoho-wrapper{background:#fff;font-family:Inter,system-ui,-apple-system,sans-serif;color:#111827;width:100%;min-height:calc(100vh - 80px)}
.zoho-heading{font-size:24px;font-weight:700;padding:28px 28px 24px;border-bottom:1px solid #edf0f5;background:#fff}
.zoho-form-body{padding:34px 28px 30px;display:flex;flex-direction:column;gap:22px}
.zoho-grid-row{display:grid;grid-template-columns:190px minmax(350px,500px);align-items:center;gap:24px}
.zoho-grid-row.align-top{align-items:flex-start}
.zoho-label{font-size:15px;font-weight:400;color:#111827}
.zoho-label .required{color:#e11d48;margin-left:2px}
.zoho-input-container{width:100%;max-width:500px;position:relative}
.zoho-control{width:100%;height:42px;padding:9px 12px;font-size:15px;border:1px solid #d7deea;border-radius:6px;background:#fff;color:#111827;box-sizing:border-box}
textarea.zoho-control{height:auto;min-height:70px}
.zoho-control:focus{outline:none;border-color:#4f8df7;box-shadow:0 0 0 2px rgba(79,141,247,.12)}
.zoho-search-select-container{position:relative;width:100%}
.search-input-trigger{background:#fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E") no-repeat right 13px center;background-size:15px;cursor:pointer;padding-right:38px}
.zoho-dropdown-menu{position:absolute;top:46px;left:0;width:100%;background:#fff;border:1px solid #d7deea;border-radius:6px;max-height:280px;overflow-y:auto;z-index:999;box-shadow:0 8px 22px rgba(15,23,42,.12);display:none}
.dropdown-search-box-wrapper{padding:9px;border-bottom:1px solid #edf0f5;background:#fff;position:sticky;top:0}
.dropdown-search-field{width:100%;height:36px;padding:7px 10px;border:1px solid #d7deea;border-radius:5px}
.zoho-option-item{padding:11px 14px;cursor:pointer;border-bottom:1px solid #f1f5f9}
.zoho-option-item:hover{background:#f4f8ff}
.opt-title{font-weight:600;font-size:14px}
.opt-meta{font-size:12px;color:#64748b;margin-top:3px}
.meta-panel-box{background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:18px;display:grid;grid-template-columns:repeat(3,1fr);gap:18px}
.meta-block strong{font-size:12px;color:#475569;text-transform:uppercase;display:block;margin-bottom:5px}
.meta-block span{font-size:14px;color:#0f172a;font-weight:600}
.zoho-input-group-gear{display:flex;align-items:center;position:relative}
.gear-setting-btn{position:absolute;right:10px;border:none;background:transparent;cursor:pointer;color:#2563eb;font-size:16px}
.table-section{margin-top:8px;max-width:980px}
.table-title{font-size:18px;font-weight:600;margin-bottom:14px}
.zoho-data-table{width:100%;border-collapse:collapse;border:1px solid #dfe5ef;font-size:14px;background:#fff}
.zoho-data-table th{background:#f8fafc;color:#64748b;font-weight:700;padding:13px 12px;border-bottom:1px solid #e2e8f0;text-transform:uppercase;font-size:12px}
.zoho-data-table td{padding:13px 12px;border-bottom:1px solid #f1f5f9}
.zoho-split-layout{display:grid;grid-template-columns:minmax(450px,760px) 360px;gap:38px;margin-top:18px;align-items:start}
.summary-card{background:#fbfbfd;border-radius:12px;padding:18px 24px}
.summary-row,.excess-alert-row{display:flex;justify-content:space-between;padding:10px 0;font-size:15px;color:#111827}
.excess-alert-row{font-weight:500}
#summary_excess{color:#dc2626!important}
.zoho-upload-container{display:inline-block;border:1px solid #d7deea;border-radius:6px;padding:11px 16px;background:#fff;cursor:pointer;text-align:left;min-width:160px}
.zoho-upload-container:hover{background:#f8fafc}
.upload-trigger-text{color:#111827;font-size:15px;font-weight:500}
.upload-limits-hint{font-size:13px;color:#64748b;margin-top:10px}
.footer-action-bar{position:sticky;bottom:0;background:#fff;border-top:1px solid #e5e7eb;padding:16px 28px;display:flex;gap:12px;z-index:999;margin-top:30px;width:100%;justify-content:flex-start;align-items:center;flex-wrap:wrap;box-shadow:0 -2px 12px rgba(0,0,0,.05)}
.btn-zoho-blue{background:#4087f5;color:#fff;border:1px solid #4087f5;padding:10px 18px;border-radius:6px;font-size:15px;cursor:pointer;font-weight:600}
.btn-zoho-white{background:#fff;color:#111827;border:1px solid #d7deea;padding:10px 18px;border-radius:6px;font-size:15px;cursor:pointer;font-weight:600}
.btn-zoho-cancel{color:#111827;padding:10px 8px;text-decoration:none;font-size:15px;font-weight:500}
@media(max-width:900px){.zoho-grid-row,.zoho-split-layout{grid-template-columns:1fr}.footer-action-bar{left:0;padding:15px;justify-content:center}.btn-zoho-blue,.btn-zoho-white{width:100%;text-align:center}}
</style>

<?php
$selectedCustomerId = $invoice['customer_id'] ?? '';
$selectedCustomerName = $invoice['cname'] ?? '';
$selectedInvoiceId = $invoice['id'] ?? '';
$selectedBalance = $invoice['balance_due'] ?? 0;
$selectedTotal = $invoice['total'] ?? 0;
$selectedInvoiceNumber = $invoice['invoice_number'] ?? '';
?>

<div class="zoho-wrapper">
    <div class="zoho-heading">Record Payment</div>

    <form method="POST" action="<?= base_url('invoice/payments/store') ?>" class="zoho-form-body" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <input type="hidden" name="invoice_id" id="invoice_id" value="<?= esc($selectedInvoiceId) ?>">

        <div class="zoho-grid-row">
            <div class="zoho-label">Customer Name<span class="required">*</span></div>
            <div class="zoho-input-container">
                <div class="zoho-search-select-container">
                    <input type="text" id="customer_search_trigger" class="zoho-control search-input-trigger" placeholder="Select a Customer" readonly onclick="toggleSearchDropdown()" value="<?= esc($selectedCustomerName) ?>">
                    <input type="hidden" name="customer_id" id="customer_id" value="<?= esc($selectedCustomerId) ?>" required>

                    <div class="zoho-dropdown-menu" id="searchDropdownMenu">
                        <div class="dropdown-search-box-wrapper">
                            <input type="text" id="dropdownFilterInput" class="dropdown-search-field" placeholder="Search customers..." oninput="filterDropdownList()">
                        </div>

                        <div id="optionsListWrapper">
                            <?php foreach($customers as $c): ?>
                                <div class="zoho-option-item"
                                     data-id="<?= $c['id'] ?>"
                                     data-name="<?= esc($c['display_name']) ?>"
                                     onclick="selectCustomerOption(this)">
                                    <div class="opt-title"><?= esc($c['display_name']) ?></div>
                                    <div class="opt-meta">
                                        <?= esc($c['company_name'] ?? 'No Company') ?> |
                                        <?= esc($c['email'] ?? 'No Email') ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="zoho-grid-row align-top" id="metaPanelRow" style="display:none;">
            <div></div>
            <div class="zoho-input-container">
                <div class="meta-panel-box">
                    <div class="meta-block"><strong>Email Address</strong><span id="meta_email">--</span></div>
                    <div class="meta-block"><strong>Phone / Mobile</strong><span id="meta_phone">--</span></div>
                    <div class="meta-block"><strong>Company</strong><span id="meta_company">--</span></div>
                </div>
            </div>
        </div>

        <div class="zoho-grid-row">
            <div class="zoho-label">Amount Received<span class="required">*</span></div>
            <div class="zoho-input-container">
                <div style="position:relative;display:flex;align-items:center;">
                    <span style="position:absolute;left:12px;color:#64748b;">INR</span>
                    <input type="number" name="amount" id="amount_received" class="zoho-control" step="0.01" min="0" value="<?= !empty($selectedBalance) ? number_format((float)$selectedBalance, 2, '.', '') : '0.00' ?>" style="padding-left:45px;" required oninput="distributeAmountAndCalc()">
                </div>
            </div>
        </div>

        <div class="zoho-grid-row">
            <div class="zoho-label">Bank Charges (if any)</div>
            <div class="zoho-input-container">
                <input type="number" name="bank_charges" id="bank_charges" class="zoho-control" step="0.01" min="0" value="0.00" oninput="calculateSummaryPanel()">
            </div>
        </div>

        <div class="zoho-grid-row">
            <div class="zoho-label">Payment Date<span class="required">*</span></div>
            <div class="zoho-input-container">
                <input type="date" name="payment_date" id="payment_date" class="zoho-control" value="<?= date('Y-m-d') ?>" required>
            </div>
        </div>

        <div class="zoho-grid-row">
            <div class="zoho-label">Payment #</div>
            <div class="zoho-input-container">
                <div class="zoho-input-group-gear">
                    <input type="text" name="payment_number" id="payment_number" class="zoho-control" value="Auto Generated" readonly style="padding-right:35px;background:#f8fafc;">
                    <button type="button" class="gear-setting-btn">⚙️</button>
                </div>
            </div>
        </div>

        <div class="zoho-grid-row">
            <div class="zoho-label">Payment Mode</div>
            <div class="zoho-input-container">
                <select name="payment_mode" id="payment_mode" class="zoho-control">
                    <option value="cash">Cash</option>
                    <option value="bank_remittance">Bank Remittance</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="cheque">Cheque</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="upi">UPI</option>
                </select>
            </div>
        </div>

        <div class="zoho-grid-row">
            <div class="zoho-label">Reference#</div>
            <div class="zoho-input-container">
                <input type="text" name="reference" id="reference_number" class="zoho-control">
            </div>
        </div>

        <div class="zoho-grid-row">
            <div class="zoho-label">Tax deducted?</div>
            <div class="zoho-input-container" style="display:flex;gap:22px;align-items:center;">
                <label><input type="radio" name="tax_deducted" value="no" checked> No Tax deducted</label>
                <label><input type="radio" name="tax_deducted" value="yes"> Yes, TDS (Income Tax)</label>
            </div>
        </div>

        <div class="table-section">
            <div class="table-title">Unpaid Invoices</div>
            <table class="zoho-data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Invoice Number</th>
                        <th style="text-align:right;">Invoice Amount</th>
                        <th style="text-align:right;">Amount Due</th>
                        <th style="text-align:right;width:160px;">Payment</th>
                    </tr>
                </thead>
                <tbody id="unpaidInvoicesBody">
                    <?php if(!empty($invoice)): ?>
                        <tr>
                            <td><?= !empty($invoice['invoice_date']) ? date('d/m/Y', strtotime($invoice['invoice_date'])) : '-' ?></td>
                            <td style="font-weight:600;color:#2563eb;"><?= esc($selectedInvoiceNumber) ?></td>
                            <td style="text-align:right;">₹<?= number_format((float)$selectedTotal, 2) ?></td>
                            <td style="text-align:right;color:#dc2626;">₹<?= number_format((float)$selectedBalance, 2) ?></td>
                            <td style="text-align:right;">
                                <input type="number"
                                    name="allocated_amount[<?= esc($selectedInvoiceId) ?>]"
                                    class="zoho-control invoice-allocation-input"
                                    style="text-align:right;max-width:140px;display:inline-block;"
                                    step="0.01"
                                    min="0"
                                    max="<?= esc($selectedBalance) ?>"
                                    data-due="<?= esc($selectedBalance) ?>"
                                    value="<?= number_format((float)$selectedBalance, 2, '.', '') ?>"
                                    oninput="calculateTotalFromGrid()">
                            </td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center;padding:40px;color:#64748b;">
                                There are no unpaid invoices associated with this customer.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="zoho-split-layout">
            <div style="display:flex;flex-direction:column;gap:20px;">
                <div>
                    <div class="zoho-label" style="margin-bottom:8px;">Notes (Internal use. Not visible to customer)</div>
                    <textarea name="notes" id="notes" class="zoho-control" rows="4"></textarea>
                </div>

                <div>
                    <div class="zoho-label" style="margin-bottom:8px;">Attachments</div>
                    <div class="zoho-upload-container" onclick="document.getElementById('file_upload_input').click()">
                        <input type="file" name="attachments[]" id="file_upload_input" multiple style="display:none;" onchange="handleFileSelectionDisplay(this)">
                        <div class="upload-trigger-text">↥ Upload File</div>
                        <div class="upload-limits-hint">You can upload a maximum of 3 files, 5MB each</div>
                        <div id="file_names_preview" style="margin-top:8px;font-weight:600;font-size:12px;color:#1e293b;"></div>
                    </div>
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-row"><span>Amount Received :</span><span id="summary_received">0.00</span></div>
                <div class="summary-row"><span>Amount used for Payments :</span><span id="summary_used">0.00</span></div>
                <div class="summary-row"><span>Amount Refunded :</span><span>0.00</span></div>
                <div class="excess-alert-row"><span>⚠️ Amount in Excess:</span><span id="summary_excess">₹ 0.00</span></div>
            </div>
        </div>

        <div class="footer-action-bar">
            <button type="submit" name="save_status" value="draft" class="btn-zoho-white">Save as Draft</button>
            <button type="submit" name="save_status" value="paid" class="btn-zoho-blue">Save as Paid</button>
            <a href="<?= base_url('invoice/payments') ?>" class="btn-zoho-cancel">Cancel</a>
        </div>
    </form>
</div>

<script>
const BASE_URL = '<?= rtrim(base_url(), '/') ?>/';

function toggleSearchDropdown() {
    const menu = document.getElementById('searchDropdownMenu');
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    if (menu.style.display === 'block') {
        document.getElementById('dropdownFilterInput').focus();
    }
}

document.addEventListener('click', function(event) {
    const container = document.querySelector('.zoho-search-select-container');
    if (container && !container.contains(event.target)) {
        document.getElementById('searchDropdownMenu').style.display = 'none';
    }
});

function filterDropdownList() {
    const filter = document.getElementById('dropdownFilterInput').value.toLowerCase();
    document.querySelectorAll('.zoho-option-item').forEach(item => {
        item.style.display = item.innerText.toLowerCase().includes(filter) ? 'block' : 'none';
    });
}

function selectCustomerOption(element) {
    const id = element.getAttribute('data-id');
    const name = element.getAttribute('data-name');

    document.getElementById('customer_search_trigger').value = name;
    document.getElementById('customer_id').value = id;
    document.getElementById('invoice_id').value = '';
    document.getElementById('searchDropdownMenu').style.display = 'none';

    onCustomerChange(id);
}

function onCustomerChange(customerId) {
    if (!customerId) return;
    fetchCustomerMeta(customerId);
    fetchUnpaidInvoices(customerId);
}

function fetchCustomerMeta(customerId) {
    fetch(BASE_URL + 'invoice/payments/get_customer_details/' + customerId)
        .then(res => res.ok ? res.json() : null)
        .then(customer => {
            if (customer) {
                document.getElementById('meta_email').innerText = customer.email || 'N/A';
                document.getElementById('meta_phone').innerText = customer.phone || 'N/A';
                document.getElementById('meta_company').innerText = customer.company_name || 'N/A';
                document.getElementById('metaPanelRow').style.display = 'grid';
            }
        })
        .catch(err => console.error(err));
}

function formatDateDMY(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return dateStr;
    return String(d.getDate()).padStart(2, '0') + '/' + String(d.getMonth() + 1).padStart(2, '0') + '/' + d.getFullYear();
}

function fetchUnpaidInvoices(customerId) {
    const tbody = document.getElementById('unpaidInvoicesBody');

    fetch(BASE_URL + 'invoice/payments/get_unpaid_invoices/' + customerId)
        .then(response => response.json())
        .then(data => {
            tbody.innerHTML = '';

            if (!data || data.length === 0) {
                tbody.innerHTML = `<tr>
                    <td colspan="5" style="text-align:center;padding:40px;color:#64748b;">
                        There are no unpaid invoices associated with this customer.
                    </td>
                </tr>`;
            } else {
                data.forEach(inv => {
                    tbody.insertAdjacentHTML('beforeend', `
                        <tr>
                            <td>${formatDateDMY(inv.invoice_date)}</td>
                            <td style="font-weight:600;color:#2563eb;">${inv.invoice_number}</td>
                            <td style="text-align:right;">₹${parseFloat(inv.total || 0).toFixed(2)}</td>
                            <td style="text-align:right;color:#dc2626;">₹${parseFloat(inv.balance_due || 0).toFixed(2)}</td>
                            <td style="text-align:right;">
                                <input type="number"
                                    name="allocated_amount[${inv.id}]"
                                    class="zoho-control invoice-allocation-input"
                                    style="text-align:right;max-width:140px;display:inline-block;"
                                    step="0.01"
                                    min="0"
                                    max="${inv.balance_due}"
                                    data-due="${inv.balance_due}"
                                    value="0.00"
                                    oninput="calculateTotalFromGrid()">
                            </td>
                        </tr>
                    `);
                });

                distributeAmountAndCalc();
            }

            calculateSummaryPanel();
        })
        .catch(error => console.error(error));
}

function distributeAmountAndCalc() {
    let totalReceived = parseFloat(document.getElementById('amount_received').value) || 0;

    document.querySelectorAll('.invoice-allocation-input').forEach(input => {
        const due = parseFloat(input.getAttribute('data-due')) || 0;

        if (totalReceived >= due) {
            input.value = due.toFixed(2);
            totalReceived -= due;
        } else if (totalReceived > 0) {
            input.value = totalReceived.toFixed(2);
            totalReceived = 0;
        } else {
            input.value = '0.00';
        }
    });

    calculateSummaryPanel();
}

function calculateTotalFromGrid() {
    let sum = 0;

    document.querySelectorAll('.invoice-allocation-input').forEach(input => {
        let val = parseFloat(input.value) || 0;
        let due = parseFloat(input.getAttribute('data-due')) || 0;

        if (val > due) {
            val = due;
            input.value = due.toFixed(2);
        }

        sum += val;
    });

    document.getElementById('amount_received').value = sum.toFixed(2);
    calculateSummaryPanel();
}

function calculateSummaryPanel() {
    const amountReceived = parseFloat(document.getElementById('amount_received').value) || 0;
    let totalAllocated = 0;

    document.querySelectorAll('.invoice-allocation-input').forEach(input => {
        totalAllocated += parseFloat(input.value) || 0;
    });

    let excess = amountReceived - totalAllocated;
    if (excess < 0) excess = 0;

    document.getElementById('summary_received').innerText = amountReceived.toFixed(2);
    document.getElementById('summary_used').innerText = totalAllocated.toFixed(2);
    document.getElementById('summary_excess').innerText = '₹ ' + excess.toFixed(2);
}

function handleFileSelectionDisplay(input) {
    const files = input.files;
    const previewDiv = document.getElementById('file_names_preview');
    previewDiv.innerHTML = '';

    if (files.length > 3) {
        alert('Maximum 3 files are allowed!');
        input.value = '';
        return;
    }

    let names = [];
    for (let i = 0; i < files.length; i++) {
        if (files[i].size > 5 * 1024 * 1024) {
            alert('Each file must be less than 5MB.');
            input.value = '';
            previewDiv.innerHTML = '';
            return;
        }
        names.push(files[i].name);
    }

    previewDiv.innerText = 'Selected: ' + names.join(', ');
}

document.addEventListener('DOMContentLoaded', function() {
    const selectedCustomer = document.getElementById('customer_id').value;

    if (selectedCustomer) {
        fetchCustomerMeta(selectedCustomer);
    }

    calculateSummaryPanel();
});
</script>

<?= $this->endSection() ?>