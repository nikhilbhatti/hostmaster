<?= $this->extend('layout/main') ?>
<?php $page_title = 'Edit Payment'; ?>
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
.zoho-input-group-gear{display:flex;align-items:center;position:relative}
.gear-setting-btn{position:absolute;right:10px;border:none;background:transparent;cursor:pointer;color:#2563eb;font-size:16px}
.zoho-split-layout{display:grid;grid-template-columns:minmax(450px,760px) 360px;gap:38px;margin-top:18px;align-items:start}
.summary-card{background:#fbfbfd;border-radius:12px;padding:18px 24px}
.summary-row,.excess-alert-row{display:flex;justify-content:space-between;padding:10px 0;font-size:15px;color:#111827}
.excess-alert-row{font-weight:500}
#summary_excess{color:#dc2626!important}
.zoho-upload-container{display:inline-block;border:1px solid #d7deea;border-radius:6px;padding:11px 16px;background:#fff;cursor:pointer;text-align:left;min-width:160px}
.zoho-upload-container:hover{background:#f8fafc}
.upload-trigger-text{color:#111827;font-size:15px;font-weight:500}
.upload-limits-hint{font-size:13px;color:#64748b;margin-top:10px}
.footer-action-bar{
    position:sticky;
    bottom:0;
    background:#fff;
    border-top:1px solid #e5e7eb;
    padding:16px 28px;
    display:flex;
    gap:12px;
    z-index:999;
    margin-top:30px;
    width:100%;
    justify-content:flex-start;
    align-items:center;
    flex-wrap:wrap;
    box-shadow:0 -2px 12px rgba(0,0,0,.05);
}
.btn-zoho-blue{background:#4087f5;color:#fff;border:1px solid #4087f5;padding:10px 18px;border-radius:6px;font-size:15px;cursor:pointer;font-weight:600}
.btn-zoho-white{background:#fff;color:#111827;border:1px solid #d7deea;padding:10px 18px;border-radius:6px;font-size:15px;cursor:pointer;font-weight:600}
.btn-zoho-cancel{color:#111827;padding:10px 8px;text-decoration:none;font-size:15px;font-weight:500}
.alert-danger-custom{background:#fef2f2;border:1px solid #fca5a5;color:#991b1b;padding:12px 18px;border-radius:6px;font-size:14px;max-width:760px}
@media(max-width:900px){
    .zoho-grid-row,.zoho-split-layout{grid-template-columns:1fr}
    .footer-action-bar{left:0;padding:15px;justify-content:center}
    .btn-zoho-blue,.btn-zoho-white{width:100%;text-align:center}
}
</style>

<div class="zoho-wrapper">

    <div class="zoho-heading">
        Edit Payment <?= !empty($payment['payment_number']) ? '(' . esc($payment['payment_number']) . ')' : '' ?>
    </div>

    <form method="POST" action="<?= base_url('invoice/payments/update/'.$payment['id'] . ((isset($source) && $source === 'invoice') ? '?source=invoice' : '')) ?>" class="zoho-form-body" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <?php if(session()->getFlashdata('errors')): ?>
            <div class="alert-danger-custom">
                <ul style="margin:0;padding-left:20px;">
                    <?php foreach(session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="zoho-grid-row">
            <div class="zoho-label">Customer Name<span class="required">*</span></div>
            <div class="zoho-input-container">
                <select name="customer_id" id="customer_id" class="zoho-control" required>
                    <option value="">Select Customer</option>
                    <?php foreach($customers as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($c['id'] == $payment['customer_id']) ? 'selected' : '' ?>>
                            <?= esc($c['display_name'] ?? $c['cname'] ?? $c['name'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="zoho-grid-row">
            <div class="zoho-label">Invoice Number</div>
            <div class="zoho-input-container">
                <select name="invoice_id" id="invoice_id" class="zoho-control">
                    <option value="">Select Invoice</option>
                    <?php foreach($invoices as $inv): ?>
                        <option value="<?= $inv['id'] ?>" <?= ($inv['id'] == ($payment['invoice_id'] ?? '')) ? 'selected' : '' ?>>
                            <?= esc($inv['invoice_number']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="zoho-grid-row">
            <div class="zoho-label">Amount Received<span class="required">*</span></div>
            <div class="zoho-input-container">
                <div style="position:relative;display:flex;align-items:center;">
                    <span style="position:absolute;left:12px;color:#64748b;">INR</span>
                    <input type="number" name="amount" id="amount_received" class="zoho-control" step="0.01" value="<?= esc($payment['amount'] ?? '0.00') ?>" style="padding-left:45px;" required oninput="calculateSummaryPanel()">
                </div>
            </div>
        </div>

        <div class="zoho-grid-row">
            <div class="zoho-label">Bank Charges (if any)</div>
            <div class="zoho-input-container">
                <input type="number" name="bank_charges" id="bank_charges" class="zoho-control" step="0.01" value="<?= esc($payment['bank_charges'] ?? '0.00') ?>" oninput="calculateSummaryPanel()">
            </div>
        </div>

        <div class="zoho-grid-row">
            <div class="zoho-label">Payment Date<span class="required">*</span></div>
            <div class="zoho-input-container">
                <input type="date" name="payment_date" id="payment_date" class="zoho-control" value="<?= !empty($payment['payment_date']) ? date('Y-m-d', strtotime($payment['payment_date'])) : date('Y-m-d') ?>" required>
            </div>
        </div>

        <div class="zoho-grid-row">
            <div class="zoho-label">Payment #<span class="required">*</span></div>
            <div class="zoho-input-container">
                <div class="zoho-input-group-gear">
                    <input type="text" name="payment_number" id="payment_number" class="zoho-control" value="<?= esc($payment['payment_number'] ?? '') ?>" required style="padding-right:35px;">
                    <button type="button" class="gear-setting-btn">⚙️</button>
                </div>
            </div>
        </div>

        <div class="zoho-grid-row">
            <div class="zoho-label">Payment Mode</div>
            <div class="zoho-input-container">
                <?php $mode = $payment['payment_mode'] ?? ''; ?>
                <select name="payment_mode" id="payment_mode" class="zoho-control">
                    <option value="Cash" <?= ($mode == 'Cash' || $mode == 'cash') ? 'selected' : '' ?>>Cash</option>
                    <option value="Bank Remittance" <?= ($mode == 'Bank Remittance') ? 'selected' : '' ?>>Bank Remittance</option>
                    <option value="Bank Transfer" <?= ($mode == 'Bank Transfer' || $mode == 'bank') ? 'selected' : '' ?>>Bank Transfer</option>
                    <option value="Cheque" <?= ($mode == 'Cheque' || $mode == 'cheque') ? 'selected' : '' ?>>Cheque</option>
                    <option value="Credit Card" <?= ($mode == 'Credit Card') ? 'selected' : '' ?>>Credit Card</option>
                    <option value="UPI" <?= ($mode == 'UPI' || $mode == 'upi') ? 'selected' : '' ?>>UPI</option>
                </select>
            </div>
        </div>

        <div class="zoho-grid-row">
            <div class="zoho-label">Reference#</div>
            <div class="zoho-input-container">
                <input type="text" name="reference" id="reference" class="zoho-control" value="<?= esc($payment['reference'] ?? '') ?>">
            </div>
        </div>

        <div class="zoho-grid-row">
            <div class="zoho-label">Tax deducted?</div>
            <div class="zoho-input-container" style="display:flex;gap:22px;align-items:center;">
                <?php $tax = $payment['tax_deducted'] ?? 'no'; ?>
                <label><input type="radio" name="tax_deducted" value="no" <?= ($tax == 'no') ? 'checked' : '' ?>> No Tax deducted</label>
                <label><input type="radio" name="tax_deducted" value="yes" <?= ($tax == 'yes') ? 'checked' : '' ?>> Yes, TDS (Income Tax)</label>
            </div>
        </div>

        <div class="zoho-split-layout">

            <div style="display:flex;flex-direction:column;gap:20px;">

                <div>
                    <div class="zoho-label" style="margin-bottom:8px;">Notes (Internal use. Not visible to customer)</div>
                    <textarea name="notes" id="notes" class="zoho-control" rows="4"><?= esc($payment['notes'] ?? '') ?></textarea>
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
                <div class="summary-row">
                    <span>Amount Received :</span>
                    <span id="summary_received"><?= number_format($payment['amount'] ?? 0, 2) ?></span>
                </div>

                <div class="summary-row">
                    <span>Amount used for Payments :</span>
                    <span id="summary_used"><?= number_format($payment['amount'] ?? 0, 2) ?></span>
                </div>

                <div class="summary-row">
                    <span>Amount Refunded :</span>
                    <span>0.00</span>
                </div>

                <div class="excess-alert-row">
                    <span>⚠️ Amount in Excess:</span>
                    <span id="summary_excess">₹ 0.00</span>
                </div>
            </div>

        </div>

        <div class="footer-action-bar">
            <button type="submit" class="btn-zoho-blue">Update Payment</button>

            <a href="<?= base_url('invoice/payments') ?>" class="btn-zoho-cancel">Cancel</a>
        </div>

    </form>

</div>

<script>
function calculateSummaryPanel() {
    const amountReceived = parseFloat(document.getElementById('amount_received').value) || 0;

    document.getElementById('summary_received').innerText = amountReceived.toFixed(2);
    document.getElementById('summary_used').innerText = amountReceived.toFixed(2);
    document.getElementById('summary_excess').innerText = '₹ 0.00';
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
        names.push(files[i].name);
    }

    previewDiv.innerText = 'Selected: ' + names.join(', ');
}

calculateSummaryPanel();
</script>

<?= $this->endSection() ?>