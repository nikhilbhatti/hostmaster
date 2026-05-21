<?= $this->extend('layout/main') ?>
<?php $page_title = $c ? 'Edit Customer' : 'New Customer'; ?>

<?= $this->section('content') ?>

<style>

.customer-page{
    background:#f6f8fb;
    min-height:100vh;
    padding:24px;
}

.customer-card{
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:10px;
    overflow:hidden;
}

.customer-header{
    padding:28px 30px;
    border-bottom:1px solid #e5e7eb;
}

.customer-title{
    font-size:42px;
    font-weight:400;
    color:#111827;
}

.prefill-bar{
    background:#eef4ff;
    padding:16px 30px;
    border-bottom:1px solid #dbe7ff;
    font-size:15px;
    color:#374151;
}

.prefill-bar a{
    color:#2563eb;
    text-decoration:none;
    font-weight:600;
}

.customer-body{
    padding:36px 30px;
}

.form-row{
    display:grid;
    grid-template-columns:220px 1fr;
    gap:32px;
    margin-bottom:28px;
    align-items:start;
}

.form-label{
    font-size:16px;
    color:#111827;
    padding-top:12px;
}

.red{
    color:#ef4444;
}

.inline-radio{
    display:flex;
    gap:20px;
    align-items:center;
    margin-top:10px;
}

.inline-radio label{
    display:flex;
    align-items:center;
    gap:8px;
    font-size:15px;
}

.name-grid{
    display:grid;
    grid-template-columns:170px 1fr 1fr;
    gap:14px;
}

.input,
.select,
.textarea{
    width:100%;
    border:1px solid #d6dce5;
    border-radius:8px;
    background:#fff;
    font-size:15px;
    color:#111827;
}

.input,
.select{
    height:46px;
    padding:0 14px;
}

.textarea{
    min-height:90px;
    padding:14px;
}

.error-text{
    color:#ef4444;
    font-size:14px;
    margin-top:8px;
}

.phone-grid{
    display:grid;
    grid-template-columns:90px 1fr 90px 1fr;
    gap:10px;
}

.tabs{
    display:flex;
    gap:34px;
    border-bottom:1px solid #e5e7eb;
    margin-top:40px;
}

.tab-btn{
    padding:16px 0;
    cursor:pointer;
    font-size:15px;
    color:#374151;
    border-bottom:3px solid transparent;
}

.tab-btn.active{
    color:#2563eb;
    border-color:#2563eb;
    font-weight:600;
}

.tab-content{
    display:none;
    padding-top:30px;
}

.tab-content.active{
    display:block;
}

.address-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:60px;
}

.address-title{
    font-size:20px;
    font-weight:600;
    margin-bottom:24px;
}

.address-form{
    display:grid;
    grid-template-columns:180px 1fr;
    gap:18px;
    margin-bottom:18px;
    align-items:center;
}

.footer-bar{
    position:sticky;
    bottom:0;
    background:#fff;
    padding:20px 30px;
    border-top:1px solid #e5e7eb;
    display:flex;
    gap:14px;
}

.btn{
    border:none;
    border-radius:8px;
    padding:12px 24px;
    font-size:15px;
    cursor:pointer;
    text-decoration:none;
}

.btn-primary{
    background:#4087f5;
    color:#fff;
}

.btn-light{
    background:#f3f4f6;
    color:#111827;
}

.contact-table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

.contact-table th{
    background:#f4f6fa;
    padding:16px;
    border:1px solid #e5e7eb;
    text-align:left;
    font-size:14px;
    color:#374151;
}

.contact-table td{
    border:1px solid #e5e7eb;
    padding:10px;
}

.small-input,
.small-select{
    width:100%;
    border:none;
    outline:none;
    background:transparent;
    font-size:14px;
    min-height:38px;
}

.add-contact-btn{
    margin-top:18px;
    background:#f3f4f6;
    border:1px solid #d1d5db;
}

