<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lupa Password - Sleepy Panda</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <img src="{{ asset('img/logo_sleepy.png') }}" alt="Sleepy Panda" class="auth-logo">
            <h3 style="color: var(--sp-text); margin-bottom: 10px;">Lupa password?</h3>
            <p class="auth-subtitle">Instruksi untuk melakukan reset password akan dikirim melalui email yang kamu gunakan untuk mendaftar.</p>

            <div id="errorAlert" class="alert-custom" style="display: none;"></div>
            <div id="successAlert" class="alert-success-custom" style="display: none;"></div>

            <form id="forgotForm">
                @csrf
                <div class="form-group-custom" id="emailGroup">
                    <i class="bi bi-envelope form-icon"></i>
                    <input type="email" name="email" id="email" placeholder="Email" autocomplete="email">
                </div>
                <div id="emailError" class="error-message" style="display: none;"></div>

                <button type="submit" class="btn btn-primary-custom mt-3" id="submitBtn">Reset Password</button>
            </form>

            <p class="auth-footer"><a href="{{ route('login') }}">Kembali ke Login</a></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('forgotForm');
            const emailInput = document.getElementById('email');
            const emailGroup = document.getElementById('emailGroup');
            const emailError = document.getElementById('emailError');
            const errorAlert = document.getElementById('errorAlert');
            const successAlert = document.getElementById('successAlert');
            const submitBtn = document.getElementById('submitBtn');

            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            function showError(message) {
                emailError.textContent = message;
                emailError.style.display = 'block';
                emailGroup.classList.add('is-invalid');
            }

            function hideError() {
                emailError.style.display = 'none';
                emailGroup.classList.remove('is-invalid');
            }

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                hideError();
                errorAlert.style.display = 'none';
                successAlert.style.display = 'none';

                const email = emailInput.value.trim();

                if (!email) {
                    showError('Email tidak boleh kosong');
                    return;
                }

                if (!isValidEmail(email)) {
                    showError('Email Anda Salah');
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.textContent = 'Mengirim...';

                try {
                    const response = await axios.post('/api/auth/forgot-password', { email: email });
                    successAlert.textContent = 'OTP dan password baru telah dikirim ke email Anda.';
                    successAlert.style.display = 'block';
                    emailInput.value = '';
                } catch (error) {
                    const message = error.response?.data?.message || 'Email Anda Salah';
                    errorAlert.textContent = message;
                    errorAlert.style.display = 'block';
                }

                submitBtn.disabled = false;
                submitBtn.textContent = 'Reset Password';
            });
        });
    </script>
</body>
</html>
