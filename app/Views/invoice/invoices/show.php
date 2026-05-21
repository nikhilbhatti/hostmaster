<?= $this->extend('layout/main') ?>
<?php $page_title = $inv['invoice_number']; ?>
<?= $this->section('content') ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<?php
function numToWordsINR($n) {
    $n = round($n, 2); $p = explode('.', $n); $r = (int)$p[0]; $ps = isset($p[1])?(int)$p[1]:0;
    if(!$r&&!$ps) return 'Indian Rupee Zero Only';
    $d1=['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen'];
    $d2=['','Ten','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];
    $g=['','Thousand','Lakh','Crore'];
    $no=$r;$res=[];$i=0;
    while($no>0){
        if($i==0){$cur=$no%1000;$no=floor($no/1000);if($cur>0){$c=floor($cur/100);$t=$cur%100;$tmp='';if($c)$tmp=$d1[$c].' Hundred';if($t){if($tmp)$tmp.=' ';$tmp.=($t<20)?$d1[$t]:$d2[floor($t/10)].(($t%10)?' '.$d1[$t%10]:'');}$res[]=$tmp;}}
        else{$cur=$no%100;$no=floor($no/100);if($cur>0){$tmp=($cur<20)?$d1[$cur]:$d2[floor($cur/10)].(($cur%10)?' '.$d1[$cur%10]:'');$res[]=$tmp.' '.$g[$i];}}
        $i++;
    }
    $res=array_reverse(array_filter($res));
    $out='Indian Rupee '.implode(' ',$res);
    if($ps>0){$pt=($ps<20)?$d1[$ps]:$d2[floor($ps/10)].(($ps%10)?' '.$d1[$ps%10]:'');$out.=' And '.$pt.' Paise';}
    return $out.' Only';
}
$total_words = numToWordsINR($inv['total']);
?>

<style>
/* ════════════════════════════════════════════════
   TOP BAR & STATUS
════════════════════════════════════════════════ */
.inv-topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
.inv-statusbar{background:#fff;border:1px solid #e8eaed;border-radius:8px;padding:12px 24px;margin-bottom:20px;display:flex;align-items:center;gap:20px}
.s-amt{font-size:20px;font-weight:700}.s-lbl{font-size:11px;color:#6b7280;margin-bottom:1px}.s-div{width:1px;height:32px;background:#e5e7eb}

/* ════════════════════════════════════════════════
   DROPDOWN (shared)
════════════════════════════════════════════════ */
.zdrop{position:relative;display:inline-block}
.zdrop-menu{display:none;position:absolute;right:0;top:calc(100% + 4px);background:#fff;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.14);z-index:600;min-width:220px;padding:6px 0}
.zdrop:hover .zdrop-menu,.zdrop.show .zdrop-menu{display:block}
.zdrop-menu a,.zdrop-menu button{display:flex;align-items:center;gap:8px;width:100%;padding:10px 16px;background:none;border:none;font-size:13px;color:#374151;text-decoration:none;cursor:pointer}
.zdrop-menu a:hover,.zdrop-menu button:hover{background:#f0f4ff;color:#2563eb}
.zdrop-div{height:1px;background:#e8eaed;margin:4px 0}
.zdrop-label{padding:8px 16px 4px;font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em}

/* ════════════════════════════════════════════════
   INVOICE DOCUMENT
════════════════════════════════════════════════ */
#printArea{background:#e8eaee;padding:28px}
.tax-invoice{
    width:794px;min-height:1060px;margin:0 auto;background:#fff;
    position:relative;padding:72px 52px 50px;
    font-family:'Times New Roman',Georgia,serif;color:#111;
    box-shadow:0 2px 14px rgba(0,0,0,.12);box-sizing:border-box;
}
/* CSS-var driven header */
.tax-invoice::before{content:'';position:absolute;top:0;left:0;right:0;height:68px;background:var(--hdr,radial-gradient(160px 36px at 22% 2px,transparent 55%,#fff 57% 62%,transparent 64%),radial-gradient(360px 70px at 80% 30px,transparent 57%,#0b7285 58% 60%,transparent 62%),linear-gradient(160deg,#0b8fa3 0 58%,transparent 58%),#08a6bf)}
.tax-invoice::after{content:'';position:absolute;top:32px;left:0;right:0;height:2px;background:var(--hdr-line,#0b7285)}
.inv-body{position:relative;z-index:2}
.draft-stamp{position:absolute;top:28px;left:-6px;background:#8fa1a5;color:#fff;font:700 13px Arial;letter-spacing:1px;padding:9px 42px;transform:rotate(-45deg);transform-origin:left top;z-index:10}

/* Gear button inside invoice */
.inv-gear{position:absolute;top:74px;right:50px;z-index:30}

/* Invoice grid */
.inv-frame{border:1px solid #888}
.inv-top-row{display:grid;grid-template-columns:1fr 1fr;border-bottom:1px solid #888;min-height:88px}
.inv-company{padding:8px 10px;font-size:12px;line-height:1.35}
.inv-company h2{font-size:14px;font-weight:bold;margin:0 0 3px}
.inv-logo-img{max-width:110px;max-height:56px;object-fit:contain;margin-bottom:4px;display:block}
.inv-title{display:flex;align-items:center;justify-content:flex-end;padding:8px 10px;font-size:24px;font-weight:400;letter-spacing:.4px}

.inv-meta-grid{display:grid;grid-template-columns:1fr 1fr;border-bottom:1px solid #888}
.inv-meta-left{border-right:1px solid #888}
.meta-row{display:grid;grid-template-columns:155px 1fr;font-size:12px;min-height:22px;align-items:center;padding:0 8px;border-bottom:1px solid #eee}
.meta-row:last-child{border-bottom:none}.meta-row .v{font-weight:bold}

.addr-grid{display:grid;grid-template-columns:1fr 1fr;border-bottom:1px solid #888}
.addr-grid>div:first-child{border-right:1px solid #888}
.addr-title{background:#e8e8e8;border-bottom:1px solid #888;padding:4px 8px;font-size:12px;font-weight:bold}
.addr-body{min-height:50px;padding:8px;font-size:12px;line-height:1.4}
.cname-link{color:#1769d1;font-weight:bold;text-decoration:underline}

.items-tbl{width:100%;border-collapse:collapse;font-size:12px}
.items-tbl th{border-right:1px solid #888;border-bottom:1px solid #888;padding:5px 7px;font-weight:bold;background:#fff;text-align:left}
.items-tbl th:last-child,.items-tbl td:last-child{border-right:none}
.items-tbl td{border-right:1px solid #888;border-bottom:1px solid #bbb;padding:5px 7px;vertical-align:top}
.it-name{font-weight:600}.it-desc{font-size:11px;color:#555;margin-top:1px}

.summary-grid{display:grid;grid-template-columns:1.2fr .8fr;border-bottom:1px solid #888}
.words-box{padding:8px;font-size:12px;border-right:1px solid #888;min-height:108px}
.words-box .wl{font-weight:bold;margin-bottom:3px}.words-box .wv{font-weight:bold;font-style:italic}
.totals-box{font-size:12px}
.tot-r{display:grid;grid-template-columns:1fr 120px;border-bottom:1px solid #ddd;min-height:23px;align-items:center}
.tot-r span:first-child{text-align:right;padding-right:10px;font-weight:bold}
.tot-r span:last-child{text-align:right;padding-right:8px;font-weight:bold}

.footer-grid{display:grid;grid-template-columns:1.2fr .8fr;min-height:96px}
.footer-left{border-right:1px solid #888;padding:8px;font-size:12px}
.footer-right{display:flex;align-items:flex-end;justify-content:center;font-size:12px;padding-bottom:8px}

/* ════════════════════════════════════════════════
   SETTINGS MODAL — Zoho Edit Template
════════════════════════════════════════════════ */
.s-overlay{display:none;position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:1000;align-items:flex-start;justify-content:center;padding-top:28px}
.s-overlay.open{display:flex}
.s-box{background:#fff;border-radius:5px;box-shadow:0 14px 45px rgba(0,0,0,.24);width:980px;max-width:96vw;max-height:90vh;display:flex;flex-direction:column;overflow:hidden}
.s-head{display:flex;align-items:center;justify-content:space-between;padding:15px 22px;border-bottom:1px solid #e5e7eb;flex-shrink:0}
.s-head h3{font-size:17px;font-weight:700;margin:0}
.s-close{background:none;border:none;font-size:24px;color:#dc2626;cursor:pointer;line-height:1}
.s-wrap{display:flex;flex:1;overflow:hidden}

/* Left sidebar */
.s-sidebar{width:136px;background:#172033;flex-shrink:0;overflow-y:auto}
.s-tab{padding:14px 8px;text-align:center;cursor:pointer;color:#8ea3c0;font-size:11px;font-weight:500;border-left:3px solid transparent;transition:.15s;line-height:1.4}
.s-tab i{display:block;font-size:19px;margin-bottom:3px}
.s-tab:hover{color:#c8d8ec;background:rgba(255,255,255,.06)}
.s-tab.on{color:#fff;background:#2f65c8;border-left-color:#76a9ff}

/* Content area */
.s-content{flex:1;overflow-y:auto;padding:22px 26px}
.s-panel{display:none}.s-panel.on{display:block}
.s-ptitle{font-size:18px;font-weight:700;margin:0 0 16px;color:#1a1f36}
.s-sub{font-size:13px;font-weight:600;color:#374151;margin:18px 0 9px;padding-bottom:4px;border-bottom:1px solid #f1f3f9}
.sg{display:grid;gap:12px;margin-bottom:14px}
.sg-2{grid-template-columns:1fr 1fr}.sg-3{grid-template-columns:1fr 1fr 1fr}
.sl{font-size:12px;font-weight:500;color:#6b7280;margin-bottom:4px;display:block}
.si{width:100%;border:1px solid #d1d5db;border-radius:6px;padding:8px 11px;font-size:13px;outline:none;box-sizing:border-box;font-family:inherit}
.si:focus{border-color:#5065e8;box-shadow:0 0 0 2px rgba(80,101,232,.1)}
.sta{width:100%;border:1px solid #d1d5db;border-radius:6px;padding:8px 11px;font-size:13px;outline:none;min-height:80px;resize:vertical;box-sizing:border-box;font-family:inherit}
.sc-row{display:flex;align-items:center;gap:9px;margin-bottom:9px;font-size:13px;color:#374151}
.sc-row input[type=checkbox]{width:15px;height:15px;cursor:pointer;accent-color:#5065e8}
.sdiv{height:1px;background:#e8eaed;margin:16px 0}

/* Paper size selector */
.paper-opts{display:flex;gap:12px;margin-bottom:14px}
.paper-opt{padding:8px 18px;border:1.5px solid #d1d5db;border-radius:6px;cursor:pointer;font-size:13px;transition:.15s;display:flex;align-items:center;gap:6px}
.paper-opt.on{border-color:#5065e8;background:#f0f4ff;color:#5065e8;font-weight:600}

/* Header background tiles */
.hdr-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:16px}
.hdr-tile{height:60px;border:2px solid #e5e7eb;border-radius:7px;cursor:pointer;overflow:hidden;transition:.15s;position:relative}
.hdr-tile:hover,.hdr-tile.on{border-color:#5065e8;box-shadow:0 0 0 2px rgba(80,101,232,.2)}
.hdr-tile .tile-lbl{position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,.4);color:#fff;font-size:10px;text-align:center;padding:2px 0}
.h-wave{background:radial-gradient(160px 36px at 22% 2px,transparent 55%,#fff 57% 62%,transparent 64%),radial-gradient(360px 70px at 80% 30px,transparent 57%,#0b7285 58% 60%,transparent 62%),linear-gradient(160deg,#0b8fa3 0 58%,transparent 58%),#08a6bf}
.h-teal{background:linear-gradient(135deg,#0ea5e9 0%,#0284c7 100%)}
.h-green{background:linear-gradient(135deg,#059669,#047857)}
.h-purple{background:linear-gradient(135deg,#7c3aed,#4c1d95)}
.h-orange{background:linear-gradient(135deg,#ea580c,#9a3412)}
.h-navy{background:linear-gradient(160deg,#1e3a5f 0 60%,#0b1e36 60%)}
.h-rose{background:linear-gradient(135deg,#e11d48,#9f1239)}
.h-minimal{background:repeating-linear-gradient(90deg,#f1f5f9 0 2px,transparent 2px 20px),#fff}
.h-bubble{background:radial-gradient(circle at 12% 44%,#19b2c7 0 10px,transparent 11px),radial-gradient(circle at 24% 30%,#fff 0 9px,#19b2c7 10px,transparent 12px),radial-gradient(circle at 65% 25%,#19b2c7 0 7px,transparent 8px),#fff}
.h-dots{background:radial-gradient(circle,#0b8fa3 0 3px,transparent 4px) 0 0/18px 18px,#fff}
.h-stripe{background:repeating-linear-gradient(160deg,#0ea5e9 0 18px,#0284c7 18px 36px)}
.h-none{background:#f8fafc;display:flex;align-items:center;justify-content:center;color:#9ca3af;font-size:11px}

/* Template cards */
.tpl-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:14px}
.tpl-card{border:2px solid #e5e7eb;border-radius:8px;overflow:hidden;cursor:pointer;transition:.15s}
.tpl-card:hover{border-color:#94a3b8}.tpl-card.on{border-color:#3483fa;box-shadow:0 0 0 3px rgba(52,131,250,.15)}
.tpl-prev{height:88px;position:relative;display:flex;align-items:center;justify-content:center;overflow:hidden}
.tpl-sel{background:#eaf3ff;color:#2473e8;text-align:center;padding:6px;font-weight:700;font-size:11px;letter-spacing:1px}
.tpl-name{padding:9px 12px;font-size:13px;font-weight:500}

/* Logo upload */
.logo-zone{border:2px dashed #d1d5db;border-radius:8px;padding:28px;text-align:center;cursor:pointer;background:#fafafa;transition:.15s}
.logo-zone:hover{border-color:#5065e8;background:#f0f4ff}

/* Modal footer */
.s-foot{padding:12px 22px;border-top:1px solid #e5e7eb;display:flex;gap:10px;flex-shrink:0}
.btn-sav{background:#3483fa;color:#fff;border:none;border-radius:6px;padding:9px 22px;font-size:13px;font-weight:600;cursor:pointer}.btn-sav:hover{background:#2463d4}
.btn-can{background:#fff;border:1px solid #d1d5db;border-radius:6px;padding:9px 18px;font-size:13px;cursor:pointer}

/* Margin sliders */
.margin-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px}
.margin-item label{font-size:12px;color:#6b7280;display:block;margin-bottom:3px}
.margin-item input[type=number]{width:100%;border:1px solid #d1d5db;border-radius:5px;padding:7px 10px;font-size:13px;outline:none}

/* Print/PDF */
@media print{body *{visibility:hidden!important}#printArea,#printArea *{visibility:visible!important}#printArea{position:fixed!important;top:0;left:0;width:100%;padding:0!important;background:#fff!important}.tax-invoice{width:100%!important;box-shadow:none!important;padding:58px 42px 40px!important}.no-print,.inv-gear,.inv-topbar,.inv-statusbar{display:none!important}}
.pdf-go .no-print,.pdf-go .inv-gear{display:none!important}
.pdf-go #printArea{padding:0!important;background:#fff!important}
.pdf-go .tax-invoice{box-shadow:none!important;margin:0!important}
</style>

<!-- ── TOP BAR ── -->
<div class="inv-topbar no-print">
    <h4 style="font-size:17px;font-weight:700"><?= esc($inv['invoice_number']) ?></h4>
    <div style="display:flex;gap:7px;align-items:center">
        <?php if($inv['status']!=='paid'): ?>
        <a href="<?= base_url('invoice/payments/create/'.$inv['id']) ?>" class="btn btn-success"><i class="bi bi-credit-card"></i> Record Payment</a>
        <?php endif; ?>
        <a href="<?= base_url('invoice/invoices/edit/'.$inv['id']) ?>" class="btn btn-outline"><i class="bi bi-pencil"></i> Edit</a>
        <div class="zdrop" id="pdfDrop">
            <button type="button" class="btn btn-outline" onclick="event.stopPropagation();this.closest('.zdrop').classList.toggle('show')">
                <i class="bi bi-file-earmark-pdf"></i> PDF/Print <i class="bi bi-chevron-down" style="font-size:10px"></i>
            </button>
            <div class="zdrop-menu">
                <button type="button" onclick="event.stopPropagation();dlPDF()"><i class="bi bi-file-earmark-pdf" style="color:#dc2626"></i> Download PDF</button>
                <button type="button" onclick="event.stopPropagation();window.print()"><i class="bi bi-printer"></i> Print Invoice</button>
                <div class="zdrop-div"></div>
                <button type="button" onclick="event.stopPropagation();dlExcel()"><i class="bi bi-file-earmark-excel" style="color:#16a34a"></i> Export to Excel</button>
            </div>
        </div>
        <a href="<?= base_url('invoice/invoices') ?>" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
</div>

<!-- ── STATUS BAR ── -->
<div class="inv-statusbar no-print">
    <div><div class="s-lbl">Invoice Amount</div><div class="s-amt">₹<?= number_format($inv['total'],2) ?></div></div>
    <div class="s-div"></div>
    <div><div class="s-lbl">Amount Paid</div><div class="s-amt" style="color:#16a34a">₹<?= number_format($inv['paid_amount'],2) ?></div></div>
    <div class="s-div"></div>
    <div><div class="s-lbl">Balance Due</div><div class="s-amt" style="color:#dc2626">₹<?= number_format($inv['balance_due'],2) ?></div></div>
    <div style="margin-left:auto"><span class="badge badge-<?= esc($inv['status']) ?>" style="font-size:12px;padding:5px 14px"><?= strtoupper(esc($inv['status'])) ?></span></div>
</div>

<!-- ══════════════════════════════════════
     INVOICE DOCUMENT
══════════════════════════════════════════ -->
<div id="printArea">
<div class="tax-invoice" id="invDoc">

    <?php if(strtolower($inv['status'])==='draft'): ?>
    <div class="draft-stamp">Draft</div>
    <?php endif; ?>

    <!-- Customize button inside invoice -->
    <div class="inv-gear no-print">
        <div class="zdrop" id="custDrop">
            <button type="button" style="background:#3483fa;color:#fff;border:none;border-radius:6px;padding:7px 13px;font-size:13px;font-weight:500;cursor:pointer;display:flex;align-items:center;gap:6px" onclick="event.stopPropagation();this.closest('.zdrop').classList.toggle('show')">
                <i class="bi bi-gear"></i> Customize <i class="bi bi-chevron-down" style="font-size:10px"></i>
            </button>
            <div class="zdrop-menu" style="min-width:230px">
                <div class="zdrop-label">Spreadsheet Template</div>
                <button type="button" onclick="event.stopPropagation();openS('tpl')" style="background:#3483fa;color:#fff;margin:2px 8px 8px;width:calc(100% - 16px);border-radius:6px;justify-content:center">Change Template</button>
                <button type="button" onclick="event.stopPropagation();openS('header')"><i class="bi bi-layout-text-window-reverse"></i> Edit Template</button>
                <div class="zdrop-div"></div>
                <button type="button" onclick="event.stopPropagation();openS('logo')"><i class="bi bi-image"></i> Update Logo &amp; Address</button>
                <button type="button" onclick="event.stopPropagation();openS('txn')"><i class="bi bi-card-list"></i> Transaction Details</button>
                <button type="button" onclick="event.stopPropagation();openS('table')"><i class="bi bi-table"></i> Item Table</button>
                <button type="button" onclick="event.stopPropagation();openS('total')"><i class="bi bi-calculator"></i> Total Section</button>
                <button type="button" onclick="event.stopPropagation();openS('other')"><i class="bi bi-three-dots"></i> Other Details</button>
            </div>
        </div>
    </div>

    <div class="inv-body">
    <div class="inv-frame">

        <!-- Company + Title -->
        <div class="inv-top-row">
            <div class="inv-company">
                <div id="logoBox"></div>
                <h2 id="co-name"><?= esc(session()->get('uname') ?? 'Admin') ?></h2>
                <div id="co-addr"></div>
                <div id="co-state"><?= esc($inv['b_state'] ?? '') ?></div>
                <div id="co-country">India</div>
                <div id="co-phone"></div>
                <div id="co-email"><?= esc($inv['cemail'] ?? '') ?></div>
                <div id="co-web"></div>
            </div>
            <div class="inv-title" id="inv-title-text">TAX INVOICE</div>
        </div>

        <!-- Invoice Meta -->
        <div class="inv-meta-grid">
            <div class="inv-meta-left">
                <div class="meta-row"><span>#</span><span class="v"><?= esc($inv['invoice_number']) ?></span></div>
                <div class="meta-row"><span>Invoice Date</span><span class="v"><?= date('d/m/Y',strtotime($inv['invoice_date'])) ?></span></div>
                <div class="meta-row"><span>Terms</span><span class="v"><?= esc(str_replace('_',' ',ucfirst($inv['payment_terms']))) ?></span></div>
                <div class="meta-row"><span>Due Date</span><span class="v"><?= date('d/m/Y',strtotime($inv['due_date'])) ?></span></div>
                <?php if($inv['reference']): ?><div class="meta-row"><span>P.O.#</span><span class="v"><?= esc($inv['reference']) ?></span></div><?php endif; ?>
                <?php if($inv['subject']): ?><div class="meta-row"><span>Subject</span><span class="v"><?= esc($inv['subject']) ?></span></div><?php endif; ?>
            </div>
            <div></div>
        </div>

        <!-- Bill To / Ship To -->
        <div class="addr-grid">
            <div>
                <div class="addr-title">Bill To</div>
                <div class="addr-body">
                    <span class="cname-link"><?= esc($inv['cname']) ?></span><br>
                    <?php if($inv['b_address1']): ?><?= esc($inv['b_address1']) ?><br><?php endif; ?>
                    <?php if($inv['b_address2']): ?><?= esc($inv['b_address2']) ?><br><?php endif; ?>
                    <?php if($inv['b_city']): ?><?= esc($inv['b_city']) ?>, <?= esc($inv['b_state']) ?> <?= esc($inv['b_zip']) ?><br><?php endif; ?>
                    <?= esc($inv['b_country'] ?? 'India') ?>
                    <?php if($inv['cgstin']): ?><br><strong>GSTIN:</strong> <?= esc($inv['cgstin']) ?><?php endif; ?>
                </div>
            </div>
            <div>
                <div class="addr-title">Ship To</div>
                <div class="addr-body" id="ship-to">
                    <?= esc($inv['cname']) ?><br>
                    <?php if($inv['b_address1']): ?><?= esc($inv['b_address1']) ?><br><?php endif; ?>
                    <?php if($inv['b_city']): ?><?= esc($inv['b_city']) ?>, <?= esc($inv['b_state']) ?><br><?php endif; ?>
                    <?= esc($inv['b_country'] ?? 'India') ?>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-tbl" id="itmTbl">
            <thead>
                <tr>
                    <th style="width:34px;text-align:center">#</th>
                    <th id="th-item">Item &amp; Description</th>
                    <th style="width:68px;text-align:right" id="th-qty">Qty</th>
                    <th style="width:88px;text-align:right" id="th-rate">Rate</th>
                    <th style="width:105px;text-align:right" id="th-amt">Amount</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($items as $idx=>$item): ?>
            <tr>
                <td style="text-align:center"><?= $idx+1 ?></td>
                <td><div class="it-name"><?= esc($item['item_name']) ?></div><?php if($item['description']): ?><div class="it-desc"><?= esc($item['description']) ?></div><?php endif; ?></td>
                <td style="text-align:right"><?= number_format($item['qty'],2) ?></td>
                <td style="text-align:right"><?= number_format($item['rate'],2) ?></td>
                <td style="text-align:right"><?= number_format($item['amount'],2) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Summary: Words + Totals -->
        <div class="summary-grid">
            <div class="words-box" id="words-section">
                <div class="wl">Total In Words</div>
                <div class="wv" id="words-val"><?= esc($total_words) ?></div>
                <br>
                <div class="wl">Notes</div>
                <div id="notes-val"><?= $inv['customer_notes']?nl2br(esc($inv['customer_notes'])):'Thank you for your business.' ?></div>
            </div>
            <div class="totals-box">
                <div class="tot-r" id="row-subtotal"><span>Sub Total</span><span><?= number_format($inv['sub_total'],2) ?></span></div>
                <?php if($inv['discount_amount']>0): ?>
                <div class="tot-r" id="row-discount"><span>Discount</span><span>-<?= number_format($inv['discount_amount'],2) ?></span></div>
                <?php endif; ?>
                <?php if($inv['tax_total']>0): ?>
                <div class="tot-r" id="row-tax"><span>Tax</span><span><?= number_format($inv['tax_total'],2) ?></span></div>
                <?php endif; ?>
                <div class="tot-r"><span>Total</span><span>₹<?= number_format($inv['total'],2) ?></span></div>
                <?php if($inv['paid_amount']>0): ?>
                <div class="tot-r" id="row-paid"><span>Payment Made</span><span>(-) <?= number_format($inv['paid_amount'],2) ?></span></div>
                <?php endif; ?>
                <div class="tot-r" id="row-balance"><span>Balance Due</span><span>₹<?= number_format($inv['balance_due'],2) ?></span></div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-grid">
            <div class="footer-left" id="terms-val">
                <?php if($inv['terms']): ?><strong>Terms &amp; Conditions</strong><br><?= nl2br(esc($inv['terms'])) ?><?php else: ?>Thank you for your business.<?php endif; ?>
            </div>
            <div class="footer-right" id="sig-block">Authorized Signature</div>
        </div>

    </div><!-- .inv-frame -->
    </div><!-- .inv-body -->
</div><!-- .tax-invoice -->
</div><!-- #printArea -->

<!-- Payments -->
<?php if(!empty($payments)): ?>
<div class="card no-print" style="margin-top:20px">
    <div class="card-header"><h5>Payments Received</h5></div>
    <div class="table-wrap">
        <table class="ztable">
            <thead><tr><th>Payment#</th><th>Date</th><th>Mode</th><th>Reference</th><th>Amount</th></tr></thead>
            <tbody>
            <?php foreach($payments as $p): ?>
            <tr>
                <td style="font-weight:600;color:#5065e8"><?= esc($p['payment_number']) ?></td>
                <td><?= esc($p['payment_date']) ?></td>
                <td><?= ucfirst(esc($p['payment_mode'])) ?></td>
                <td><?= $p['reference']?esc($p['reference']):'-' ?></td>
                <td style="color:#16a34a;font-weight:600">₹<?= number_format($p['amount'],2) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- ══════════════════════════════════════
     SETTINGS MODAL
══════════════════════════════════════════ -->
<div class="s-overlay no-print" id="sModal" onclick="if(event.target===this)closeS()">
<div class="s-box">
    <div class="s-head">
        <h3 id="s-modal-title">Edit Template</h3>
        <button type="button" class="s-close" onclick="closeS()">&times;</button>
    </div>
    <div class="s-wrap">

        <!-- Left Tabs -->
        <div class="s-sidebar">
            <div class="s-tab on" data-tab="tpl" onclick="switchTab('tpl')"><i class="bi bi-grid-1x2"></i>Templates</div>
            <div class="s-tab" data-tab="header" onclick="switchTab('header')"><i class="bi bi-layout-text-window-reverse"></i>Header &amp; Footer</div>
            <div class="s-tab" data-tab="logo" onclick="switchTab('logo')"><i class="bi bi-image"></i>Logo &amp; Address</div>
            <div class="s-tab" data-tab="txn" onclick="switchTab('txn')"><i class="bi bi-card-list"></i>Transaction Details</div>
            <div class="s-tab" data-tab="table" onclick="switchTab('table')"><i class="bi bi-table"></i>Table</div>
            <div class="s-tab" data-tab="total" onclick="switchTab('total')"><i class="bi bi-calculator"></i>Total</div>
            <div class="s-tab" data-tab="other" onclick="switchTab('other')"><i class="bi bi-three-dots"></i>Other Details</div>
        </div>

        <!-- Panels -->
        <div class="s-content">

            <!-- ① Templates -->
            <div class="s-panel on" id="p-tpl">
                <div class="s-ptitle">Choose Template</div>
                <input class="si" placeholder="Search Template..." oninput="filterTpl(this.value)" style="margin-bottom:14px">
                <div class="tpl-grid">
                    <div class="tpl-card on" data-t="spreadsheet" onclick="applyTpl('spreadsheet',this)">
                        <div class="tpl-prev h-wave"></div>
                        <div class="tpl-sel">★ SELECTED</div>
                        <div class="tpl-name">Spreadsheet Template</div>
                    </div>
                    <div class="tpl-card" data-t="clean" onclick="applyTpl('clean',this)">
                        <div class="tpl-prev h-teal" style="color:#fff;font-size:20px;font-weight:300;letter-spacing:3px">INVOICE</div>
                        <div class="tpl-name">Clean Template</div>
                    </div>
                    <div class="tpl-card" data-t="classic" onclick="applyTpl('classic',this)">
                        <div class="tpl-prev h-navy" style="color:#fff;font-size:16px;font-weight:700;letter-spacing:2px">TAX INVOICE</div>
                        <div class="tpl-name">Classic Template</div>
                    </div>
                    <div class="tpl-card" data-t="modern" onclick="applyTpl('modern',this)">
                        <div class="tpl-prev h-purple" style="color:#fff;font-size:18px;font-weight:300;letter-spacing:4px">INVOICE</div>
                        <div class="tpl-name">Modern Template</div>
                    </div>
                    <div class="tpl-card" data-t="minimal" onclick="applyTpl('minimal',this)">
                        <div class="tpl-prev h-minimal" style="color:#374151;font-size:16px;font-weight:600">TAX INVOICE</div>
                        <div class="tpl-name">Minimal Template</div>
                    </div>
                    <div class="tpl-card" data-t="bold" onclick="applyTpl('bold',this)">
                        <div class="tpl-prev h-rose" style="color:#fff;font-size:16px;font-weight:800;letter-spacing:1px">TAX INVOICE</div>
                        <div class="tpl-name">Bold Template</div>
                    </div>
                </div>
            </div>

            <!-- ② Header & Footer -->
            <div class="s-panel" id="p-header">
                <div class="s-ptitle">Header &amp; Footer</div>

                <div class="s-sub">Background Image / Style</div>
                <div class="hdr-grid">
                    <div class="hdr-tile h-wave on" onclick="applyHdr('wave',this)" title="Wave"><div class="tile-lbl">Wave</div></div>
                    <div class="hdr-tile h-teal" onclick="applyHdr('teal',this)" title="Teal"><div class="tile-lbl">Teal</div></div>
                    <div class="hdr-tile h-green" onclick="applyHdr('green',this)" title="Green"><div class="tile-lbl">Green</div></div>
                    <div class="hdr-tile h-purple" onclick="applyHdr('purple',this)" title="Purple"><div class="tile-lbl">Purple</div></div>
                    <div class="hdr-tile h-orange" onclick="applyHdr('orange',this)" title="Orange"><div class="tile-lbl">Orange</div></div>
                    <div class="hdr-tile h-navy" onclick="applyHdr('navy',this)" title="Navy"><div class="tile-lbl">Navy</div></div>
                    <div class="hdr-tile h-rose" onclick="applyHdr('rose',this)" title="Rose"><div class="tile-lbl">Rose</div></div>
                    <div class="hdr-tile h-bubble" onclick="applyHdr('bubble',this)" title="Bubble"><div class="tile-lbl">Bubble</div></div>
                    <div class="hdr-tile h-dots" onclick="applyHdr('dots',this)" title="Dots"><div class="tile-lbl">Dots</div></div>
                    <div class="hdr-tile h-stripe" onclick="applyHdr('stripe',this)" title="Stripe"><div class="tile-lbl">Stripe</div></div>
                    <div class="hdr-tile h-minimal" onclick="applyHdr('minimal',this)" title="Minimal"><div class="tile-lbl">Minimal</div></div>
                    <div class="hdr-tile h-none" onclick="applyHdr('none',this)">None<div class="tile-lbl">None</div></div>
                </div>

                <div class="sdiv"></div>
                <div class="s-sub">Paper Size</div>
                <div class="paper-opts">
                    <div class="paper-opt" onclick="setPaper('a4',this)">A4</div>
                    <div class="paper-opt on" onclick="setPaper('letter',this)">Letter</div>
                    <div class="paper-opt" onclick="setPaper('a5',this)">A5</div>
                </div>

                <div class="s-sub">Margins (inches)</div>
                <div class="margin-grid">
                    <div class="margin-item"><label>Top</label><input type="number" value="0.7" step="0.1" min="0" id="mg-top"></div>
                    <div class="margin-item"><label>Bottom</label><input type="number" value="0.7" step="0.1" min="0" id="mg-bottom"></div>
                    <div class="margin-item"><label>Left</label><input type="number" value="0.55" step="0.1" min="0" id="mg-left"></div>
                    <div class="margin-item"><label>Right</label><input type="number" value="0.4" step="0.1" min="0" id="mg-right"></div>
                </div>

                <div class="sdiv"></div>
                <div class="s-sub">Footer</div>
                <div class="sc-row"><input type="checkbox" id="show-sig" checked onchange="document.getElementById('sig-block').style.display=this.checked?'flex':'none'"><label for="show-sig">Show Authorized Signature</label></div>
                <div class="sg sg-2">
                    <div><label class="sl">Signature Label</label><input class="si" id="sig-label" value="Authorized Signature" oninput="document.getElementById('sig-block').textContent=this.value"></div>
                </div>
            </div>

            <!-- ③ Logo & Address -->
            <div class="s-panel" id="p-logo">
                <div class="s-ptitle">Logo &amp; Organization Address</div>
                <div class="s-sub">Organization Logo</div>
                <div class="logo-zone" onclick="document.getElementById('logo-file').click()">
                    <div id="logo-drop-inner">
                        <i class="bi bi-cloud-upload" style="font-size:26px;color:#9ca3af;display:block;margin-bottom:6px"></i>
                        <div style="font-size:13px;color:#6b7280">Click to upload your logo</div>
                        <div style="font-size:11px;color:#9ca3af;margin-top:3px">PNG, JPG, GIF · max 1MB · 240×240px recommended</div>
                    </div>
                </div>
                <input type="file" id="logo-file" accept="image/*" style="display:none" onchange="uploadLogo(event)">
                <div class="s-sub">Resize Logo</div>
                <input type="range" id="logo-size" min="40" max="160" value="100" style="width:100%" oninput="resizeLogo(this.value)">
                <div class="sdiv"></div>
                <div class="s-sub">Organization Details</div>
                <div class="sc-row"><input type="checkbox" id="show-co-name" checked onchange="document.getElementById('co-name').style.display=this.checked?'block':'none'"><label for="show-co-name">Show Organization Name</label></div>
                <div class="sg sg-2">
                    <div><label class="sl">Organization Name</label><input class="si" id="org-name" value="<?= esc(session()->get('uname')??'') ?>" oninput="document.getElementById('co-name').textContent=this.value"></div>
                    <div><label class="sl">Phone</label><input class="si" id="org-phone" oninput="document.getElementById('co-phone').innerHTML=this.value?this.value+'<br>':''"></div>
                </div>
                <div class="sg sg-2">
                    <div><label class="sl">Email</label><input class="si" id="org-email" value="<?= esc($inv['cemail']??'') ?>" oninput="document.getElementById('co-email').textContent=this.value"></div>
                    <div><label class="sl">Website</label><input class="si" id="org-web" oninput="document.getElementById('co-web').innerHTML=this.value?'<br>'+this.value:''"></div>
                </div>
                <div><label class="sl">Street Address</label><input class="si" id="org-street" style="margin-bottom:10px" oninput="updateCoAddr()"></div>
                <div class="sg sg-3">
                    <div><label class="sl">City</label><input class="si" id="org-city" oninput="updateCoAddr()"></div>
                    <div><label class="sl">State</label><input class="si" id="org-state" value="<?= esc($inv['b_state']??'') ?>" oninput="document.getElementById('co-state').textContent=this.value"></div>
                    <div><label class="sl">Pin Code</label><input class="si" id="org-pin" oninput="updateCoAddr()"></div>
                </div>
                <div class="sc-row"><input type="checkbox" id="show-co-addr" checked onchange="document.getElementById('co-addr').style.display=this.checked?'block':'none'"><label for="show-co-addr">Show Organization Address</label></div>
            </div>

            <!-- ④ Transaction Details -->
            <div class="s-panel" id="p-txn">
                <div class="s-ptitle">Transaction Details</div>
                <div class="s-sub">Invoice Number Format</div>
                <div class="sg sg-2">
                    <div><label class="sl">Prefix</label><input class="si" id="inv-prefix" value="INV-"></div>
                    <div><label class="sl">Next Number</label><input class="si" id="inv-next" value="<?= intval(substr($inv['invoice_number'],4))+1 ?>"></div>
                </div>
                <div class="sdiv"></div>
                <div class="s-sub">Show / Hide Fields</div>
                <div class="sc-row"><input type="checkbox" id="f-invdate" checked><label for="f-invdate">Invoice Date</label></div>
                <div class="sc-row"><input type="checkbox" id="f-duedate" checked><label for="f-duedate">Due Date</label></div>
                <div class="sc-row"><input type="checkbox" id="f-terms" checked><label for="f-terms">Payment Terms</label></div>
                <div class="sc-row"><input type="checkbox" id="f-ref" checked><label for="f-ref">Reference# / P.O.#</label></div>
                <div class="sc-row"><input type="checkbox" id="f-subject" checked><label for="f-subject">Subject</label></div>
                <div class="sc-row"><input type="checkbox" id="f-shipto" checked onchange="document.querySelector('.addr-grid>div:last-child').style.display=this.checked?'block':'none'"><label for="f-shipto">Ship To Address</label></div>
                <div class="sc-row"><input type="checkbox" id="f-gstin" checked><label for="f-gstin">Customer GSTIN</label></div>
                <div class="sdiv"></div>
                <div class="s-sub">Field Labels</div>
                <div class="sg sg-2">
                    <div><label class="sl">Invoice Title Label</label><input class="si" id="title-label" value="TAX INVOICE" oninput="document.getElementById('inv-title-text').textContent=this.value"></div>
                    <div><label class="sl">Bill To Label</label><input class="si" id="billto-label" value="Bill To" oninput="document.querySelectorAll('.addr-title')[0].textContent=this.value"></div>
                </div>
            </div>

            <!-- ⑤ Table -->
            <div class="s-panel" id="p-table">
                <div class="s-ptitle">Item Table</div>
                <div class="s-sub">Column Labels</div>
                <div class="sg sg-2">
                    <div><label class="sl">Item Column</label><input class="si" id="col-item" value="Item &amp; Description" oninput="document.getElementById('th-item').textContent=this.value"></div>
                    <div><label class="sl">Qty Column</label><input class="si" id="col-qty" value="Qty" oninput="document.getElementById('th-qty').textContent=this.value"></div>
                </div>
                <div class="sg sg-2">
                    <div><label class="sl">Rate Column</label><input class="si" id="col-rate" value="Rate" oninput="document.getElementById('th-rate').textContent=this.value"></div>
                    <div><label class="sl">Amount Column</label><input class="si" id="col-amt" value="Amount" oninput="document.getElementById('th-amt').textContent=this.value"></div>
                </div>
                <div class="sdiv"></div>
                <div class="s-sub">Show / Hide Columns</div>
                <div class="sc-row"><input type="checkbox" id="col-show-qty" checked onchange="toggleTableCol('th-qty',this.checked)"><label for="col-show-qty">Show Quantity</label></div>
                <div class="sc-row"><input type="checkbox" id="col-show-rate" checked onchange="toggleTableCol('th-rate',this.checked)"><label for="col-show-rate">Show Rate</label></div>
                <div class="sc-row"><input type="checkbox" id="col-show-disc" checked><label for="col-show-disc">Show Discount</label></div>
                <div class="sdiv"></div>
                <div class="s-sub">Row Style</div>
                <div class="sc-row"><input type="checkbox" id="alt-row" onchange="applyAltRow(this.checked)"><label for="alt-row">Alternate row background color</label></div>
                <div class="sg sg-2">
                    <div><label class="sl">Header Background</label><input type="color" value="#ffffff" id="th-bg" oninput="document.querySelectorAll('#itmTbl th').forEach(t=>t.style.background=this.value)" style="width:100%;height:38px;border-radius:6px;border:1px solid #d1d5db;cursor:pointer"></div>
                    <div><label class="sl">Header Text Color</label><input type="color" value="#111111" id="th-color" oninput="document.querySelectorAll('#itmTbl th').forEach(t=>t.style.color=this.value)" style="width:100%;height:38px;border-radius:6px;border:1px solid #d1d5db;cursor:pointer"></div>
                </div>
            </div>

            <!-- ⑥ Total -->
            <div class="s-panel" id="p-total">
                <div class="s-ptitle">Total Section</div>
                <div class="sc-row"><input type="checkbox" id="show-subtotal" checked onchange="toggleTotRow('row-subtotal',this.checked)"><label for="show-subtotal">Show Sub Total</label></div>
                <div class="sc-row"><input type="checkbox" id="show-discount" checked onchange="toggleTotRow('row-discount',this.checked)"><label for="show-discount">Show Discount</label></div>
                <div class="sc-row"><input type="checkbox" id="show-tax" checked onchange="toggleTotRow('row-tax',this.checked)"><label for="show-tax">Show Tax Details</label></div>
                <div class="sc-row"><input type="checkbox" id="show-paid" checked onchange="toggleTotRow('row-paid',this.checked)"><label for="show-paid">Show Payment Made</label></div>
                <div class="sc-row"><input type="checkbox" id="show-balance" checked onchange="toggleTotRow('row-balance',this.checked)"><label for="show-balance">Show Balance Due</label></div>
                <div class="sdiv"></div>
                <div class="s-sub">Total In Words</div>
                <div class="sc-row"><input type="checkbox" id="show-words" checked onchange="document.getElementById('words-section').style.display=this.checked?'block':'none'"><label for="show-words">Show Total In Words</label></div>
                <div class="sdiv"></div>
                <div class="s-sub">Currency</div>
                <div class="sg sg-2">
                    <div><label class="sl">Currency Symbol</label><input class="si" id="curr-sym" value="₹" style="max-width:90px"></div>
                    <div><label class="sl">Decimal Places</label><select class="si"><option value="2" selected>2</option><option value="0">0</option><option value="3">3</option></select></div>
                </div>
            </div>

            <!-- ⑦ Other Details -->
            <div class="s-panel" id="p-other">
                <div class="s-ptitle">Other Details</div>
                <div class="s-sub">Notes &amp; Terms</div>
                <div style="margin-bottom:14px">
                    <label class="sl">Customer Notes</label>
                    <textarea class="sta" id="notes-input"><?= esc($inv['customer_notes']??'Thank you for your business.') ?></textarea>
                </div>
                <div style="margin-bottom:14px">
                    <label class="sl">Terms &amp; Conditions</label>
                    <textarea class="sta" id="terms-input"><?= esc($inv['terms']??'') ?></textarea>
                </div>
                <div class="sdiv"></div>
                <div class="s-sub">Signature</div>
                <div class="logo-zone" onclick="document.getElementById('sig-img-file').click()" style="margin-bottom:10px">
                    <div id="sig-img-area">
                        <i class="bi bi-pen" style="font-size:22px;color:#9ca3af;display:block;margin-bottom:5px"></i>
                        <div style="font-size:13px;color:#6b7280">Upload signature image</div>
                        <div style="font-size:11px;color:#9ca3af;margin-top:2px">PNG, JPG · transparent background preferred</div>
                    </div>
                </div>
                <input type="file" id="sig-img-file" accept="image/*" style="display:none" onchange="uploadSig(event)">
                <div class="sdiv"></div>
                <div class="s-sub">Custom Fields</div>
                <table class="ztable" style="width:100%">
                    <thead><tr><th>Field</th><th>Show in PDF</th><th>Status</th></tr></thead>
                    <tbody>
                        <tr><td>Discount</td><td><input type="checkbox" checked></td><td style="color:#16a34a">Active</td></tr>
                        <tr><td>Terms &amp; Conditions</td><td><input type="checkbox" checked></td><td style="color:#16a34a">Active</td></tr>
                        <tr><td>Subject</td><td><input type="checkbox" checked></td><td style="color:#16a34a">Active</td></tr>
                        <tr><td>Reference#</td><td><input type="checkbox" checked></td><td style="color:#16a34a">Active</td></tr>
                        <tr><td>Authorized Signature</td><td><input type="checkbox" checked></td><td style="color:#16a34a">Active</td></tr>
                    </tbody>
                </table>
            </div>

        </div><!-- .s-content -->
    </div><!-- .s-wrap -->
    <div class="s-foot">
        <button type="button" class="btn-sav" onclick="saveSettings()"><i class="bi bi-check2"></i> Save</button>
        <button type="button" class="btn-can" onclick="closeS()">Cancel</button>
    </div>
</div><!-- .s-box -->
</div><!-- .s-overlay -->

<script>
// ── Modal ─────────────────────────────────────────────────
function openS(tab) {
    document.getElementById('sModal').classList.add('open');
    switchTab(tab || 'tpl');
    document.querySelectorAll('.zdrop').forEach(d => d.classList.remove('show'));
}
function closeS() { document.getElementById('sModal').classList.remove('open'); }
function switchTab(t) {
    document.querySelectorAll('.s-tab').forEach(el => el.classList.toggle('on', el.dataset.tab === t));
    document.querySelectorAll('.s-panel').forEach(el => el.classList.toggle('on', el.id === 'p-' + t));
}

// Close dropdowns on outside click
document.addEventListener('click', e => {
    if (!e.target.closest('.zdrop')) document.querySelectorAll('.zdrop').forEach(d => d.classList.remove('show'));
});

// ── Header Backgrounds ────────────────────────────────────
const HDR = {
    wave: `radial-gradient(160px 36px at 22% 2px,transparent 55%,#fff 57% 62%,transparent 64%),radial-gradient(360px 70px at 80% 30px,transparent 57%,#0b7285 58% 60%,transparent 62%),linear-gradient(160deg,#0b8fa3 0 58%,transparent 58%),#08a6bf`,
    teal: `linear-gradient(135deg,#0ea5e9,#0284c7)`,
    green: `linear-gradient(135deg,#059669,#047857)`,
    purple: `linear-gradient(135deg,#7c3aed,#4c1d95)`,
    orange: `linear-gradient(135deg,#ea580c,#9a3412)`,
    navy: `linear-gradient(160deg,#1e3a5f 0 60%,#0b1e36 60%)`,
    rose: `linear-gradient(135deg,#e11d48,#9f1239)`,
    bubble: `radial-gradient(circle at 12% 44%,#19b2c7 0 10px,transparent 11px),radial-gradient(circle at 24% 30%,#fff 0 9px,#19b2c7 10px,transparent 12px),radial-gradient(circle at 65% 25%,#19b2c7 0 7px,transparent 8px),#fff`,
    dots: `radial-gradient(circle,#0b8fa3 0 3px,transparent 4px) 0 0/18px 18px,#fff`,
    stripe: `repeating-linear-gradient(160deg,#0ea5e9 0 18px,#0284c7 18px 36px)`,
    minimal: `repeating-linear-gradient(90deg,#f1f5f9 0 2px,transparent 2px 20px),#fff`,
    none: `#f8fafc`
};
const LINE_CLR = { wave:'#0b7285', teal:'#0284c7', green:'#047857', purple:'#4c1d95', orange:'#9a3412', navy:'#0b1e36', rose:'#9f1239', bubble:'#19b2c7', dots:'#0b8fa3', stripe:'#0284c7', minimal:'#e5e7eb', none:'#e5e7eb' };

function applyHdr(key, el) {
    const doc = document.getElementById('invDoc');
    doc.style.setProperty('--hdr', HDR[key] || HDR.wave);
    doc.style.setProperty('--hdr-line', LINE_CLR[key] || LINE_CLR.wave);
    document.querySelectorAll('.hdr-tile').forEach(t => t.classList.remove('on'));
    if (el) el.classList.add('on');
}

// ── Templates ─────────────────────────────────────────────
const TPL_MAP = { spreadsheet:'wave', clean:'teal', classic:'navy', modern:'purple', minimal:'minimal', bold:'rose' };
function applyTpl(t, card) {
    document.querySelectorAll('.tpl-card').forEach(c => { c.classList.remove('on'); const s=c.querySelector('.tpl-sel'); if(s)s.remove(); });
    card.classList.add('on');
    const strip = document.createElement('div'); strip.className = 'tpl-sel'; strip.textContent = '★ SELECTED';
    card.insertBefore(strip, card.querySelector('.tpl-name'));
    applyHdr(TPL_MAP[t] || 'wave');
    closeS();
}
function filterTpl(q) {
    document.querySelectorAll('.tpl-card').forEach(c => {
        c.style.display = c.querySelector('.tpl-name').textContent.toLowerCase().includes(q.toLowerCase()) ? '' : 'none';
    });
}

// ── Paper ─────────────────────────────────────────────────
function setPaper(p, el) {
    document.querySelectorAll('.paper-opt').forEach(o => o.classList.remove('on'));
    el.classList.add('on');
}

// ── Logo ──────────────────────────────────────────────────
let logoSize = 100;
function uploadLogo(e) {
    const f = e.target.files[0]; if(!f) return;
    if(f.size > 1048576) { alert('Max 1MB'); return; }
    const r = new FileReader();
    r.onload = ev => {
        const src = ev.target.result;
        document.getElementById('logo-drop-inner').innerHTML = `<img src="${src}" style="max-width:160px;max-height:70px;border:1px solid #e5e7eb;border-radius:4px"> <button onclick="removeLogo()" style="display:block;margin:6px auto 0;background:#dc2626;color:#fff;border:none;border-radius:4px;padding:3px 10px;font-size:12px;cursor:pointer">Remove</button>`;
        document.getElementById('logoBox').innerHTML = `<img id="logo-img" src="${src}" class="inv-logo-img" style="width:${logoSize}px">`;
    };
    r.readAsDataURL(f);
}
function removeLogo() {
    document.getElementById('logo-file').value = '';
    document.getElementById('logo-drop-inner').innerHTML = '<i class="bi bi-cloud-upload" style="font-size:26px;color:#9ca3af;display:block;margin-bottom:6px"></i><div style="font-size:13px;color:#6b7280">Click to upload your logo</div><div style="font-size:11px;color:#9ca3af;margin-top:3px">PNG, JPG, GIF · max 1MB</div>';
    document.getElementById('logoBox').innerHTML = '';
}
function resizeLogo(v) {
    logoSize = v;
    const img = document.getElementById('logo-img');
    if (img) img.style.width = v + 'px';
}

// ── Company Address ───────────────────────────────────────
function updateCoAddr() {
    const s = document.getElementById('org-street').value;
    const c = document.getElementById('org-city').value;
    const p = document.getElementById('org-pin').value;
    let h = '';
    if (s) h += s + '<br>';
    if (c || p) h += [c, p].filter(Boolean).join(', ') + '<br>';
    document.getElementById('co-addr').innerHTML = h;
}

// ── Table ─────────────────────────────────────────────────
function toggleTableCol(thId, show) {
    const idx = document.getElementById(thId).cellIndex;
    document.querySelectorAll('#itmTbl tr').forEach(tr => { if(tr.cells[idx]) tr.cells[idx].style.display = show ? '' : 'none'; });
}
function applyAltRow(on) {
    document.querySelectorAll('#itmTbl tbody tr').forEach((tr, i) => { tr.style.background = (on && i%2===0) ? '#f8f9fc' : ''; });
}

// ── Total rows ────────────────────────────────────────────
function toggleTotRow(id, show) {
    const el = document.getElementById(id);
    if (el) el.style.display = show ? 'grid' : 'none';
}

// ── Signature image ───────────────────────────────────────
function uploadSig(e) {
    const f = e.target.files[0]; if(!f) return;
    const r = new FileReader();
    r.onload = ev => {
        document.getElementById('sig-img-area').innerHTML = `<img src="${ev.target.result}" style="max-width:160px;max-height:60px">`;
        document.getElementById('sig-block').innerHTML = `<div style="text-align:center"><img src="${ev.target.result}" style="max-width:120px;max-height:50px;display:block;margin:0 auto 4px"><span>${document.getElementById('sig-label')?.value||'Authorized Signature'}</span></div>`;
    };
    r.readAsDataURL(f);
}

// ── Save Settings ─────────────────────────────────────────
function saveSettings() {
    const notes = document.getElementById('notes-input').value;
    const terms = document.getElementById('terms-input').value;
    document.getElementById('notes-val').innerHTML = notes.replace(/\n/g, '<br>');
    document.getElementById('terms-val').innerHTML = terms
        ? '<strong>Terms &amp; Conditions</strong><br>' + terms.replace(/\n/g, '<br>')
        : 'Thank you for your business.';
    closeS();
}

// ── PDF Download ──────────────────────────────────────────
async function dlPDF() {
    const { jsPDF } = window.jspdf;
    const el = document.getElementById('invDoc');
    document.body.classList.add('pdf-go');
    await new Promise(r => setTimeout(r, 400));
    const canvas = await html2canvas(el, { scale: 2, useCORS: true, backgroundColor: '#fff', scrollX: 0, scrollY: 0, windowWidth: el.scrollWidth, windowHeight: el.scrollHeight });
    const img = canvas.toDataURL('image/jpeg', 1.0);
    const pdf = new jsPDF('p', 'mm', 'a4');
    let h = (canvas.height * 210) / canvas.width;
    if (h > 297) h = 297;
    pdf.addImage(img, 'JPEG', 0, 0, 210, h);
    pdf.save('<?= esc($inv["invoice_number"]) ?>.pdf');
    document.body.classList.remove('pdf-go');
}

// ── Excel Export ──────────────────────────────────────────
function dlExcel() {
    const wb = XLSX.utils.book_new();
    const rows = [
        ['TAX INVOICE'], ['Invoice#', '<?= esc($inv["invoice_number"]) ?>'], ['Date', '<?= date("d/m/Y", strtotime($inv["invoice_date"])) ?>'],
        ['Due Date', '<?= date("d/m/Y", strtotime($inv["due_date"])) ?>'], ['Customer', '<?= esc($inv["cname"]) ?>'], [],
        ['#', 'Item', 'Qty', 'Rate', 'Amount']
    ];
    document.querySelectorAll('#itmTbl tbody tr').forEach((tr, i) => {
        const td = tr.querySelectorAll('td');
        if (td.length >= 5) rows.push([i+1, td[1].innerText.trim(), td[2].innerText.trim(), td[3].innerText.trim(), td[4].innerText.trim()]);
    });
    rows.push([], ['','','','Sub Total','₹<?= number_format($inv["sub_total"],2) ?>'], ['','','','Tax','₹<?= number_format($inv["tax_total"],2) ?>'], ['','','','Total','₹<?= number_format($inv["total"],2) ?>'], ['','','','Balance Due','₹<?= number_format($inv["balance_due"],2) ?>']);
    const ws = XLSX.utils.aoa_to_sheet(rows);
    XLSX.utils.book_append_sheet(wb, ws, 'Invoice');
    XLSX.writeFile(wb, '<?= esc($inv["invoice_number"]) ?>.xlsx');
}
</script>
<?= $this->endSection() ?>