<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - @yield('title', 'Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        font-family: sans-serif;
        transition: background 0.3s, color 0.3s;
    }

    body {
        display: flex;
        flex-direction: column;
    }

    .wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .sidebar {
        width: 220px;
        background-color: #2c3e50;
        color: #fff;
        position: fixed;
        height: 100vh;
        overflow-y: auto;
        z-index: 1000;
        transition: transform 0.3s ease;
    }

    .sidebar a {
        color: #fff;
        padding: 12px 20px;
        display: block;
        text-decoration: none;
    }

    .sidebar a:hover,
    .sidebar .active {
        background-color: #3498db;
    }

    .main-content {
        flex: 1;
        margin-left: 220px;
        padding: 20px;
        background: rgba(59, 130, 246, 0.10);
        transition: margin-left 0.3s;
    }

    footer {
        margin-left: 220px;
        background: #f8f9fa;
        padding: 15px;
        text-align: center;
        border-top: 1px solid #ddd;
    }

    /* Sidebar toggle button (hidden on large screens) */
    .menu-toggle {
        display: none;
    }

    /* Dark mode */
    .dark-mode {
        background-color: #1a1a1a;
        color: #e0e0e0;
    }

    .dark-mode .sidebar {
        background-color: #111;
    }

    .dark-mode footer {
        background-color: #333;
        color: #ccc;
    }

    .toggle-container {
        position: absolute;
        right: 15px;
        top: 15px;
    }

    .toggle-container button {
        background: none;
        border: none;
        font-size: 18px;
        color: #fff;
    }

    .dark-mode .toggle-container button {
        color: #f1f1f1;
    }

    /* Responsive styles */
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.show {
            transform: translateX(0);
        }

        .main-content,
        footer {
            margin-left: 0;
        }

        .menu-toggle {
            display: block;
            position: fixed;
            top: 10px;
            right: 15px;
            background: none;
            border: none;
            font-size: 24px;
            z-index: 1001;
            color: #000;
        }


        .dark-mode .menu-toggle {
            color: #fff;
        }
    }
</style>



    @stack('styles')
</head>
<body>

<!-- Sidebar Toggle Button -->
<button class="menu-toggle d-md-none" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom">
        <h4 class="m-0">Admin Panel</h4>
        <div class="toggle-container">
            <button onclick="toggleDarkMode()" title="Toggle Dark Mode">
                <i class="fas fa-adjust"></i>
            </button>
        </div>
    </div>

    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>

    <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
        <i class="fa fa-box"></i> Products
    </a>
    <a href="{{ route('admin.services.index') }}">
    <i class="fas fa-tools"></i> Services
</a>

    <a href="{{ route('admin.expenses.index') }}" class="{{ request()->routeIs('admin.expenses.index') ? 'active' : '' }}">
        <i class="fas fa-money-bill-wave"></i> View Expenses
    </a>

    <a href="{{ route('admin.transactions.create') }}" class="{{ request()->routeIs('admin.transactions.create') ? 'active' : '' }}">
        <i class="fas fa-exchange-alt"></i> Record S & P
    </a>

    <a href="{{ route('admin.stock-reconciliation.index') }}" class="{{ request()->routeIs('admin.stock-reconciliation.index') ? 'active' : '' }}">
        <i class="fas fa-balance-scale"></i> Stock Reconciliation
    </a>

    <a href="{{ route('admin.invoice') }}" class="{{ request()->routeIs('admin.invoice') ? 'active' : '' }}">
        <i class="fas fa-file-invoice"></i> Invoice Generator
    </a>

    {{-- Reports Dropdown --}}
    <a class="d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#reportsMenu" role="button" aria-expanded="false" aria-controls="reportsMenu">
        <span><i class="fas fa-chart-bar"></i> Reports</span>
        <i class="fas fa-chevron-down small"></i>
    </a>
    <div class="collapse ps-3 {{ request()->is('admin/reports*') || request()->routeIs('admin.profits') ? 'show' : '' }}" id="reportsMenu">
        <a href="{{ route('admin.reports.today') }}" class="d-block {{ request()->routeIs('admin.reports.today') ? 'active' : '' }}">
            <i class="fas fa-calendar-day"></i> Today's Report
        </a>
        <a href="{{ route('admin.reports.expiry') }}" class="d-block {{ request()->routeIs('admin.reports.expiry') ? 'active' : '' }}">
            <i class="fas fa-calendar-times"></i> Expiry Report
        </a>
        <a href="{{ route('admin.reports.monthly') }}" class="d-block {{ request()->routeIs('admin.reports.monthly') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i> Monthly Report
        </a>
        <a href="{{ route('admin.reports.index', ['type' => 'inventory']) }}" class="d-block {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}">
            <i class="fas fa-boxes"></i> Inventory Report
        </a>
        <a href="{{ route('admin.profits') }}" class="d-block {{ request()->routeIs('admin.profits') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i> Profit Report
        </a>
    </div>

    <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="fas fa-users"></i> Manage Users
    </a>

    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</div>

<div class="wrapper">
    <!-- Main Content -->
    <div class="main-content">
        <h2>@yield('title')</h2>
        <hr>
        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="text-center py-3 bg-light border-top">
        @yield('footer', '© ' . date('Y') . ' Jtech Systems. All rights reserved.')
    </footer>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('show');
    }

    function toggleDarkMode() {
        const body = document.body;
        body.classList.toggle('dark-mode');
        localStorage.setItem('darkMode', body.classList.contains('dark-mode') ? 'on' : 'off');
    }

    // On load, check localStorage for theme
    document.addEventListener("DOMContentLoaded", function () {
        if (localStorage.getItem('darkMode') === 'on') {
            document.body.classList.add('dark-mode');
        }
    });
</script>
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('show');
    }

    function toggleDarkMode() {
        const body = document.body;
        body.classList.toggle('dark-mode');
        localStorage.setItem('darkMode', body.classList.contains('dark-mode') ? 'on' : 'off');
    }

    // On load, check localStorage for theme
    document.addEventListener("DOMContentLoaded", function () {
        if (localStorage.getItem('darkMode') === 'on') {
            document.body.classList.add('dark-mode');
        }
    });

    // Hide sidebar when clicking outside on small screens
    document.addEventListener('click', function (event) {
        const sidebar = document.getElementById('sidebar');
        const toggleButton = document.querySelector('.menu-toggle');

        // Only apply on small screens
        if (window.innerWidth <= 768) {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnToggle = toggleButton.contains(event.target);

            if (!isClickInsideSidebar && !isClickOnToggle) {
                sidebar.classList.remove('show');
            }
        }
    });
</script>


@stack('scripts')
</body>
</html>
