
<?= $this->extend('layout/main') ?>
<?php $page_title = $invoice ? 'Edit Invoice' : 'New Invoice'; ?>
<?= $this->section('content') ?>

<style>
/* ── Searchable Customer Dropdown ── */
.cust-dropdown { position:relative; }
.cust-dropdown .cd-input-wrap { display:flex; align-items:center; border:1.5px solid #5065e8; border-radius:6px; background:#fff; padding:0 10px; gap:8px; }
.cust-dropdown .cd-input-wrap input { border:none; outline:none; font-size:14px; padding:9px 0; flex:1; color:#1a1f36; }
.cust-dropdown .cd-input-wrap .cd-arrow { color:#5065e8; cursor:pointer; font-size:18px; }
.cust-dropdown .cd-list { display:none; position:absolute; top:calc(100% + 4px); left:0; right:0; background:#fff; border:1px solid #e8eaed; border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,.12); z-index:999; max-height:280px; overflow-y:auto; }
.cust-dropdown .cd-list.open { display:block; }
.cust-dropdown .cd-search { padding:10px 12px; border-bottom:1px solid #f1f3f9; }
.cust-dropdown .cd-search input { width:100%; border:1px solid #e8eaed; border-radius:6px; padding:7px 10px; font-size:13px; outline:none; }
.cust-dropdown .cd-item { display:flex; align-items:center; gap:10px; padding:10px 14px; cursor:pointer; transition:.15s; }
.cust-dropdown .cd-item:hover { background:#f0f4ff; }
.cust-dropdown .cd-item.selected { background:#eff2ff; }
.cust-dropdown .cd-avatar { width:34px; height:34px; border-radius:50%; background:#5065e8; color:#fff; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700; flex-shrink:0; }
.cust-dropdown .cd-item-info { flex:1; min-width:0; }
.cust-dropdown .cd-item-name { font-size:13px; font-weight:600; color:#1a1f36; }
.cust-dropdown .cd-item-sub { font-size:11px; color:#6b7280; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.cust-dropdown .cd-new { display:flex; align-items:center; gap:8px; padding:10px 14px; color:#5065e8; font-size:13px; font-weight:500; cursor:pointer; border-top:1px solid #f1f3f9; }
.cust-dropdown .cd-new:hover { background:#f0f4ff; }

/* ── Customer Info Panel ── */
.cust-panel { background:#f8f9fc; border:1px solid #e8eaed; border-radius:8px; padding:14px 18px; margin-top:12px; display:none; }
.cust-panel.show { display:flex; gap:20px; justify-content:space-between; }
.cust-panel .cp-name { font-size:14px; font-weight:700; color:#1a1f36; margin-bottom:4px; }
.cust-panel .cp-line { font-size:12px; color:#6b7280; line-height:1.7; }
.cust-panel .cp-badge { display:inline-block; background:#e0e7ff; color:#4338ca; font-size:11px; font-weight:600; padding:2px 8px; border-radius:20px; margin-top:4px; }

/* ── Items Table ── */
.items-tbl { width:100%; border-collapse:collapse; }
.items-tbl th { background:#f8f9fc; padding:9px 12px; font-size:11px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.04em; border-bottom:1px solid #e8eaed; text-align:left; }
.items-tbl td { padding:8px 10px; border-bottom:1px solid #f5f5f5; vertical-align:middle; }
.items-tbl tbody tr:last-child td { border-bottom:none; }
.items-tbl input.num { width:100%; border:none; outline:none; font-size:13px; text-align:right; background:transparent; padding:2px 4px; }
.items-tbl input.num:focus { background:#f0f4ff; border-radius:4px; }
.items-tbl .row-amt { font-weight:600; font-size:13px; text-align:right; color:#1a1f36; }
.items-tbl .desc-input { width:100%; border:none; outline:none; font-size:12px; color:#6b7280; background:transparent; margin-top:2px; padding:2px 0; }

/* ── Item Searchable Dropdown ── */
.item-dropdown { position:relative; }
.item-dropdown .id-input { width:100%; border:1px solid #e8eaed; border-radius:6px; padding:6px 10px; font-size:13px; color:#1a1f36; background:#fff; outline:none; cursor:pointer; }
.item-dropdown .id-input:focus { border-color:#5065e8; }
.item-dropdown .id-list { display:none; position:absolute; top:calc(100% + 2px); left:0; min-width:260px; background:#fff; border:1px solid #e8eaed; border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,.12); z-index:1000; max-height:220px; overflow-y:auto; }
.item-dropdown .id-list.open { display:block; }
.item-dropdown .id-opt { padding:9px 14px; font-size:13px; color:#1a1f36; cursor:pointer; }
.item-dropdown .id-opt:hover { background:#f0f4ff; }
.item-dropdown .id-opt .id-opt-sub { font-size:11px; color:#9ca3af; margin-top:1px; }

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

<!-- hidden field for action (draft/sent) -->
<input type="hidden" name="inv_action" id="inv_action" value="draft">

<!-- ── CUSTOMER + HEADER ── -->
<div class="card" style="margin-bottom:16px;">
    <div class="card-body">

        <div style="display:grid; grid-template-columns:1fr auto; gap:20px; align-items:start;">

            <!-- Customer Searchable Dropdown -->
            <div>
                <label style="font-size:13px; font-weight:600; color:#e53e3e; display:block; margin-bottom:6px;">Customer Name *</label>
                <div class="cust-dropdown" id="custDropdown">
                    <div class="cd-input-wrap">
                        <input type="text" id="custDisplay" placeholder="Select or search a customer..." readonly onclick="toggleCustList()" autocomplete="off">
                        <i class="bi bi-chevron-down cd-arrow" onclick="toggleCustList()"></i>
                    </div>
                    <input type="hidden" name="customer_id" id="custId" required>
                    <div class="cd-list" id="custList">
                        <div class="cd-search"><input type="text" id="custSearch" placeholder="Search customers..." oninput="filterCustomers(this.value)"></div>
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
                                        <?php if ($cu['work_phone']): ?> &nbsp;|&nbsp; <i class="bi bi-telephone" style="font-size:10px"></i> <?= esc($cu['work_phone']) ?><?php endif; ?>
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

                <!-- Customer Info Panel -->
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

            <!-- Invoice# + Ref -->
            <div style="min-width:220px;">
                <div style="margin-bottom:12px;">
                    <label style="font-size:13px; font-weight:600; color:#e53e3e; display:block; margin-bottom:6px;">Invoice# *</label>
                    <?php
                        $db   = \Config\Database::connect();
                        $last = $db->query("SELECT invoice_number FROM invoices WHERE invoice_number LIKE 'INV-%' ORDER BY id DESC LIMIT 1")->getRowArray();
                        if ($last && !empty($last['invoice_number'])) {
                            $lastNum = preg_replace('/[^0-9]/', '', $last['invoice_number']);
                            $num = !empty($lastNum) ? ((int)$lastNum + 1) : 1;
                        } else {
                            $num = 1;
                        }
                        $nextNum = $invoice ? $invoice['invoice_number'] : 'INV-' . str_pad($num, 6, '0', STR_PAD_LEFT);
                    ?>
                    <input type="text" name="invoice_number" class="form-control"
                        value="<?= esc($nextNum) ?>"
                        placeholder="Auto generate"
                        style="border:1.5px solid #5065e8;border-radius:6px;padding:9px 12px;font-size:14px;font-weight:600;color:#1a1f36;background:#fff;min-width:180px;">
                </div>
                <div>
                    <label style="font-size:13px; font-weight:500; color:#374151; display:block; margin-bottom:6px;">Reference#</label>
                    <input type="text" name="reference" class="form-control" value="<?= esc($invoice['reference'] ?? '') ?>" placeholder="Optional">
                </div>
            </div>
        </div>

        <!-- Row 2: Dates + Terms + Subject -->
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr 2fr; gap:16px; margin-top:18px;">
            <div>
                <label style="font-size:13px; font-weight:600; color:#e53e3e; display:block; margin-bottom:6px;">Invoice Date *</label>
                <input type="date" name="invoice_date" id="invoiceDate" class="form-control" value="<?= $invoice['invoice_date'] ?? date('Y-m-d') ?>" onchange="updateDueDate()" required>
            </div>
            <div>
                <label style="font-size:13px; font-weight:500; color:#374151; display:block; margin-bottom:6px;">Terms</label>
                <select name="payment_terms" id="payTerms" class="form-select" onchange="updateDueDate()">
                    <?php foreach (['due_on_receipt' => 'Due on Receipt', 'net15' => 'Net 15', 'net30' => 'Net 30', 'net45' => 'Net 45', 'net60' => 'Net 60'] as $k => $v): ?>
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

<!-- ── ITEM TABLE ── -->
<div class="card" style="margin-bottom:16px;">
    <div style="padding:14px 20px; border-bottom:1px solid #e8eaed; display:flex; align-items:center; justify-content:space-between;">
        <span style="font-size:14px; font-weight:600;">Item Table</span>

        <div style="display:flex;gap:8px;align-items:center;">
            <button type="button" onclick="openNewItemModalForTable()" class="btn btn-outline btn-sm">
                <i class="bi bi-plus-circle"></i> New Item
            </button>

            <button type="button" onclick="addRow()" class="btn btn-outline btn-sm">
                <i class="bi bi-plus"></i> Add Line
            </button>
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table class="items-tbl" id="itemsTable">
            <thead>
                <tr>
                    <th style="min-width:220px;">Item Details</th>
                    <th style="width:110px;">HSN/SAC</th>
                    <th style="width:80px; text-align:center;">Qty</th>
                    <th style="width:70px;">Unit</th>
                    <th style="width:110px; text-align:right;">Rate (₹)</th>
                    <th style="width:90px; text-align:center;">Disc %</th>
                    <th style="width:140px;">Tax</th>
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

<!-- ── NOTES + TOTALS ── -->
<div style="display:grid; grid-template-columns:1fr 340px; gap:20px; margin-bottom:20px;">
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Customer Notes</label>
                <textarea name="customer_notes" class="form-control" rows="3" placeholder="Will be displayed on the invoice"><?= esc($invoice['customer_notes'] ?? 'Thank you for your business.') ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Terms & Conditions</label>
                <textarea name="terms" class="form-control" rows="3" placeholder="e.g. Goods once sold cannot be returned"><?= esc($invoice['terms'] ?? '') ?></textarea>
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

<!-- ── ACTION BUTTONS ── -->
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
<div id="newItemModal"
     style="
        display:none;
        position:fixed;
        inset:0;
        background:rgba(15,23,42,.45);
        z-index:9999;
        align-items:center;
        justify-content:center;
        padding:20px;
     ">

    <div style="
            background:#fff;
            width:720px;
            max-width:100%;
            border-radius:14px;
            overflow:hidden;
            box-shadow:0 20px 45px rgba(0,0,0,.18);
         ">

        <!-- HEADER -->
        <div style="
                padding:18px 24px;
                border-bottom:1px solid #eef1f6;
                display:flex;
                align-items:center;
                justify-content:space-between;
             ">

            <div>
                <div style="
                        font-size:24px;
                        font-weight:700;
                        color:#111827;
                        margin-bottom:2px;
                     ">
                    New Item
                </div>

                <div style="
                        font-size:13px;
                        color:#6b7280;
                     ">
                    Create a new item for invoice
                </div>
            </div>

            <button type="button"
                    onclick="closeNewItemModal()"

                    style="
                        border:none;
                        background:#f3f4f6;
                        width:34px;
                        height:34px;
                        border-radius:8px;
                        cursor:pointer;
                        font-size:18px;
                     ">
                ×
            </button>
        </div>

        <!-- BODY -->
        <div style="padding:24px;">

            <!-- TOP GRID -->
            <div style="
                    display:grid;
                    grid-template-columns:1fr 1fr;
                    gap:18px;
                    margin-bottom:18px;
                 ">

                <!-- ITEM TYPE -->
                <div>
                    <label style="
                            font-size:13px;
                            font-weight:600;
                            color:#374151;
                            margin-bottom:7px;
                            display:block;
                         ">
                        Item Type
                    </label>

                    <select id="new_item_type"
                            class="form-control">

                        <option value="product">
                            Product
                        </option>

                        <option value="service">
                            Service
                        </option>

                    </select>
                </div>

                <!-- ITEM NAME -->
                <div>
                    <label style="
                            font-size:13px;
                            font-weight:600;
                            color:#374151;
                            margin-bottom:7px;
                            display:block;
                         ">
                        Item Name *
                    </label>

                    <input id="new_item_name"
                           class="form-control"
                           placeholder="Enter item name">
                </div>
            </div>

            <!-- SECOND GRID -->
            <div style="
                    display:grid;
                    grid-template-columns:1fr 1fr 1fr;
                    gap:18px;
                    margin-bottom:18px;
                 ">

                <!-- SKU -->
                <div>
                    <label style="
                            font-size:13px;
                            font-weight:600;
                            color:#374151;
                            margin-bottom:7px;
                            display:block;
                         ">
                        SKU
                    </label>

                    <input id="new_item_sku"
                           class="form-control"
                           placeholder="AUTO">
                </div>

                <!-- UNIT -->
                <div>
                    <label style="
                            font-size:13px;
                            font-weight:600;
                            color:#374151;
                            margin-bottom:7px;
                            display:block;
                         ">
                        Unit
                    </label>

                    <select id="new_item_unit"
                            class="form-control">

                        <option value="PCS">PCS</option>
                        <option value="KG">KG</option>
                        <option value="G">G</option>
                        <option value="LTR">LTR</option>
                        <option value="ML">ML</option>
                        <option value="M">M</option>
                        <option value="CM">CM</option>
                        <option value="HRS">HRS</option>
                        <option value="DAYS">DAYS</option>
                        <option value="BOX">BOX</option>
                        <option value="DOZEN">DOZEN</option>

                    </select>
                </div>

                <!-- TAX -->
                <div>
                    <label style="
                            font-size:13px;
                            font-weight:600;
                            color:#374151;
                            margin-bottom:7px;
                            display:block;
                         ">
                        Tax
                    </label>

                    <select id="new_item_tax_id"
                            class="form-control">

                        <option value="">
                            None
                        </option>

                        <?php foreach($taxes as $t): ?>

                            <option value="<?= $t['id'] ?>">
                                <?= esc($t['name']) ?>
                                (<?= esc($t['rate']) ?>%)
                            </option>

                        <?php endforeach; ?>

                    </select>
                </div>
            </div>

            <!-- THIRD GRID -->
            <div style="
                    display:grid;
                    grid-template-columns:1fr 1fr;
                    gap:18px;
                    margin-bottom:18px;
                 ">

                <!-- SELLING -->
                <div>
                    <label style="
                            font-size:13px;
                            font-weight:600;
                            color:#374151;
                            margin-bottom:7px;
                            display:block;
                         ">
                        Selling Price *
                    </label>

                    <input id="new_item_rate"
                           type="number"
                           class="form-control"
                           placeholder="0.00">
                </div>

                <!-- HSN -->
                <div>
                    <label style="
                            font-size:13px;
                            font-weight:600;
                            color:#374151;
                            margin-bottom:7px;
                            display:block;
                         ">
                        HSN / SAC
                    </label>

                    <input id="new_item_hsn_sac"
                           class="form-control"
                           placeholder="HSN/SAC Number">
                </div>
            </div>

            <!-- DESCRIPTION -->
            <div style="margin-bottom:20px;">

                <label style="
                        font-size:13px;
                        font-weight:600;
                        color:#374151;
                        margin-bottom:7px;
                        display:block;
                     ">
                    Description
                </label>

                <textarea id="new_item_desc"
                          class="form-control"

                          placeholder="Item description..."

                          style="
                            min-height:110px;
                            resize:vertical;
                          "></textarea>
            </div>

        </div>

        <!-- FOOTER -->
        <div style="
                padding:18px 24px;
                border-top:1px solid #eef1f6;
                display:flex;
                justify-content:flex-end;
                gap:10px;
                background:#fafbfc;
             ">

            <button type="button"
                    class="btn btn-outline"
                    onclick="closeNewItemModal()">

                Cancel

            </button>

            <button type="button"
                    class="btn btn-primary"
                    onclick="saveNewItemFromInvoice()">

                Save Item

            </button>

        </div>

    </div>
</div>
<script>
let _items = <?= json_encode($items) ?>;
const _taxes = <?= json_encode($taxes) ?>;

let currentNewItemRow = null;

/* ── Submit with correct action ── */
function submitInvoice(action) {
    document.getElementById('inv_action').value = action;
    document.getElementById('invoiceForm').submit();
}

/* ── Customer dropdown ── */
function toggleCustList() {
    const list = document.getElementById('custList');
    list.classList.toggle('open');
    if (list.classList.contains('open')) document.getElementById('custSearch').focus();
}

function filterCustomers(q) {
    q = q.toLowerCase();
    document.querySelectorAll('#custOptions .cd-item').forEach(el => {
        const name = (el.dataset.name || '').toLowerCase();
        const email = (el.dataset.email || '').toLowerCase();
        const match = name.includes(q) || email.includes(q);
        el.style.display = match ? '' : 'none';
    });
}

function selectCustomer(el) {
    document.getElementById('custId').value = el.dataset.id;
    document.getElementById('custDisplay').value = el.dataset.name;
    document.getElementById('custList').classList.remove('open');

    document.querySelectorAll('#custOptions .cd-item').forEach(i => i.classList.remove('selected'));
    el.classList.add('selected');

    const addr = [
        el.dataset.address1,
        el.dataset.address2,
        el.dataset.city,
        el.dataset.state,
        el.dataset.zip,
        el.dataset.country
    ].filter(Boolean).join(', ');

    document.getElementById('cp_name').textContent = el.dataset.name;
    document.getElementById('cp_addr').textContent = addr || 'No address on file';
    document.getElementById('cp_contact').textContent = [el.dataset.email, el.dataset.phone].filter(Boolean).join('  |  ');

    const gstinEl = document.getElementById('cp_gstin');

    if (el.dataset.gstin) {
        gstinEl.textContent = 'GSTIN: ' + el.dataset.gstin;
        gstinEl.style.display = 'inline-block';
    } else {
        gstinEl.style.display = 'none';
    }

    const termsMap = {
        due_on_receipt: 'Due on Receipt',
        net15: 'Net 15',
        net30: 'Net 30',
        net45: 'Net 45',
        net60: 'Net 60'
    };

    document.getElementById('cp_currency').textContent = el.dataset.currency ? 'Currency: ' + el.dataset.currency : '';
    document.getElementById('cp_terms').textContent = el.dataset.terms ? 'Terms: ' + (termsMap[el.dataset.terms] || el.dataset.terms) : '';
    document.getElementById('custPanel').classList.add('show');

    if (el.dataset.terms) {
        document.getElementById('payTerms').value = el.dataset.terms;
        updateDueDate();
    }
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('#custDropdown')) {
        document.getElementById('custList').classList.remove('open');
    }
});

function updateDueDate() {
    const terms = document.getElementById('payTerms').value;
    const invDate = document.getElementById('invoiceDate').value;

    if (!invDate) return;

    const days = {
        due_on_receipt: 0,
        net15: 15,
        net30: 30,
        net45: 45,
        net60: 60
    };

    const d = new Date(invDate);
    d.setDate(d.getDate() + (days[terms] || 0));
    document.getElementById('dueDate').value = d.toISOString().split('T')[0];
}

/* ── Item row with HSN/SAC + searchable item dropdown ── */
let rowIdx = 0;

function addRow(d = {}) {
    rowIdx++;

    const ri = rowIdx;
    const tbody = document.getElementById('liTbody');

    const taxOpts = _taxes.map(t =>
        `<option value="${t.id}" data-rate="${t.rate}" ${d.tax_id == t.id ? 'selected' : ''}>${t.name} (${t.rate}%)</option>`
    ).join('');

    const units = ['pcs','kg','g','ltr','ml','m','hrs','days','box','dozen'];

    const tr = document.createElement('tr');
    tr.id = 'row-' + ri;

    tr.innerHTML = `
    <td style="padding:10px 12px; min-width:220px;">
        <div class="item-dropdown" id="idrop-${ri}">
            <input type="text" class="id-input iname-display"
                placeholder="Type to search item..."
                value="${d.item_name ?? ''}"
                autocomplete="off"
                oninput="filterItems(${ri}, this.value)"
                onfocus="openItemList(${ri})"
            >

            <div class="id-list" id="ilist-${ri}">
                ${_items.map(i => `
                    <div class="id-opt"
                        data-id="${i.id}"
                        data-name="${i.name}"
                        data-price="${i.selling_price ?? 0}"
                        data-tax="${i.tax_id ?? ''}"
                        data-hsn="${i.hsn_sac ?? ''}"
                        onclick="selectItem(${ri}, this)">
                        ${i.name}
                        <div class="id-opt-sub">
                            ${i.hsn_sac ? 'HSN/SAC: ' + i.hsn_sac : ''}
                            ${i.selling_price ? ' ₹' + parseFloat(i.selling_price).toFixed(2) : ''}
                        </div>
                    </div>
                `).join('')}

                <div class="id-opt" onclick="openNewItemModal(${ri})" style="color:#2563eb;font-weight:700;">
                    + Add New Item
                </div>
            </div>
        </div>

        <input type="hidden" name="item_id[]" class="iid" value="${d.item_id ?? ''}">
        <input type="hidden" name="item_name[]" class="iname" value="${d.item_name ?? ''}">
        <input type="text" name="item_desc[]" class="desc-input" placeholder="Item description" value="${d.description ?? ''}">
    </td>

    <td style="padding:10px 8px;">
        <input type="text" name="hsn_sac[]" class="hsn-input"
            value="${d.hsn_sac ?? ''}"
            placeholder="HSN/SAC"
            style="width:100%;border:1px solid #e8eaed;border-radius:6px;padding:6px 8px;font-size:12px;background:#fff;outline:none;"
            onfocus="this.style.borderColor='#5065e8'"
            onblur="this.style.borderColor='#e8eaed'">
    </td>

    <td style="padding:10px 8px; text-align:center;">
        <input type="number" name="qty[]" class="num calc qty" value="${d.qty ?? 1}" min="0.01" step="0.01"
            style="width:65px;border:1px solid #e8eaed;border-radius:6px;padding:6px 6px;font-size:13px;text-align:center;background:#fff;outline:none;"
            onfocus="this.style.borderColor='#5065e8'"
            onblur="this.style.borderColor='#e8eaed'">
    </td>

    <td style="padding:10px 8px;">
        <select name="unit[]" style="border:1px solid #e8eaed;border-radius:6px;padding:6px 8px;font-size:12px;background:#fff;width:100%;outline:none;">
            ${units.map(u => `<option ${(d.unit ?? 'pcs') == u ? 'selected' : ''}>${u}</option>`).join('')}
        </select>
    </td>

    <td style="padding:10px 8px;">
        <input type="number" name="rate[]" class="num calc rate" value="${d.rate ?? 0}" min="0" step="0.01"
            style="width:100%;border:1px solid #e8eaed;border-radius:6px;padding:6px 8px;font-size:13px;text-align:right;background:#fff;outline:none;"
            onfocus="this.style.borderColor='#5065e8'"
            onblur="this.style.borderColor='#e8eaed'">
    </td>

    <td style="padding:10px 8px; text-align:center;">
        <div style="display:flex;align-items:center;justify-content:center;gap:3px;">
            <input type="number" name="item_discount[]" class="num calc disc" value="${d.discount ?? 0}" min="0" max="100"
                style="width:55px;border:1px solid #e8eaed;border-radius:6px;padding:6px 5px;font-size:13px;text-align:center;background:#fff;outline:none;"
                onfocus="this.style.borderColor='#5065e8'"
                onblur="this.style.borderColor='#e8eaed'">
            <span style="color:#6b7280;font-size:12px;">%</span>
        </div>
    </td>

    <td style="padding:10px 8px;">
        <select name="tax_id[]" class="tselect" style="width:100%;border:1px solid #e8eaed;border-radius:6px;padding:6px 8px;font-size:12px;background:#fff;outline:none;">
            <option value="" data-rate="0">No Tax</option>
            ${taxOpts}
        </select>

        <input type="hidden" name="tax_rate[]" class="trate" value="${d.tax_rate ?? 0}">
    </td>

    <td style="padding:10px 8px; text-align:right;" class="ramt">
        ₹${parseFloat(d.amount ?? 0).toFixed(2)}
    </td>

    <td style="padding:10px 8px; text-align:center;">
        <span onclick="document.getElementById('row-${ri}').remove();calcAll()"
            style="cursor:pointer;color:#dc2626;font-size:22px;line-height:1;font-weight:300;">×</span>
    </td>`;

    tbody.appendChild(tr);

    tr.querySelectorAll('.calc').forEach(el => el.addEventListener('input', calcAll));

    tr.querySelector('.tselect').onchange = function() {
        tr.querySelector('.trate').value = this.options[this.selectedIndex].dataset.rate || 0;
        calcAll();
    };

    calcAll();
}

/* ── Item search functions ── */
function openItemList(ri) {
    document.querySelectorAll('.id-list.open').forEach(l => l.classList.remove('open'));
    document.getElementById('ilist-' + ri).classList.add('open');
}

function filterItems(ri, q) {
    q = q.toLowerCase();
    const list = document.getElementById('ilist-' + ri);
    list.classList.add('open');

    list.querySelectorAll('.id-opt').forEach(opt => {
        const name = (opt.dataset.name || '').toLowerCase();

        if (!opt.dataset.name) {
            opt.style.display = '';
        } else {
            opt.style.display = name.includes(q) ? '' : 'none';
        }
    });
}

function selectItem(ri, opt) {
    const tr = document.getElementById('row-' + ri);

    tr.querySelector('.iid').value = opt.dataset.id;
    tr.querySelector('.iname').value = opt.dataset.name;
    tr.querySelector('.iname-display').value = opt.dataset.name;
    tr.querySelector('.rate').value = opt.dataset.price || 0;
    tr.querySelector('.hsn-input').value = opt.dataset.hsn || '';

    if (opt.dataset.tax) {
        const ts = tr.querySelector('.tselect');

        for (let o of ts.options) {
            if (o.value == opt.dataset.tax) {
                o.selected = true;
                tr.querySelector('.trate').value = o.dataset.rate || 0;
                break;
            }
        }
    }

    document.getElementById('ilist-' + ri).classList.remove('open');
    calcAll();
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.item-dropdown')) {
        document.querySelectorAll('.id-list.open').forEach(l => l.classList.remove('open'));
    }
});

/* ── New Item Modal Functions ── */
function openNewItemModalForTable() {
    addRow();
    currentNewItemRow = rowIdx;
    openNewItemModal(currentNewItemRow);
}

function openNewItemModal(ri) {
    currentNewItemRow = ri;

    document.getElementById('new_item_name').value = '';
    document.getElementById('new_item_hsn_sac').value = '';
    document.getElementById('new_item_rate').value = '';
    document.getElementById('new_item_unit').value = 'pcs';
    document.getElementById('new_item_tax_id').value = '';
    document.getElementById('new_item_desc').value = '';

    document.getElementById('newItemModal').style.display = 'flex';
}

function closeNewItemModal() {
    document.getElementById('newItemModal').style.display = 'none';
}

function saveNewItemFromInvoice() {

    const name = document.getElementById('new_item_name').value.trim();

    if (!name) {
        alert('Item name is required.');
        return;
    }

    const item = {
        id: '',
        name: name,
        hsn_sac: document.getElementById('new_item_hsn_sac').value || '',
        selling_price: document.getElementById('new_item_rate').value || 0,
        unit: document.getElementById('new_item_unit').value || 'pcs',
        tax_id: document.getElementById('new_item_tax_id').value || '',
        description: document.getElementById('new_item_desc').value || ''
    };

    const tr = document.getElementById('row-' + currentNewItemRow);

    if (tr) {
        tr.querySelector('.iid').value = '';
        tr.querySelector('.iname').value = item.name;
        tr.querySelector('.iname-display').value = item.name;
        tr.querySelector('.hsn-input').value = item.hsn_sac;
        tr.querySelector('.rate').value = item.selling_price;

        const descInput = tr.querySelector('.desc-input');
        if (descInput) {
            descInput.value = item.description;
        }

        const unitSelect = tr.querySelector('select[name="unit[]"]');
        if (unitSelect) {
            unitSelect.value = item.unit.toLowerCase();
        }

        const ts = tr.querySelector('.tselect');

        if (ts) {
            ts.value = item.tax_id;

            const selectedOption = ts.options[ts.selectedIndex];

            tr.querySelector('.trate').value = selectedOption
                ? (selectedOption.dataset.rate || 0)
                : 0;
        }
    }

    closeNewItemModal();
    calcAll();
}
/* ── Totals calculation ── */
function calcAll() {
    let sub = 0, tax = 0;

    document.querySelectorAll('#liTbody tr').forEach(tr => {
        const qty = parseFloat(tr.querySelector('.qty')?.value) || 0;
        const rate = parseFloat(tr.querySelector('.rate')?.value) || 0;
        const disc = parseFloat(tr.querySelector('.disc')?.value) || 0;
        const tr_ = parseFloat(tr.querySelector('.trate')?.value) || 0;

        const base = qty * rate * (1 - disc / 100);
        const ta = base * tr_ / 100;
        const amt = base + ta;

        const el = tr.querySelector('.ramt');
        if (el) el.textContent = '₹' + amt.toFixed(2);

        sub += base;
        tax += ta;
    });

    const dt = document.getElementById('discType')?.value;
    const dv = parseFloat(document.getElementById('discVal')?.value) || 0;
    const da = dt === 'percent' ? sub * dv / 100 : dv;
    const tot = sub - da + tax;

    document.getElementById('sub_total').value = sub.toFixed(2);
    document.getElementById('tax_total').value = tax.toFixed(2);
    document.getElementById('disc_amount').value = da.toFixed(2);
    document.getElementById('total_hidden').value = tot.toFixed(2);

    document.getElementById('dSub').textContent = '₹' + sub.toFixed(2);
    document.getElementById('dTax').textContent = '₹' + tax.toFixed(2);
    document.getElementById('dDisc').textContent = '-₹' + da.toFixed(2);
    document.getElementById('dTotal').textContent = '₹' + tot.toFixed(2);
}

document.getElementById('discType')?.addEventListener('change', calcAll);
document.getElementById('discVal')?.addEventListener('input', calcAll);

/* ── Load existing items edit mode ── */
const existingItems = <?= json_encode($invoice_items ?? []) ?>;

if (existingItems.length) {
    existingItems.forEach(i => addRow(i));
} else {
    addRow();
}

/* ── Pre-select customer edit mode ── */
<?php if ($invoice && $invoice['customer_id']): ?>
const preEl = document.querySelector('#custOptions .cd-item[data-id="<?= $invoice['customer_id'] ?>"]');

if (preEl) {
    selectCustomer(preEl);
}
<?php endif; ?>
</script>

<?= $this->endSection() ?>
