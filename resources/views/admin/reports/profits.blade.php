@extends('layouts.admin')
@section('title', 'Profit Report')

@section('content')
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        Profit Chart (Last 4 Months)
    </div>
    <div class="card-body">
        <canvas id="profitChart" height="120"></canvas>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-success text-white">
                Top 10 by Profit<br>(Last 1 Month)
            </div>
            <div class="card-body">
                <canvas id="monthlyTopProductsChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-warning text-dark">
                Top 10 by Profit<br>(Last 1 Week)
            </div>
            <div class="card-body">
                <canvas id="weeklyTopProductsChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-info text-white">
                Top 10 by Profit<br>(Yesterday)
            </div>
            <div class="card-body">
                <canvas id="yesterdayTopProductsChart" height="200"></canvas>
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

    // Bar Chart for Monthly Profit
    new Chart(document.getElementById('profitChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode(collect($profits)->pluck('month')) !!},
            datasets: [{
                label: 'Profit (Ksh)',
                data: {!! json_encode(collect($profits)->pluck('profit')) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => 'Ksh ' + value.toLocaleString()
                    }
                }
            }
        }
    });

    // Reusable Doughnut Chart Function
    function generateDoughnutChart(canvasId, labels, data, colors) {
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
                        position: 'bottom',
                        labels: {
                            font: { size: 10 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return context.label + ': Ksh ' + context.raw.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    generateDoughnutChart(
        'monthlyTopProductsChart',
        {!! json_encode($monthlyTopProducts->pluck('product')) !!},
        {!! json_encode($monthlyTopProducts->pluck('profit')) !!},
        colorPalette
    );

    generateDoughnutChart(
        'weeklyTopProductsChart',
        {!! json_encode($weeklyTopProducts->pluck('product')) !!},
        {!! json_encode($weeklyTopProducts->pluck('profit')) !!},
        colorPalette
    );

    generateDoughnutChart(
        'yesterdayTopProductsChart',
        {!! json_encode($yesterdayTopProducts->pluck('product')) !!},
        {!! json_encode($yesterdayTopProducts->pluck('profit')) !!},
        colorPalette
    );
</script>
@endpush
