<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>Sipariş Masanda - Restaurant Management System</title>
    <meta name="description" content="Sipariş Masanda Restaurant Management Application - Giriş">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no, minimal-ui">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="msapplication-tap-highlight" content="no">

    <!-- Modern Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #667eea;
            --primary-dark: #5a6fd8;
            --secondary: #764ba2;
            --accent: #f093fb;
            --success: #10b981;
            --warning: #f59e0b;
            --error: #ef4444;
            --text-dark: #1f2937;
            --text-gray: #6b7280;
            --text-light: #9ca3af;
            --bg-light: #f8fafc;
            --bg-white: #ffffff;
            --border: #e5e7eb;
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-soft: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-success: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --radius: 0.75rem;
            --radius-lg: 1rem;
            --radius-xl: 1.5rem;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            background: var(--gradient-primary);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(120, 219, 226, 0.2) 0%, transparent 50%);
            z-index: -1;
            animation: gradientShift 8s ease-in-out infinite;
        }

        @keyframes gradientShift {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        /* Floating particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .particle:nth-child(1) { left: 20%; animation-delay: 0s; }
        .particle:nth-child(2) { left: 40%; animation-delay: 1s; }
        .particle:nth-child(3) { left: 60%; animation-delay: 2s; }
        .particle:nth-child(4) { left: 80%; animation-delay: 3s; }
        .particle:nth-child(5) { left: 10%; animation-delay: 4s; }

        @keyframes float {
            0%, 100% { 
                transform: translateY(100vh) rotate(0deg); 
                opacity: 0; 
            }
            10% { opacity: 1; }
            90% { opacity: 1; }
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .login-card {
            background: var(--bg-white);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
            overflow: hidden;
            width: 100%;
            max-width: 1000px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 600px;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideUp 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(60px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Left Side - Branding */
        .branding-side {
            background: var(--gradient-primary);
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            padding: 2.5rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            min-height: 600px;
        }

        .branding-side::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
            animation: backgroundMove 20s linear infinite;
        }

        @keyframes backgroundMove {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(-60px, -60px) rotate(360deg); }
        }

        .brand-logo {
            font-size: 3rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1rem;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 2;
            letter-spacing: -0.02em;
            animation: logoGlow 3s ease-in-out infinite alternate;
        }

        @keyframes logoGlow {
            from { text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3); }
            to { text-shadow: 0 4px 30px rgba(255, 255, 255, 0.4); }
        }

        .brand-tagline {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
            font-weight: 400;
            margin-bottom: 2rem;
            position: relative;
            z-index: 2;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-top: 2rem;
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 100%;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.12);
            padding: 1.2rem;
            border-radius: 12px;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
            position: relative;
            overflow: hidden;
            height: auto;
            min-height: 110px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            text-align: left;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-card:nth-child(1) { animation-delay: 0.1s; }
        .feature-card:nth-child(2) { animation-delay: 0.2s; }
        .feature-card:nth-child(3) { animation-delay: 0.3s; }
        .feature-card:nth-child(4) { animation-delay: 0.4s; }
        .feature-card:nth-child(5) { animation-delay: 0.5s; }
        .feature-card:nth-child(6) { animation-delay: 0.6s; }

        .feature-card:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .feature-icon-wrapper {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0.15) 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.8rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
            align-self: flex-start;
        }

        .feature-card:hover .feature-icon-wrapper {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.35) 0%, rgba(255, 255, 255, 0.25) 100%);
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            font-size: 1.3rem;
            color: white;
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1);
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
        }

        .feature-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .feature-content h4 {
            color: white;
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 0.4rem;
            line-height: 1.2;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
        }

        .feature-content p {
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.75rem;
            line-height: 1.4;
            font-weight: 400;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            flex-grow: 1;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Right Side - Form */
        .form-side {
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .form-subtitle {
            color: var(--text-gray);
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            border: 2px solid var(--border);
            border-radius: var(--radius);
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--bg-white);
            color: var(--text-dark);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .form-input:valid:not(:placeholder-shown) {
            border-color: var(--success);
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .form-input:focus + .input-icon,
        .form-input:valid + .input-icon {
            color: var(--primary);
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
        }

        .password-toggle:hover {
            color: var(--primary);
            background: rgba(102, 126, 234, 0.1);
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .checkbox-wrapper {
            position: relative;
            margin-right: 0.75rem;
        }

        .checkbox {
            opacity: 0;
            position: absolute;
        }

        .checkmark {
            width: 20px;
            height: 20px;
            border: 2px solid var(--border);
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .checkbox:checked + .checkmark {
            background: var(--gradient-primary);
            border-color: var(--primary);
        }

        .checkmark::after {
            content: '✓';
            color: white;
            font-size: 0.75rem;
            font-weight: bold;
            opacity: 0;
            transform: scale(0);
            transition: all 0.2s ease;
        }

        .checkbox:checked + .checkmark::after {
            opacity: 1;
            transform: scale(1);
        }

        .remember-label {
            font-size: 0.875rem;
            color: var(--text-gray);
            cursor: pointer;
        }

        .submit-button {
            width: 100%;
            padding: 0.875rem;
            background: var(--gradient-primary);
            color: white;
            border: none;
            border-radius: var(--radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .submit-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .submit-button:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .submit-button:hover::before {
            left: 100%;
        }

        .submit-button:active {
            transform: translateY(0);
        }

        .forgot-password {
            text-align: center;
        }

        .forgot-link {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
        }

        .forgot-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--gradient-primary);
            transition: width 0.3s ease;
        }

        .forgot-link:hover::after {
            width: 100%;
        }

        .alert {
            padding: 0.875rem 1rem;
            border-radius: var(--radius);
            margin-bottom: 1rem;
            border: 1px solid;
            font-size: 0.875rem;
        }

        .alert-error {
            background: #fef2f2;
            border-color: #fecaca;
            color: #b91c1c;
        }

        .alert-success {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #166534;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .login-card {
                grid-template-columns: 1fr;
                max-width: 400px;
                min-height: auto;
            }

            .branding-side {
                padding: 2rem 1.5rem;
            }

            .brand-logo {
                font-size: 2.5rem;
            }

            .branding-side {
                min-height: auto;
                padding: 2rem 1.5rem;
                justify-content: center;
            }

            .features-grid {
                grid-template-columns: 1fr;
                gap: 0.8rem;
                margin-top: 1.5rem;
            }

            .feature-card {
                padding: 1rem;
                min-height: 90px;
                flex-direction: row;
                align-items: center;
                text-align: left;
            }

            .feature-icon-wrapper {
                width: 35px;
                height: 35px;
                margin-bottom: 0;
                margin-right: 0.8rem;
                flex-shrink: 0;
            }

            .feature-icon {
                font-size: 1rem;
            }

            .feature-content h4 {
                font-size: 0.85rem;
                margin-bottom: 0.2rem;
                white-space: normal;
            }

            .feature-content p {
                font-size: 0.7rem;
                line-height: 1.3;
                -webkit-line-clamp: 1;
            }

            .form-side {
                padding: 2rem 1.5rem;
            }

            .form-title {
                font-size: 1.75rem;
            }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 1rem;
            }

            .form-side {
                padding: 1.5rem 1rem;
            }

            .branding-side {
                padding: 1.5rem 1rem;
            }
        }

        /* Loading state */
        .loading {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Focus styles for accessibility */
        .form-input:focus,
        .submit-button:focus,
        .password-toggle:focus,
        .forgot-link:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }
    </style>
</head>

<body>
    <!-- Floating particles -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="login-container">
        <div class="login-card">
            <!-- Left Side - Branding -->
            <div class="branding-side">
                <div class="brand-logo">Sipariş Masanda</div>
                <div class="brand-tagline">Modern Restaurant Management System</div>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-utensils feature-icon"></i>
                        </div>
                        <div class="feature-content">
                            <h4>Menü & Sipariş</h4>
                            <p>Dijital menü ve hızlı sipariş alma sistemi</p>
                        </div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-chart-line feature-icon"></i>
                        </div>
                        <div class="feature-content">
                            <h4>Gelişmiş Raporlama</h4>
                            <p>Satış analitiği ve performans raporları</p>
                        </div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-users feature-icon"></i>
                        </div>
                        <div class="feature-content">
                            <h4>Müşteri Yönetimi</h4>
                            <p>CRM sistemi ve müşteri sadakat programları</p>
                        </div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-mobile-alt feature-icon"></i>
                        </div>
                        <div class="feature-content">
                            <h4>Mobil Uyumlu</h4>
                            <p>Responsive tasarım, her cihazda mükemmel</p>
                        </div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-cash-register feature-icon"></i>
                        </div>
                        <div class="feature-content">
                            <h4>POS Entegrasyonu</h4>
                            <p>Nakit, kart ve dijital ödeme seçenekleri</p>
                        </div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-calendar-check feature-icon"></i>
                        </div>
                        <div class="feature-content">
                            <h4>Rezervasyon</h4>
                            <p>Online masa rezervasyonu ve takip sistemi</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="form-side">
                <div class="form-header">
                    <h1 class="form-title">Hoş Geldiniz</h1>
                    <p class="form-subtitle">Hesabınıza giriş yaparak devam edin</p>
                </div>

                @if (session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <form id="loginForm" action="{{ route('login.post') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="phone">Telefon Numarası</label>
                        <div class="input-wrapper">
                            <input 
                                type="text" 
                                id="phone" 
                                name="phone" 
                                class="form-input"
                                placeholder="5XX XXX XX XX" 
                                value="{{ old('phone') }}" 
                                required
                                autocomplete="tel"
                            >
                            <i class="fas fa-phone input-icon"></i>
                        </div>
                        @error('phone')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Şifre</label>
                        <div class="input-wrapper">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-input"
                                placeholder="Şifrenizi girin" 
                                required
                                autocomplete="current-password"
                            >
                            <i class="fas fa-lock input-icon"></i>
                            <button type="button" class="password-toggle" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="remember-me">
                        <div class="checkbox-wrapper">
                            <input type="checkbox" name="remember" id="remember" class="checkbox">
                            <div class="checkmark"></div>
                        </div>
                        <label for="remember" class="remember-label">Beni hatırla</label>
                    </div>

                    <button type="submit" class="submit-button" id="submitBtn">
                        <span class="button-text">Giriş Yap</span>
                    </button>

                    <div class="forgot-password">
                        <a href="#" class="forgot-link">Şifremi unuttum</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Password toggle functionality
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Phone number formatting
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.length > 10) {
                value = value.slice(0, 10);
            }
            
            if (value.length > 0) {
                if (value.length <= 3) {
                    value = value;
                } else if (value.length <= 6) {
                    value = value.slice(0, 3) + ' ' + value.slice(3);
                } else if (value.length <= 8) {
                    value = value.slice(0, 3) + ' ' + value.slice(3, 6) + ' ' + value.slice(6);
                } else {
                    value = value.slice(0, 3) + ' ' + value.slice(3, 6) + ' ' + value.slice(6, 8) + ' ' + value.slice(8, 10);
                }
            }
            
            e.target.value = value;
        });

        // Form submission with loading state
        document.getElementById('loginForm').addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            const buttonText = submitBtn.querySelector('.button-text');
            
            submitBtn.disabled = true;
            buttonText.innerHTML = '<span class="loading"></span> Giriş yapılıyor...';
        });

        // Smooth animations on load
        window.addEventListener('load', function() {
            document.body.style.opacity = '1';
        });
    </script>
</body>
</html>