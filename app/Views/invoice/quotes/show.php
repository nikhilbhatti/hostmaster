<?= $this->extend('layout/main') ?>
<?php $page_title=$q['quote_number']; ?>
<?= $this->section('content') ?>
<div class="section-header">
  <h4><?= $q['quote_number'] ?></h4>
  <div style="display:flex;gap:8px">
    <a href="/quotes/convert/<?= $q['id'] ?>" class="btn btn-success" onclick="return confirm('Convert to Invoice?')"><i class="bi bi-arrow-right-circle"></i> Convert to Invoice</a>
    <a href="/quotes/edit/<?= $q['id'] ?>" class="btn btn-outline"><i class="bi bi-pencil"></i> Edit</a>
    <button onclick="window.print()" class="btn btn-outline"><i class="bi bi-printer"></i> Print</button>
    <a href="/quotes" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Back</a>
  </div>
</div>
<div class="card">
  <div class="card-body">
    <div class="detail-grid" style="margin-bottom:24px">
      <div>
        <div class="detail-label">Customer</div>
        <div class="detail-value" style="font-size:16px;font-weight:600"><?= esc($q['cname']) ?></div>
        <?php if($q['b_address1']): ?><div style="color:#6b7280;font-size:13px;margin-top:4px"><?= esc($q['b_address1']) ?><br><?= esc($q['b_city']) ?> <?= esc($q['b_state']) ?> <?= esc($q['b_zip']) ?></div><?php endif; ?>
        <?php if($q['cgstin']): ?><div style="font-size:12px;margin-top:6px;color:#6b7280">GSTIN: <?= esc($q['cgstin']) ?></div><?php endif; ?>
      </div>
      <div style="text-align:right">
        <div style="font-size:22px;font-weight:700;color:#1a1f36"><?= $q['quote_number'] ?></div>
        <div style="margin-top:8px"><span class="badge badge-<?= $q['status'] ?>"><?= $q['status'] ?></span></div>
        <div style="margin-top:12px;font-size:13px;color:#6b7280">Date: <?= $q['quote_date'] ?></div>
        <div style="font-size:13px;color:#6b7280">Expiry: <?= $q['expiry_date'] ?></div>
      </div>
    </div>
    <table class="ztable" style="margin-bottom:20px">
      <thead><tr><th>#</th><th>Item & Description</th><th>Qty</th><th>Rate</th><th>Disc%</th><th>Tax%</th><th style="text-align:right">Amount</th></tr></thead>
      <tbody>
      <?php foreach($items as $i=>$item): ?>
      <tr>
        <td><?= $i+1 ?></td>
        <td><div style="font-weight:500"><?= esc($item['item_name']) ?></div><?php if($item['description']): ?><div style="font-size:12px;color:#6b7280"><?= esc($item['description']) ?></div><?php endif; ?></td>
        <td><?= $item['qty'] ?> <?= esc($item['unit']) ?></td>
        <td>₹<?= number_format($item['rate'],2) ?></td>
        <td><?= $item['discount'] ?>%</td>
        <td><?= $item['tax_rate'] ?>%</td>
        <td style="text-align:right;font-weight:600">₹<?= number_format($item['amount'],2) ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <div style="display:flex;justify-content:flex-end">
      <table class="totals-table" style="width:300px">
        <tr><td style="color:#6b7280">Sub Total</td><td style="text-align:right">₹<?= number_format($q['sub_total'],2) ?></td></tr>
        <?php if($q['discount_value']>0): ?><tr><td style="color:#6b7280">Discount</td><td style="text-align:right;color:#dc2626">-₹<?= number_format($q['discount_amount'],2) ?></td></tr><?php endif; ?>
        <?php if($q['tax_total']>0): ?><tr><td style="color:#6b7280">Tax</td><td style="text-align:right">₹<?= number_format($q['tax_total'],2) ?></td></tr><?php endif; ?>
        <tr class="total-row"><td style="padding-top:12px;font-weight:700">Total</td><td style="text-align:right;font-size:18px;font-weight:700;padding-top:12px">₹<?= number_format($q['total'],2) ?></td></tr>
      </table>
    </div>
    <?php if($q['customer_notes']): ?><div style="margin-top:24px;padding-top:16px;border-top:1px solid #e8eaed"><div class="detail-label">Customer Notes</div><div style="font-size:13px;color:#374151;margin-top:4px"><?= nl2br(esc($q['customer_notes'])) ?></div></div><?php endif; ?>
  </div>
</div>
<?= $this->endSection() ?>