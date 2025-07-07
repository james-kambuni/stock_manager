@extends('layouts.admin')
@section('title', 'Monthly Profit Report')
@section('content')

<div class="card">
    <div class="card-header bg-primary text-white">
        Profit Chart (Last 4 Months)
    </div>
    <div class="card-body">
        <canvas id="profitChart" height="120"></canvas>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('profitChart').getContext('2d');

    const profitChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($profits, 'month')) !!},
            datasets: [{
                label: 'Profit (Ksh)',
                data: {!! json_encode(array_column($profits, 'profit')) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => 'Ksh ' + value
                    }
                }
            }
        }
    });
</script>
@endpush
