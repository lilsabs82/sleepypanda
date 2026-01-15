<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Database User - Sleepy Panda</title>
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
            font-size: 0.75rem;
            margin-bottom: 15px;
        }
        .stat-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .stat-icon {
            font-size: 1.8rem;
            color: var(--sp-text);
        }
        .stat-value {
            color: var(--sp-text);
            font-size: 1.8rem;
            font-weight: 700;
        }
        /* Table Section */
        .table-section {
            background-color: var(--sp-dark);
            border-radius: 12px;
            padding: 25px;
            border: 1px solid var(--sp-border);
        }
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 20px;
        }
        .table-search {
            flex: 1;
            max-width: 400px;
            background-color: var(--sp-darker);
            border: 1px solid var(--sp-border);
            border-radius: 8px;
            padding: 10px 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .table-search input {
            background: transparent;
            border: none;
            color: var(--sp-text);
            outline: none;
            width: 100%;
            font-size: 0.9rem;
        }
        .table-search input::placeholder { color: var(--sp-text-muted); }
        .table-search i { color: var(--sp-text-muted); }
        .table-actions {
            display: flex;
            gap: 15px;
        }
        .table-btn {
            background-color: var(--sp-darker);
            border: 1px solid var(--sp-border);
            color: var(--sp-text);
            padding: 10px 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        .table-btn:hover {
            background-color: var(--sp-border);
        }
        .table-btn i { font-size: 1rem; }
        /* User Table */
        .user-table {
            width: 100%;
            border-collapse: collapse;
        }
        .user-table th {
            color: var(--sp-text-muted);
            font-size: 0.85rem;
            font-weight: 500;
            text-align: left;
            padding: 15px 10px;
            border-bottom: 1px solid var(--sp-border);
        }
        .user-table td {
            padding: 20px 10px;
            border-bottom: 1px solid var(--sp-border);
            vertical-align: middle;
        }
        .user-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .user-cell-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--sp-border);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .user-cell-avatar i {
            color: var(--sp-text-muted);
            font-size: 1.2rem;
        }
        .user-cell-info {
            display: flex;
            flex-direction: column;
        }
        .user-cell-name {
            color: var(--sp-text);
            font-size: 0.9rem;
            font-weight: 500;
        }
        .user-cell-id {
            color: var(--sp-text-muted);
            font-size: 0.8rem;
        }
        .contact-cell {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .contact-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
        }
        .contact-item i { color: var(--sp-text-muted); }
        .contact-item a {
            color: #60a5fa;
            text-decoration: none;
        }
        .contact-item span { color: var(--sp-text); }
        .sleep-cell {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }
        .sleep-item {
            color: var(--sp-text);
            font-size: 0.85rem;
        }
        .status-badge {
            padding: 6px 16px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-block;
        }
        .status-badge.active {
            background-color: #3b82f6;
            color: white;
        }
        .status-badge.inactive {
            background-color: transparent;
            border: 1px solid #f87171;
            color: #f87171;
        }
        .last-active-cell {
            color: var(--sp-text);
            font-size: 0.85rem;
        }
        .last-active-cell div:last-child {
            color: var(--sp-text-muted);
        }
        @media (max-width: 1200px) {
            .stats-row { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .stats-row { grid-template-columns: 1fr; }
            .search-box { display: none; }
            .table-header { flex-direction: column; }
            .table-search { max-width: 100%; }
            .user-table { font-size: 0.8rem; }
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
                    <a href="{{ route('reset-password') }}" class="sidebar-submenu-item">Reset Password</a>
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
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" id="navbarSearch" placeholder="Search">
                </div>
            </div>
            <div class="user-info">
                <div class="user-avatar"></div>
                <span>Halo, <span id="userName">User</span></span>
                <button class="logout-btn" id="logoutBtn"><i class="bi bi-box-arrow-right"></i></button>
            </div>
        </nav>

        <div class="dashboard-content">
            <!-- Stats Row -->
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-label">Total Users</div>
                    <div class="stat-content">
                        <i class="bi bi-person stat-icon"></i>
                        <span class="stat-value" id="totalUsers">0</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Active Users</div>
                    <div class="stat-content">
                        <i class="bi bi-person stat-icon"></i>
                        <span class="stat-value" id="activeUsers">0</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">New Users</div>
                    <div class="stat-content">
                        <i class="bi bi-person-plus stat-icon"></i>
                        <span class="stat-value" id="newUsers">0</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Inactive Users</div>
                    <div class="stat-content">
                        <i class="bi bi-person-x stat-icon"></i>
                        <span class="stat-value" id="inactiveUsers">0</span>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="table-section">
                <div class="table-header">
                    <div class="table-search">
                        <i class="bi bi-search"></i>
                        <input type="text" id="searchInput" placeholder="Search by name, email, or ID">
                    </div>
                    <div class="table-actions">
                        <button class="table-btn" id="sortBtn">
                            <i class="bi bi-filter"></i>
                            Sort by
                        </button>
                        <button class="table-btn" id="refreshBtn">
                            <i class="bi bi-arrow-clockwise"></i>
                            Refresh
                        </button>
                    </div>
                </div>

                <table class="user-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Contact</th>
                            <th>Sleep Status</th>
                            <th>Status</th>
                            <th>Last Active</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        <!-- Rows will be rendered by JS -->
                    </tbody>
                </table>
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

            let allUsers = [];
            let sortOrder = 'asc';
            let searchQuery = '';

            // Load stats
            async function loadStats() {
                try {
                    const res = await axios.get('/api/users/stats', { headers });
                    document.getElementById('totalUsers').textContent = res.data.total_users;
                    document.getElementById('activeUsers').textContent = res.data.active_users;
                    document.getElementById('newUsers').textContent = res.data.new_users;
                    document.getElementById('inactiveUsers').textContent = res.data.inactive_users;
                } catch (e) {
                    console.error('Failed to load stats', e);
                }
            }

            // Load all users once
            async function loadAllUsers() {
                try {
                    const res = await axios.get('/api/users?per_page=100', { headers });
                    allUsers = res.data.users;
                    filterAndRenderTable();
                } catch (e) {
                    console.error('Failed to load users', e);
                }
            }

            // Filter and render table based on search
            function filterAndRenderTable() {
                let filtered = allUsers;

                if (searchQuery) {
                    const query = searchQuery.toLowerCase();
                    filtered = allUsers.filter(user =>
                        user.name.toLowerCase().includes(query) ||
                        user.email.toLowerCase().includes(query) ||
                        user.phone.toLowerCase().includes(query) ||
                        user.id.toString().includes(query)
                    );
                }

                // Sort
                filtered.sort((a, b) => {
                    const nameA = a.name.toLowerCase();
                    const nameB = b.name.toLowerCase();
                    if (sortOrder === 'asc') {
                        return nameA.localeCompare(nameB);
                    } else {
                        return nameB.localeCompare(nameA);
                    }
                });

                renderTable(filtered);
            }

            function renderTable(data) {
                const tbody = document.getElementById('userTableBody');
                let html = '';

                if (data.length === 0) {
                    html = '<tr><td colspan="5" style="text-align: center; color: var(--sp-text-muted);">No users found</td></tr>';
                } else {
                    data.forEach(user => {
                        html += `
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-cell-avatar">
                                            <i class="bi bi-person"></i>
                                        </div>
                                        <div class="user-cell-info">
                                            <span class="user-cell-name">${user.name}</span>
                                            <span class="user-cell-id">ID #${user.id}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="contact-cell">
                                        <div class="contact-item">
                                            <i class="bi bi-envelope"></i>
                                            <a href="mailto:${user.email}">${user.email}</a>
                                        </div>
                                        <div class="contact-item">
                                            <span>${user.phone}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="sleep-cell">
                                        <span class="sleep-item">Avg. Sleep: ${user.avgSleep}</span>
                                        <span class="sleep-item">Quality: ${user.quality}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge ${user.status}">${user.status === 'active' ? 'Active' : 'Not Active'}</span>
                                </td>
                                <td>
                                    <div class="last-active-cell">
                                        <div>${user.lastActive}</div>
                                        <div>${user.lastTime}</div>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                }

                tbody.innerHTML = html;
            }

            // Initial load
            await loadStats();
            await loadAllUsers();

            // Search functionality
            let searchTimeout;
            const searchInput = document.getElementById('searchInput');
            const navbarSearch = document.getElementById('navbarSearch');

            // Table search - for users
            searchInput.addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    searchQuery = e.target.value;
                    filterAndRenderTable();
                }, 300);
            });

            // Navbar search - for menu/pages
            const menuItems = [
                { name: 'Dashboard', url: '/dashboard' },
                { name: 'Jurnal', url: '/jurnal' },
                { name: 'Report', url: '/report' },
                { name: 'Database User', url: '/database-user' },
                { name: 'Update Data', url: '/update-data' },
                { name: 'Reset Password', url: '/reset-password-admin' }
            ];

            // Create dropdown for navbar search
            const searchDropdown = document.createElement('div');
            searchDropdown.id = 'searchDropdown';
            searchDropdown.style.cssText = 'position: absolute; top: 100%; left: 0; right: 0; background: var(--sp-dark); border: 1px solid var(--sp-border); border-radius: 8px; margin-top: 5px; display: none; z-index: 1001; max-height: 200px; overflow-y: auto;';
            navbarSearch.parentElement.style.position = 'relative';
            navbarSearch.parentElement.appendChild(searchDropdown);

            navbarSearch.addEventListener('input', function(e) {
                const query = e.target.value.toLowerCase();
                if (!query) {
                    searchDropdown.style.display = 'none';
                    return;
                }

                const filtered = menuItems.filter(item =>
                    item.name.toLowerCase().includes(query)
                );

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
                setTimeout(() => {
                    searchDropdown.style.display = 'none';
                }, 200);
            });

            // Sort functionality
            document.getElementById('sortBtn').addEventListener('click', function() {
                sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
                filterAndRenderTable();
            });

            // Refresh functionality
            document.getElementById('refreshBtn').addEventListener('click', function() {
                searchInput.value = '';
                searchQuery = '';
                sortOrder = 'asc';
                loadStats();
                loadAllUsers();
            });
        });
    </script>
</body>
</html>
