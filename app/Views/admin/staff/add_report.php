<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <!-- Simple & Clean Report Card -->
            <div class="card border-0 shadow-lg" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold mb-0" style="color: #333;">New Daily Report</h4>
                    <a href="<?= base_url('staff/dashboard') ?>" class="btn-close shadow-none"></a>
                </div>
                
                <div class="card-body p-4">
                    <!-- Alerts -->
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger border-0 small py-2"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>

                    <form action="<?= base_url('staff/save-report') ?>" method="POST">
                        <div class="mb-4">
                            <textarea 
                                name="description" 
                                class="form-control border-0 bg-light p-4 shadow-none" 
                                rows="8" 
                                style="border-radius: 15px; font-size: 1.1rem; resize: none;"
                                placeholder="What did you work on today?..." 
                                required></textarea>
                        </div>

                        <!-- Hidden fields for defaults (Minimal design ke liye) -->
                        <input type="hidden" name="report_date" value="<?= date('Y-m-d') ?>">
                        <input type="hidden" name="task_title" value="Daily Work Update">
                        <input type="hidden" name="status" value="completed">
                        <input type="hidden" name="project_id" value="0">
                        <input type="hidden" name="hours_spent" value="8">

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold py-3 shadow-sm" style="background-color: #6366f1; border: none; border-radius: 12px;">
                                Submit Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Backdrop effect jaisa aapne image mein dikhaya hai */
    body {
        background-color: #f3f4f6 !important;
    }
    
    .form-control::placeholder {
        color: #999;
        font-weight: 400;
    }

    .btn-primary:hover {
        background-color: #4f46e5 !important;
        transform: translateY(-1px);
    }

    /* Animation for smooth entry */
    .card {
        animation: modalFadeUp 0.4s ease-out;
    }

    @keyframes modalFadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
<?= $this->endSection() ?>