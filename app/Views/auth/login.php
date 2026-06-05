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
            --purple: #8b5cf6;
            --cyan: #22d3ee;
            --dark: #020617;
            --card: rgba(15, 23, 42, 0.72);
            --border: rgba(255, 255, 255, 0.14);
            --text: #f8fafc;
            --muted: #94a3b8;
        }

        * {
            box-sizing: border-box;
        }

        html {
            min-height: 100%;
        }

        body {
            min-height: 100vh;
            min-height: 100dvh;
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text);
            overflow-x: hidden;
            background:
                radial-gradient(circle at 15% 20%, rgba(99,102,241,.35), transparent 28%),
                radial-gradient(circle at 85% 75%, rgba(34,211,238,.20), transparent 28%),
                linear-gradient(135deg, #020617 0%, #0f172a 45%, #1e1b4b 100%);
        }

        .page-wrapper {
            min-height: 100vh;
            min-height: 100dvh;
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            position: relative;
            padding: 28px;
        }

        .grid-bg {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
            background-size: 55px 55px;
            mask-image: linear-gradient(to bottom, rgba(0,0,0,1), rgba(0,0,0,.25));
            pointer-events: none;
        }

        .shape {
            position: fixed;
            border-radius: 999px;
            filter: blur(90px);
            opacity: .75;
            animation: float 15s ease-in-out infinite alternate;
            pointer-events: none;
        }

        .shape-1 {
            width: 360px;
            height: 360px;
            background: rgba(99,102,241,.45);
            top: -100px;
            left: -80px;
        }

        .shape-2 {
            width: 320px;
            height: 320px;
            background: rgba(34,211,238,.28);
            right: -70px;
            bottom: -80px;
            animation-delay: -6s;
        }

        @keyframes float {
            from { transform: translate(0, 0) scale(1); }
            to { transform: translate(45px, 60px) scale(1.08); }
        }

        .left-panel {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 34px;
            border-radius: 34px;
            background:
                linear-gradient(145deg, rgba(255,255,255,.08), rgba(255,255,255,.02));
            border: 1px solid var(--border);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            overflow: hidden;
        }

        .left-panel::after {
            content: "";
            position: absolute;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,.12);
            right: -150px;
            top: -130px;
        }

        .brand-top {
            display: flex;
            align-items: center;
            gap: 14px;
            position: relative;
            z-index: 2;
        }

        .brand-mark {
            width: 52px;
            height: 52px;
            border-radius: 18px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--primary), var(--purple));
            box-shadow: 0 20px 45px rgba(99,102,241,.35);
        }

        .brand-name {
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .hero-content {
            max-width: 590px;
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.12);
            color: #c7d2fe;
            font-size: .82rem;
            font-weight: 700;
            margin-bottom: 22px;
        }

        .hero-content h1 {
            font-size: clamp(2.5rem, 5vw, 5rem);
            line-height: .95;
            letter-spacing: -0.075em;
            font-weight: 800;
            margin-bottom: 22px;
        }

        .gradient-text {
            background: linear-gradient(135deg, #ffffff, #a5b4fc, #67e8f9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-content p {
            color: var(--muted);
            font-size: 1.05rem;
            line-height: 1.75;
            max-width: 520px;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-top: 32px;
            position: relative;
            z-index: 2;
        }

        .stat-card {
            padding: 18px;
            border-radius: 22px;
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.1);
        }

        .stat-card h4 {
            margin: 0;
            font-weight: 800;
            font-size: 1.5rem;
        }

        .stat-card span {
            color: var(--muted);
            font-size: .8rem;
        }

        .right-panel {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .login-card {
            width: 100%;
            max-width: 455px;
            padding: 42px;
            border-radius: 34px;
            background: var(--card);
            border: 1px solid var(--border);
            box-shadow:
                0 40px 100px rgba(0,0,0,.45),
                inset 0 1px 0 rgba(255,255,255,.08);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
        }

        .secure-icon {
            width: 82px;
            height: 82px;
            margin: 0 auto 22px;
            border-radius: 28px;
            display: grid;
            place-items: center;
            position: relative;
            background: linear-gradient(135deg, rgba(99,102,241,.28), rgba(34,211,238,.14));
            border: 1px solid rgba(255,255,255,.14);
            box-shadow: 0 22px 45px rgba(99,102,241,.22);
        }

        .secure-icon::before {
            content: "";
            position: absolute;
            inset: -8px;
            border-radius: 32px;
            border: 1px dashed rgba(129,140,248,.45);
            animation: rotate 16s linear infinite;
        }

        @keyframes rotate {
            to { transform: rotate(360deg); }
        }

        .secure-icon i {
            font-size: 2rem;
            color: #c7d2fe;
        }

        .login-header h3 {
            color: #fff;
            font-weight: 800;
            font-size: 1.9rem;
            letter-spacing: -0.04em;
        }

        .login-header p {
            color: var(--muted) !important;
        }

        .input-box {
            position: relative;
            margin-bottom: 20px;
        }

        .input-box > i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            transition: .3s;
            z-index: 2;
        }

        .form-control-custom {
            width: 100%;
            min-height: 56px;
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 18px;
            background: rgba(255,255,255,.065);
            color: #fff;
            padding: 15px 54px 15px 50px;
            outline: none;
            font-size: .95rem;
            font-weight: 600;
            transition: .3s;
        }

        .form-control-custom::placeholder {
            color: #64748b;
        }

        .form-control-custom:focus {
            border-color: rgba(129,140,248,.85);
            background: rgba(255,255,255,.09);
            box-shadow:
                0 0 0 4px rgba(99,102,241,.16),
                0 16px 35px rgba(0,0,0,.18);
        }

        .form-control-custom:focus + i {
            color: #a5b4fc;
        }

        .password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #64748b;
            z-index: 5;
            transition: .3s;
        }

        .password-toggle:hover {
            color: #c7d2fe;
        }

        .password-toggle i {
            position: static;
            transform: none;
        }

        .form-check-input {
            background-color: rgba(255,255,255,.08);
            border-color: rgba(255,255,255,.18);
        }

        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .help-link {
            color: #a5b4fc;
        }

        .btn-submit {
            width: 100%;
            min-height: 58px;
            border: none;
            border-radius: 18px;
            background: linear-gradient(135deg, var(--primary), var(--purple));
            color: #fff;
            font-weight: 800;
            letter-spacing: .3px;
            box-shadow: 0 22px 40px rgba(99,102,241,.35);
            transition: .35s cubic-bezier(.175,.885,.32,1.275);
        }

        .btn-submit:hover {
            color: #fff;
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 28px 55px rgba(99,102,241,.45);
        }

        .error-toast {
            background: rgba(225,29,72,.12);
            border: 1px solid rgba(225,29,72,.25);
            border-left: 5px solid #fb7185;
            border-radius: 16px;
            padding: 13px 16px;
            color: #fecdd3;
            font-size: .86rem;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
        }

        .footer-links {
            text-align: center;
            margin-top: 28px;
            font-size: .84rem;
            color: var(--muted);
        }

        .footer-links p {
            color: var(--muted);
        }

        @media (max-width: 992px) {
            .page-wrapper {
                grid-template-columns: 1fr;
                padding: 20px;
            }

            .left-panel {
                display: none;
            }

            .right-panel {
                padding: 0;
                min-height: calc(100vh - 40px);
                min-height: calc(100dvh - 40px);
            }
        }

        @media (max-width: 500px) {
            .page-wrapper {
                padding: 0;
            }

            .right-panel {
                min-height: 100vh;
                min-height: 100dvh;
            }

            .login-card {
                max-width: 100%;
                min-height: 100vh;
                min-height: 100dvh;
                border-radius: 0;
                border: none;
                padding: 32px 22px;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .secure-icon {
                width: 68px;
                height: 68px;
                border-radius: 22px;
            }

            .login-header h3 {
                font-size: 1.55rem;
            }

            .login-header p {
                margin-bottom: 32px !important;
            }

            .form-control-custom {
                font-size: 16px;
            }

            .d-flex.justify-content-between {
                gap: 12px;
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>

<div class="grid-bg"></div>
<div class="shape shape-1"></div>
<div class="shape shape-2"></div>

<div class="page-wrapper">

    <section class="left-panel animate__animated animate__fadeInLeft">
        <div class="brand-top">
            <div class="brand-mark">
                <i class="fas fa-server"></i>
            </div>
            <div>
                <div class="brand-name">HostMaster Enterprise</div>
                <small class="text-white-50">Smart Business Control Panel</small>
            </div>
        </div>

        <div class="hero-content">
            <div class="hero-badge">
                <i class="fas fa-shield-halved"></i>
                Secure Admin Access
            </div>

            <h1>
                Manage your system with <span class="gradient-text">confidence.</span>
            </h1>

            <p>
                A premium enterprise dashboard experience for managing hosting, clients,
                billing, reports, backups and business operations from one secure panel.
            </p>

            <div class="stats-row">
                <div class="stat-card">
                    <h4>99.9%</h4>
                    <span>Uptime</span>
                </div>

                <div class="stat-card">
                    <h4>24/7</h4>
                    <span>Secure Access</span>
                </div>

                <div class="stat-card">
                    <h4>Pro</h4>
                    <span>Dashboard</span>
                </div>
            </div>
        </div>

        <div class="text-white-50 small position-relative" style="z-index:2;">
            <i class="fas fa-lock me-2"></i> Protected with secure authentication
        </div>
    </section>

    <section class="right-panel">
        <div class="login-card animate__animated animate__fadeInRight">
            <div class="text-center login-header">
                <div class="secure-icon animate__animated animate__zoomIn">
                    <i class="fas fa-fingerprint"></i>
                </div>

                <h3 class="mb-1">Welcome Back</h3>
                <p class="small mb-5">Sign in to continue to HostMaster dashboard</p>
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
                        <label class="form-check-label text-white-50 small" for="remember" style="cursor: pointer; user-select: none;">
                            Remember session
                        </label>
                    </div>

                    <a href="#" class="help-link text-decoration-none small fw-bold opacity-75">
                        Help?
                    </a>
                </div>

                <button type="submit" class="btn btn-submit">
                    Sign In to System <i class="fas fa-arrow-right ms-2 small"></i>
                </button>
            </form>

            <div class="footer-links">
                <p class="mb-0">&copy; <?= date('Y') ?> HostMaster Enterprise</p>
                <span class="extra-small opacity-50" style="font-size: 10px;">SECURE END-TO-END ENCRYPTED</span>
            </div>
        </div>
    </section>

</div>

<script>
    function togglePass() {
        const passInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (passInput.type === 'password') {
            passInput.type = 'text';
            eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
            eyeIcon.parentElement.style.color = '#a5b4fc';
        } else {
            passInput.type = 'password';
            eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
            eyeIcon.parentElement.style.color = '#64748b';
        }
    }
</script>

</body>
</html>