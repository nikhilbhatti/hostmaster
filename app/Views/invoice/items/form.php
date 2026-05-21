<?= $this->extend('layout/main') ?>
<?php $page_title=$item?'Edit Item':'New Item'; ?>
<?= $this->section('content') ?>

<div class="section-header">
  <h4><?= $item?'Edit Item':'New Item' ?></h4>
  <a href="<?= base_url('invoice/items') ?>" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Cancel</a>
</div>

<div class="card" style="max-width:640px"><div class="card-body">
  <form method="POST" action="<?= base_url('invoice/items/' . ($item ? 'update/'.$item['id'] : 'store')) ?>">
  <?= csrf_field() ?>
  
  <div class="row">
    <div class="col"><label class="form-label">Item Type</label>
    <select name="item_type" class="form-select"><option value="product" <?= ($item['item_type']??'')=='product'?'selected':'' ?>>Product</option><option value="service" <?= ($item['item_type']??'')=='service'?'selected':'' ?>>Service</option></select></div>
    <div class="col-2"><label class="form-label">Item Name *</label><input type="text" name="name" class="form-control" value="<?= esc($item['name']??'') ?>" required></div>
  </div>
  
  <div class="row" style="margin-top:16px">
    <div class="col"><label class="form-label">SKU</label><input type="text" name="sku" class="form-control" value="<?= esc($item['sku']??'') ?>" placeholder="AUTO"></div>
    <div class="col"><label class="form-label">Unit</label>
    <select name="unit" class="form-select">
    <?php foreach(['pcs','kg','g','ltr','ml','m','cm','hrs','days','box','dozen'] as $u): ?>
    <option value="<?= $u ?>" <?= ($item['unit']??'pcs')==$u?'selected':'' ?>><?= strtoupper($u) ?></option>
    <?php endforeach; ?>
    </select></div>
    <div class="col"><label class="form-label">Tax</label>
    <select name="tax_id" class="form-select"><option value="">None</option>
    <?php foreach($taxes as $t): ?><option value="<?= $t['id'] ?>" <?= ($item['tax_id']??'')==$t['id']?'selected':'' ?>><?= esc($t['name']) ?></option><?php endforeach; ?>
    </select></div>
  </div>
  
  <div class="row" style="margin-top:16px">
    <div class="col"><label class="form-label">Selling Price *</label><input type="number" name="selling_price" step="0.01" min="0" class="form-control" value="<?= $item['selling_price']??'' ?>" required></div>
    <div class="col"><label class="form-label">Purchase Price</label><input type="number" name="purchase_price" step="0.01" min="0" class="form-control" value="<?= $item['purchase_price']??'' ?>"></div>
  </div>
  
  <div class="form-group" style="margin-top:16px"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3" placeholder="Item description..."><?= esc($item['description']??'') ?></textarea></div>
  
  <button type="submit" class="btn btn-primary"><?= $item?'Update':'Save' ?> Item</button>
  <a href="<?= base_url('invoice/items') ?>" class="btn btn-outline" style="margin-left:8px">Cancel</a>
  </form>
</div></div>

<?= $this->endSection() ?>