<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-5">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-calendar-plus me-2"></i> Request New Leave
                    </h5>
                </div>
                <div class="card-body px-4 pb-4 pt-0">
                    
                    <!-- SUCCESS MESSAGE -->
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center" role="alert">
                            <i class="fas fa-check-circle me-2 fs-4"></i>
                            <div><?= session()->getFlashdata('success') ?></div>
                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- EASY ERROR MESSAGE (Matching your image_f3c314.png) -->
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-dismissible fade show p-3 mb-4 border-0 shadow-sm" role="alert" style="background-color: #fce4e4; border-radius: 12px;">
                            <div class="d-flex align-items-start">
                                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 30px;">
                                    <i class="fas fa-exclamation"></i>
                                </div>
                                <div style="color: #631717;">
                                    <strong class="d-block h6 mb-1">Error: <span class="fw-normal">Insufficient Balance!</span></strong>
                                    <p class="mb-0 small opacity-75">
                                        <?= session()->getFlashdata('error') ?>
                                    </p>
                                </div>
                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" style="padding: 1.25rem;"></button>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('staff/submit-leave') ?>" method="POST">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label small fw-bold text-muted">LEAVE TYPE</label>
                                <select name="leave_type" id="leave_type" class="form-select shadow-none border-2" required>
                                    <option value="">Select Type</option>
                                    <?php foreach($leave_types as $lt): ?>
                                        <option value="<?= $lt['id'] ?>"><?= $lt['leave_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-12 mb-3 d-none" id="duration_wrapper">
                                <label class="form-label small fw-bold text-muted">LEAVE DURATION</label>
                                <select name="leave_duration" id="leave_duration" class="form-select shadow-none border-2">
                                    <option value="full_day">Full Day</option>
                                    <option value="half_day">Half Day</option>
                                    <option value="short_leave">Short Leave (2 Hours)</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted" id="label_from_date">FROM DATE</label>
                                <input type="date" name="from_date" id="from_date" class="form-control shadow-none border-2" required min="<?= date('Y-m-d') ?>">
                            </div>

                            <div class="col-md-6 mb-3" id="to_date_wrapper">
                                <label class="form-label small fw-bold text-muted">TO DATE</label>
                                <input type="date" name="to_date" id="to_date" class="form-control shadow-none border-2" required min="<?= date('Y-m-d') ?>">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label small fw-bold text-muted">REASON FOR LEAVE</label>
                                <textarea name="reason" class="form-control shadow-none border-2" rows="3" placeholder="Enter leave reason here..." required></textarea>
                            </div>

                            <div class="col-md-12 mt-2">
                                <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm fw-bold border-0" style="background: linear-gradient(45deg, #4e73df, #224abe);">
                                    SUBMIT APPLICATION
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card shadow-sm border-0 overflow-hidden">
                <div class="card-header bg-danger text-white py-3 border-0">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-bullhorn me-2"></i> Company Official Holidays Calendar
                    </h6>
                </div>
                <div class="card-body">
                    <div id="staffLeaveCalendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assets -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const leaveTypeSelect = document.getElementById('leave_type');
        const durationWrapper = document.getElementById('duration_wrapper');
        const leaveDurationSelect = document.getElementById('leave_duration');
        const toDateWrapper = document.getElementById('to_date_wrapper');
        const fromDateInput = document.getElementById('from_date');
        const toDateInput = document.getElementById('to_date');
        const labelFromDate = document.getElementById('label_from_date');

        // Logic for Half day / Short leave / Full Day
        leaveTypeSelect.addEventListener('change', function() {
            const selectedText = this.options[this.selectedIndex].text.toLowerCase();
            const options = leaveDurationSelect.options;
            
            for (let i = 0; i < options.length; i++) options[i].style.display = 'block';

            if (selectedText.includes('short')) {
                durationWrapper.classList.remove('d-none');
                toDateWrapper.classList.add('d-none');
                toDateInput.required = false;
                labelFromDate.innerText = "SELECT DATE";
                leaveDurationSelect.value = "short_leave";
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value !== 'short_leave') options[i].style.display = 'none';
                }
            } else if (selectedText.includes('half')) {
                durationWrapper.classList.remove('d-none');
                toDateWrapper.classList.add('d-none');
                toDateInput.required = false;
                labelFromDate.innerText = "SELECT DATE";
                leaveDurationSelect.value = "half_day";
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value !== 'half_day') options[i].style.display = 'none';
                }
            } else {
                durationWrapper.classList.add('d-none'); 
                toDateWrapper.classList.remove('d-none');
                toDateInput.required = true;
                labelFromDate.innerText = "FROM DATE";
                leaveDurationSelect.value = "full_day";
            }

            if (toDateWrapper.classList.contains('d-none')) {
                toDateInput.value = fromDateInput.value;
            }
        });

        fromDateInput.addEventListener('change', function() {
            if (toDateWrapper.classList.contains('d-none')) {
                toDateInput.value = this.value;
            }
        });

        // Calendar Config
        var calendarEl = document.getElementById('staffLeaveCalendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            events: '<?= site_url('staff/get-official-holidays') ?>',
            eventClassNames: 'holiday-event',
            dateClick: function(info) {
                fromDateInput.value = info.dateStr;
                toDateInput.value = info.dateStr;
                fromDateInput.dispatchEvent(new Event('change'));
            }
        });
        calendar.render();
    });
</script>

<style>
    #staffLeaveCalendar { min-height: 520px; background: #fff; padding: 10px; border-radius: 8px; }
    .fc-event.holiday-event { 
        background-color: #dc3545 !important; border: none !important; padding: 5px !important;
        font-weight: bold; color: white !important; font-size: 12px !important; border-radius: 4px;
    }
    .form-control:focus, .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.1);
    }
    .alert { animation: slideIn 0.4s ease-out; }
    @keyframes slideIn {
        from { transform: translateY(-15px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>

<?= $this->endSection() ?>