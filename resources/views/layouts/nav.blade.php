<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/admin') }}">Stock Admin</a>
        <div>
            <a class="nav-link text-white" href="{{ route('admin.products.index') }}">Products</a>
            <a class="nav-link text-white" href="{{ route('admin.reports.generate') }}">Reports</a>
        </div>
    </div>
</nav>
