@extends('layouts.admin')

@section('title', 'Quick Stats')

@section('content')
    <!-- Quick Stats -->
    <div class="row mb-5">
        <div class="col-md-3">
            <div class="card text-white bg-primary shadow-sm">
                <div class="card-body">
                    <h6>Total Products</h6>
                    <h4>{{ $productCount ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success shadow-sm">
                <div class="card-body">
                    <h6>Sales Today</h6>
                    <h4>{{ $salesToday ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning shadow-sm">
                <div class="card-body">
                    <h6>Purchases Today</h6>
                    <h4>{{ $purchasesToday ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger shadow-sm">
                <div class="card-body">
                    <h6>Users</h6>
                    <h4>{{ $userCount ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Profit Chart -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white fw-semibold">Profit Overview (Last 4 Months)</div>
        <div class="card-body">
            <canvas id="profitChart" height="100"></canvas>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center text-muted mt-5 mb-3 small">
        &copy; {{ date('Y') }} Junik Stock System. All rights reserved.
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('profitChart').getContext('2d');
    const profitChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'Profit (Ksh)',
                data: {!! json_encode($profits) !!},
                backgroundColor: 'rgba(13, 110, 253, 0.2)',
                borderColor: '#0d6efd',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush

@push('styles')
<style>
.hover-shadow:hover {
    transform: translateY(-2px);
    transition: 0.3s ease;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
}
</style>
@endpush
