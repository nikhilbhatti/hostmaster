<?= $this->extend('layout/main') ?>
<?php $page_title = $item ? 'Edit Item' : 'New Item'; ?>
<?= $this->section('content') ?>

<style>
.item-page-wrap{max-width:980px;margin:0 auto 70px auto;}
.item-topbar{display:flex;align-items:center;justify-content:space-between;gap:14px;margin-bottom:20px;}
.item-topbar h4{font-size:24px;font-weight:800;color:#20242a;margin:0;letter-spacing:-.4px;}
.item-topbar .sub{font-size:13px;color:#7b8494;margin-top:4px;}
.item-card{background:#fff;border:1px solid #e7eaf0;border-radius:18px;box-shadow:0 8px 26px rgba(15,23,42,.05);overflow:hidden;}
.item-card-head{padding:18px 22px;border-bottom:1px solid #eef0f5;display:flex;align-items:center;justify-content:space-between;background:linear-gradient(180deg,#fff,#fbfcff);}
.item-card-head .title{font-size:16px;font-weight:800;color:#252a31;display:flex;align-items:center;gap:8px;}
.item-card-body{padding:24px;}
.form-grid{display:grid;grid-template-columns:repeat(12,1fr);gap:18px;align-items:end;}
.fg-12{grid-column:span 12}.fg-6{grid-column:span 6}.fg-4{grid-column:span 4}.fg-3{grid-column:span 3}
.clean-label{display:block;font-size:12px;font-weight:800;color:#5f6877;text-transform:uppercase;letter-spacing:.7px;margin-bottom:8px;}
.req{color:#ef4444;}
.clean-input,.clean-select,.clean-textarea{width:100%;border:1px solid #dde3ec;border-radius:12px;background:#f8fafc;color:#1f2937;font-size:14px;padding:13px 14px;outline:none;transition:.2s;box-sizing:border-box;}
.clean-select{appearance:auto;}
.clean-input:focus,.clean-select:focus,.clean-textarea:focus{background:#fff;border-color:#6366f1;box-shadow:0 0 0 4px rgba(99,102,241,.10);}
.clean-textarea{min-height:105px;resize:vertical;line-height:1.6;}
.help-text{font-size:11px;color:#8a94a6;margin-top:6px;}
.input-icon{position:relative;}
.input-icon .prefix{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#6b7280;font-weight:700;}
.input-icon .has-prefix{padding-left:34px;}
.form-actions{display:flex;gap:10px;align-items:center;justify-content:flex-end;margin-top:24px;padding-top:20px;border-top:1px solid #eef0f5;}
.btn-main{background:#6366f1;color:#fff;border:1px solid #6366f1;border-radius:12px;padding:12px 24px;font-weight:800;text-decoration:none;display:inline-flex;align-items:center;gap:8px;cursor:pointer;transition:.2s;}
.btn-main:hover{background:#4f46e5;color:#fff;transform:translateY(-1px);}
.btn-lightx{background:#fff;color:#374151;border:1px solid #d8dee8;border-radius:12px;padding:12px 20px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:8px;}
.btn-lightx:hover{background:#f8fafc;color:#111827;}
@media(max-width:768px){.item-page-wrap{max-width:100%;}.fg-6,.fg-4,.fg-3{grid-column:span 12}.item-card-body{padding:18px}.item-topbar{align-items:flex-start}.form-actions{justify-content:stretch;flex-direction:column}.btn-main,.btn-lightx{width:100%;justify-content:center}}
</style>

<div class="item-page-wrap">
  <div class="item-topbar">
    <div>
      <h4><?= $item ? 'Edit Item' : 'New Item' ?></h4>
      <div class="sub">Add product/service details for invoice billing.</div>
    </div>
    <a href="<?= base_url('invoice/items') ?>" class="btn-lightx">
      <i class="bi bi-arrow-left"></i> Cancel
    </a>
  </div>

  <div class="item-card">
    <div class="item-card-head">
      <div class="title"><i class="bi bi-box-seam" style="color:#6366f1"></i> Item Information</div>
      <span style="font-size:12px;color:#8a94a6;font-weight:700;">Fields marked <span class="req">*</span> are required</span>
    </div>

    <div class="item-card-body">
      <form method="POST" action="<?= base_url('invoice/items/' . ($item ? 'update/'.$item['id'] : 'store')) ?>">
        <?= csrf_field() ?>

        <div class="form-grid">
          <div class="fg-6">
            <label class="clean-label">Item Type</label>
            <select name="item_type" class="clean-select">
              <option value="product" <?= ($item['item_type'] ?? 'product') == 'product' ? 'selected' : '' ?>>Product</option>
              <option value="service" <?= ($item['item_type'] ?? '') == 'service' ? 'selected' : '' ?>>Service</option>
            </select>
          </div>

          <div class="fg-6">
            <label class="clean-label">Item Name <span class="req">*</span></label>
            <input type="text" name="name" class="clean-input" value="<?= esc($item['name'] ?? '') ?>" placeholder="e.g. Website Hosting" required>
          </div>

          <div class="fg-4">
            <label class="clean-label">SKU</label>
            <input type="text" name="sku" class="clean-input" value="<?= esc($item['sku'] ?? '') ?>" placeholder="AUTO">
            <div class="help-text">Leave blank for auto/manual SKU.</div>
          </div>

          <div class="fg-4">
            <label class="clean-label">Unit</label>
            <select name="unit" class="clean-select">
              <?php foreach(['pcs','kg','g','ltr','ml','m','cm','hrs','days','box','dozen'] as $u): ?>
                <option value="<?= $u ?>" <?= ($item['unit'] ?? 'pcs') == $u ? 'selected' : '' ?>><?= strtoupper($u) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="fg-4">
            <label class="clean-label">Tax</label>
            <select name="tax_id" class="clean-select">
              <option value="">None</option>
              <?php foreach($taxes as $t): ?>
                <option value="<?= $t['id'] ?>" <?= ($item['tax_id'] ?? '') == $t['id'] ? 'selected' : '' ?>><?= esc($t['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="fg-6">
            <label class="clean-label">Selling Price <span class="req">*</span></label>
            <div class="input-icon">
              <span class="prefix">₹</span>
              <input type="number" name="selling_price" step="0.01" min="0" class="clean-input has-prefix" value="<?= esc($item['selling_price'] ?? '') ?>" placeholder="0.00" required>
            </div>
          </div>

          <div class="fg-6">
            <label class="clean-label">Purchase Price</label>
            <div class="input-icon">
              <span class="prefix">₹</span>
              <input type="number" name="purchase_price" step="0.01" min="0" class="clean-input has-prefix" value="<?= esc($item['purchase_price'] ?? '') ?>" placeholder="0.00">
            </div>
          </div>

          <div class="fg-12">
            <label class="clean-label">Description</label>
            <textarea name="description" class="clean-textarea" rows="4" placeholder="Item description..."> <?= esc($item['description'] ?? '') ?></textarea>
          </div>
        </div>

        <div class="form-actions">
          <a href="<?= base_url('invoice/items') ?>" class="btn-lightx">Cancel</a>
          <button type="submit" class="btn-main">
            <i class="bi bi-check-circle"></i> <?= $item ? 'Update Item' : 'Save Item' ?>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
