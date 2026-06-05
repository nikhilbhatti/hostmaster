<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Login | HostMaster Enterprise</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --accent: #818cf8;
            --bg-dark: #0f172a;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --danger: #e11d48;
        }

        * {
            box-sizing: border-box;
        }

        html {
            width: 100%;
            min-height: 100%;
        }

        body { 
            min-height: 100vh;
            min-height: 100dvh;
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 24px;
            overflow-x: hidden;
            overflow-y: auto;
            background-color: #f8fafc;
            background-image: 
                radial-gradient(circle at 20% 25%, rgba(99, 102, 241, 0.13) 0%, transparent 36%),
                radial-gradient(circle at 80% 75%, rgba(79, 70, 229, 0.12) 0%, transparent 36%),
                linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
        }

        .shape {
            position: fixed;
            filter: blur(75px);
            z-index: -1;
            border-radius: 50%;
            animation: float 18s infinite alternate ease-in-out;
            pointer-events: none;
        }

        .shape-1 {
            width: 360px;
            height: 360px;
            background: rgba(99, 102, 241, 0.18);
            top: -100px;
            left: -90px;
        }

        .shape-2 {
            width: 280px;
            height: 280px;
            background: rgba(129, 140, 248, 0.16);
            bottom: -70px;
            right: -70px;
            animation-delay: -5s;
        }

        @keyframes float {
            0% {
                transform: translate(0, 0) scale(1);
            }

            100% {
                transform: translate(45px, 70px) scale(1.06);
            }
        }

        .login-card { 
            width: 100%;
            max-width: 430px; 
            border-radius: 28px; 
            border: 1px solid rgba(255, 255, 255, 0.9); 
            box-shadow: 0 28px 80px -24px rgba(15, 23, 42, 0.22); 
            background: rgba(255, 255, 255, 0.82); 
            backdrop-filter: blur(22px);
            -webkit-backdrop-filter: blur(22px);
            padding: 42px;
            transition: transform 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-2px);
        }

        .brand-icon-wrapper {
            width: 68px;
            height: 68px;
            background: #fff;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 22px;
            box-shadow: 0 16px 35px -12px rgba(79, 70, 229, 0.35);
            position: relative;
            border: 1px solid #eef2ff;
        }

        .brand-icon-wrapper::after {
            content: "";
            position: absolute;
            inset: -7px;
            border-radius: 24px;
            border: 1px solid rgba(99, 102, 241, 0.12);
        }

        .brand-icon-wrapper i {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .login-header h3 { 
            color: var(--text-dark); 
            font-weight: 800; 
            font-size: 1.78rem;
            letter-spacing: -0.04em;
        }

        .login-header p {
            color: var(--text-muted) !important;
            margin-bottom: 34px !important;
        }

        .input-box {
            position: relative;
            margin-bottom: 20px;
        }

        .input-box > i {
            position: absolute;
            left: 17px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            transition: 0.25s;
            z-index: 2;
        }

        .form-control-custom { 
            width: 100%;
            height: 54px;
            background: rgba(255, 255, 255, 0.86); 
            border: 1.5px solid var(--border); 
            border-radius: 15px;
            padding: 0 50px; 
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-dark);
            transition: all 0.25s ease;
            outline: none;
        }

        .form-control-custom::placeholder {
            color: #94a3b8;
            font-weight: 500;
        }

        .form-control-custom:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.11);
        }

        .form-control-custom:focus + i {
            color: var(--primary);
        }

        .password-toggle {
            position: absolute;
            right: 17px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            z-index: 5;
            transition: 0.25s;
        }

        .password-toggle:hover {
            color: var(--primary);
        }

        .password-toggle i {
            position: static;
            transform: none;
        }

        .form-check-input {
            cursor: pointer;
            border-color: #cbd5e1;
        }

        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .form-check-label {
            color: var(--text-muted) !important;
        }

        .help-link {
            color: var(--primary);
            transition: 0.25s;
        }

        .help-link:hover {
            color: var(--primary-dark);
        }

        .btn-submit { 
            background: linear-gradient(135deg, var(--primary), var(--primary-dark)); 
            color: white; 
            border-radius: 15px; 
            height: 54px;
            padding: 0 16px; 
            font-weight: 800; 
            transition: all 0.25s ease; 
            border: none; 
            width: 100%;
            margin-top: 10px;
            letter-spacing: 0.3px;
            box-shadow: 0 16px 30px -14px rgba(79, 70, 229, 0.75);
        }

        .btn-submit:hover { 
            color: #fff;
            transform: translateY(-2px); 
            box-shadow: 0 22px 36px -16px rgba(79, 70, 229, 0.9);
        }

        .error-toast {
            background: #fff1f2;
            border-radius: 14px;
            padding: 12px 16px;
            color: var(--danger);
            font-size: 0.85rem;
            margin-bottom: 22px;
            border: 1px solid #fecdd3;
            border-left: 5px solid var(--danger);
            display: flex;
            align-items: center;
        }

        .footer-links {
            text-align: center;
            margin-top: 28px;
            font-size: 0.84rem;
            color: var(--text-muted);
        }

        @media (max-width: 768px) {
            body {
                padding: 18px;
            }

            .login-card {
                max-width: 100%;
                padding: 36px 28px;
                border-radius: 24px;
            }

            .login-header h3 {
                font-size: 1.6rem;
            }

            .brand-icon-wrapper {
                width: 62px;
                height: 62px;
                border-radius: 18px;
                margin-bottom: 20px;
            }
        }

        @media (max-width: 500px) {
            body {
                padding: 0;
                align-items: stretch;
                background: #ffffff;
            }

            .shape {
                display: none;
            }

            .login-card {
                min-height: 100vh;
                min-height: 100dvh;
                max-width: 100%;
                border-radius: 0;
                border: none;
                box-shadow: none;
                background: #fff;
                backdrop-filter: none;
                -webkit-backdrop-filter: none;
                padding: 30px 22px;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .login-card:hover {
                transform: none;
            }

            .login-header h3 {
                font-size: 1.48rem;
            }

            .login-header p {
                margin-bottom: 30px !important;
            }

            .form-control-custom {
                font-size: 16px;
                height: 52px;
            }

            .btn-submit {
                height: 52px;
            }

            .d-flex.justify-content-between {
                gap: 12px;
                flex-wrap: wrap;
            }
        }

        @media (max-width: 360px) {
            .login-card {
                padding: 26px 16px;
            }

            .brand-icon-wrapper {
                width: 58px;
                height: 58px;
            }

            .form-control-custom {
                padding-left: 46px;
                padding-right: 46px;
            }

            .input-box > i {
                left: 15px;
            }

            .password-toggle {
                right: 15px;
            }
        }
    </style>
</head>
<body>

<div class="shape shape-1"></div>
<div class="shape shape-2"></div>

<div class="login-card animate__animated animate__fadeIn">
    <div class="text-center login-header">
        <div class="brand-icon-wrapper animate__animated animate__zoomIn">
            <i class="fas fa-fingerprint fa-2x"></i>
        </div>
        <h3 class="mb-1">Admin Login</h3>
        <p class="text-muted small mb-5">Access your HostMaster dashboard</p>
    </div>
    
    <?php if(session()->getFlashdata('error')): ?>
        <div class="error-toast animate__animated animate__headShake">
            <i class="fas fa-exclamation-circle me-3"></i>
            <div><?= session()->getFlashdata('error') ?></div>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('auth/authenticate') ?>" method="POST" autocomplete="off">
        <?= csrf_field() ?>
        
        <div class="input-box">
            <input type="text" name="username" class="form-control-custom" placeholder="Username" required autofocus>
            <i class="fas fa-user"></i>
        </div>

        <div class="input-box">
            <input type="password" name="password" id="password" class="form-control-custom" placeholder="Password" required>
            <i class="fas fa-lock"></i>
            <span class="password-toggle" onclick="togglePass()">
                <i class="fas fa-eye" id="eyeIcon"></i>
            </span>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 px-1">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" style="cursor: pointer;">
                <label class="form-check-label text-muted small" for="remember" style="cursor: pointer; user-select: none;">
                    Remember session
                </label>
            </div>
            <a href="#" class="help-link text-decoration-none small fw-bold opacity-75 hover-opacity-100">Help?</a>
        </div>

        <button type="submit" class="btn btn-submit shadow-sm">
            Sign In to System <i class="fas fa-arrow-right ms-2 small"></i>
        </button>
    </form>

    <div class="footer-links">
        <p class="mb-0">&copy; <?= date('Y') ?> HostMaster Enterprise</p>
        <span class="extra-small opacity-50" style="font-size: 10px;">SECURE END-TO-END ENCRYPTED</span>
    </div>
</div>

<script>
    function togglePass() {
        const passInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        
        if (passInput.type === 'password') {
            passInput.type = 'text';
            eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
            eyeIcon.parentElement.style.color = 'var(--primary)';
        } else {
            passInput.type = 'password';
            eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
            eyeIcon.parentElement.style.color = '#94a3b8';
        }
    }
</script>

</body>
</html>