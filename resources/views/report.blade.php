<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Report - Sleepy Panda</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo_sleepy.png') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            margin-bottom: 25px;
        }
        /* Stats Row */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background-color: var(--sp-dark);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid var(--sp-border);
        }
        .stat-label {
            color: var(--sp-text-muted);
            font-size: 0.8rem;
            margin-bottom: 12px;
        }
        .stat-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .stat-icon {
            font-size: 1.8rem;
        }
        .stat-icon.users { color: var(--sp-text); }
        .stat-icon.insomnia { color: #f472b6; }
        .stat-icon.time { color: var(--sp-text); }
        .stat-value {
            color: var(--sp-text);
            font-size: 1.8rem;
            font-weight: 700;
        }
        /* Report Content */
        .report-content {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 25px;
        }
        .chart-section {
            background-color: var(--sp-dark);
            border-radius: 12px;
            padding: 25px;
            border: 1px solid var(--sp-border);
        }
        .chart-header {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .filter-dropdown {
            background-color: transparent;
            border: none;
            color: var(--sp-text);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .filter-dropdown select {
            background: transparent;
            border: none;
            color: var(--sp-text);
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            outline: none;
            appearance: none;
            padding-right: 5px;
        }
        .filter-dropdown select option {
            background-color: var(--sp-dark);
            color: var(--sp-text);
            font-size: 1rem;
        }
        .filter-dropdown i {
            font-size: 1rem;
            color: var(--sp-text);
        }
        }
        .filter-dropdown select option {
            background-color: var(--sp-dark);
            color: var(--sp-text);
        }
        .chart-date-header {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 15px;
        }
        .chart-date-select {
            background-color: var(--sp-darker);
            border: 1px solid var(--sp-border);
            color: var(--sp-text);
            padding: 8px 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            cursor: pointer;
        }
        .chart-date-select select {
            background: transparent;
            border: none;
            color: var(--sp-text);
            font-size: 0.85rem;
            cursor: pointer;
            outline: none;
            appearance: none;
        }
        .chart-date-select select option {
            background-color: var(--sp-dark);
            color: var(--sp-text);
        }
        .chart-label {
            color: var(--sp-text-muted);
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        /* Alert Section */
        .alert-section {
            background-color: var(--sp-dark);
            border-radius: 12px;
            padding: 25px;
            border: 1px solid var(--sp-border);
        }
        .alert-title {
            color: var(--sp-text);
            font-size: 1.1rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 20px;
        }
        .alert-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .alert-card {
            background-color: var(--sp-darker);
            border-radius: 10px;
            padding: 15px;
            border: 1px solid var(--sp-border);
        }
        .alert-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .alert-date {
            color: var(--sp-text);
            font-size: 0.85rem;
            font-weight: 600;
        }
        .alert-time-ago {
            color: var(--sp-text-muted);
            font-size: 0.75rem;
        }
        .alert-card-body {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .alert-icon {
            background-color: rgba(248, 113, 113, 0.2);
            border-radius: 8px;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .alert-icon i {
            color: #f87171;
            font-size: 1.2rem;
        }
        .alert-user-info {
            flex: 1;
        }
        .alert-user {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 5px;
        }
        .alert-user-emoji {
            font-size: 1rem;
        }
        .alert-user-id {
            color: var(--sp-text);
            font-size: 0.8rem;
        }
        .alert-detail {
            color: var(--sp-text-muted);
            font-size: 0.75rem;
            margin-top: 5px;
        }
        .alert-duration {
            text-align: right;
        }
        .alert-duration-label {
            color: #f472b6;
            font-size: 0.7rem;
        }
        .alert-duration-value {
            color: #f472b6;
            font-size: 0.8rem;
            font-weight: 600;
        }
        @media (max-width: 1200px) {
            .stats-row { grid-template-columns: repeat(2, 1fr); }
            .report-content { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
            .stats-row { grid-template-columns: 1fr; }
            .search-box { display: none; }
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
                <a href="{{ route('report') }}" class="sidebar-menu-item active">Report</a>
                <a href="{{ route('database-user') }}" class="sidebar-menu-item">Database User</a>
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
            <h1 class="page-title" id="pageTitle">Report Insomnia daily</h1>

            <!-- Stats Row -->
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-label">Total Users</div>
                    <div class="stat-content">
                        <i class="bi bi-person stat-icon users"></i>
                        <span class="stat-value" id="totalUsers">5500</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Insomnia Cases</div>
                    <div class="stat-content">
                        <i class="bi bi-person stat-icon insomnia"></i>
                        <span class="stat-value" id="insomniaCases">700</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Time to Sleep</div>
                    <div class="stat-content">
                        <i class="bi bi-clock stat-icon time"></i>
                        <span class="stat-value" id="timeToSleep">110 min</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Avarange Sleep Time</div>
                    <div class="stat-content">
                        <i class="bi bi-clock stat-icon time"></i>
                        <span class="stat-value" id="avgSleepTime">5.5 h</span>
                    </div>
                </div>
            </div>

            <!-- Report Content -->
            <div class="report-content">
                <div class="chart-section">
                    <div class="chart-header">
                        <div class="filter-dropdown">
                            <select id="filterSelect">
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                    </div>
                    <div class="chart-date-header">
                        <div class="chart-date-select">
                            <select id="dateSelect">
                                <!-- Options will be populated by JS -->
                            </select>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                    </div>
                    <div class="chart-label">Users</div>
                    <canvas id="reportChart" height="280"></canvas>
                </div>

                <div class="alert-section">
                    <h3 class="alert-title">Alert Insomnia Terbaru</h3>
                    <div class="alert-list" id="alertList">
                        <!-- Alerts will be rendered by JS -->
                    </div>
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

            // Chart setup
            Chart.defaults.color = '#94a3b8';
            Chart.defaults.borderColor = '#2d3561';

            let currentChart = null;
            const chartCanvas = document.getElementById('reportChart');
            const filterSelect = document.getElementById('filterSelect');
            const dateSelect = document.getElementById('dateSelect');
            const pageTitle = document.getElementById('pageTitle');
            const alertList = document.getElementById('alertList');

            let apiData = {};
            let currentType = 'daily';
            let currentDateIndex = 0;

            async function loadData(type) {
                try {
                    const res = await axios.get(`/api/report/${type}`, { headers });
                    apiData[type] = res.data;
                    return res.data;
                } catch (e) {
                    console.error('Failed to load report data', e);
                    return null;
                }
            }

            function populateDateOptions(items) {
                dateSelect.innerHTML = '';
                items.forEach((d, index) => {
                    const option = document.createElement('option');
                    option.value = index;
                    option.textContent = d.label;
                    dateSelect.appendChild(option);
                });
                currentDateIndex = 0;
            }

            function updateStats(stats) {
                document.getElementById('totalUsers').textContent = stats.totalUsers;
                document.getElementById('insomniaCases').textContent = stats.insomniaCases;
                document.getElementById('timeToSleep').textContent = stats.timeToSleep;
                document.getElementById('avgSleepTime').textContent = stats.avgSleepTime;
            }

            function renderAlerts(alerts) {
                let html = '';
                if (!alerts || alerts.length === 0) {
                    html = '<div style="text-align: center; color: var(--sp-text-muted);">No alerts</div>';
                } else {
                    alerts.forEach(alert => {
                        html += `
                            <div class="alert-card">
                                <div class="alert-card-header">
                                    <span class="alert-date">${alert.date}</span>
                                    <span class="alert-time-ago">${alert.timeAgo}</span>
                                </div>
                                <div class="alert-card-body">
                                    <div class="alert-icon">
                                        <i class="bi bi-bell"></i>
                                    </div>
                                    <div class="alert-user-info">
                                        <div class="alert-user">
                                            <span class="alert-user-emoji">😟</span>
                                            <span class="alert-user-id">User ID ${alert.userId}</span>
                                        </div>
                                        <div class="alert-detail">Tidak Tidur selama ${alert.noSleep} terakhir</div>
                                    </div>
                                    <div class="alert-duration">
                                        <div class="alert-duration-label">Avarage Durasi tidur</div>
                                        <div class="alert-duration-value">${alert.duration}</div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                }
                alertList.innerHTML = html;
            }

            function renderChart(chartData, chartLabels) {
                if (currentChart) {
                    currentChart.destroy();
                }

                currentChart = new Chart(chartCanvas, {
                    type: 'bar',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            label: 'Users',
                            data: chartData,
                            backgroundColor: '#be6a7a',
                            borderRadius: 4,
                            barThickness: 45
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#2d3561' } },
                            x: { grid: { color: '#2d3561' } }
                        }
                    }
                });
            }

            async function updateView(type, dateIndex = 0) {
                const data = apiData[type] || await loadData(type);
                if (!data) return;

                pageTitle.textContent = data.title;

                let items;
                if (type === 'daily') items = data.dates;
                else if (type === 'weekly') items = data.weeks;
                else items = data.months;

                if (dateIndex === 0) {
                    populateDateOptions(items);
                }

                if (items && items.length > dateIndex) {
                    const item = items[dateIndex];
                    updateStats(item.stats);
                    renderAlerts(item.alerts);
                    renderChart(item.chart, data.chartLabels);
                }
            }

            // Initial load
            await updateView('daily');

            // Filter type change (Daily/Weekly/Monthly)
            filterSelect.addEventListener('change', async function() {
                currentType = this.value;
                currentDateIndex = 0;
                await updateView(currentType, 0);
            });

            // Date filter change
            dateSelect.addEventListener('change', async function() {
                currentDateIndex = parseInt(this.value);
                await updateView(currentType, currentDateIndex);
            });
        });
    </script>
</body>
</html>
