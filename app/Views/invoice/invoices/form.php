<?= $this->extend('layout/main') ?>
<?php $page_title = $invoice ? 'Edit Invoice' : 'New Invoice'; ?>
<?= $this->section('content') ?>

<style>
/* ── Customer Dropdown ── */
.cust-dropdown { position:relative; }
.cust-dropdown .cd-input-wrap { display:flex; align-items:center; border:1.5px solid #5065e8; border-radius:6px; background:#fff; padding:0 10px; gap:8px; }
.cust-dropdown .cd-input-wrap input { border:none; outline:none; font-size:14px; padding:9px 0; flex:1; color:#1a1f36; }
.cust-dropdown .cd-input-wrap .cd-arrow { color:#5065e8; cursor:pointer; font-size:18px; }
.cust-dropdown .cd-list { display:none; position:absolute; top:calc(100% + 4px); left:0; right:0; background:#fff; border:1px solid #e8eaed; border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,.12); z-index:9999; max-height:280px; overflow-y:auto; }
.cust-dropdown .cd-list.open { display:block; }
.cust-dropdown .cd-search { padding:10px 12px; border-bottom:1px solid #f1f3f9; position:sticky; top:0; background:#fff; z-index:2; }
.cust-dropdown .cd-search input { width:100%; border:1px solid #e8eaed; border-radius:6px; padding:7px 10px; font-size:13px; outline:none; }
.cust-dropdown .cd-item { display:flex; align-items:center; gap:10px; padding:10px 14px; cursor:pointer; transition:.15s; }
.cust-dropdown .cd-item:hover { background:#f0f4ff; }
.cust-dropdown .cd-item.selected { background:#eff2ff; }
.cust-dropdown .cd-avatar { width:34px; height:34px; border-radius:50%; background:#5065e8; color:#fff; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700; flex-shrink:0; }
.cust-dropdown .cd-item-info { flex:1; min-width:0; }
.cust-dropdown .cd-item-name { font-size:13px; font-weight:600; color:#1a1f36; }
.cust-dropdown .cd-item-sub { font-size:11px; color:#6b7280; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.cust-dropdown .cd-new { display:flex; align-items:center; gap:8px; padding:10px 14px; color:#5065e8; font-size:13px; font-weight:500; cursor:pointer; border-top:1px solid #f1f3f9; position:sticky; bottom:0; background:#fff; }
.cust-dropdown .cd-new:hover { background:#f0f4ff; }

/* ── Customer Info Panel ── */
.cust-panel { background:#f8f9fc; border:1px solid #e8eaed; border-radius:8px; padding:14px 18px; margin-top:12px; display:none; }
.cust-panel.show { display:flex; gap:20px; justify-content:space-between; }
.cust-panel .cp-name { font-size:14px; font-weight:700; color:#1a1f36; margin-bottom:4px; }
.cust-panel .cp-line { font-size:12px; color:#6b7280; line-height:1.7; }
.cust-panel .cp-badge { display:inline-block; background:#e0e7ff; color:#4338ca; font-size:11px; font-weight:600; padding:2px 8px; border-radius:20px; margin-top:4px; }

/* ── Items Table ── */
.items-tbl { width:100%; border-collapse:collapse; }
.items-tbl th { background:#f8f9fc; padding:9px 12px; font-size:11px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.04em; border-bottom:1px solid #e8eaed; text-align:left; white-space:nowrap; }
.items-tbl td { padding:8px 6px; border-bottom:1px solid #f5f5f5; vertical-align:top; }
.items-tbl tbody tr:last-child td { border-bottom:none; }
.items-tbl .desc-input { width:100%; border:none; outline:none; font-size:12px; color:#6b7280; background:transparent; margin-top:4px; padding:2px 0; }

/* ── Item Searchable Dropdown ── */
.item-dd { position:relative; }
.item-dd-input {
    width:100%;
    border:1px solid #e8eaed;
    border-radius:6px;
    padding:7px 10px;
    font-size:13px;
    color:#1a1f36;
    background:#fff;
    outline:none;
    box-sizing:border-box;
}
.item-dd-input:focus { border-color:#5065e8; box-shadow:0 0 0 2px rgba(80,101,232,.1); }

/* Item dropdown must stay outside table/card overflow */
.item-dd-menu {
    display:none;
    position:fixed;
    background:#fff;
    border:1px solid #e8eaed;
    border-radius:8px;
    box-shadow:0 14px 35px rgba(0,0,0,.18);
    z-index:999999;
    width:320px;
    min-width:280px;
    max-width:420px;
    overflow:hidden;
}
#globalItemMenu { display:none !important; position:fixed !important; }
#globalItemMenu.open { display:block !important; }
.item-dd-menu.open { display:block; }

.item-dd-search-wrap {
    padding:8px 10px;
    border-bottom:1px solid #f1f3f9;
    background:#f9fafb;
}
.item-dd-search {
    width:100%;
    border:1px solid #e8eaed;
    border-radius:6px;
    padding:6px 10px;
    font-size:13px;
    outline:none;
    box-sizing:border-box;
}
.item-dd-search:focus { border-color:#5065e8; background:#fff; }
.item-dd-scroll { max-height:240px; overflow-y:auto; overscroll-behavior:contain; }
.item-dd-opt { padding:10px 14px; font-size:13px; color:#1a1f36; cursor:pointer; border-bottom:1px solid #f8f9fc; text-align:left; }
.item-dd-opt:hover { background:#f0f4ff; }
.item-dd-opt .opt-sub { font-size:11px; color:#9ca3af; margin-top:2px; }
.item-dd-opt.add-new { color:#5065e8; font-weight:600; border-top:1px solid #f1f3f9; background:#fafbff; }
.item-dd-opt.add-new:hover { background:#eff2ff; }

/* ── Totals ── */
.totals-wrap { padding:16px 20px; }
.totals-row { display:flex; justify-content:space-between; padding:7px 0; font-size:13px; }
.totals-row.grand { border-top:2px solid #e8eaed; margin-top:4px; padding-top:12px; }
.totals-row.grand .tl { font-size:15px; font-weight:700; }
.totals-row.grand .tr { font-size:18px; font-weight:700; color:#1a1f36; }
.tl { color:#6b7280; }
.tr { font-weight:500; }

/* ── Action Buttons ── */
.inv-actions { display:flex; gap:10px; margin-bottom:40px; flex-wrap:wrap; }
.btn-draft { background:#fff; color:#374151; border:1.5px solid #d1d5db; font-weight:600; }
.btn-draft:hover { background:#f9fafb; }
.btn-send { background:#5065e8; color:#fff; border:1.5px solid #5065e8; font-weight:600; }
.btn-send:hover { background:#3d52d5; }

.num-input {
    width:100%;
    border:1px solid #e8eaed;
    border-radius:6px;
    padding:6px 8px;
    font-size:13px;
    text-align:right;
    background:#fff;
    outline:none;
    box-sizing:border-box;
}
.num-input:focus { border-color:#5065e8; }
#itemsTableWrap { overflow-x:auto; }
body.item-menu-open #globalItemMenu { pointer-events:auto; }
body.customer-menu-open #globalItemMenu { display:none !important; }
#globalItemMenu:not(.open) { display:none !important; left:-99999px !important; top:-99999px !important; }

/* FINAL DROPDOWN FIX: customer dropdown and item dropdown are fully separate */
#itemsTableWrap, .card, .card-body { overflow: visible !important; }
.item-dd { position: relative !important; }
.item-dd .item-dd-menu {
    display: none;
    position: absolute !important;
    top: calc(100% + 6px) !important;
    left: 0 !important;
    right: auto !important;
    width: 360px !important;
    max-width: 90vw !important;
    background: #fff;
    border: 1px solid #e8eaed;
    border-radius: 8px;
    box-shadow: 0 14px 35px rgba(0,0,0,.18);
    z-index: 999999 !important;
    overflow: hidden;
}
.item-dd .item-dd-menu.open { display: block !important; }
.item-dd .item-dd-scroll { max-height: 240px !important; overflow-y: auto !important; }
#globalItemMenu { display: none !important; }

</style>

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
    <h4 style="font-size:18px; font-weight:700;">
        <i class="bi bi-file-earmark-text" style="color:#5065e8; margin-right:6px;"></i>
        <?= $invoice ? 'Edit Invoice' : 'New Invoice' ?>
    </h4>
    <a href="<?= base_url('invoice/invoices') ?>" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Cancel</a>
</div>

<form method="POST" action="<?= base_url($invoice ? 'invoice/invoices/update/' . $invoice['id'] : 'invoice/invoices/store') ?>" id="invoiceForm">
<?= csrf_field() ?>
<input type="hidden" name="inv_action" id="inv_action" value="draft">

<div class="card" style="margin-bottom:16px;">
    <div class="card-body">
        <div style="display:grid; grid-template-columns:1fr auto; gap:20px; align-items:start;">
            <div>
                <label style="font-size:13px; font-weight:600; color:#e53e3e; display:block; margin-bottom:6px;">Customer Name *</label>
                <div class="cust-dropdown" id="custDropdown">
                    <div class="cd-input-wrap">
                        <input type="text" id="custDisplay" placeholder="Select or search a customer..." readonly onclick="toggleCustList()" autocomplete="off">
                        <i class="bi bi-chevron-down cd-arrow" onclick="toggleCustList()"></i>
                    </div>
                    <input type="hidden" name="customer_id" id="custId" required>
                    <div class="cd-list" id="custList">
                        <div class="cd-search">
                            <input type="text" id="custSearch" placeholder="Search customers..." oninput="filterCustomers(this.value)">
                        </div>
                        <div id="custOptions">
                            <?php foreach ($customers as $cu): ?>
                            <div class="cd-item"
                                data-id="<?= $cu['id'] ?>"
                                data-name="<?= esc($cu['display_name']) ?>"
                                data-email="<?= esc($cu['email']) ?>"
                                data-phone="<?= esc($cu['work_phone']) ?>"
                                data-gstin="<?= esc($cu['gstin']) ?>"
                                data-address1="<?= esc($cu['b_address1']) ?>"
                                data-address2="<?= esc($cu['b_address2']) ?>"
                                data-city="<?= esc($cu['b_city']) ?>"
                                data-state="<?= esc($cu['b_state']) ?>"
                                data-zip="<?= esc($cu['b_zip']) ?>"
                                data-country="<?= esc($cu['b_country']) ?>"
                                data-terms="<?= esc($cu['payment_terms']) ?>"
                                onclick="selectCustomer(this)">
                                <div class="cd-avatar"><?= strtoupper(substr($cu['display_name'], 0, 1)) ?></div>
                                <div class="cd-item-info">
                                    <div class="cd-item-name"><?= esc($cu['display_name']) ?></div>
                                    <div class="cd-item-sub">
                                        <?php if ($cu['email']): ?><i class="bi bi-envelope" style="font-size:10px"></i> <?= esc($cu['email']) ?><?php endif; ?>
                                        <?php if ($cu['work_phone']): ?>&nbsp;|&nbsp;<i class="bi bi-telephone" style="font-size:10px"></i> <?= esc($cu['work_phone']) ?><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="cd-new" onclick="window.open('<?= base_url('invoice/customers/create') ?>','_blank')">
                            <i class="bi bi-plus-circle"></i> Add New Customer
                        </div>
                    </div>
                </div>
                <div class="cust-panel" id="custPanel">
                    <div>
                        <div class="cp-name" id="cp_name"></div>
                        <div class="cp-line" id="cp_addr"></div>
                        <div class="cp-line" id="cp_contact"></div>
                        <span class="cp-badge" id="cp_gstin" style="display:none"></span>
                    </div>
                    <div style="text-align:right; white-space:nowrap;">
                        <div class="cp-line" id="cp_currency"></div>
                        <div class="cp-line" id="cp_terms"></div>
                    </div>
                </div>
            </div>

            <div style="min-width:220px;">
                <div style="margin-bottom:12px;">
                    <label style="font-size:13px; font-weight:600; color:#e53e3e; display:block; margin-bottom:6px;">Invoice# *</label>
                    <?php
                        $db   = \Config\Database::connect();
                        $last = $db->query("SELECT invoice_number FROM invoices WHERE invoice_number LIKE 'INV-%' ORDER BY id DESC LIMIT 1")->getRowArray();
                        if ($last && !empty($last['invoice_number'])) {
                            $lastNum = preg_replace('/[^0-9]/', '', $last['invoice_number']);
                            $num = !empty($lastNum) ? ((int)$lastNum + 1) : 1;
                        } else { $num = 1; }
                        $nextNum = $invoice ? $invoice['invoice_number'] : 'INV-' . str_pad($num, 6, '0', STR_PAD_LEFT);
                    ?>
                    <input type="text" name="invoice_number" class="form-control"
                        value="<?= esc($nextNum) ?>"
                        style="border:1.5px solid #5065e8;border-radius:6px;padding:9px 12px;font-size:14px;font-weight:600;color:#1a1f36;background:#fff;min-width:180px;">
                </div>
                <div>
                    <label style="font-size:13px; font-weight:500; color:#374151; display:block; margin-bottom:6px;">Reference#</label>
                    <input type="text" name="reference" class="form-control" value="<?= esc($invoice['reference'] ?? '') ?>" placeholder="Optional">
                </div>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr 1fr 2fr; gap:16px; margin-top:18px;">
            <div>
                <label style="font-size:13px; font-weight:600; color:#e53e3e; display:block; margin-bottom:6px;">Invoice Date *</label>
                <input type="date" name="invoice_date" id="invoiceDate" class="form-control" value="<?= $invoice['invoice_date'] ?? date('Y-m-d') ?>" onchange="updateDueDate()" required>
            </div>
            <div>
                <label style="font-size:13px; font-weight:500; color:#374151; display:block; margin-bottom:6px;">Terms</label>
                <select name="payment_terms" id="payTerms" class="form-select" onchange="updateDueDate()">
                    <?php foreach (['due_on_receipt'=>'Due on Receipt','net15'=>'Net 15','net30'=>'Net 30','net45'=>'Net 45','net60'=>'Net 60'] as $k=>$v): ?>
                        <option value="<?= $k ?>" <?= ($invoice['payment_terms'] ?? 'net30') == $k ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="font-size:13px; font-weight:500; color:#374151; display:block; margin-bottom:6px;">Due Date</label>
                <input type="date" name="due_date" id="dueDate" class="form-control" value="<?= $invoice['due_date'] ?? date('Y-m-d', strtotime('+30 days')) ?>">
            </div>
            <div>
                <label style="font-size:13px; font-weight:500; color:#374151; display:block; margin-bottom:6px;">Subject</label>
                <input type="text" name="subject" class="form-control" value="<?= esc($invoice['subject'] ?? '') ?>" placeholder="e.g. Invoice for services rendered">
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom:16px;">
    <div style="padding:14px 20px; border-bottom:1px solid #e8eaed; display:flex; align-items:center; justify-content:space-between;">
        <span style="font-size:14px; font-weight:600;">Item Table</span>
        <button type="button" onclick="addRow()" class="btn btn-outline btn-sm"><i class="bi bi-plus"></i> Add Line</button>
    </div>

    <div id="itemsTableWrap">
        <table class="items-tbl" id="itemsTable">
            <thead>
                <tr>
                    <th style="min-width:240px;">Item Details</th>
                    <th style="width:110px;">HSN/SAC</th>
                    <th style="width:80px; text-align:center;">Qty</th>
                    <th style="width:75px;">Unit</th>
                    <th style="width:110px; text-align:right;">Rate (₹)</th>
                    <th style="width:90px; text-align:center;">Disc %</th>
                    <th style="width:145px;">Tax</th>
                    <th style="width:110px; text-align:right;">Amount (₹)</th>
                    <th style="width:36px;"></th>
                </tr>
            </thead>
            <tbody id="liTbody"></tbody>
        </table>
    </div>

    <div style="padding:10px 20px; border-top:1px solid #f5f5f5;">
        <button type="button" onclick="addRow()" style="background:none;border:none;color:#5065e8;font-size:13px;font-weight:500;cursor:pointer;display:flex;align-items:center;gap:4px;">
            <i class="bi bi-plus-circle"></i> Add Another Line
        </button>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 340px; gap:20px; margin-bottom:20px;">
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Customer Notes</label>
                <textarea name="customer_notes" class="form-control" rows="3"><?= esc($invoice['customer_notes'] ?? 'Thank you for your business.') ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Terms & Conditions</label>
                <textarea name="terms" class="form-control" rows="3"><?= esc($invoice['terms'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="totals-wrap">
            <div class="totals-row"><span class="tl">Sub Total</span><span class="tr" id="dSub">₹0.00</span></div>
            <div class="totals-row" style="align-items:center;">
                <span class="tl">Discount</span>
                <span class="tr" style="display:flex;align-items:center;gap:4px;">
                    <select id="discType" name="discount_type" onchange="calcAll()" style="border:1px solid #d1d5db;border-radius:4px;padding:3px 6px;font-size:12px;">
                        <option value="percent" <?= ($invoice['discount_type'] ?? 'percent') == 'percent' ? 'selected' : '' ?>>%</option>
                        <option value="fixed"   <?= ($invoice['discount_type'] ?? '') == 'fixed' ? 'selected' : '' ?>>Fixed</option>
                    </select>
                    <input type="number" id="discVal" name="discount_value" oninput="calcAll()" value="<?= $invoice['discount_value'] ?? 0 ?>" min="0" step="0.01" style="width:70px;border:1px solid #d1d5db;border-radius:4px;padding:3px 8px;font-size:12px;">
                </span>
            </div>
            <div class="totals-row"><span class="tl">- Discount</span><span class="tr" style="color:#dc2626;" id="dDisc">-₹0.00</span></div>
            <div class="totals-row"><span class="tl">Tax Total</span><span class="tr" id="dTax">₹0.00</span></div>
            <div class="totals-row grand"><span class="tl">Total (₹)</span><span class="tr" id="dTotal">₹0.00</span></div>
        </div>
        <input type="hidden" name="sub_total"       id="sub_total">
        <input type="hidden" name="tax_total"       id="tax_total">
        <input type="hidden" name="discount_amount" id="disc_amount">
        <input type="hidden" name="total"           id="total_hidden">
    </div>
</div>

<div class="inv-actions">
    <button type="button" onclick="submitInvoice('draft')" class="btn btn-draft">
        <i class="bi bi-file-earmark"></i> Save as Draft
    </button>
    <button type="button" onclick="submitInvoice('sent')" class="btn btn-send">
        <i class="bi bi-check-circle"></i> Save and Send
    </button>
    <a href="<?= base_url('invoice/invoices') ?>" class="btn btn-outline">Cancel</a>
</div>
</form>

<div id="globalItemMenu" class="item-dd-menu" style="display:none;left:-99999px;top:-99999px;">
    <div class="item-dd-search-wrap">
        <input type="text" class="item-dd-search" id="globalItemSearch" placeholder="Filter list or type new..." autocomplete="off">
    </div>
    <div class="item-dd-scroll" id="globalItemScroll"></div>
</div>

<script>
let _items  = <?= json_encode($items) ?>;
const _taxes = <?= json_encode($taxes) ?>;

// Existing invoice rows for Edit Invoice page.
// Controller me variable ka naam alag ho sakta hai, isliye common names check kiye gaye hain.
const _existingInvoiceItems = <?= json_encode($invoice_items ?? $invoiceItems ?? $line_items ?? $invoiceLineItems ?? []) ?>;

function submitInvoice(action) {
    document.getElementById('inv_action').value = action;
    document.getElementById('invoiceForm').submit();
}

/* ── Customer Mechanics ── */
function toggleCustList() {
    // Customer dropdown open hote hi item dropdown force close
    closeGlobalMenu(true);

    const list = document.getElementById('custList');
    const willOpen = !list.classList.contains('open');
    list.classList.toggle('open', willOpen);
    document.body.classList.toggle('customer-menu-open', willOpen);

    if (willOpen) {
        setTimeout(() => {
            const s = document.getElementById('custSearch');
            if (s) s.focus();
        }, 0);
    }
}
function filterCustomers(q) {
    q = q.toLowerCase();
    document.querySelectorAll('#custOptions .cd-item').forEach(el => {
        const match = el.dataset.name.toLowerCase().includes(q) || (el.dataset.email||'').toLowerCase().includes(q);
        el.style.display = match ? '' : 'none';
    });
}
function selectCustomer(el) {
    document.getElementById('custId').value      = el.dataset.id;
    document.getElementById('custDisplay').value = el.dataset.name;
    document.getElementById('custList').classList.remove('open');
    document.body.classList.remove('customer-menu-open');
    document.querySelectorAll('#custOptions .cd-item').forEach(i => i.classList.remove('selected'));
    el.classList.add('selected');

    const addr = [el.dataset.address1,el.dataset.address2,el.dataset.city,el.dataset.state,el.dataset.zip,el.dataset.country].filter(Boolean).join(', ');
    document.getElementById('cp_name').textContent    = el.dataset.name;
    document.getElementById('cp_addr').textContent    = addr || 'No address on file';
    document.getElementById('cp_contact').textContent = [el.dataset.email,el.dataset.phone].filter(Boolean).join('  |  ');

    const gEl = document.getElementById('cp_gstin');
    if (el.dataset.gstin) { gEl.textContent='GSTIN: '+el.dataset.gstin; gEl.style.display='inline-block'; }
    else gEl.style.display='none';

    const tmap = {due_on_receipt:'Due on Receipt',net15:'Net 15',net30:'Net 30',net45:'Net 45',net60:'Net 60'};
    document.getElementById('cp_currency').textContent = el.dataset.currency?'Currency: '+el.dataset.currency:'';
    document.getElementById('cp_terms').textContent    = el.dataset.terms?'Terms: '+(tmap[el.dataset.terms]||el.dataset.terms):'';
    document.getElementById('custPanel').classList.add('show');
    if (el.dataset.terms) { document.getElementById('payTerms').value=el.dataset.terms; updateDueDate(); }
}
function updateDueDate() {
    const terms=document.getElementById('payTerms').value;
    const invDate=document.getElementById('invoiceDate').value;
    if (!invDate) return;
    const days={due_on_receipt:0,net15:15,net30:30,net45:45,net60:60};
    const d=new Date(invDate); d.setDate(d.getDate()+(days[terms]||0));
    document.getElementById('dueDate').value=d.toISOString().split('T')[0];
}

/* ── Item Dropdown Position Fix ── */
let _activeRowIdx = null;
let _itemSearch   = '';

function buildItemMenu() {
    const scroll = document.getElementById('globalItemScroll');
    const q = _itemSearch.toLowerCase().trim();
    scroll.innerHTML = '';

    const filtered = q ? _items.filter(i => i.name.toLowerCase().includes(q) || (i.hsn_sac||'').toLowerCase().includes(q)) : _items;

    filtered.forEach(item => {
        const div = document.createElement('div');
        div.className = 'item-dd-opt';
        div.innerHTML = `
            <div style="font-weight:600;">${item.name}</div>
            <div class="opt-sub">
                ${item.hsn_sac ? 'HSN/SAC: '+item.hsn_sac+' &nbsp;|&nbsp;' : ''}
                ₹${parseFloat(item.selling_price||0).toFixed(2)}
            </div>`;
        // Click handle using onmousedown to trigger before blur event takes place
        div.onmousedown = (e) => { 
            e.preventDefault(); 
            pickItem(item); 
        };
        scroll.appendChild(div);
    });

    // Manual item add option if search string is present
    if (q) {
        const addDiv = document.createElement('div');
        addDiv.className = 'item-dd-opt add-new';
        addDiv.innerHTML = `<i class="bi bi-plus-circle"></i> Use "<b>${q}</b>" as Manual Entry`;
        addDiv.onmousedown = (e) => { 
            e.preventDefault(); 
            useCustomItem(q); 
        };
        scroll.appendChild(addDiv);
    }
}

function openGlobalMenu(ri, inputEl) {
    // Sirf item table ke item-name input par hi item dropdown open hoga.
    // Customer/search/other input par kabhi bhi item dropdown open nahi hoga.
    if (!inputEl || !inputEl.classList.contains('iname-display') || !inputEl.closest('#itemsTable')) {
        closeGlobalMenu(true);
        return;
    }

    const custList = document.getElementById('custList');
    if (custList) custList.classList.remove('open');
    document.body.classList.remove('customer-menu-open');

    _activeRowIdx = ri;
    _itemSearch = inputEl.value || '';

    const searchBox = document.getElementById('globalItemSearch');
    searchBox.value = _itemSearch;
    buildItemMenu();

    const menu = document.getElementById('globalItemMenu');

    // Important: keep menu directly under <body>, not inside table/card.
    // This prevents clipping by #itemsTableWrap overflow and keeps dropdown outside.
    if (menu.parentElement !== document.body) document.body.appendChild(menu);

    menu.classList.add('open');
    document.body.classList.add('item-menu-open');
    positionGlobalItemMenu(inputEl);

    setTimeout(() => {
        searchBox.focus();
        searchBox.select();
    }, 0);
}

function positionGlobalItemMenu(inputEl) {
    const menu = document.getElementById('globalItemMenu');
    if (!inputEl || !menu.classList.contains('open')) return;

    const rect = inputEl.getBoundingClientRect();
    const gap = 6;
    const viewportW = window.innerWidth || document.documentElement.clientWidth;
    const viewportH = window.innerHeight || document.documentElement.clientHeight;

    let width = Math.max(rect.width, 320);
    width = Math.min(width, viewportW - 24);

    let left = rect.left;
    if (left + width > viewportW - 12) left = viewportW - width - 12;
    if (left < 12) left = 12;

    // Open below if space is available, otherwise open above.
    const estimatedHeight = Math.min(330, viewportH - 24);
    let top = rect.bottom + gap;
    if (top + estimatedHeight > viewportH && rect.top > estimatedHeight) {
        top = rect.top - estimatedHeight - gap;
    }
    if (top < 12) top = 12;

    menu.style.left = left + 'px';
    menu.style.top = top + 'px';
    menu.style.width = width + 'px';

    const scroll = document.getElementById('globalItemScroll');
    const searchWrap = menu.querySelector('.item-dd-search-wrap');
    const available = viewportH - top - 24 - (searchWrap ? searchWrap.offsetHeight : 50);
    scroll.style.maxHeight = Math.max(140, Math.min(260, available)) + 'px';
}

function handleInputSearch(ri, val) {
    _itemSearch = val;
    const tr = document.getElementById('row-' + ri);
    if(tr) {
        tr.querySelector('.iname').value = val;
    }
    buildItemMenu();
}

function closeGlobalMenu(force = false) {
    const menu = document.getElementById('globalItemMenu');
    if (menu) {
        menu.classList.remove('open');
        menu.style.left = '-99999px';
        menu.style.top = '-99999px';
    }
    document.body.classList.remove('item-menu-open');
    if (force) _activeRowIdx = null;
}

function pickItem(item) {
    if (_activeRowIdx === null) return;
    const tr = document.getElementById('row-' + _activeRowIdx);
    if (!tr) return;

    tr.querySelector('.iid').value           = item.id;
    tr.querySelector('.iname').value         = item.name;
    tr.querySelector('.iname-display').value = item.name;
    tr.querySelector('.rate').value          = parseFloat(item.selling_price||0).toFixed(2);
    tr.querySelector('.hsn-input').value     = item.hsn_sac || '';

    const desc = tr.querySelector('.desc-input');
    if (desc && item.description) desc.value = item.description;

    if (item.tax_id) {
        const ts = tr.querySelector('.tselect');
        for (let o of ts.options) {
            if (o.value == item.tax_id) {
                o.selected = true;
                tr.querySelector('.trate').value = o.dataset.rate || 0;
                break;
            }
        }
    }
    closeGlobalMenu();
    calcAll();
}

function useCustomItem(name) {
    if (_activeRowIdx === null) return;
    const tr = document.getElementById('row-' + _activeRowIdx);
    if (!tr) return;

    tr.querySelector('.iid').value           = '';
    tr.querySelector('.iname').value         = name;
    tr.querySelector('.iname-display').value = name;

    closeGlobalMenu();
    tr.querySelector('.rate').focus();
}

/* ── Add New Items Logic ── */
function escapeHtml(v) {
    return String(v ?? '').replace(/[&<>'"]/g, function(c) {
        return {'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[c];
    });
}
let rowIdx = 0;
function addRow(d = {}) {
    rowIdx++;
    const ri = rowIdx;
    const tbody = document.getElementById('liTbody');
    const taxOpts = _taxes.map(t => `<option value="${t.id}" data-rate="${t.rate}" ${d.tax_id == t.id ? 'selected' : ''}>${t.name} (${t.rate}%)</option>`).join('');
    const units = ['pcs','kg','g','ltr','ml','m','hrs','days','box','dozen'];

    const tr = document.createElement('tr');
    tr.id = 'row-' + ri;
    tr.innerHTML = `
    <td style="padding:8px 10px; min-width:240px;">
        <div class="item-dd" id="idrop-${ri}">
            <input type="text"
                   class="item-dd-input iname-display"
                   id="idinput-${ri}"
                   placeholder="Type or search item..."
                   value="${escapeHtml(d.item_name ?? '')}"
                   autocomplete="off"
                   onclick="openGlobalMenu(${ri}, this)"
                   oninput="handleInputSearch(${ri}, this.value)"
            >
        </div>
        <input type="hidden" name="item_id[]"   class="iid"   value="${escapeHtml(d.item_id ?? '')}">
        <input type="hidden" name="item_name[]" class="iname" value="${escapeHtml(d.item_name ?? '')}">
        <input type="text"   name="item_desc[]" class="desc-input" placeholder="Description (optional)" value="${escapeHtml(d.description ?? '')}">
    </td>
    <td style="padding:8px 6px; width:110px;">
        <input type="text" name="hsn_sac[]" class="hsn-input num-input" value="${escapeHtml(d.hsn_sac ?? '')}" placeholder="HSN/SAC" style="text-align:left;">
    </td>
    <td style="padding:8px 6px; width:80px; text-align:center;">
        <input type="number" name="qty[]" class="num-input calc qty" value="${d.qty ?? 1}" min="0.01" step="0.01" style="text-align:center;">
    </td>
    <td style="padding:8px 6px; width:75px;">
        <select name="unit[]" style="border:1px solid #e8eaed;border-radius:6px;padding:6px 4px;font-size:12px;background:#fff;width:100%;outline:none;">
            ${units.map(u => `<option ${(d.unit??'pcs')==u?'selected':''}>${u}</option>`).join('')}
        </select>
    </td>
    <td style="padding:8px 6px; width:110px;">
        <input type="number" name="rate[]" class="num-input calc rate" value="${d.rate ?? 0}" min="0" step="0.01">
    </td>
    <td style="padding:8px 6px; width:90px; text-align:center;">
        <div style="display:flex;align-items:center;justify-content:center;gap:2px;">
            <input type="number" name="item_discount[]" class="num-input calc disc" value="${d.discount ?? 0}" min="0" max="100" style="width:55px;text-align:center;">
            <span style="color:#6b7280;font-size:12px;">%</span>
        </div>
    </td>
    <td style="padding:8px 6px; width:145px;">
        <select name="tax_id[]" class="tselect" style="width:100%;border:1px solid #e8eaed;border-radius:6px;padding:6px 8px;font-size:12px;background:#fff;outline:none;">
            <option value="" data-rate="0">No Tax</option>${taxOpts}
        </select>
        <input type="hidden" name="tax_rate[]" class="trate" value="${d.tax_rate ?? 0}">
    </td>
    <td style="padding:8px 6px; width:110px; text-align:right; font-weight:600; font-size:13px;" class="ramt">
        ₹${parseFloat(d.amount ?? 0).toFixed(2)}
    </td>
    <td style="padding:8px 6px; width:36px; text-align:center;">
        <span onclick="document.getElementById('row-${ri}').remove();calcAll()" style="cursor:pointer;color:#dc2626;font-size:22px;line-height:1;font-weight:300;">×</span>
    </td>`;

    tbody.appendChild(tr);
    tr.querySelectorAll('.calc').forEach(el => el.addEventListener('input', calcAll));
    tr.querySelector('.tselect').onchange = function() {
        tr.querySelector('.trate').value = this.options[this.selectedIndex].dataset.rate || 0;
        calcAll();
    };

    // Edit mode: agar tax_rate DB se blank hai, selected tax option se rate fill kar do.
    const taxSelect = tr.querySelector('.tselect');
    const taxRateInput = tr.querySelector('.trate');
    if (taxSelect && taxRateInput && (taxRateInput.value === '' || taxRateInput.value === null)) {
        taxRateInput.value = taxSelect.options[taxSelect.selectedIndex]?.dataset.rate || 0;
    }
    
    tr.querySelector('.iname-display').addEventListener('change', function() {
        tr.querySelector('.iname').value = this.value;
    });

    calcAll();
}

/* ── Global Document Click Handlers (Safe replacement for standard blur glitch) ── */
// Capture phase: pehle hi decide karenge kaunsa dropdown open rehna hai.
document.addEventListener('mousedown', function(e) {
    if (e.target.closest('#custDropdown')) {
        closeGlobalMenu(true);
        return;
    }

    if (e.target.closest('.iname-display') && e.target.closest('#itemsTable')) {
        const custList = document.getElementById('custList');
        if (custList) custList.classList.remove('open');
        document.body.classList.remove('customer-menu-open');
        return;
    }

    if (!e.target.closest('#globalItemMenu') && !e.target.closest('.item-dd')) {
        closeGlobalMenu(true);
    }
    if (!e.target.closest('#custDropdown')) {
        const custList = document.getElementById('custList');
        if (custList) custList.classList.remove('open');
        document.body.classList.remove('customer-menu-open');
    }
}, true);

document.addEventListener('click', function(e) {
    if (!e.target.closest('#custDropdown')) {
        const custList = document.getElementById('custList');
        if (custList) custList.classList.remove('open');
        document.body.classList.remove('customer-menu-open');
    }
    if (!e.target.closest('.item-dd') && !e.target.closest('#globalItemMenu')) {
        closeGlobalMenu(true);
    }
});


window.addEventListener('scroll', function() {
    if (_activeRowIdx !== null) {
        const inp = document.getElementById('idinput-' + _activeRowIdx);
        if (inp) positionGlobalItemMenu(inp);
    }
}, true);

window.addEventListener('resize', function() {
    if (_activeRowIdx !== null) {
        const inp = document.getElementById('idinput-' + _activeRowIdx);
        if (inp) positionGlobalItemMenu(inp);
    }
});

// Setup dynamic global search inside dropdown container
document.getElementById('globalItemSearch').addEventListener('input', function() {
    _itemSearch = this.value;
    if(_activeRowIdx !== null) {
        const inp = document.getElementById('idinput-' + _activeRowIdx);
        if(inp) {
            inp.value = this.value;
            const tr = document.getElementById('row-' + _activeRowIdx);
            if(tr) tr.querySelector('.iname').value = this.value;
        }
    }
    buildItemMenu();
});

/* ── Standard Calculations ── */
function calcAll() {
    let sub = 0, tax = 0;
    document.querySelectorAll('#liTbody tr').forEach(tr => {
        const qty  = parseFloat(tr.querySelector('.qty')?.value)   || 0;
        const rate = parseFloat(tr.querySelector('.rate')?.value)  || 0;
        const disc = parseFloat(tr.querySelector('.disc')?.value)  || 0;
        const tr_  = parseFloat(tr.querySelector('.trate')?.value) || 0;

        const base = qty * rate * (1 - disc / 100);
        const ta   = base * tr_ / 100;
        const amt  = base + ta;

        const el = tr.querySelector('.ramt');
        if (el) el.textContent = '₹' + amt.toFixed(2);
        sub += base; tax += ta;
    });

    let dVal = parseFloat(document.getElementById('discVal').value) || 0;
    let dType = document.getElementById('discType').value;
    let dAmt = dType === 'percent' ? (sub * dVal / 100) : dVal;
    let total = sub - dAmt + tax;

    document.getElementById('dSub').textContent = '₹' + sub.toFixed(2);
    document.getElementById('dDisc').textContent = '-₹' + dAmt.toFixed(2);
    document.getElementById('dTax').textContent = '₹' + tax.toFixed(2);
    document.getElementById('dTotal').textContent = '₹' + total.toFixed(2);

    document.getElementById('sub_total').value = sub.toFixed(2);
    document.getElementById('tax_total').value = tax.toFixed(2);
    document.getElementById('disc_amount').value = dAmt.toFixed(2);
    document.getElementById('total_hidden').value = total.toFixed(2);
}

function normalizeEditRow(r) {
    r = r || {};

    const itemId = r.item_id ?? r.product_id ?? r.id ?? '';
    const masterItem = itemId ? _items.find(i => String(i.id) === String(itemId)) : null;

    return {
        item_id: itemId,
        item_name: r.item_name ?? r.name ?? r.item ?? r.product_name ?? (masterItem ? masterItem.name : ''),
        description: r.description ?? r.item_desc ?? r.desc ?? (masterItem ? (masterItem.description || '') : ''),
        hsn_sac: r.hsn_sac ?? r.hsn ?? r.sac ?? (masterItem ? (masterItem.hsn_sac || '') : ''),
        qty: r.qty ?? r.quantity ?? 1,
        unit: r.unit ?? (masterItem ? (masterItem.unit || 'pcs') : 'pcs'),
        rate: r.rate ?? r.selling_price ?? r.price ?? r.unit_price ?? (masterItem ? (masterItem.selling_price || 0) : 0),
        discount: r.discount ?? r.item_discount ?? r.discount_percent ?? 0,
        tax_id: r.tax_id ?? (masterItem ? (masterItem.tax_id || '') : ''),
        tax_rate: r.tax_rate ?? r.rate_percent ?? r.gst_rate ?? '',
        amount: r.amount ?? r.line_total ?? r.total ?? 0
    };
}

// Edit page par saved/current item rows load karo; New page par single empty row.
if (Array.isArray(_existingInvoiceItems) && _existingInvoiceItems.length > 0) {
    _existingInvoiceItems.forEach(row => addRow(normalizeEditRow(row)));
} else {
    addRow();
}
calcAll();
</script>
<script>
/* ===== FINAL PATCH: Local item dropdown, no global/fixed dropdown ===== */
(function(){
    // Remove old global item dropdown permanently
    const oldGlobal = document.getElementById('globalItemMenu');
    if (oldGlobal) oldGlobal.remove();

    window.closeAllItemMenus = function(){
        document.querySelectorAll('.row-item-menu.open').forEach(m => m.classList.remove('open'));
    };

    window.closeGlobalMenu = function(){ closeAllItemMenus(); };

    window.toggleCustList = function() {
        closeAllItemMenus();
        const list = document.getElementById('custList');
        list.classList.toggle('open');
        if (list.classList.contains('open')) document.getElementById('custSearch').focus();
    };

    window.buildRowItemMenu = function(ri, q){
        const menu = document.getElementById('rowItemMenu-' + ri);
        if (!menu) return;
        const search = menu.querySelector('.row-item-search');
        const scroll = menu.querySelector('.row-item-scroll');
        q = (q || '').toLowerCase().trim();
        if (search && document.activeElement !== search) search.value = q;
        scroll.innerHTML = '';

        const filtered = q
            ? _items.filter(i => (i.name || '').toLowerCase().includes(q) || (i.hsn_sac || '').toLowerCase().includes(q))
            : _items;

        filtered.forEach(item => {
            const div = document.createElement('div');
            div.className = 'item-dd-opt';
            div.innerHTML = `<div style="font-weight:600;">${escapeHtml(item.name || '')}</div>
                <div class="opt-sub">${item.hsn_sac ? 'HSN/SAC: '+escapeHtml(item.hsn_sac)+' &nbsp;|&nbsp;' : ''}₹${parseFloat(item.selling_price || 0).toFixed(2)}</div>`;
            div.onmousedown = function(e){ e.preventDefault(); pickItemForRow(ri, item); };
            scroll.appendChild(div);
        });

        if (q) {
            const addDiv = document.createElement('div');
            addDiv.className = 'item-dd-opt add-new';
            addDiv.innerHTML = `<i class="bi bi-plus-circle"></i> Use "<b>${escapeHtml(q)}</b>" as Manual Entry`;
            addDiv.onmousedown = function(e){ e.preventDefault(); useCustomItemForRow(ri, q); };
            scroll.appendChild(addDiv);
        }
    };

    window.openRowItemMenu = function(ri, inputEl){
        if (!inputEl || !inputEl.classList.contains('iname-display')) return;
        const custList = document.getElementById('custList');
        if (custList) custList.classList.remove('open');
        closeAllItemMenus();
        buildRowItemMenu(ri, inputEl.value || '');
        const menu = document.getElementById('rowItemMenu-' + ri);
        if (menu) {
            menu.classList.add('open');
            const search = menu.querySelector('.row-item-search');
            setTimeout(() => { if (search) { search.focus(); search.select(); } }, 0);
        }
    };

    window.handleRowItemInput = function(ri, val){
        const tr = document.getElementById('row-' + ri);
        if (tr) tr.querySelector('.iname').value = val;
        buildRowItemMenu(ri, val);
        const menu = document.getElementById('rowItemMenu-' + ri);
        if (menu) menu.classList.add('open');
    };

    window.pickItemForRow = function(ri, item){
        const tr = document.getElementById('row-' + ri);
        if (!tr) return;
        tr.querySelector('.iid').value = item.id || '';
        tr.querySelector('.iname').value = item.name || '';
        tr.querySelector('.iname-display').value = item.name || '';
        tr.querySelector('.rate').value = parseFloat(item.selling_price || 0).toFixed(2);
        tr.querySelector('.hsn-input').value = item.hsn_sac || '';
        const desc = tr.querySelector('.desc-input');
        if (desc) desc.value = item.description || '';
        if (item.tax_id) {
            const ts = tr.querySelector('.tselect');
            for (let o of ts.options) {
                if (o.value == item.tax_id) {
                    o.selected = true;
                    tr.querySelector('.trate').value = o.dataset.rate || 0;
                    break;
                }
            }
        }
        closeAllItemMenus();
        calcAll();
    };

    window.useCustomItemForRow = function(ri, name){
        const tr = document.getElementById('row-' + ri);
        if (!tr) return;
        tr.querySelector('.iid').value = '';
        tr.querySelector('.iname').value = name;
        tr.querySelector('.iname-display').value = name;
        closeAllItemMenus();
        tr.querySelector('.rate').focus();
    };

    window.addRow = function(d = {}) {
        rowIdx++;
        const ri = rowIdx;
        const tbody = document.getElementById('liTbody');
        const taxOpts = _taxes.map(t => `<option value="${t.id}" data-rate="${t.rate}" ${d.tax_id == t.id ? 'selected' : ''}>${escapeHtml(t.name)} (${t.rate}%)</option>`).join('');
        const units = ['pcs','kg','g','ltr','ml','m','hrs','days','box','dozen'];

        const tr = document.createElement('tr');
        tr.id = 'row-' + ri;
        tr.innerHTML = `
        <td style="padding:8px 10px; min-width:240px; position:relative;">
            <div class="item-dd" id="idrop-${ri}">
                <input type="text" class="item-dd-input iname-display" id="idinput-${ri}" placeholder="Type or search item..." value="${escapeHtml(d.item_name ?? '')}" autocomplete="off">
                <div class="item-dd-menu row-item-menu" id="rowItemMenu-${ri}">
                    <div class="item-dd-search-wrap"><input type="text" class="item-dd-search row-item-search" placeholder="Filter list or type new..." autocomplete="off"></div>
                    <div class="item-dd-scroll row-item-scroll"></div>
                </div>
            </div>
            <input type="hidden" name="item_id[]" class="iid" value="${escapeHtml(d.item_id ?? '')}">
            <input type="hidden" name="item_name[]" class="iname" value="${escapeHtml(d.item_name ?? '')}">
            <input type="text" name="item_desc[]" class="desc-input" placeholder="Description (optional)" value="${escapeHtml(d.description ?? '')}">
        </td>
        <td style="padding:8px 6px; width:110px;"><input type="text" name="hsn_sac[]" class="hsn-input num-input" value="${escapeHtml(d.hsn_sac ?? '')}" placeholder="HSN/SAC" style="text-align:left;"></td>
        <td style="padding:8px 6px; width:80px; text-align:center;"><input type="number" name="qty[]" class="num-input calc qty" value="${d.qty ?? 1}" min="0.01" step="0.01" style="text-align:center;"></td>
        <td style="padding:8px 6px; width:75px;"><select name="unit[]" style="border:1px solid #e8eaed;border-radius:6px;padding:6px 4px;font-size:12px;background:#fff;width:100%;outline:none;">${units.map(u => `<option ${(d.unit??'pcs')==u?'selected':''}>${u}</option>`).join('')}</select></td>
        <td style="padding:8px 6px; width:110px;"><input type="number" name="rate[]" class="num-input calc rate" value="${d.rate ?? 0}" min="0" step="0.01"></td>
        <td style="padding:8px 6px; width:90px; text-align:center;"><div style="display:flex;align-items:center;justify-content:center;gap:2px;"><input type="number" name="item_discount[]" class="num-input calc disc" value="${d.discount ?? 0}" min="0" max="100" style="width:55px;text-align:center;"><span style="color:#6b7280;font-size:12px;">%</span></div></td>
        <td style="padding:8px 6px; width:145px;"><select name="tax_id[]" class="tselect" style="width:100%;border:1px solid #e8eaed;border-radius:6px;padding:6px 8px;font-size:12px;background:#fff;outline:none;"><option value="" data-rate="0">No Tax</option>${taxOpts}</select><input type="hidden" name="tax_rate[]" class="trate" value="${d.tax_rate ?? 0}"></td>
        <td style="padding:8px 6px; width:110px; text-align:right; font-weight:600; font-size:13px;" class="ramt">₹${parseFloat(d.amount ?? 0).toFixed(2)}</td>
        <td style="padding:8px 6px; width:36px; text-align:center;"><span onclick="document.getElementById('row-${ri}').remove();calcAll()" style="cursor:pointer;color:#dc2626;font-size:22px;line-height:1;font-weight:300;">×</span></td>`;

        tbody.appendChild(tr);
        tr.querySelectorAll('.calc').forEach(el => el.addEventListener('input', calcAll));
        tr.querySelector('.tselect').onchange = function(){ tr.querySelector('.trate').value = this.options[this.selectedIndex].dataset.rate || 0; calcAll(); };
        const nameInput = tr.querySelector('.iname-display');
        const searchInput = tr.querySelector('.row-item-search');
        nameInput.addEventListener('focus', function(){ openRowItemMenu(ri, this); });
        nameInput.addEventListener('click', function(){ openRowItemMenu(ri, this); });
        nameInput.addEventListener('input', function(){ handleRowItemInput(ri, this.value); });
        nameInput.addEventListener('change', function(){ tr.querySelector('.iname').value = this.value; });
        searchInput.addEventListener('input', function(){ nameInput.value = this.value; handleRowItemInput(ri, this.value); });

        const taxSelect = tr.querySelector('.tselect');
        const taxRateInput = tr.querySelector('.trate');
        if (taxSelect && taxRateInput && (taxRateInput.value === '' || taxRateInput.value === null)) {
            taxRateInput.value = taxSelect.options[taxSelect.selectedIndex]?.dataset.rate || 0;
        }
        calcAll();
    };

    document.addEventListener('mousedown', function(e){
        if (e.target.closest('#custDropdown')) { closeAllItemMenus(); return; }
        if (!e.target.closest('.item-dd')) closeAllItemMenus();
    }, true);

    // Rebuild rows using corrected dropdown after old code has loaded
    const tbody = document.getElementById('liTbody');
    if (tbody) tbody.innerHTML = '';
    rowIdx = 0;
    if (Array.isArray(_existingInvoiceItems) && _existingInvoiceItems.length > 0) {
        _existingInvoiceItems.forEach(row => addRow(normalizeEditRow(row)));
    } else {
        addRow();
    }
    calcAll();
})();
</script>

<?= $this->endSection() ?>