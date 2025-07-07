@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Reports</h2>

    <!-- <form action="{{ route('admin.reports.generate') }}" method="GET"> -->
        <div class="row mb-4">
            <div class="col-md-3">
                <label>Report Type</label>
                <select name="type" class="form-select" required>
                    <option value="sales">Sales Report</option>
                    <option value="purchases">Purchases Report</option>
                    <option value="inventory">Inventory Report</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Start Date</label>
                <input type="date" name="start_date" class="form-control">
            </div>
            <div class="col-md-3">
                <label>End Date</label>
                <input type="date" name="end_date" class="form-control">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-secondary w-100" type="submit">Generate</button>
            </div>
        </div>
    </form>

    @if(request('type') == 'inventory')
    <h5>Inventory Report</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Stock</th>
                <th>Cost</th>
                <th>Selling</th>
                <th>Profit/Unit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->stock }}</td>
                    <td>{{ $item->cost_price }}</td>
                    <td>{{ $item->selling_price }}</td>
                    <td>{{ $item->selling_price - $item->cost_price }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <h5>{{ ucfirst(request('type')) }} Report</h5>
    <canvas id="reportChart" height="80"></canvas>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @php
                $labels = []; $values = [];
            @endphp
            @foreach($data as $item)
                @php
                    $labels[] = $item->product->name;
                    $values[] = $item->quantity;
                @endphp
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->unit_cost ?? $item->unit_price }}</td>
                    <td>{{ ($item->unit_cost ?? $item->unit_price) * $item->quantity }}</td>
                    <td>{{ $item->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

</div>
@endsection


@if(request('type') !== 'inventory')
<script>
    const ctx = document.getElementById('reportChart');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: 'Quantity',
                data: {!! json_encode($values) !!},
                backgroundColor: 'rgba(13, 110, 253, 0.7)',
                borderColor: '#0d6efd',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endif
