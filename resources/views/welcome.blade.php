<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sleepy Panda</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo_sleepy.png') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        .welcome-card {
            background-color: var(--sp-dark);
            border: 2px solid var(--sp-text-muted);
            border-radius: 16px;
            padding: 40px 30px;
            width: 100%;
            max-width: 400px;
            min-height: 580px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .welcome-btn-primary {
            background-color: var(--sp-teal);
            border: none;
            border-radius: 10px;
            padding: 14px 40px;
            font-size: 1rem;
            font-weight: 600;
            color: var(--sp-dark);
            width: 100%;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .welcome-btn-primary:hover {
            background-color: var(--sp-teal-hover);
            color: var(--sp-dark);
        }
        .welcome-btn-secondary {
            background-color: transparent;
            border: 2px solid var(--sp-text-muted);
            border-radius: 10px;
            padding: 12px 40px;
            font-size: 1rem;
            font-weight: 500;
            color: var(--sp-text);
            width: 100%;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .welcome-btn-secondary:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: var(--sp-text);
            color: var(--sp-text);
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="welcome-card">
            <img src="{{ asset('img/logo_sleepy.png') }}" alt="Sleepy Panda" class="auth-logo">
            <h1 class="auth-title">Sleepy Panda</h1>
            <p class="auth-subtitle" style="font-style: normal !important;">Mulai dengan masuk atau mendaftar untuk melihat analisa tidur mu.</p>

            <div class="d-flex flex-column gap-3 mt-4" style="width: 100%;">
                <a href="{{ route('login') }}" class="welcome-btn-primary">Masuk</a>
                <a href="{{ route('register') }}" class="welcome-btn-secondary">Daftar</a>
            </div>
        </div>
    </div>
</body>
</html>
