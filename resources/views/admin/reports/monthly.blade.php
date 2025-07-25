@extends('layouts.admin')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@section('content')
<div class="container mt-4">
    <h4>Monthly Report — {{ now()->format('F Y') }}</h4>
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Sales</h5>
                    <p class="card-text">KSh {{ number_format($totalSales, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Total Purchases</h5>
                    <p class="card-text">KSh {{ number_format($totalPurchases, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Total Expenses</h5>
                    <p class="card-text">KSh {{ number_format($totalExpenses, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Net Profit</h5>
                    <p class="card-text">KSh {{ number_format($netProfit, 2) }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Monthly Overview Chart</h5>
                <canvas id="monthlyChart" height="120"></canvas>
            </div>
        </div>
    </div>
</div>

</div>
@endsection
@push('scripts')
<script>
    const ctx = document.getElementById('monthlyChart').getContext('2d');

    const monthlyChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Sales', 'Purchases', 'Expenses', 'Profit'],
            datasets: [{
                label: 'Amount (KSh)',
                data: [
                    {{ $totalSales }},
                    {{ $totalPurchases }},
                    {{ $totalExpenses }},
                    {{ $netProfit }}
                ],
                backgroundColor: [
                    'rgba(0, 123, 255, 0.6)',
                    'rgba(255, 193, 7, 0.6)',
                    'rgba(220, 53, 69, 0.6)',
                    'rgba(40, 167, 69, 0.6)'
                ],
                borderColor: [
                    'rgba(0, 123, 255, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(220, 53, 69, 1)',
                    'rgba(40, 167, 69, 1)'
                ],
                borderWidth: 1,
                borderRadius: 10,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'KSh ' + Number(context.raw).toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'KSh ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
