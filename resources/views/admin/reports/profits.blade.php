@extends('layouts.admin')

@section('title', 'Profit Report')

@section('content')

<div class="container-fluid mt-3">

    <!-- ================= MAIN PROFIT CHART ================= -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <strong>Profit Chart (Last 4 Months)</strong>
        </div>
        <div class="card-body" style="height: 320px;">
            <canvas id="profitChart"></canvas>
        </div>
    </div>

    <!-- ================= TOP CARDS ================= -->
    <div class="row">

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    Top 10 Products (Last Month)
                </div>
                <div class="card-body" style="height: 300px;">
                    <canvas id="monthlyTopProductsChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-warning text-dark">
                    Top 10 Products (Last Week)
                </div>
                <div class="card-body" style="height: 300px;">
                    <canvas id="weeklyTopProductsChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    Top 10 Products (Yesterday)
                </div>
                <div class="card-body" style="height: 300px;">
                    <canvas id="yesterdayTopProductsChart"></canvas>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const colorPalette = [
    '#4dc9f6', '#f67019', '#f53794', '#537bc4', '#acc236',
    '#166a8f', '#00a950', '#58595b', '#8549ba', '#e6194b'
];

// ================= SAFE DATA =================
const months = {!! json_encode($profits->pluck('month') ?? [], JSON_NUMERIC_CHECK) !!};
const profitData = {!! json_encode($profits->pluck('profit') ?? [], JSON_NUMERIC_CHECK) !!};

// ================= PROFIT BAR CHART =================
new Chart(document.getElementById('profitChart'), {
    type: 'bar',
    data: {
        labels: months,
        datasets: [{
            label: 'Profit (KSh)',
            data: profitData,
            backgroundColor: 'rgba(54, 162, 235, 0.7)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1,
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: value => 'KSh ' + value.toLocaleString()
                }
            }
        }
    }
});

// ================= SAFE FUNCTION =================
function generateDoughnutChart(canvasId, labels, data, colors) {

    if (!labels.length || !data.length) return;

    new Chart(document.getElementById(canvasId), {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                borderColor: '#fff',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// ================= MONTHLY =================
generateDoughnutChart(
    'monthlyTopProductsChart',
    {!! json_encode($monthlyTopProducts->pluck('product') ?? []) !!},
    {!! json_encode($monthlyTopProducts->pluck('profit') ?? [], JSON_NUMERIC_CHECK) !!},
    colorPalette
);

// ================= WEEKLY =================
generateDoughnutChart(
    'weeklyTopProductsChart',
    {!! json_encode($weeklyTopProducts->pluck('product') ?? []) !!},
    {!! json_encode($weeklyTopProducts->pluck('profit') ?? [], JSON_NUMERIC_CHECK) !!},
    colorPalette
);

// ================= YESTERDAY =================
generateDoughnutChart(
    'yesterdayTopProductsChart',
    {!! json_encode($yesterdayTopProducts->pluck('product') ?? []) !!},
    {!! json_encode($yesterdayTopProducts->pluck('profit') ?? [], JSON_NUMERIC_CHECK) !!},
    colorPalette
);
</script>
@endpush