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
            min-height: 100vh;
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
        <a href="{{ route('admin.invoice') }}" class="{{ request()->routeIs('admin.invoice') ? 'active' : '' }}">
         <i class="fas fa-file-invoice"></i> Invoice Generator
        </a>
        <a href="{{ route('admin.reports.today') }}" class="{{ request()->routeIs('admin.reports.today') ? 'active' : '' }}">
            <i class="fas fa-calendar-day"></i> Today's Report
        </a>
        <a href="{{ route('admin.reports.index', ['type' => 'inventory']) }}" class="{{ request()->routeIs('admin.reports.index') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i> Reports
        </a>
        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i> Manage Users
        </a>
        <a href="{{ route('admin.profits') }}" class="{{ request()->routeIs('admin.profits') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i> Profit Report
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