.remove-row{
    width:28px;
    height:28px;
    border:none;
    border-radius:50%;
    background:#ef4444;
    color:#fff;
    cursor:pointer;
    font-size:18px;
    line-height:1;
}

.remove-row:hover{
    background:#dc2626;
}

@media(max-width:991px){

.form-row,
.address-form{
    grid-template-columns:1fr;
}

.address-grid{
    grid-template-columns:1fr;
    gap:30px;
}

.name-grid,
.phone-grid{
    grid-template-columns:1fr;
}

.tabs{
    overflow:auto;
}

}

</style>

<div class="customer-page">

<form method="POST" action="<?= base_url('invoice/customers/' . ($c ? 'update/'.$c['id'] : 'store')) ?>">

<?= csrf_field() ?>

<div class="customer-card">

    <div class="customer-header">
        <div class="customer-title">
            <?= $c ? 'Edit Customer' : 'New Customer' ?>
        </div>
    </div>

    <div class="prefill-bar">
        Prefill Customer details from the GST portal using the Customer's GSTIN.
        <a href="#">Prefill ›</a>
    </div>

    <div class="customer-body">

        <!-- CUSTOMER TYPE -->

        <div class="form-row">

            <div class="form-label">
                Customer Type
            </div>

            <div class="inline-radio">

                <label>
                    <input 
                        type="radio"
                        name="customer_type"
                        value="business"
                        <?= ($c['customer_type'] ?? 'business') == 'business' ? 'checked' : '' ?>
                    >
                    Business
                </label>

                <label>
                    <input 
                        type="radio"
                        name="customer_type"
                        value="individual"
                        <?= ($c['customer_type'] ?? '') == 'individual' ? 'checked' : '' ?>
                    >
                    Individual
                </label>

            </div>

        </div>

        <!-- PRIMARY CONTACT -->

        <div class="form-row">

            <div class="form-label">
                Primary Contact
            </div>

            <div class="name-grid">

                <select name="salutation" class="select">

                    <option value="">Salutation</option>

                    <?php foreach(['Mr.','Mrs.','Ms.','Dr.'] as $s): ?>

                    <option 
                        value="<?= $s ?>"
                        <?= ($c['salutation'] ?? '') == $s ? 'selected' : '' ?>
                    >
                        <?= $s ?>
                    </option>

                    <?php endforeach; ?>

                </select>

                <input 
                    type="text"
                    name="first_name"
                    class="input"
                    placeholder="First Name"
                    value="<?= esc($c['first_name'] ?? '') ?>"
                >

                <input 
                    type="text"
                    name="last_name"
                    class="input"
                    placeholder="Last Name"
                    value="<?= esc($c['last_name'] ?? '') ?>"
                >

            </div>

        </div>

        <!-- COMPANY -->

        <div class="form-row">

            <div class="form-label">
                Company Name
            </div>

            <div>
                <input 
                    type="text"
                    name="company_name"
                    class="input"
                    value="<?= esc($c['company_name'] ?? '') ?>"
                >
            </div>

        </div>

        <!-- DISPLAY NAME -->

        <div class="form-row">

            <div class="form-label red">
                Display Name*
            </div>

            <div>

                <input 
                    type="text"
                    name="display_name"
                    class="input"
                    required
                    placeholder="Select or type to add"
                    value="<?= esc($c['display_name'] ?? '') ?>"
                >

                <div class="error-text">
                    Enter the Display Name of your customer.
                </div>

            </div>

        </div>

        <!-- CURRENCY -->

        <div class="form-row">

            <div class="form-label">
                Currency
            </div>

            <div>

                <select name="currency" class="select">
                    <option value="INR">INR- Indian Rupee</option>
                </select>

            </div>

        </div>

        <!-- EMAIL -->

        <div class="form-row">

            <div class="form-label">
                Email Address
            </div>

            <div>

                <input 
                    type="email"
                    name="email"
                    class="input"
                    value="<?= esc($c['email'] ?? '') ?>"
                >

            </div>

        </div>

        <!-- PHONE -->

        <div class="form-row">

            <div class="form-label">
                Phone
            </div>

            <div class="phone-grid">

                <select class="select">
                    <option>+91</option>
                </select>

                <input 
                    type="text"
                    name="work_phone"
                    class="input"
                    placeholder="Work Phone"
                    value="<?= esc($c['work_phone'] ?? '') ?>"
                >

                <select class="select">
                    <option>+91</option>
                </select>

                <input 
                    type="text"
                    name="mobile"
                    class="input"
                    placeholder="Mobile"
                    value="<?= esc($c['mobile'] ?? '') ?>"
                >

            </div>

        </div>

        <!-- LANGUAGE -->

        <div class="form-row">

            <div class="form-label">
                Customer Language
            </div>

            <div>

                <select name="language" class="select">
                    <option value="English">English</option>
                    <option value="Hindi">Hindi</option>
                </select>

            </div>

        </div>

        <!-- TABS -->

        <div class="tabs">

            <div class="tab-btn active" onclick="openTab(event,'details')">
                Other Details
            </div>

            <div class="tab-btn" onclick="openTab(event,'address')">
                Address
            </div>

            <div class="tab-btn" onclick="openTab(event,'contacts')">
                Contact Persons
            </div>

            <div class="tab-btn" onclick="openTab(event,'custom')">
                Custom Fields
            </div>

            <div class="tab-btn" onclick="openTab(event,'remarks')">
                Remarks
            </div>

        </div>

        <!-- DETAILS TAB -->

        <div class="tab-content active" id="details">

            <div class="form-row">

                <div class="form-label">
                    PAN
                </div>

                <div>
                    <input 
                        type="text"
                        name="pan"
                        class="input"
                        value="<?= esc($c['pan'] ?? '') ?>"
                    >
                </div>

            </div>

            <div class="form-row">

                <div class="form-label">
                    Payment Terms
                </div>

                <div>

                    <select name="payment_terms" class="select">
                        <option value="due_on_receipt">Due on Receipt</option>
                        <option value="net15">Net 15</option>
                        <option value="net30">Net 30</option>
                    </select>

                </div>

            </div>

            <div class="form-row">

                <div class="form-label">
                    Enable Portal?
                </div>

                <div>

                    <label style="display:flex;align-items:center;gap:10px;">
                        <input type="checkbox" name="portal_access">
                        Allow portal access for this customer
                    </label>

                </div>

            </div>

        </div>

        <!-- ADDRESS TAB -->

        <div class="tab-content" id="address">

            <div class="address-grid">

                <div>

                    <div class="address-title">
                        Billing Address
                    </div>

                    <?php
                    $billing = [
                        'Attention' => 'b_attention',
                        'Country/Region' => 'b_country',
                        'Address Line 1' => 'b_address1',
                        'Address Line 2' => 'b_address2',
                        'City' => 'b_city',
                        'State' => 'b_state',
                        'Pin Code' => 'b_zip',
                        'Phone' => 'b_phone'
                    ];
                    ?>

                    <?php foreach($billing as $label => $name): ?>

                    <div class="address-form">

                        <div><?= $label ?></div>

                        <div>

                            <input 
                                type="text"
                                name="<?= $name ?>"
                                class="input"
                                value="<?= esc($c[$name] ?? '') ?>"
                            >

                        </div>

                    </div>

                    <?php endforeach; ?>

                </div>

                <div>

                    <div class="address-title">
                        Shipping Address
                    </div>

                    <?php
                    $shipping = [
                        'Attention' => 's_attention',
                        'Country/Region' => 's_country',
                        'Address Line 1' => 's_address1',
                        'Address Line 2' => 's_address2',
                        'City' => 's_city',
                        'State' => 's_state',
                        'Pin Code' => 's_zip',
                        'Phone' => 's_phone'
                    ];
                    ?>

                    <?php foreach($shipping as $label => $name): ?>

                    <div class="address-form">

                        <div><?= $label ?></div>

                        <div>

                            <input 
                                type="text"
                                name="<?= $name ?>"
                                class="input"
                                value="<?= esc($c[$name] ?? '') ?>"
                            >

                        </div>

                    </div>

                    <?php endforeach; ?>

                </div>

            </div>

        </div>

        <!-- CONTACT PERSON TAB -->

        <div class="tab-content" id="contacts">

            <table class="contact-table">

                <thead>
                    <tr>
                        <th>Salutation</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email Address</th>
                        <th>Work Phone</th>
                        <th>Mobile</th>
                        <th width="60">Action</th>
                    </tr>
                </thead>

                <tbody id="contactPersonBody">

                    <tr>

                        <td>
                            <select name="cp_salutation[]" class="small-select">
                                <option value="">--</option>
                                <option>Mr.</option>
                                <option>Mrs.</option>
                                <option>Ms.</option>
                                <option>Dr.</option>
                            </select>
                        </td>

                        <td>
                            <input type="text" class="small-input" name="cp_first_name[]">
                        </td>

                        <td>
                            <input type="text" class="small-input" name="cp_last_name[]">
                        </td>

                        <td>
                            <input type="email" class="small-input" name="cp_email[]">
                        </td>

                        <td>
                            <input type="text" class="small-input" name="cp_work_phone[]">
                        </td>

                        <td>
                            <input type="text" class="small-input" name="cp_mobile[]">
                        </td>

                        <td style="text-align:center;">
                            <button 
                                type="button"
                                class="remove-row"
                                onclick="removeContactRow(this)"
                            >
                                ×
                            </button>
                        </td>

                    </tr>

                </tbody>

            </table>

            <button 
                type="button"
                class="btn btn-light add-contact-btn"
                onclick="addContactPerson()"
            >
                + Add Contact Person
            </button>

        </div>

        <!-- CUSTOM -->

        <div class="tab-content" id="custom">

            <div style="padding:20px 0;color:#6b7280;">
                No custom fields added.
            </div>

        </div>

        <!-- REMARKS -->

        <div class="tab-content" id="remarks">

            <textarea 
                name="notes"
                class="textarea"
                placeholder="Remarks..."
            ><?= esc($c['notes'] ?? '') ?></textarea>

        </div>

    </div>

    <div class="footer-bar">

        <button type="submit" class="btn btn-primary">
            Save
        </button>

        <a href="<?= base_url('invoice/customers') ?>" class="btn btn-light">
            Cancel
        </a>

    </div>

