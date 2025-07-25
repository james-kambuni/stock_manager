@extends('layouts.admin')

@section('title', 'Quick Stats')

@section('content')
<div class="row mb-5">
    <div class="col-md-3">
        <div class="card text-white bg-gradient-primary shadow-sm hover-shadow">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">Total Products</h6>
                    <h4>{{ $productCount ?? 0 }}</h4>
                </div>
                <i class="fas fa-box fa-2x"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-gradient-success shadow-sm hover-shadow">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">Sales Today</h6>
                    <h5>{{ $salesCount }} PCS</h5>
                    <h6>Ksh {{ number_format($salesTotal, 2) }}</h6>
                </div>
                <i class="fas fa-shopping-cart fa-2x"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-gradient-warning shadow-sm hover-shadow">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">Purchases Today</h6>
                    <h5>{{ $purchasesCount }} PCS</h5>
                    <h6>Ksh {{ number_format($purchasesTotal, 2) }}</h6>
                </div>
                <i class="fas fa-truck-loading fa-2x"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-gradient-danger shadow-sm hover-shadow">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">Users</h6>
                    <h4>{{ $userCount ?? 0 }}</h4>
                </div>
                <i class="fas fa-users fa-2x"></i>
            </div>
        </div>
    </div>
</div>

<!-- Profit Chart -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white fw-semibold">
        <i class="fas fa-chart-area me-2 text-primary"></i> Profit Overview (Last 4 Months)
    </div>
    <div class="card-body" style="height: 400px;">
        <canvas id="profitChart"></canvas>
    </div>
</div>
@endsection

@section('footer')
<footer class="text-center text-muted py-3 small mt-auto">
    &copy; {{ date('Y') }} <strong>J-Solution Stock System</strong>. All rights reserved.
</footer>
@endsection

@push('styles')
<style>
    .hover-shadow:hover {
        transform: translateY(-3px);
        transition: 0.3s ease;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }
    .bg-gradient-primary {
        background: linear-gradient(to right, #0062E6, #33AEFF);
    }
    .bg-gradient-success {
        background: linear-gradient(to right, #28a745, #85e085);
    }
    .bg-gradient-warning {
        background: linear-gradient(to right, #ffc107, #ffe57f);
    }
    .bg-gradient-danger {
        background: linear-gradient(to right, #dc3545, #ff7f7f);
    }
    #profitChart {
        width: 100% !important;
        height: 100% !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('profitChart').getContext('2d');
    const profitChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'Profit (Ksh)',
                data: {!! json_encode($profits) !!},
                backgroundColor: 'rgba(13, 110, 253, 0.6)',
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 1,
                borderRadius: 10,
                barThickness: 40
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Ksh ' + value;
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
