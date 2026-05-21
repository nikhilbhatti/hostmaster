<?= $this->extend('layout/main') ?>
<?php $page_title = 'Payments Received'; ?>
<?= $this->section('content') ?>

<style>
/* ── ZOHO PREMIUM SPLIT DASHBOARD ENGINE (INDEX MASTER) ── */
.zoho-split-dashboard {
    display: grid;
    grid-template-columns: 350px 1fr;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    min-height: calc(100vh - 100px);
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    margin: 10px 0;
    overflow: hidden;
}

/* Left Sidebar Master Queue List */
.zoho-sidebar {
    background: #ffffff;
    border-right: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    height: calc(100vh - 100px);
}
.sidebar-header-row {
    padding: 16px 20px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8fafc;
}
.sidebar-main-title {
    font-size: 15px;
    font-weight: 600;
    color: #1e293b;
}
.btn-zoho-new {
    background: #2563eb;
    color: #ffffff;
    padding: 5px 12px;
    font-size: 12px;
    font-weight: 500;
    border-radius: 4px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-zoho-new:hover { background: #1d4ed8; }

.sidebar-scroll-stack {
    overflow-y: auto;
    flex: 1;
}
.payment-master-card {
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    text-decoration: none !important;
    color: inherit !important;
    transition: background 0.1s ease;
}
.payment-master-card:hover { background: #f8fafc; }
.payment-master-card.active-selected {
    background: #f0f6ff;
    border-left: 4px solid #2563eb;
}
.meta-card-left { display: flex; flex-direction: column; gap: 3px; }
.card-cust-name { font-weight: 600; font-size: 13.5px; color: #1e293b; }
.card-sub-hints { font-size: 12px; color: #64748b; }
.card-status-badge { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px; }
.card-meta-right { text-align: right; font-weight: 600; font-size: 13.5px; color: #0f172a; }

/* Right Section Document Viewer Workspace */
.zoho-viewer-workspace {
    background: #f8fafc;
    display: flex;
    flex-direction: column;
    height: calc(100vh - 100px);
    overflow-y: auto;
}
.viewer-top-ribbon {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    padding: 12px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 10;
}
.ribbon-doc-title { font-size: 18px; font-weight: 600; color: #0f172a; }
.ribbon-actions-group { display: flex; gap: 8px; }

/* Control Buttons Minimal Styling */
.btn-ribbon-utility {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #334155;
    padding: 6px 14px;
    font-size: 13px;
    font-weight: 500;
    border-radius: 4px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none !important;
}
.btn-ribbon-utility:hover { background: #f8fafc; border-color: #94a3b8; }
.btn-ribbon-delete { color: #dc2626; }
.btn-ribbon-delete:hover { background: #fef2f2; border-color: #fca5a5; }

/* What's Next Alert Box */
.whats-next-banner {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 16px 20px;
    margin: 20px 24px 0 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.banner-text-side { font-size: 13px; color: #334155; }
.banner-text-side strong { color: #0f172a; font-weight: 600; display: block; font-size: 11px; margin-bottom: 2px; letter-spacing: 0.5px; }
.btn-action-paid-trigger { background: #2563eb; color: #fff; border: 1px solid #2563eb; padding: 5px 12px; font-size: 12.5px; font-weight: 500; border-radius: 4px; text-decoration: none !important; }
.btn-action-paid-trigger:hover { background: #1d4ed8; }

/* ── PROFESSIONAL A4 CANVAS PRINT SHEET ── */
.canvas-scroll-container { padding: 24px; }
.receipt-a4-sheet {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    max-width: 800px;
    width: 100%;
    margin: 0 auto;
    box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    padding: 50px 60px;
    box-sizing: border-box;
    position: relative;
    min-height: 842px;
}

/* Diagonal Draft Status Watermark */
.draft-corner-tag {
    position: absolute;
    top: 24px;
    left: -12px;
    background: #94a3b8;
    color: #ffffff;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 28px;
    transform: rotate(-45deg);
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.sheet-header-flex { display: flex; justify-content: space-between; margin-bottom: 45px; }
.brand-identity-box { display: flex; flex-direction: column; gap: 3px; }
.brand-main-logo { font-size: 26px; font-weight: 700; color: #0f172a; text-transform: lowercase; margin-bottom: 4px; letter-spacing: -0.5px; }
.brand-address-text { font-size: 13px; color: #475569; line-height: 1.6; }
.sheet-main-title { font-size: 22px; font-weight: 400; color: #475569; text-transform: uppercase; letter-spacing: 1px; text-align: right; }

/* Mid Info Param Matrix */
.sheet-meta-matrix {
    display: grid;
    grid-template-columns: 1fr 260px;
    gap: 40px;
    margin-top: 35px;
    align-items: flex-start;
}
.matrix-data-table { width: 100%; border-collapse: collapse; }
.matrix-data-table td { padding: 8px 0; font-size: 13px; color: #334155; vertical-align: top; }
.matrix-data-table td:first-child { color: #64748b; width: 140px; }
.matrix-data-table td strong { color: #0f172a; font-weight: 500; }

/* Zoho Green Box Configuration */
.zoho-green-amount-box {
    background: #71a062 !important; /* Zoho Green Emerald Shade */
    color: #ffffff !important;
    padding: 20px;
    border-radius: 4px;
    text-align: left;
}
.green-box-label { font-size: 11px; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; font-weight: 500; }
.green-box-sum { font-size: 26px; font-weight: 700; }

/* Allocation Linked Items Table */
.allocation-header-title { font-size: 12px; font-weight: 600; color: #0f172a; margin-top: 50px; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
.allocation-grid-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.allocation-grid-table th { border-bottom: 2px solid #e2e8f0; padding: 10px 0; text-align: left; color: #64748b; font-weight: 500; }
.allocation-grid-table td { border-bottom: 1px solid #f1f5f9; padding: 14px 0; color: #334155; }

/* Empty Placeholder Central Box */
.empty-center-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 80px 40px;
    color: #94a3b8;
    text-align: center;
    font-style: italic;
}

/* ── STRICT PRINT ENGINE OVERRIDES (FIXED PDF STRUCTURE) ── */
@media print {
    /* Main dashboard layout wrapper completely hidden */
    html, body {
        background: #ffffff !important;
        margin: 0 !important;
        padding: 0 !important;
        height: auto !important;
    }
    
    /* Layout structural containers hidden */
    .zoho-split-dashboard,
    .zoho-sidebar,
    .viewer-top-ribbon,
    .whats-next-banner,
    .canvas-scroll-container {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
        display: block !important;
    }
    
    /* Hide dashboard sidebar and layout elements completely */
    .zoho-sidebar,
    .viewer-top-ribbon,
    .whats-next-banner {
        display: none !important;
    }

    /* Target canvas block extraction */
    .receipt-a4-sheet {
        position: static !important;
        margin: 0 auto !important;
        padding: 40px 50px !important;
        box-shadow: none !important;
        border: none !important;
        width: 100% !important;
        max-width: 100% !important;
        min-height: auto !important;
        page-break-inside: avoid !important;
    }

    /* Force absolute rendering of CSS rules for chrome/safari print drivers */
    * {
        -webkit-print-color-adjust: exact !important;
        color-adjust: exact !important;
    }

    /* Ensure Zoho Emerald Green remains visible */
    .zoho-green-amount-box {
        background-color: #71a062 !important;
        color: #ffffff !important;
    }
}
</style>

<div class="zoho-split-dashboard">
    
    <div class="zoho-sidebar">
        <div class="sidebar-header-row">
            <span class="sidebar-main-title">Payments Received</span>
            <a href="<?= base_url('invoice/payments/create') ?>" class="btn-zoho-new">
                <i class="bi bi-plus"></i> New
            </a>
        </div>
        
        <div class="sidebar-scroll-stack">
            <?php 
                $active_id = $active_payment['id'] ?? (!empty($payments) ? $payments[0]['id'] : null); 
            ?>
            <?php if(!empty($payments)): ?>
                <?php foreach($payments as $index => $pay): ?>
                    <a href="<?= base_url('invoice/payments?active_id=' . $pay['id']) ?>" class="payment-master-card <?= ($pay['id'] == $active_id) ? 'active-selected' : '' ?>">
                        <div class="meta-card-left">
                            <span class="card-cust-name"><?= esc($pay['cname']) ?></span>
                            <span class="card-sub-hints"><?= esc($pay['payment_number']) ?> • <?= date('d/m/Y', strtotime($pay['payment_date'])) ?></span>
                            <span class="card-status-badge">Draft</span>
                        </div>
                        <div class="card-meta-right">
                            ₹<?= number_format($pay['amount'], 2) ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="padding: 30px; text-align: center; color: #94a3b8; font-size: 13px;">No payments recorded yet.</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="zoho-viewer-workspace">
        <?php 
            $current_p = null;
            if (!empty($payments)) {
                foreach ($payments as $pay) {
                    if ($pay['id'] == $active_id) {
                        $current_p = $pay;
                        break;
                    }
                }
                if (!$current_p) $current_p = $payments[0];
            }
        ?>

        <?php if($current_p): ?>
            <div class="viewer-top-ribbon">
                <div class="ribbon-doc-title"><?= esc($current_p['payment_number']) ?></div>
                <div class="ribbon-actions-group">
                    <a href="<?= base_url('invoice/payments/edit/' . $current_p['id']) ?>" class="btn-ribbon-utility">✏️ Edit</a>
                    <button type="button" class="btn-ribbon-utility" onclick="window.print()">🖨️ PDF / Print</button>
                    <button type="button" class="btn-ribbon-utility" onclick="alert('Status marked as paid!')">✔️ Mark as Paid</button>
                    <a href="<?= base_url('invoice/payments/delete/' . $current_p['id']) ?>" class="btn-ribbon-utility btn-ribbon-delete" onclick="return confirm('Are you sure you want to drop this payment track?')">🗑️ Delete</a>
                </div>
            </div>

            <div class="whats-next-banner">
                <div class="banner-text-side">
                    <strong>✨ WHAT'S NEXT?</strong>
                    Mark the payment as Paid to confirm that it has been received and closed.
                </div>
                <div>
                    <a href="#" class="btn-action-paid-trigger" onclick="alert('Status confirmed successfully!')">Mark as Paid</a>
                </div>
            </div>

            <div class="canvas-scroll-container">
                <div class="receipt-a4-sheet">
                    
                    <div class="draft-corner-tag">Draft</div>

                    <div class="sheet-header-flex">
                        <div class="brand-identity-box">
                            <div class="brand-main-logo">slysis</div>
                            <div class="brand-address-text">
                                Himachal Pradesh<br>
                                India<br>
                                91-7876728830<br>
                                bhattinikhil530@gmail.com
                            </div>
                        </div>
                        <div class="sheet-main-title">Payment Receipt</div>
                    </div>

                    <hr style="border: 0; border-top: 1px solid #f1f5f9; margin-bottom: 20px;">

                    <div class="sheet-meta-matrix">
                        <div>
                            <table class="matrix-data-table">
                                <tr>
                                    <td>Payment Date</td>
                                    <td><strong><?= date('d/m/Y', strtotime($current_p['payment_date'])) ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Reference Number</td>
                                    <td><strong><?= !empty($current_p['reference']) ? esc($current_p['reference']) : '--' ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Payment Mode</td>
                                    <td><strong><?= esc(ucfirst($current_p['payment_mode'])) ?></strong></td>
                                </tr>
                            </table>
                        </div>

                        <div class="zoho-green-amount-box">
                            <div class="green-box-label">Amount Received</div>
                            <div class="green-box-sum">₹<?= number_format($current_p['amount'], 2) ?></div>
                        </div>
                    </div>

                    <div style="margin-top: 45px; max-width: 350px;">
                        <div style="font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Bill To</div>
                        <div style="font-weight: 600; font-size: 14px; color: #2563eb;"><?= esc($current_p['cname']) ?></div>
                        <div style="font-size: 13px; color: #475569; margin-top: 2px; line-height: 1.4;">
                            Individual Account / Corporate Customer
                        </div>
                    </div>

                    <div class="allocation-header-title">Payment For:</div>
                    <table class="allocation-grid-table">
                        <thead>
                            <tr>
                                <th style="width: 50%;">Invoice Number</th>
                                <th style="text-align: right; width: 25%;">Invoice Amount</th>
                                <th style="text-align: right; width: 25%;">Payment Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="font-weight: 600; color: #0f172a;">
                                    <?php if(!empty($current_p['invoice_number'])): ?>
                                        <span style="color:#2563eb;">
                                            <?= esc($current_p['invoice_number']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: #94a3b8; font-style: italic;">Unallocated Advance / Credit</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">₹<?= number_format($current_p['amount'], 2) ?></td>
                                <td style="text-align: right; font-weight: 600; color: #111827;">₹<?= number_format($current_p['amount'], 2) ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <div style="margin-top: 120px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 15px;">
                        Thank you for your business! This is a system generated payment receipt by slysis.
                    </div>

                </div>
            </div>
        <?php else: ?>
            <div class="empty-center-state">
                <i class="bi bi-inbox" style="font-size: 32px; margin-bottom: 10px; color:#cbd5e1;"></i>
                No payments records selected or available to preview.
            </div>
        <?php endif; ?>
    </div>

</div>

<?= $this->endSection() ?>