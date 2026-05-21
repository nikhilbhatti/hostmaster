<?= $this->extend('layout/main') ?>
<?php $page_title = $quote ? 'Edit Quote' : 'New Quote'; ?>
<?= $this->section('content') ?>

<div class="section-header">
    <h4><?= $quote ? 'Edit Quote' : 'New Quote' ?></h4>
    <a href="<?= base_url('invoice/quotes') ?>" class="btn btn-outline">
        <i class="bi bi-arrow-left"></i> Cancel
    </a>
</div>

<form method="POST" action="<?= base_url('invoice/quotes/' . ($quote ? 'update/'.$quote['id'] : 'store')) ?>">
<?= csrf_field() ?>

<div class="card" style="margin-bottom:16px">
    <div class="card-body">

        <div class="row">
            <div class="col-2">
                <label class="form-label">Customer Name *</label>

                <select name="customer_id" id="customer_id" class="form-select" required onchange="fillCustomerDetails(this)">
                    <option value="">-- Select Customer --</option>

                    <?php foreach($customers as $cu): ?>
                        <option value="<?= $cu['id'] ?>"
                            data-email="<?= esc($cu['email'] ?? '') ?>"
                            data-phone="<?= esc($cu['phone'] ?? $cu['mobile'] ?? '') ?>"
                            data-company="<?= esc($cu['company_name'] ?? $cu['company'] ?? '') ?>"
                            <?= (($quote['customer_id'] ?? '') == $cu['id']) ? 'selected' : '' ?>>

                            <?= esc($cu['display_name']) ?>

                        </option>
                    <?php endforeach; ?>

                </select>
            </div>

            <div class="col">
                <label class="form-label">Reference#</label>
                <input type="text" name="reference" class="form-control" value="<?= esc($quote['reference'] ?? '') ?>">
            </div>
        </div>

        <div id="customerDetailsBox" style="display:none;margin-top:16px;background:#f8fafc;border:1px solid #e5e7eb;border-radius:6px;padding:14px;">
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;">
                <div>
                    <div style="font-size:12px;color:#6b7280;">Email</div>
                    <div id="cust_email" style="font-size:13px;font-weight:600;">--</div>
                </div>

                <div>
                    <div style="font-size:12px;color:#6b7280;">Phone</div>
                    <div id="cust_phone" style="font-size:13px;font-weight:600;">--</div>
                </div>

                <div>
                    <div style="font-size:12px;color:#6b7280;">Company</div>
                    <div id="cust_company" style="font-size:13px;font-weight:600;">--</div>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top:16px">
            <div class="col">
                <label class="form-label">Quote Date</label>
                <input type="date" name="quote_date" class="form-control" value="<?= $quote['quote_date'] ?? date('Y-m-d') ?>" required>
            </div>

            <div class="col">
                <label class="form-label">Expiry Date</label>
                <input type="date" name="expiry_date" class="form-control" value="<?= $quote['expiry_date'] ?? date('Y-m-d', strtotime('+30 days')) ?>">
            </div>

            <div class="col-2">
                <label class="form-label">Subject</label>
                <input type="text" name="subject" class="form-control" value="<?= esc($quote['subject'] ?? '') ?>" placeholder="e.g. Quote for services">
            </div>
        </div>

    </div>
</div>

