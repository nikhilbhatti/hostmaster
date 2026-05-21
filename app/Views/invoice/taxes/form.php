<?= $this->extend('layout/main') ?>

<?php $page_title = $tax ? 'Edit Tax' : 'New Tax'; ?>

<?= $this->section('content') ?>

<div class="section-header">
    <h4><?= $tax ? 'Edit Tax' : 'New Tax' ?></h4>

    <a href="<?= base_url('invoice/taxes') ?>" class="btn btn-outline">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="card" style="max-width:480px">
    <div class="card-body">

        <form method="POST" action="<?= base_url('invoice/taxes/' . ($tax ? 'update/'.$tax['id'] : 'store')) ?>">

            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label">Tax Name *</label>

                <input 
                    type="text" 
                    name="name" 
                    class="form-control"
                    value="<?= esc($tax['name'] ?? '') ?>"
                    placeholder="e.g. GST 18%"
                    required
                >
            </div>

            <div class="form-group">
                <label class="form-label">Rate (%) *</label>

                <input 
                    type="number"
                    name="rate"
                    step="0.01"
                    min="0"
                    max="100"
                    class="form-control"
                    value="<?= $tax['rate'] ?? '' ?>"
                    placeholder="18"
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary">
                <?= $tax ? 'Update' : 'Save' ?> Tax
            </button>

            <a href="<?= base_url('invoice/taxes') ?>" class="btn btn-outline" style="margin-left:8px">
                Cancel
            </a>

        </form>

    </div>
</div>

<?= $this->endSection() ?>