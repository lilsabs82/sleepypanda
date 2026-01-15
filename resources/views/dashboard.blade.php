<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Sleepy Panda</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo_sleepy.png') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-wrapper {
            min-height: 100vh;
            background-color: var(--sp-darker);
        }
        /* Sidebar */
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
        .sidebar.open {
            left: 0;
        }
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
        .sidebar-overlay.show {
            display: block;
        }
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
        /* Navbar */
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
        .navbar-brand-dashboard img {
            height: 40px;
        }
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
        .search-box input::placeholder {
            color: var(--sp-text-muted);
        }
        .search-box i {
            color: var(--sp-text-muted);
        }
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
        .dashboard-content {
            padding: 30px;
        }
        .chart-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .chart-card {
            background-color: var(--sp-dark);
            border-radius: 16px;
            padding: 20px;
            border: 1px solid var(--sp-border);
        }
        .chart-title {
            color: var(--sp-text);
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .chart-legend {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-bottom: 10px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.8rem;
            color: var(--sp-text-muted);
        }
        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }
        .legend-dot.female { background-color: #f472b6; }
        .legend-dot.male { background-color: #60a5fa; }
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background-color: var(--sp-dark);
            border-radius: 16px;
            padding: 20px;
            border: 1px solid var(--sp-border);
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .stat-icon {
            font-size: 2.5rem;
            color: var(--sp-text-muted);
        }
        .stat-content {
            flex: 1;
        }
        .stat-label {
            color: var(--sp-text-muted);
            font-size: 0.85rem;
            margin-bottom: 5px;
        }
        .stat-value {
            color: var(--sp-text);
            font-size: 2rem;
            font-weight: 700;
        }
        .stat-value.highlight {
            color: var(--sp-text);
        }
        .full-chart {
            grid-column: 1 / -1;
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
        .logout-btn:hover {
            background-color: var(--sp-border);
        }
        @media (max-width: 1200px) {
            .chart-row { grid-template-columns: 1fr; }
            .stats-row { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .stats-row { grid-template-columns: 1fr; }
            .search-box { display: none; }
        }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <h2 class="sidebar-title">Admin Site</h2>
            <nav class="sidebar-menu">
                <a href="{{ route('dashboard') }}" class="sidebar-menu-item active">Dashboard</a>
                <a href="{{ route('jurnal') }}" class="sidebar-menu-item">Jurnal</a>
                <a href="{{ route('report') }}" class="sidebar-menu-item">Report</a>
                <a href="{{ route('database-user') }}" class="sidebar-menu-item">Database User</a>
            </nav>
        </aside>

        <!-- Navbar -->
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

        <!-- Content -->
        <div class="dashboard-content">
            <!-- Charts Row -->
            <div class="chart-row">
                <!-- Daily Report -->
                <div class="chart-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <h3 class="chart-title">Daily Report</h3>
                        <div class="chart-legend">
                            <div class="legend-item"><span class="legend-dot female"></span> Female</div>
                            <div class="legend-item"><span class="legend-dot male"></span> Male</div>
                        </div>
                    </div>
                    <canvas id="dailyChart" height="200"></canvas>
                </div>

                <!-- Weekly Report -->
                <div class="chart-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <h3 class="chart-title">Weekly Report</h3>
                        <div class="chart-legend">
                            <div class="legend-item"><span class="legend-dot female"></span> Female</div>
                            <div class="legend-item"><span class="legend-dot male"></span> Male</div>
                        </div>
                    </div>
                    <canvas id="weeklyChart" height="200"></canvas>
                </div>

                <!-- Monthly Report -->
                <div class="chart-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <h3 class="chart-title">Monthly Report</h3>
                        <div class="chart-legend">
                            <div class="legend-item"><span class="legend-dot female"></span> Female</div>
                            <div class="legend-item"><span class="legend-dot male"></span> Male</div>
                        </div>
                    </div>
                    <canvas id="monthlyChart" height="200"></canvas>
                </div>
            </div>

            <!-- Stats Row -->
            <div class="stats-row">
                <div class="stat-card">
                    <i class="bi bi-people stat-icon"></i>
                    <div class="stat-content">
                        <div class="stat-label">Total Users</div>
                        <div class="stat-value" id="totalUsers">0</div>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="bi bi-person stat-icon"></i>
                    <div class="stat-content">
                        <div class="stat-label">Female Users</div>
                        <div class="stat-value highlight" id="femaleUsers">0</div>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="bi bi-person stat-icon"></i>
                    <div class="stat-content">
                        <div class="stat-label">Male Users</div>
                        <div class="stat-value" id="maleUsers">0</div>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="bi bi-clock stat-icon"></i>
                    <div class="stat-content">
                        <div class="stat-label">Avarage Time</div>
                        <div class="stat-value" id="avgTime">0</div>
                    </div>
                </div>
            </div>

            <!-- Average Sleep Time Chart -->
            <div class="chart-row">
                <div class="chart-card full-chart">
                    <div class="d-flex justify-content-between align-items-start">
                        <h3 class="chart-title">Average Users Sleep Time</h3>
                        <div class="chart-legend">
                            <div class="legend-item"><span class="legend-dot female"></span> Female</div>
                            <div class="legend-item"><span class="legend-dot male"></span> Male</div>
                        </div>
                    </div>
                    <canvas id="sleepTimeChart" height="100"></canvas>
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

            // Get user info
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

            // Chart defaults
            Chart.defaults.color = '#94a3b8';
            Chart.defaults.borderColor = '#2d3561';

            // Load stats
            try {
                const statsRes = await axios.get('/api/dashboard/stats', { headers });
                document.getElementById('totalUsers').textContent = statsRes.data.total_users;
                document.getElementById('femaleUsers').textContent = statsRes.data.female_users;
                document.getElementById('maleUsers').textContent = statsRes.data.male_users;
                document.getElementById('avgTime').textContent = statsRes.data.average_time;
            } catch (e) {
                console.error('Failed to load stats', e);
            }

            // Load Daily Chart
            try {
                const dailyRes = await axios.get('/api/dashboard/daily-report', { headers });
                new Chart(document.getElementById('dailyChart'), {
                    type: 'bar',
                    data: {
                        labels: dailyRes.data.labels,
                        datasets: [
                            { label: 'Female', data: dailyRes.data.female, backgroundColor: '#f472b6', borderRadius: 4 },
                            { label: 'Male', data: dailyRes.data.male, backgroundColor: '#60a5fa', borderRadius: 4 }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, grid: { color: '#2d3561' } }, x: { grid: { display: false } } }
                    }
                });
            } catch (e) {
                console.error('Failed to load daily chart', e);
            }

            // Load Weekly Chart
            try {
                const weeklyRes = await axios.get('/api/dashboard/weekly-report', { headers });
                new Chart(document.getElementById('weeklyChart'), {
                    type: 'bar',
                    data: {
                        labels: weeklyRes.data.labels,
                        datasets: [
                            { label: 'Female', data: weeklyRes.data.female, backgroundColor: '#f472b6', borderRadius: 4 },
                            { label: 'Male', data: weeklyRes.data.male, backgroundColor: '#60a5fa', borderRadius: 4 }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, grid: { color: '#2d3561' } }, x: { grid: { display: false } } }
                    }
                });
            } catch (e) {
                console.error('Failed to load weekly chart', e);
            }

            // Load Monthly Chart
            try {
                const monthlyRes = await axios.get('/api/dashboard/monthly-report', { headers });
                new Chart(document.getElementById('monthlyChart'), {
                    type: 'bar',
                    data: {
                        labels: monthlyRes.data.labels,
                        datasets: [
                            { label: 'Female', data: monthlyRes.data.female, backgroundColor: '#f472b6', borderRadius: 4 },
                            { label: 'Male', data: monthlyRes.data.male, backgroundColor: '#60a5fa', borderRadius: 4 }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, grid: { color: '#2d3561' } }, x: { grid: { display: false } } }
                    }
                });
            } catch (e) {
                console.error('Failed to load monthly chart', e);
            }

            // Load Sleep Time Chart
            try {
                const sleepRes = await axios.get('/api/dashboard/sleep-time-chart', { headers });
                new Chart(document.getElementById('sleepTimeChart'), {
                    type: 'line',
                    data: {
                        labels: sleepRes.data.labels,
                        datasets: [
                            { label: 'Female', data: sleepRes.data.female, borderColor: '#f472b6', backgroundColor: 'transparent', tension: 0.4, pointRadius: 4, pointBackgroundColor: '#f472b6' },
                            { label: 'Male', data: sleepRes.data.male, borderColor: '#60a5fa', backgroundColor: 'transparent', tension: 0.4, pointRadius: 4, pointBackgroundColor: '#60a5fa' }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, max: 10, grid: { color: '#2d3561' }, title: { display: true, text: 'Time (h)', color: '#94a3b8' } },
                            x: { grid: { color: '#2d3561' } }
                        }
                    }
                });
            } catch (e) {
                console.error('Failed to load sleep time chart', e);
            }
        });
    </script>
</body>
</html>
