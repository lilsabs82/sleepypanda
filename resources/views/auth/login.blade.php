<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Sleepy Panda</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo_sleepy.png') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        .auth-card {
            background-color: var(--sp-dark);
            border: 2px solid var(--sp-text-muted);
            border-radius: 16px;
            padding: 40px 30px;
            width: 100%;
            max-width: 400px;
            min-height: 580px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .form-group-custom {
            background-color: #252b4a;
            border: none;
            border-radius: 8px;
            padding: 14px 15px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .form-group-custom .form-icon {
            color: #4a5568;
            font-size: 1.1rem;
        }
        .form-group-custom input {
            background: transparent;
            border: none;
            color: var(--sp-text);
            width: 100%;
            outline: none;
            font-size: 0.95rem;
        }
        .form-group-custom input::placeholder {
            color: #4a5568;
        }
        .btn-primary-custom {
            background-color: var(--sp-teal);
            border: none;
            border-radius: 10px;
            padding: 14px 40px;
            font-size: 1rem;
            font-weight: 600;
            color: var(--sp-dark);
            width: 100%;
            max-width: 100%;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        .btn-primary-custom:hover:not(:disabled) {
            background-color: var(--sp-teal-hover);
            color: var(--sp-dark);
        }
        .forgot-section {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 55%;
            background-color: #1a1f3c;
            padding: 30px;
            border-radius: 14px;
            transform: translateY(100%);
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: center;
            z-index: 10;
        }
        .forgot-section.show {
            transform: translateY(0);
        }
        .forgot-back {
            position: absolute;
            top: 15px;
            left: 15px;
            color: var(--sp-text-muted);
            font-size: 1.2rem;
            cursor: pointer;
            transition: color 0.3s ease;
            display: none;
        }
        .forgot-back:hover {
            color: var(--sp-text);
        }
        .forgot-title {
            color: var(--sp-text);
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .forgot-desc {
            color: var(--sp-text-muted);
            font-size: 0.8rem;
            margin-bottom: 20px;
            line-height: 1.4;
        }
        .forgot-input {
            background-color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
        }
        .forgot-input .form-icon {
            color: #6b7280;
            font-size: 1.1rem;
        }
        .forgot-input input {
            background: transparent;
            border: none;
            color: #1a1f3c;
            width: 100%;
            outline: none;
            font-size: 0.95rem;
        }
        .forgot-input input::placeholder {
            color: #9ca3af;
        }
        .forgot-section .btn-primary-custom {
            width: 100%;
            max-width: 100%;
            border-radius: 10px;
            padding: 14px 15px;
            margin-top: 5px;
        }
        .divider {
            border-top: 3px solid var(--sp-teal);
            margin: 0 auto 20px;
            width: 80px;
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <img src="{{ asset('img/logo_sleepy.png') }}" alt="Sleepy Panda" class="auth-logo">
            <p class="auth-subtitle" style="font-style: normal !important;">Masuk menggunakan akun yang sudah kamu daftarkan</p>

            <div id="errorAlert" class="alert-custom" style="display: none;"></div>
            <div id="successAlert" class="alert-success-custom" style="display: none;"></div>

            <form id="loginForm" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group-custom" id="emailGroup">
                    <i class="bi bi-envelope form-icon"></i>
                    <input type="email" name="email" id="email" placeholder="Email" autocomplete="email">
                </div>
                <div id="emailError" class="error-message" style="display: none;"></div>

                <div class="form-group-custom" id="passwordGroup">
                    <i class="bi bi-lock form-icon"></i>
                    <input type="password" name="password" id="password" placeholder="Password" autocomplete="current-password">
                </div>
                <div id="passwordError" class="error-message" style="display: none;"></div>

                <a href="#" class="forgot-link" id="showForgotBtn">Lupa password?</a>

                <button type="submit" class="btn btn-primary-custom" id="submitBtn">Masuk</button>
            </form>

            <!-- Forgot Password Section -->
            <div class="forgot-section" id="forgotSection">
                <div class="divider"></div>
                <h3 class="forgot-title">Lupa password?</h3>
                <p class="forgot-desc">Instruksi untuk melakukan reset password akan dikirim melalui email yang kamu gunakan untuk mendaftar</p>

                <div id="forgotErrorAlert" class="alert-custom" style="display: none;"></div>
                <div id="forgotSuccessAlert" class="alert-success-custom" style="display: none;"></div>

                <form id="forgotForm">
                    <div class="forgot-input">
                        <i class="bi bi-envelope form-icon"></i>
                        <input type="email" id="forgotEmail" placeholder="Email">
                    </div>
                    <button type="submit" class="btn btn-primary-custom" id="resetBtn">Reset Password</button>
                </form>
            </div>

            <p class="auth-footer">Belum memiliki akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const emailGroup = document.getElementById('emailGroup');
            const passwordGroup = document.getElementById('passwordGroup');
            const emailError = document.getElementById('emailError');
            const passwordError = document.getElementById('passwordError');
            const errorAlert = document.getElementById('errorAlert');
            const successAlert = document.getElementById('successAlert');
            const submitBtn = document.getElementById('submitBtn');

            // Forgot password elements
            const showForgotBtn = document.getElementById('showForgotBtn');
            const forgotSection = document.getElementById('forgotSection');
            const forgotForm = document.getElementById('forgotForm');
            const forgotEmail = document.getElementById('forgotEmail');
            const forgotErrorAlert = document.getElementById('forgotErrorAlert');
            const forgotSuccessAlert = document.getElementById('forgotSuccessAlert');
            const resetBtn = document.getElementById('resetBtn');

            // Check if redirected from registration
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('registered') === '1') {
                successAlert.textContent = 'Registrasi berhasil! Silakan login dengan akun Anda.';
                successAlert.style.display = 'block';
                window.history.replaceState({}, document.title, '/login');
            }

            // Show forgot password section
            showForgotBtn.addEventListener('click', function(e) {
                e.preventDefault();
                forgotSection.classList.add('show');
            });

            // Close forgot password section when clicking outside
            document.addEventListener('click', function(e) {
                if (forgotSection.classList.contains('show') &&
                    !forgotSection.contains(e.target) &&
                    e.target !== showForgotBtn) {
                    forgotSection.classList.remove('show');
                }
            });

            const blockedDomains = ['ganteng.com'];

            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            function isBlockedDomain(email) {
                const domain = email.split('@')[1];
                return domain && blockedDomains.includes(domain.toLowerCase());
            }

            function showAlert(message) {
                errorAlert.textContent = message;
                errorAlert.style.display = 'block';
                successAlert.style.display = 'none';
            }

            function hideAlert() {
                errorAlert.style.display = 'none';
            }

            // Login form submit
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                hideAlert();

                const email = emailInput.value.trim();
                const password = passwordInput.value;
                let hasError = false;

                if (!email || !password) {
                    showAlert('username/password incorrect');
                    hasError = true;
                }

                if (email && !isValidEmail(email)) {
                    showAlert('username/password incorrect');
                    hasError = true;
                }

                if (email && isBlockedDomain(email)) {
                    showAlert('username/password incorrect');
                    hasError = true;
                }

                if (password && password.length < 8) {
                    showAlert('username/password incorrect');
                    hasError = true;
                }

                if (hasError) return;

                submitBtn.disabled = true;
                submitBtn.textContent = 'Loading...';

                try {
                    const response = await axios.post('/api/auth/login', {
                        email: email,
                        password: password
                    });

                    localStorage.setItem('jwt_token', response.data.access_token);
                    window.location.href = '/dashboard';
                } catch (error) {
                    showAlert('username/password incorrect');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Masuk';
                }
            });

            // Forgot password form submit
            forgotForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const email = forgotEmail.value.trim();
                forgotErrorAlert.style.display = 'none';
                forgotSuccessAlert.style.display = 'none';

                if (!email) {
                    forgotErrorAlert.textContent = 'Email tidak boleh kosong';
                    forgotErrorAlert.style.display = 'block';
                    return;
                }

                if (!isValidEmail(email)) {
                    forgotErrorAlert.textContent = 'Email Anda Salah';
                    forgotErrorAlert.style.display = 'block';
                    return;
                }

                resetBtn.disabled = true;
                resetBtn.textContent = 'Loading...';

                try {
                    const response = await axios.post('/api/auth/forgot-password', { email: email });
                    forgotSuccessAlert.textContent = response.data.message || 'OTP dan password baru telah dikirim ke email Anda.';
                    forgotSuccessAlert.style.display = 'block';
                    forgotEmail.value = '';
                } catch (error) {
                    forgotErrorAlert.textContent = error.response?.data?.message || 'Email Anda Salah';
                    forgotErrorAlert.style.display = 'block';
                }

                resetBtn.disabled = false;
                resetBtn.textContent = 'Reset Password';
            });
        });
    </script>
</body>
</html>
