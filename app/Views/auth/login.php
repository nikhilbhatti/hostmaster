<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Login | HostMaster Enterprise</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --accent: #818cf8;
            --bg-dark: #0f172a;
        }

        body { 
            background-color: #f8fafc;
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(99, 102, 241, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 80% 70%, rgba(79, 70, 229, 0.08) 0%, transparent 40%);
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            overflow: hidden;
        }

        /* Animated Background Shapes */
        .shape {
            position: absolute;
            filter: blur(80px);
            z-index: -1;
            border-radius: 50%;
            animation: float 20s infinite alternate;
        }
        .shape-1 { width: 400px; height: 400px; background: rgba(99, 102, 241, 0.15); top: -100px; left: -100px; }
        .shape-2 { width: 300px; height: 300px; background: rgba(129, 140, 248, 0.1); bottom: -50px; right: -50px; animation-delay: -5s; }

        @keyframes float {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 100px); }
        }

        .login-card { 
            width: 100%;
            max-width: 440px; 
            border-radius: 32px; 
            border: 1px solid rgba(255, 255, 255, 0.8); 
            box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.08); 
            background: rgba(255, 255, 255, 0.7); 
            backdrop-filter: blur(20px);
            padding: 50px;
            transition: transform 0.3s ease;
        }

        .brand-icon-wrapper {
            width: 70px;
            height: 70px;
            background: #fff;
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .brand-icon-wrapper i {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .login-header h3 { 
            color: #1e293b; 
            font-weight: 800; 
            font-size: 1.85rem;
            letter-spacing: -0.03em;
        }

        .input-box {
            position: relative;
            margin-bottom: 24px;
        }

        .input-box i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            transition: 0.3s;
        }

        .form-control-custom { 
            width: 100%;
            background: rgba(255, 255, 255, 0.5); 
            border: 1.5px solid #e2e8f0; 
            border-radius: 16px;
            padding: 14px 18px 14px 50px; 
            font-size: 0.95rem;
            font-weight: 500;
            color: #1e293b;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-control-custom:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .form-control-custom:focus + i {
            color: var(--primary);
        }

        .password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
        }

        .btn-submit { 
            background: var(--primary); 
            color: white; 
            border-radius: 16px; 
            padding: 16px; 
            font-weight: 700; 
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
            border: none; 
            width: 100%;
            margin-top: 15px;
            letter-spacing: 0.5px;
        }

        .btn-submit:hover { 
            background: var(--primary-dark); 
            transform: translateY(-3px); 
            box-shadow: 0 20px 30px -10px rgba(79, 70, 229, 0.4);
        }

        .error-toast {
            background: #fff1f2;
            border-radius: 14px;
            padding: 12px 18px;
            color: #e11d48;
            font-size: 0.85rem;
            margin-bottom: 25px;
            border-left: 5px solid #e11d48;
            display: flex;
            align-items: center;
        }

        .footer-links {
            text-align: center;
            margin-top: 30px;
            font-size: 0.85rem;
            color: #64748b;
        }

        /* Dark Mode support toggle (Optional) */
        @media (max-width: 500px) {
            .login-card { padding: 40px 25px; border-radius: 0; height: 100vh; max-width: 100%; border: none; background: #fff; }
            .shape { display: none; }
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
            <a href="#" class="text-indigo text-decoration-none small fw-bold opacity-75 hover-opacity-100">Help?</a>
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