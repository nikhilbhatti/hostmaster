</main> 

        <footer class="footer py-3 border-top mt-auto" style="background: #ffffff; z-index: 10;">
            <div class="container-fluid px-4">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start">
                        <p class="mb-0 small" style="color: #64748b;">
                            <span class="fw-bold text-dark">HostMaster</span> 
                            <span class="mx-1">&copy;</span> <?= date('Y') ?> 
                            <span class="badge bg-soft-indigo ms-1">hosting crm</span>
                        </p>
                    </div>
                    
                    <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
                        <div class="footer-status-badge">
                            <span class="version-tag">v2.1.0</span>
                            <span class="status-dot-wrapper">
                                <span class="status-dot"></span>
                                System Stable
                            </span>
                            <i class="fas fa-heart text-danger ms-2 animate-pulse"></i>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div> </div> <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Sidebar Toggle Logic for Mobile & Desktop
    $(document).ready(function() {
        $("#sidebarCollapse").on("click", function(e) {
            e.preventDefault();
            $(".wrapper").toggleClass("active");
        });

        // Close sidebar on mobile when clicking outside
        $(document).mouseup(function(e) {
            var container = $("#sidebar");
            if (!container.is(e.target) && container.has(e.target).length === 0 && $(window).width() <= 768) {
                $(".wrapper").removeClass("active");
            }
        });
    });
</script>

<style>
    /* Premium Footer Tweaks */
    .footer {
        border-color: rgba(0,0,0,0.05) !important;
        box-shadow: 0 -5px 15px rgba(0,0,0,0.02);
    }

    .bg-soft-indigo {
        background: rgba(99, 102, 241, 0.1);
        color: #6366f1;
        font-weight: 600;
        font-size: 0.7rem;
        padding: 4px 10px;
        border-radius: 6px;
        text-transform: uppercase;
    }

    .footer-status-badge {
        display: inline-flex;
        align-items: center;
        background: #f8fafc;
        padding: 6px 16px;
        border-radius: 50px;
        border: 1px solid #e2e8f0;
    }

    .version-tag {
        font-size: 0.75rem;
        font-weight: 700;
        color: #94a3b8;
        border-right: 2px solid #e2e8f0;
        padding-right: 12px;
        margin-right: 12px;
        line-height: 1;
    }

    .status-dot-wrapper {
        font-size: 0.75rem;
        font-weight: 600;
        color: #10b981;
        display: flex;
        align-items: center;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        background: #10b981;
        border-radius: 50%;
        margin-right: 8px;
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.4);
    }

    .animate-pulse { 
        animation: heartBeat 1.5s infinite; 
    }
    
    @keyframes heartBeat {
        0%, 28%, 70% { transform: scale(1); }
        14%, 42% { transform: scale(1.3); }
    }

    /* Modern Thin Scrollbar */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { 
        background: #cbd5e1; 
        border-radius: 10px; 
    }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    
    /* Ensure Footer stays at bottom */
    .wrapper { display: flex; min-height: 100vh; }
    #page-content-wrapper { 
        display: flex; 
        flex-direction: column; 
        width: 100%;
        overflow-x: hidden;
    }
</style>