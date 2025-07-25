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
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        .sidebar {
            width: 220px;
            background-color: #566573;
            color: #fff;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar a {
            color: #fff;
            padding: 12px;
            display: block;
            text-decoration: none;
        }

        .sidebar a:hover, .sidebar .active {
            background-color: #85c1e9;
        }

        .main-content {
            margin-left: 220px;
            flex: 1;
            padding: 20px;
            background: rgba(59, 130, 246, 0.10);
        }

        footer {
            margin-left: 220px;
            background: #f8f9fa;
        }
    </style>

    @stack('styles')
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center py-3 border-bottom">Admin Panel</h4>

    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>

    <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
        <i class="fa fa-box"></i> Products
    </a>

    <a href="{{ route('admin.expenses.index') }}" class="{{ request()->routeIs('admin.expenses.index') ? 'active' : '' }}">
        <i class="fas fa-money-bill-wave"></i> View Expenses
    </a>

    <a href="{{ route('admin.transactions.create') }}" class="{{ request()->routeIs('admin.transactions.create') ? 'active' : '' }}">
        <i class="fas fa-exchange-alt"></i> Record Transaction
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

<!-- Main Content -->
<div class="main-content">
    <h2>@yield('title')</h2>
    <hr>
    @yield('content')
</div>

<!-- Footer -->
<footer class="text-center mt-5 py-3 bg-light border-top">
    @yield('footer', '© ' . date('Y') . ' J-Solutions Ltd. All rights reserved.')
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