<div class="card" style="margin-bottom:16px">
    <div class="card-body" style="padding:0">

        <div style="padding:14px 20px;border-bottom:1px solid #e8eaed;display:flex;align-items:center;justify-content:space-between">
            <span style="font-size:14px;font-weight:600">Line Items</span>
            <button type="button" onclick="addRow()" class="btn btn-outline btn-sm">
                <i class="bi bi-plus"></i> Add Line
            </button>
        </div>

        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="background:#f8f9fc">
                        <th style="padding:10px 14px;font-size:11px;font-weight:600;color:#6b7280;text-align:left;border-bottom:1px solid #e8eaed">ITEM DETAILS</th>
                        <th style="padding:10px 8px;font-size:11px;font-weight:600;color:#6b7280;text-align:right;border-bottom:1px solid #e8eaed;width:70px">QTY</th>
                        <th style="padding:10px 8px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #e8eaed;width:80px">UNIT</th>
                        <th style="padding:10px 8px;font-size:11px;font-weight:600;color:#6b7280;text-align:right;border-bottom:1px solid #e8eaed;width:100px">RATE</th>
                        <th style="padding:10px 8px;font-size:11px;font-weight:600;color:#6b7280;text-align:right;border-bottom:1px solid #e8eaed;width:70px">DISC%</th>
                        <th style="padding:10px 8px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #e8eaed;width:140px">TAX</th>
                        <th style="padding:10px 8px;font-size:11px;font-weight:600;color:#6b7280;text-align:right;border-bottom:1px solid #e8eaed;width:100px">AMOUNT</th>
                        <th style="width:36px;border-bottom:1px solid #e8eaed"></th>
                    </tr>
                </thead>

                <tbody id="liTbody"></tbody>
            </table>
        </div>

    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:20px">

    <div class="card">
        <div class="card-body">

            <div class="form-group">
                <label class="form-label">Customer Notes</label>
                <textarea name="customer_notes" class="form-control" rows="3" placeholder="Notes to customer..."><?= esc($quote['customer_notes'] ?? 'Thank you for your business.') ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Terms & Conditions</label>
                <textarea name="terms" class="form-control" rows="3" placeholder="Terms..."><?= esc($quote['terms'] ?? '') ?></textarea>
            </div>

        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <table class="totals-table">
                <tr>
                    <td style="color:#6b7280">Sub Total</td>
                    <td style="text-align:right" id="dSub">₹0.00</td>
                </tr>

                <tr>
                    <td style="color:#6b7280">
                        Discount

                        <select id="discType" name="discount_type" style="border:1px solid #d1d5db;border-radius:4px;padding:2px 4px;font-size:12px;margin-left:4px">
                            <option value="percent" <?= (($quote['discount_type'] ?? 'percent') == 'percent') ? 'selected' : '' ?>>%</option>
                            <option value="fixed" <?= (($quote['discount_type'] ?? '') == 'fixed') ? 'selected' : '' ?>>Fixed</option>
                        </select>

                        <input type="number" id="discVal" name="discount_value" value="<?= $quote['discount_value'] ?? 0 ?>" min="0" step="0.01" style="width:60px;border:1px solid #d1d5db;border-radius:4px;padding:2px 6px;font-size:12px;margin-left:4px">
                    </td>

                    <td style="text-align:right" id="dDisc">-₹0.00</td>
                </tr>

                <tr>
                    <td style="color:#6b7280">Tax Total</td>
                    <td style="text-align:right" id="dTax">₹0.00</td>
                </tr>

                <tr class="total-row">
                    <td style="padding-top:12px;font-weight:700">Total (₹)</td>
                    <td style="text-align:right;font-weight:700;font-size:17px;padding-top:12px" id="dTotal">₹0.00</td>
                </tr>
            </table>

            <input type="hidden" name="sub_total" id="sub_total">
            <input type="hidden" name="tax_total" id="tax_total">
            <input type="hidden" name="discount_amount" id="disc_amount">
            <input type="hidden" name="total" id="total_hidden">

        </div>
    </div>

</div>

<div style="margin-top:16px;display:flex;gap:8px">
    <button type="submit" class="btn btn-primary">
        <?= $quote ? 'Update Quote' : 'Save Quote' ?>
    </button>

    <a href="<?= base_url('invoice/quotes') ?>" class="btn btn-outline">
        Cancel
    </a>
</div>

</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script>
const _items = <?= json_encode($items ?? []) ?>;
const _taxes = <?= json_encode($taxes ?? []) ?>;
let rowIdx = 0;

function fillCustomerDetails(select) {
    const option = select.options[select.selectedIndex];

    if (!option || !option.value) {
        document.getElementById('customerDetailsBox').style.display = 'none';
        return;
    }

    document.getElementById('cust_email').innerText = option.dataset.email || 'N/A';
    document.getElementById('cust_phone').innerText = option.dataset.phone || 'N/A';
    document.getElementById('cust_company').innerText = option.dataset.company || 'N/A';
    document.getElementById('customerDetailsBox').style.display = 'block';
}

