<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar - Sleepy Panda</title>
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
            background-color: #6b7280;
            border: none;
            border-radius: 10px;
            padding: 14px 40px;
            font-size: 1rem;
            font-weight: 600;
            color: var(--sp-text-muted);
            width: 100%;
            max-width: 100%;
            transition: all 0.3s ease;
            margin-top: 20px;
        }
        .btn-primary-custom:not(:disabled) {
            background-color: var(--sp-teal);
            color: var(--sp-dark);
        }
        .btn-primary-custom:hover:not(:disabled) {
            background-color: var(--sp-teal-hover);
            color: var(--sp-dark);
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <img src="{{ asset('img/logo_sleepy.png') }}" alt="Sleepy Panda" class="auth-logo">
            <p class="auth-subtitle" style="font-style: normal !important;">Daftar akun baru untuk memulai</p>

            <div id="errorAlert" class="alert-custom" style="display: none;"></div>

            <form id="registerForm" method="POST" action="{{ route('register') }}">
                @csrf
                <div class="form-group-custom" id="emailGroup">
                    <i class="bi bi-envelope form-icon"></i>
                    <input type="email" name="email" id="email" placeholder="Email" autocomplete="email">
                </div>
                <div id="emailError" class="error-message" style="display: none;"></div>

                <div class="form-group-custom" id="passwordGroup">
                    <i class="bi bi-lock form-icon"></i>
                    <input type="password" name="password" id="password" placeholder="Password" autocomplete="new-password">
                </div>
                <div id="passwordError" class="error-message" style="display: none;"></div>

                <button type="submit" class="btn btn-primary-custom" id="submitBtn" disabled>Daftar</button>
            </form>

            <p class="auth-footer">Sudah memiliki akun? <a href="{{ route('login') }}">Masuk</a></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');

            const emailGroup = document.getElementById('emailGroup');
            const passwordGroup = document.getElementById('passwordGroup');

            const emailError = document.getElementById('emailError');
            const passwordError = document.getElementById('passwordError');

            const errorAlert = document.getElementById('errorAlert');
            const submitBtn = document.getElementById('submitBtn');

            const blockedDomains = ['ganteng.com'];

            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            function isBlockedDomain(email) {
                const domain = email.split('@')[1];
                return domain && blockedDomains.includes(domain.toLowerCase());
            }

            function showError(element, group, message) {
                element.textContent = message;
                element.style.display = 'block';
                group.classList.add('is-invalid');
            }

            function hideError(element, group) {
                element.style.display = 'none';
                group.classList.remove('is-invalid');
            }

            function showAlert(message) {
                errorAlert.textContent = message;
                errorAlert.style.display = 'block';
            }

            function hideAlert() {
                errorAlert.style.display = 'none';
            }

            function validateForm() {
                hideAlert();
                hideError(emailError, emailGroup);
                hideError(passwordError, passwordGroup);

                const email = emailInput.value.trim();
                const password = passwordInput.value;
                let isValid = true;

                // Validasi email tidak boleh kosong
                if (!email) {
                    isValid = false;
                }

                // Validasi format email
                if (email && !isValidEmail(email)) {
                    showError(emailError, emailGroup, 'username/password incorrect');
                    isValid = false;
                }

                // Validasi domain yang diblokir
                if (email && isBlockedDomain(email)) {
                    showError(emailError, emailGroup, 'username/password incorrect');
                    isValid = false;
                }

                // Validasi password tidak boleh kosong
                if (!password) {
                    isValid = false;
                }

                // Validasi password minimal 8 karakter
                if (password && password.length < 8) {
                    showError(passwordError, passwordGroup, 'Password harus lebih dari 8 karakter');
                    isValid = false;
                }

                submitBtn.disabled = !isValid;
                return isValid;
            }

            // Real-time validation
            [emailInput, passwordInput].forEach(input => {
                input.addEventListener('input', validateForm);
                input.addEventListener('blur', validateForm);
            });

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                if (!validateForm()) return;

                submitBtn.disabled = true;
                submitBtn.textContent = 'Loading...';

                try {
                    const response = await axios.post('/api/auth/register', {
                        email: emailInput.value.trim(),
                        password: passwordInput.value,
                        password_confirmation: passwordInput.value
                    });

                    // Registration successful - redirect to login
                    if (response.data.success) {
                        window.location.href = '/login?registered=1';
                    }
                } catch (error) {
                    if (error.response?.data?.errors) {
                        const errors = error.response.data.errors;
                        if (errors.email) {
                            showError(emailError, emailGroup, errors.email[0]);
                        }
                        if (errors.password) {
                            showError(passwordError, passwordGroup, errors.password[0]);
                        }
                    } else {
                        showAlert('Terjadi kesalahan. Silakan coba lagi.');
                    }
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Daftar';
                    validateForm();
                }
            });
        });
    </script>
</body>
</html>