</div>

</form>

</div>

<script>

function openTab(evt,id){

    document.querySelectorAll('.tab-content').forEach(tab=>{
        tab.classList.remove('active');
    });

    document.querySelectorAll('.tab-btn').forEach(btn=>{
        btn.classList.remove('active');
    });

    document.getElementById(id).classList.add('active');

    evt.currentTarget.classList.add('active');

}


function addContactPerson(){

    let html = `
    
    <tr>

        <td>
            <select name="cp_salutation[]" class="small-select">
                <option value="">--</option>
                <option>Mr.</option>
                <option>Mrs.</option>
                <option>Ms.</option>
                <option>Dr.</option>
            </select>
        </td>

        <td>
            <input type="text" class="small-input" name="cp_first_name[]">
        </td>

        <td>
            <input type="text" class="small-input" name="cp_last_name[]">
        </td>

        <td>
            <input type="email" class="small-input" name="cp_email[]">
        </td>

        <td>
            <input type="text" class="small-input" name="cp_work_phone[]">
        </td>

        <td>
            <input type="text" class="small-input" name="cp_mobile[]">
        </td>

        <td style="text-align:center;">
            <button 
                type="button"
                class="remove-row"
                onclick="removeContactRow(this)"
            >
                ×
            </button>
        </td>

    </tr>

    `;

    document
        .getElementById('contactPersonBody')
        .insertAdjacentHTML('beforeend', html);

}


function removeContactRow(btn){

    btn.closest('tr').remove();

}

</script>

<?= $this->endSection() ?>