function addRow(d = {}) {
    rowIdx++;

    const tbody = document.getElementById('liTbody');
    const ri = rowIdx;

    const taxOpts = _taxes.map(t =>
        `<option value="${t.id}" data-rate="${t.rate}" ${d.tax_id == t.id ? 'selected' : ''}>${t.name} (${t.rate}%)</option>`
    ).join('');

    const itemOpts = _items.map(i =>
        `<option value="${i.id}" data-price="${i.selling_price}" data-tax="${i.tax_id ?? ''}" ${d.item_id == i.id ? 'selected' : ''}>${i.name}</option>`
    ).join('');

    const tr = document.createElement('tr');
    tr.id = 'row-' + ri;

    tr.innerHTML = `
        <td style="min-width:200px">
            <select name="item_id[]" class="iselect" style="width:100%;border:none;outline:none;font-size:13px;padding:4px;background:transparent">
                <option value="">-- Select Item --</option>${itemOpts}
            </select>

            <input type="hidden" name="item_name[]" class="iname" value="${d.item_name ?? ''}">

            <input type="text" name="item_desc[]" style="width:100%;border:none;outline:none;font-size:12px;color:#6b7280;padding:2px 4px;background:transparent;margin-top:2px" placeholder="Description" value="${d.description ?? ''}">
        </td>

        <td style="width:70px">
            <input type="number" name="qty[]" class="calc qty" value="${d.qty ?? 1}" min="0.01" step="0.01" style="width:100%;border:none;outline:none;font-size:13px;text-align:right;background:transparent;padding:4px">
        </td>

        <td style="width:80px">
            <select name="unit[]" style="width:100%;border:none;outline:none;font-size:12px;background:transparent;padding:4px">
                ${['pcs','kg','hrs','box','ltr','m','days'].map(u => `<option ${(d.unit ?? 'pcs') == u ? 'selected' : ''}>${u}</option>`).join('')}
            </select>
        </td>

        <td style="width:100px">
            <input type="number" name="rate[]" class="calc rate" value="${d.rate ?? 0}" min="0" step="0.01" style="width:100%;border:none;outline:none;font-size:13px;text-align:right;background:transparent;padding:4px">
        </td>

        <td style="width:70px">
            <input type="number" name="item_discount[]" class="calc disc" value="${d.discount ?? 0}" min="0" max="100" style="width:100%;border:none;outline:none;font-size:13px;text-align:right;background:transparent;padding:4px">
        </td>

        <td style="width:140px">
            <select name="tax_id[]" class="tselect" style="width:100%;border:none;outline:none;font-size:12px;background:transparent;padding:4px">
                <option value="" data-rate="0">No Tax</option>${taxOpts}
            </select>

            <input type="hidden" name="tax_rate[]" class="trate" value="${d.tax_rate ?? 0}">
        </td>

        <td style="width:100px;text-align:right;font-weight:600" class="ramt">
            ₹${parseFloat(d.amount ?? 0).toFixed(2)}
        </td>

        <td style="width:36px;text-align:center">
            <span onclick="document.getElementById('row-${ri}').remove();calcAll()" style="cursor:pointer;color:#dc2626;font-size:16px">×</span>
        </td>
    `;

    tbody.appendChild(tr);

    tr.querySelector('.iselect').onchange = function() {
        const o = this.options[this.selectedIndex];

        tr.querySelector('.iname').value = o.text !== '-- Select Item --' ? o.text : '';
        tr.querySelector('.rate').value = o.dataset.price || 0;

        if (o.dataset.tax) {
            const ts = tr.querySelector('.tselect');

            for (let op of ts.options) {
                if (op.value == o.dataset.tax) {
                    op.selected = true;
                    tr.querySelector('.trate').value = op.dataset.rate || 0;
                    break;
                }
            }
        }

        calcAll();
    };

    tr.querySelectorAll('.calc').forEach(el => el.addEventListener('input', calcAll));

    tr.querySelector('.tselect').onchange = function() {
        const o = this.options[this.selectedIndex];
        tr.querySelector('.trate').value = o.dataset.rate || 0;
        calcAll();
    };

    if (d.item_name && !d.item_id) {
        tr.querySelector('.iname').value = d.item_name;
    }

    calcAll();
}

function calcAll() {
    let sub = 0;
    let tax = 0;

    document.querySelectorAll('#liTbody tr').forEach(tr => {
        const qty = parseFloat(tr.querySelector('.qty')?.value) || 0;
        const rate = parseFloat(tr.querySelector('.rate')?.value) || 0;
        const disc = parseFloat(tr.querySelector('.disc')?.value) || 0;
        const tr_ = parseFloat(tr.querySelector('.trate')?.value) || 0;

        const base = qty * rate * (1 - disc / 100);
        const ta = base * tr_ / 100;
        const amt = base + ta;

        const el = tr.querySelector('.ramt');

        if (el) {
            el.textContent = '₹' + amt.toFixed(2);
        }

        sub += base;
        tax += ta;
    });

    const dt = document.getElementById('discType')?.value;
    const dv = parseFloat(document.getElementById('discVal')?.value) || 0;
    const da = dt === 'percent' ? sub * dv / 100 : dv;
    const total = sub - da + tax;

    document.getElementById('sub_total').value = sub.toFixed(2);
    document.getElementById('tax_total').value = tax.toFixed(2);
    document.getElementById('disc_amount').value = da.toFixed(2);
    document.getElementById('total_hidden').value = total.toFixed(2);

    document.getElementById('dSub').textContent = '₹' + sub.toFixed(2);
    document.getElementById('dTax').textContent = '₹' + tax.toFixed(2);
    document.getElementById('dDisc').textContent = '-₹' + da.toFixed(2);
    document.getElementById('dTotal').textContent = '₹' + total.toFixed(2);
}

document.getElementById('discType')?.addEventListener('change', calcAll);
document.getElementById('discVal')?.addEventListener('input', calcAll);

const existing = <?= json_encode($quote_items ?? []) ?>;

if (existing.length) {
    existing.forEach(i => addRow(i));
} else {
    addRow();
}

document.addEventListener('DOMContentLoaded', function() {
    const customerSelect = document.getElementById('customer_id');

    if (customerSelect && customerSelect.value) {
        fillCustomerDetails(customerSelect);
    }
});
</script>

<?= $this->endSection() ?>