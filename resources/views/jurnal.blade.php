<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Jurnal - Sleepy Panda</title>
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
            text-align: center;
            margin-bottom: 30px;
        }
        .jurnal-container {
            background-color: var(--sp-dark);
            border-radius: 16px;
            padding: 25px;
            border: 1px solid var(--sp-border);
        }
        .jurnal-header {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        .filter-dropdown {
            background-color: var(--sp-darker);
            border: 1px solid var(--sp-border);
            color: var(--sp-text);
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
        }
        .filter-dropdown select {
            background: transparent;
            border: none;
            color: var(--sp-text);
            font-size: 1rem;
            cursor: pointer;
            outline: none;
            appearance: none;
            padding-right: 20px;
        }
        .filter-dropdown select option {
            background-color: var(--sp-dark);
            color: var(--sp-text);
        }
        .jurnal-content {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 30px;
            align-items: stretch;
        }
        .jurnal-cards {
            display: flex;
            flex-direction: column;
            gap: 20px;
            overflow-y: auto;
        }
        .jurnal-card {
            background-color: var(--sp-darker);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid var(--sp-border);
        }
        .jurnal-card-date {
            text-align: center;
            color: var(--sp-text-muted);
            font-size: 0.85rem;
            margin-bottom: 15px;
        }
        .jurnal-card-content {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .jurnal-card-content.daily {
            justify-content: space-between;
        }
        .jurnal-card-content.weekly-monthly {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .jurnal-stat {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .jurnal-stat-icon { font-size: 1.3rem; }
        .jurnal-stat-info { display: flex; flex-direction: column; }
        .jurnal-stat-label {
            color: var(--sp-text-muted);
            font-size: 0.65rem;
        }
        .jurnal-stat-value {
            color: var(--sp-text);
            font-size: 0.8rem;
            font-weight: 600;
        }
        .jurnal-stat-large {
            grid-column: 1 / -1;
            justify-self: start;
        }
        .chart-section {
            background-color: var(--sp-darker);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid var(--sp-border);
            min-height: 350px;
        }
        .chart-header {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 15px;
        }
        .chart-date-select {
            background-color: var(--sp-dark);
            border: 1px solid var(--sp-border);
            color: var(--sp-text);
            padding: 8px 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
        }
        @media (max-width: 992px) {
            .jurnal-content { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
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
                <a href="{{ route('jurnal') }}" class="sidebar-menu-item active">Jurnal</a>
                <a href="{{ route('report') }}" class="sidebar-menu-item">Report</a>
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
            <h1 class="page-title">Jurnal Tidur Report</h1>

            <div class="jurnal-container">
                <div class="jurnal-header">
                    <div class="filter-dropdown">
                        <select id="filterSelect">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                </div>

                <div class="jurnal-content">
                    <div class="jurnal-cards" id="jurnalCards">
                        <!-- Cards will be rendered by JS -->
                    </div>

                    <div class="chart-section">
                        <div class="chart-header">
                            <div class="chart-date-select" id="chartDateLabel">
                                12 Agustus 2023 <i class="bi bi-chevron-down"></i>
                            </div>
                        </div>
                        <canvas id="jurnalChart" height="280"></canvas>
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
            const chartCanvas = document.getElementById('jurnalChart');
            const jurnalCards = document.getElementById('jurnalCards');
            const chartDateLabel = document.getElementById('chartDateLabel');
            const filterSelect = document.getElementById('filterSelect');

            let apiData = {};
            let currentType = 'daily';
            let currentIndex = 0;

            async function loadData(type) {
                try {
                    const res = await axios.get(`/api/jurnal/${type}`, { headers });
                    apiData[type] = res.data;
                    return res.data;
                } catch (e) {
                    console.error('Failed to load jurnal data', e);
                    return null;
                }
            }

            function renderDailyCards(dates) {
                let html = '';
                dates.forEach(item => {
                    html += `
                        <div class="jurnal-card">
                            <div class="jurnal-card-date">${item.label}</div>
                            <div class="jurnal-card-content daily">
                                <div class="jurnal-stat">
                                    <span class="jurnal-stat-icon">😊</span>
                                    <div class="jurnal-stat-info">
                                        <span class="jurnal-stat-label">User</span>
                                        <span class="jurnal-stat-value">${item.stats.user}</span>
                                    </div>
                                </div>
                                <div class="jurnal-stat">
                                    <span class="jurnal-stat-icon">😴</span>
                                    <div class="jurnal-stat-info">
                                        <span class="jurnal-stat-label">Avarage Durasi tidur</span>
                                        <span class="jurnal-stat-value">${item.stats.durasi}</span>
                                    </div>
                                </div>
                                <div class="jurnal-stat">
                                    <span class="jurnal-stat-icon">🌟</span>
                                    <div class="jurnal-stat-info">
                                        <span class="jurnal-stat-label">Avarage Waktu tidur</span>
                                        <span class="jurnal-stat-value">${item.stats.waktu}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                jurnalCards.innerHTML = html;
            }

            function renderWeeklyMonthlyCards(items, key) {
                let html = '';
                items.forEach(item => {
                    html += `
                        <div class="jurnal-card">
                            <div class="jurnal-card-date">${item.label}</div>
                            <div class="jurnal-card-content weekly-monthly">
                                <div class="jurnal-stat jurnal-stat-large">
                                    <span class="jurnal-stat-icon">😊</span>
                                    <div class="jurnal-stat-info">
                                        <span class="jurnal-stat-label">User</span>
                                        <span class="jurnal-stat-value">${item.stats.user}</span>
                                    </div>
                                </div>
                                <div class="jurnal-stat">
                                    <span class="jurnal-stat-icon">😴</span>
                                    <div class="jurnal-stat-info">
                                        <span class="jurnal-stat-label">Average Durasi Tidur</span>
                                        <span class="jurnal-stat-value">${item.stats.avgDurasi}</span>
                                    </div>
                                </div>
                                <div class="jurnal-stat">
                                    <span class="jurnal-stat-icon">🌟</span>
                                    <div class="jurnal-stat-info">
                                        <span class="jurnal-stat-label">Total Durasi Tidur</span>
                                        <span class="jurnal-stat-value">${item.stats.totalDurasi}</span>
                                    </div>
                                </div>
                                <div class="jurnal-stat">
                                    <span class="jurnal-stat-icon">🌙</span>
                                    <div class="jurnal-stat-info">
                                        <span class="jurnal-stat-label">Average Mulai Tidur</span>
                                        <span class="jurnal-stat-value">${item.stats.mulaiTidur}</span>
                                    </div>
                                </div>
                                <div class="jurnal-stat">
                                    <span class="jurnal-stat-icon">☀️</span>
                                    <div class="jurnal-stat-info">
                                        <span class="jurnal-stat-label">Average Bangun Tidur</span>
                                        <span class="jurnal-stat-value">${item.stats.bangunTidur}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                jurnalCards.innerHTML = html;
            }

            function renderChart(type, data, labels, dateLabel) {
                chartDateLabel.innerHTML = `${dateLabel} <i class="bi bi-chevron-down"></i>`;

                if (currentChart) {
                    currentChart.destroy();
                }

                const chartType = type === 'daily' ? 'line' : 'bar';
                currentChart = new Chart(chartCanvas, {
                    type: chartType,
                    data: {
                        labels: labels,
                        datasets: [{
                            label: type === 'daily' ? 'Users' : 'Hours',
                            data: data,
                            borderColor: '#f59e0b',
                            backgroundColor: chartType === 'bar' ? '#be6a7a' : 'transparent',
                            tension: 0.4,
                            pointRadius: chartType === 'line' ? 6 : 0,
                            pointBackgroundColor: '#f59e0b',
                            borderRadius: chartType === 'bar' ? 4 : 0,
                            barThickness: 40
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

            async function updateView(type) {
                const data = apiData[type] || await loadData(type);
                if (!data) return;

                if (type === 'daily') {
                    renderDailyCards(data.dates);
                    if (data.dates.length > 0) {
                        renderChart(type, data.dates[0].chart, data.chartLabels, data.dates[0].label);
                    }
                } else if (type === 'weekly') {
                    renderWeeklyMonthlyCards(data.weeks, 'weeks');
                    if (data.weeks.length > 0) {
                        renderChart(type, data.weeks[0].chart, data.chartLabels, data.weeks[0].label);
                    }
                } else {
                    renderWeeklyMonthlyCards(data.months, 'months');
                    if (data.months.length > 0) {
                        renderChart(type, data.months[0].chart, data.chartLabels, data.months[0].label);
                    }
                }
            }

            // Initial load
            await updateView('daily');

            // Filter change
            filterSelect.addEventListener('change', async function() {
                currentType = this.value;
                await updateView(currentType);
            });
        });
    </script>
</body>
</html>
