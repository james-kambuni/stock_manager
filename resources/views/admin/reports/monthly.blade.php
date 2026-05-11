@extends('layouts.admin')

@section('title', 'Monthly Report')

@section('content')

<!-- ================= CUSTOM STYLES ================= -->
<style>
.card-modern {
    border: none;
    border-radius: 16px;
    padding: 20px;
    color: #fff;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.card-modern:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.12);
}

.card-modern h6 {
    opacity: 0.85;
    font-size: 14px;
    margin-bottom: 5px;
}

.card-modern h3 {
    font-weight: 700;
    margin-top: 8px;
    font-size: 26px;
}

.card-modern small {
    opacity: 0.9;
    font-size: 13px;
}

.bg-gradient-blue {
    background: linear-gradient(135deg, #0d6efd, #3a86ff);
}

.bg-gradient-red {
    background: linear-gradient(135deg, #dc3545, #ff6b6b);
}

.bg-gradient-dark {
    background: linear-gradient(135deg, #212529, #343a40);
}

.icon-box {
    position: absolute;
    right: 20px;
    top: 20px;
    font-size: 32px;
    opacity: 0.2;
}
</style>

<div class="container mt-4">

    <h4 class="fw-bold mb-4">
        Monthly Report — {{ now()->format('F Y') }}
    </h4>

    <!-- ================= SUMMARY CARDS ================= -->
    <div class="row g-4">

        <!-- REVENUE -->
        <div class="col-md-4">
            <div class="card-modern bg-gradient-blue shadow-sm">
                <div class="icon-box">
                    <i class="fas fa-coins"></i>
                </div>
                <h6>Total Revenue</h6>
                <h3>KSh {{ number_format(($totalSales + $serviceProfit) ?? 0, 2) }}</h3>

                <small>
                    Products: {{ number_format($totalSales ?? 0, 2) }} <br>
                    Services: {{ number_format($serviceProfit ?? 0, 2) }}
                </small>
            </div>
        </div>

        <!-- COSTS -->
        <div class="col-md-4">
            <div class="card-modern bg-gradient-red shadow-sm">
                <div class="icon-box">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <h6>Total Costs</h6>
                <h3>KSh {{ number_format(($totalPurchases + $totalExpenses) ?? 0, 2) }}</h3>

                <small>
                    Purchases: {{ number_format($totalPurchases ?? 0, 2) }} <br>
                    Expenses: {{ number_format($totalExpenses ?? 0, 2) }}
                </small>
            </div>
        </div>

        <!-- PROFIT -->
        <div class="col-md-4">
            <div class="card-modern bg-gradient-dark shadow-sm">
                <div class="icon-box">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h6>Net Profit</h6>
                <h3>KSh {{ number_format($netProfit ?? 0, 2) }}</h3>

                <small>
                    Product: {{ number_format($productProfit ?? 0, 2) }} <br>
                    Service: {{ number_format($serviceProfit ?? 0, 2) }}
                </small>
            </div>
        </div>

    </div>

    <!-- ================= CHART ================= -->
    <div class="row mt-5">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Performance Overview</h5>
                    <canvas id="monthlyChart" height="110"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')

<!-- Font Awesome (icons) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const ctx = document.getElementById('monthlyChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Product Profit', 'Service Profit', 'Expenses', 'Net Profit'],
            datasets: [{
                data: [
                    {{ $productProfit ?? 0 }},
                    {{ $serviceProfit ?? 0 }},
                    {{ $totalExpenses ?? 0 }},
                    {{ $netProfit ?? 0 }}
                ],
                backgroundColor: [
                    '#0d6efd',
                    '#0dcaf0',
                    '#dc3545',
                    '#198754'
                ],
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
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

});
</script>

@endpush