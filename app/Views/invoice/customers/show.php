<?= $this->extend('layout/main') ?>
<?php $page_title=esc($c['display_name']); ?>
<?= $this->section('content') ?>

<div class="section-header">
  <h4><?= esc($c['display_name']) ?></h4>
  <div style="display:flex;gap:8px">
    <a href="<?= base_url('invoice/invoices/create') ?>" class="btn btn-primary"><i class="bi bi-plus"></i> New Invoice</a>
    <a href="<?= base_url('invoice/customers/edit/' . $c['id']) ?>" class="btn btn-outline"><i class="bi bi-pencil"></i> Edit</a>
    <a href="<?= base_url('invoice/customers') ?>" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Back</a>
  </div>
</div>

<div style="display:grid;grid-template-columns:300px 1fr;gap:20px">
  <div>
    <div class="card"><div class="card-body">
      <div style="text-align:center;padding:16px 0;border-bottom:1px solid #e8eaed;margin-bottom:16px">
        <div style="width:56px;height:56px;border-radius:50%;background:#5065e8;color:#fff;font-size:22px;font-weight:700;display:flex;align-items:center;justify-content:center;margin:0 auto 10px"><?= strtoupper(substr($c['display_name'],0,1)) ?></div>
        <div style="font-size:15px;font-weight:600"><?= esc($c['display_name']) ?></div>
        <div style="color:#6b7280;font-size:12px"><?= esc($c['company_name']) ?></div>
      </div>
      <?php if($c['email']): ?><div style="margin-bottom:10px"><div class="detail-label">Email</div><div class="detail-value"><?= esc($c['email']) ?></div></div><?php endif; ?>
      <?php if($c['work_phone']): ?><div style="margin-bottom:10px"><div class="detail-label">Phone</div><div class="detail-value"><?= esc($c['work_phone']) ?></div></div><?php endif; ?>
      <?php if($c['gstin']): ?><div style="margin-bottom:10px"><div class="detail-label">GSTIN</div><div class="detail-value"><?= esc($c['gstin']) ?></div></div><?php endif; ?>
      <?php if($c['b_address1']): ?><div><div class="detail-label">Billing Address</div><div class="detail-value"><?= esc($c['b_address1']) ?><?= $c['b_address2']?', '.esc($c['b_address2']):'' ?><br><?= esc($c['b_city']) ?> <?= esc($c['b_state']) ?> <?= esc($c['b_zip']) ?></div></div><?php endif; ?>
    </div></div>
  </div>
  
  <div>
    <div class="card"><div class="card-header"><h5>Invoices</h5></div><div class="table-wrap">
      <table class="ztable">
        <thead><tr><th>Invoice#</th><th>Date</th><th>Due Date</th><th>Total</th><th>Balance</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach($invoices as $inv): ?>
        <tr>
          <td><a href="<?= base_url('invoice/invoices/show/' . $inv['id']) ?>" style="color:#5065e8;font-weight:500"><?= $inv['invoice_number'] ?></a></td>
          <td><?= $inv['invoice_date'] ?></td><td><?= $inv['due_date'] ?></td>
          <td>₹<?= number_format($inv['total'],2) ?></td>
          <td>₹<?= number_format($inv['balance_due'],2) ?></td>
          <td><span class="badge badge-<?= $inv['status'] ?>"><?= $inv['status'] ?></span></td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($invoices)): ?><tr><td colspan="6" style="text-align:center;color:#9ca3af;padding:24px">No invoices</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div></div>
  </div>
</div>

<?= $this->endSection() ?>