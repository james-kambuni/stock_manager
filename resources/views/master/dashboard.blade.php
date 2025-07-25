@extends('layouts.master')

@section('title', 'Master Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white shadow rounded" style="background: linear-gradient(45deg, #007bff, #00c6ff);">
                <div class="card-body">
                    <h5>Total Tenants</h5>
                    <h3>{{ $tenants->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white shadow rounded" style="background: linear-gradient(45deg, #28a745, #66ffb2);">
                <div class="card-body">
                    <h5>Total Products</h5>
                    <h3>{{ $productCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white shadow rounded" style="background: linear-gradient(45deg, #17a2b8, #71eaff);">
                <div class="card-body">
                    <h5>Total Sales</h5>
                    <h3>KES {{ number_format($totalSales, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-dark shadow rounded" style="background: linear-gradient(45deg, #ffc107, #ffe082);">
                <div class="card-body">
                    <h5>Total Purchases</h5>
                    <h3>KES {{ number_format($totalPurchases, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Graph -->
    <div class="card shadow rounded mb-4">
        <div class="card-body">
            <h5 class="fw-semibold">Sales by Tenant</h5>
            <canvas id="salesChart" height="100"></canvas>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-muted small text-center mt-5">
        <hr>
        <p>&copy; {{ date('Y') }} Your Company Name. All rights reserved.</p>
    </footer>
</div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($salesChart['labels']) !!},
            datasets: [{
                label: 'Total Sales (KES)',
                data: {!! json_encode($salesChart['data']) !!},
                backgroundColor: 'rgba(78, 115, 223, 0.8)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'KES ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'KES ' + value;
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
