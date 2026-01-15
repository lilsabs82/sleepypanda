<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - Sleepy Panda</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo_sleepy.png') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        .dashboard-wrapper {
            min-height: 100vh;
            background-color: var(--sp-darker);
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: -280px;
            width: 280px;
            height: 100vh;
            background-color: var(--sp-dark);
            border-right: 1px solid var(--sp-border);
            z-index: 1000;
            transition: left 0.3s ease;
            padding: 30px 20px;
        }
        .sidebar.open { left: 0; }
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            display: none;
        }
        .sidebar-overlay.show { display: block; }
        .sidebar-title {
            color: var(--sp-text);
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 40px;
        }
        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .sidebar-menu-item {
            display: block;
            padding: 15px 25px;
            border: 2px solid var(--sp-border);
            border-radius: 12px;
            color: var(--sp-text-muted);
            text-decoration: none;
            text-align: center;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .sidebar-menu-item:hover {
            border-color: var(--sp-text-muted);
            color: var(--sp-text);
        }
        .sidebar-menu-item.active {
            background-color: var(--sp-teal);
            border-color: var(--sp-teal);
            color: var(--sp-dark);
            font-weight: 600;
        }
        .sidebar-submenu {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 10px;
        }
        .sidebar-submenu-item {
            display: block;
            padding: 12px 25px;
            border: 2px solid var(--sp-border);
            border-radius: 12px;
            color: var(--sp-text-muted);
            text-decoration: none;
            text-align: center;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        .sidebar-submenu-item:hover {
            border-color: var(--sp-text-muted);
            color: var(--sp-text);
        }
        .sidebar-submenu-item.active {
            background-color: var(--sp-teal);
            border-color: var(--sp-teal);
            color: var(--sp-dark);
            font-weight: 600;
        }
        .navbar-dashboard {
            background-color: var(--sp-dark);
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--sp-border);
        }
        .navbar-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .navbar-brand-dashboard {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--sp-text);
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 600;
        }
        .navbar-brand-dashboard img { height: 40px; }
        .search-box {
            background-color: var(--sp-darker);
            border: 1px solid var(--sp-border);
            border-radius: 25px;
            padding: 8px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 250px;
        }
        .search-box input {
            background: transparent;
            border: none;
            color: var(--sp-text);
            outline: none;
            width: 100%;
        }
        .search-box input::placeholder { color: var(--sp-text-muted); }
        .search-box i { color: var(--sp-text-muted); }
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--sp-text);
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--sp-border);
        }
        .menu-toggle {
            background: none;
            border: none;
            color: var(--sp-text);
            font-size: 1.5rem;
            cursor: pointer;
        }
        .logout-btn {
            background: none;
            border: 1px solid var(--sp-border);
            color: var(--sp-text);
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            margin-left: 15px;
        }
        .logout-btn:hover { background-color: var(--sp-border); }
        .dashboard-content { padding: 30px; }
        .page-title {
            color: var(--sp-text);
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 30px;
        }
        .form-section {
            background-color: var(--sp-dark);
            border-radius: 12px;
            padding: 30px;
            border: 1px solid var(--sp-border);
            max-width: 600px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            color: var(--sp-text);
            font-size: 0.9rem;
            margin-bottom: 8px;
            display: block;
        }
        .form-input {
            width: 100%;
            background-color: var(--sp-darker);
            border: 1px solid var(--sp-border);
            border-radius: 8px;
            padding: 12px 15px;
            color: var(--sp-text);
            font-size: 0.95rem;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--sp-teal);
        }
        .form-input::placeholder { color: var(--sp-text-muted); }
        .form-select {
            width: 100%;
            background-color: var(--sp-darker);
            border: 1px solid var(--sp-border);
            border-radius: 8px;
            padding: 12px 15px;
            color: var(--sp-text);
            font-size: 0.95rem;
            cursor: pointer;
        }
        .form-select:focus {
            outline: none;
            border-color: var(--sp-teal);
        }
        .btn-submit {
            background-color: var(--sp-teal);
            border: none;
            border-radius: 10px;
            padding: 14px 30px;
            color: var(--sp-dark);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-submit:hover {
            background-color: var(--sp-teal-hover);
        }
        .btn-submit:disabled {
            background-color: #5a6a7a;
            cursor: not-allowed;
        }
        .alert-success {
            background-color: rgba(45, 212, 191, 0.1);
            border: 1px solid var(--sp-teal);
            color: var(--sp-teal);
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 20px;
        }
        .alert-error {
            background-color: rgba(248, 113, 113, 0.1);
            border: 1px solid #f87171;
            color: #f87171;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 20px;
        }
        .new-password-display {
            background-color: var(--sp-darker);
            border: 1px solid var(--sp-teal);
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
        }
        .new-password-label {
            color: var(--sp-text-muted);
            font-size: 0.85rem;
            margin-bottom: 5px;
        }
        .new-password-value {
            color: var(--sp-teal);
            font-size: 1.2rem;
            font-weight: 600;
            font-family: monospace;
        }
        @media (max-width: 768px) {
            .search-box { display: none; }
            .form-section { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <aside class="sidebar" id="sidebar">
            <h2 class="sidebar-title">Admin Site</h2>
            <nav class="sidebar-menu">
                <a href="{{ route('dashboard') }}" class="sidebar-menu-item">Dashboard</a>
                <a href="{{ route('jurnal') }}" class="sidebar-menu-item">Jurnal</a>
                <a href="{{ route('report') }}" class="sidebar-menu-item">Report</a>
                <a href="{{ route('database-user') }}" class="sidebar-menu-item active">Database User</a>
                <div class="sidebar-submenu">
                    <a href="{{ route('update-data') }}" class="sidebar-submenu-item">Update Data</a>
                    <a href="{{ route('reset-password') }}" class="sidebar-submenu-item active">Reset Password</a>
                </div>
            </nav>
        </aside>

        <nav class="navbar-dashboard">
            <div class="navbar-left">
                <button class="menu-toggle" id="menuToggle"><i class="bi bi-list"></i></button>
                <a href="/dashboard" class="navbar-brand-dashboard">
                    <img src="{{ asset('img/logo_sleepy.png') }}" alt="Logo">
                    Sleepy Panda
                </a>
                <div class="search-box" style="position: relative;">
                    <i class="bi bi-search"></i>
                    <input type="text" id="navbarSearch" placeholder="Search">
                    <div id="searchDropdown" style="position: absolute; top: 100%; left: 0; right: 0; background: var(--sp-dark); border: 1px solid var(--sp-border); border-radius: 8px; margin-top: 5px; display: none; z-index: 1001;"></div>
                </div>
            </div>
            <div class="user-info">
                <div class="user-avatar"></div>
                <span>Halo, <span id="userName">User</span></span>
                <button class="logout-btn" id="logoutBtn"><i class="bi bi-box-arrow-right"></i></button>
            </div>
        </nav>

        <div class="dashboard-content">
            <h1 class="page-title">Reset Password User</h1>

            <div class="form-section">
                <div id="alertSuccess" class="alert-success" style="display: none;"></div>
                <div id="alertError" class="alert-error" style="display: none;"></div>

                <form id="resetForm">
                    <div class="form-group">
                        <label class="form-label">Pilih User</label>
                        <select class="form-select" id="userSelect" required>
                            <option value="">-- Pilih User --</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password Baru (kosongkan untuk generate otomatis)</label>
                        <input type="password" class="form-input" id="newPassword" placeholder="Password baru (min 8 karakter)">
                    </div>

                    <button type="submit" class="btn-submit" id="submitBtn">Reset Password</button>
                </form>

                <div id="newPasswordDisplay" class="new-password-display" style="display: none;">
                    <div class="new-password-label">Password baru:</div>
                    <div class="new-password-value" id="generatedPassword"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            const token = localStorage.getItem('jwt_token');

            if (!token) {
                window.location.href = '/login';
                return;
            }

            const headers = { 'Authorization': 'Bearer ' + token };

            try {
                const response = await axios.get('/api/auth/me', { headers });
                document.getElementById('userName').textContent = response.data.email.split('@')[0];
            } catch (error) {
                localStorage.removeItem('jwt_token');
                window.location.href = '/login';
                return;
            }

            // Sidebar toggle
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('open');
                sidebarOverlay.classList.toggle('show');
            });

            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('open');
                sidebarOverlay.classList.remove('show');
            });

            // Logout
            document.getElementById('logoutBtn').addEventListener('click', async function() {
                try {
                    await axios.post('/api/auth/logout', {}, { headers });
                } catch (e) {}
                localStorage.removeItem('jwt_token');
                window.location.href = '/login';
            });

            // Navbar search - for menu/pages
            const navbarSearch = document.getElementById('navbarSearch');
            const searchDropdown = document.getElementById('searchDropdown');
            const menuItems = [
                { name: 'Dashboard', url: '/dashboard' },
                { name: 'Jurnal', url: '/jurnal' },
                { name: 'Report', url: '/report' },
                { name: 'Database User', url: '/database-user' },
                { name: 'Update Data', url: '/update-data' },
                { name: 'Reset Password', url: '/reset-password-admin' }
            ];

            navbarSearch.addEventListener('input', function(e) {
                const query = e.target.value.toLowerCase();
                if (!query) {
                    searchDropdown.style.display = 'none';
                    return;
                }
                const filtered = menuItems.filter(item => item.name.toLowerCase().includes(query));
                if (filtered.length > 0) {
                    searchDropdown.innerHTML = filtered.map(item =>
                        `<a href="${item.url}" style="display: block; padding: 12px 15px; color: var(--sp-text); text-decoration: none; border-bottom: 1px solid var(--sp-border);">${item.name}</a>`
                    ).join('');
                    searchDropdown.style.display = 'block';
                } else {
                    searchDropdown.innerHTML = '<div style="padding: 12px 15px; color: var(--sp-text-muted);">No menu found</div>';
                    searchDropdown.style.display = 'block';
                }
            });
            navbarSearch.addEventListener('blur', function() {
                setTimeout(() => { searchDropdown.style.display = 'none'; }, 200);
            });

            // Load users for dropdown
            async function loadUsers() {
                try {
                    const res = await axios.get('/api/users', { headers });
                    const select = document.getElementById('userSelect');
                    res.data.users.forEach(user => {
                        const option = document.createElement('option');
                        option.value = user.id;
                        option.textContent = `${user.name} (${user.email})`;
                        select.appendChild(option);
                    });
                } catch (e) {
                    console.error('Failed to load users', e);
                }
            }

            await loadUsers();

            // Form submit
            document.getElementById('resetForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const userId = document.getElementById('userSelect').value;
                const newPassword = document.getElementById('newPassword').value;

                if (!userId) {
                    document.getElementById('alertError').textContent = 'Pilih user terlebih dahulu';
                    document.getElementById('alertError').style.display = 'block';
                    document.getElementById('alertSuccess').style.display = 'none';
                    document.getElementById('newPasswordDisplay').style.display = 'none';
                    return;
                }

                if (newPassword && newPassword.length < 8) {
                    document.getElementById('alertError').textContent = 'Password minimal 8 karakter';
                    document.getElementById('alertError').style.display = 'block';
                    document.getElementById('alertSuccess').style.display = 'none';
                    document.getElementById('newPasswordDisplay').style.display = 'none';
                    return;
                }

                document.getElementById('submitBtn').disabled = true;
                document.getElementById('submitBtn').textContent = 'Loading...';

                try {
                    const res = await axios.post('/api/users/' + userId + '/reset-password', {
                        password: newPassword || null
                    }, { headers });

                    document.getElementById('alertSuccess').textContent = 'Password berhasil direset';
                    document.getElementById('alertSuccess').style.display = 'block';
                    document.getElementById('alertError').style.display = 'none';

                    if (res.data.generated_password) {
                        document.getElementById('generatedPassword').textContent = res.data.generated_password;
                        document.getElementById('newPasswordDisplay').style.display = 'block';
                    } else {
                        document.getElementById('newPasswordDisplay').style.display = 'none';
                    }

                    document.getElementById('newPassword').value = '';
                } catch (error) {
                    document.getElementById('alertError').textContent = error.response?.data?.message || 'Gagal mereset password';
                    document.getElementById('alertError').style.display = 'block';
                    document.getElementById('alertSuccess').style.display = 'none';
                    document.getElementById('newPasswordDisplay').style.display = 'none';
                }

                document.getElementById('submitBtn').disabled = false;
                document.getElementById('submitBtn').textContent = 'Reset Password';
            });
        });
    </script>
</body>
</html>
