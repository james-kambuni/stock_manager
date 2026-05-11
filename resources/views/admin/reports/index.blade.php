@extends('layouts.admin')

@section('title', 'Reports')

@section('content')

<h4 class="mb-3">{{ ucfirst($reportType) }} Report</h4>

<div class="mb-3">
    <a href="{{ route('admin.reports.index', ['type' => 'inventory']) }}" class="btn btn-secondary">Inventory</a>
    <a href="{{ route('admin.reports.index', ['type' => 'sales']) }}" class="btn btn-success">Sales</a>
    <a href="{{ route('admin.reports.index', ['type' => 'purchases']) }}" class="btn btn-info">Purchases</a>
</div>

<form method="GET" action="{{ route('admin.reports.index') }}" class="row g-2 mb-4">
    <input type="hidden" name="type" value="{{ $reportType }}">

    <div class="col-md-3">
        <input type="date" name="from" value="{{ request('from') }}" class="form-control">
    </div>

    <div class="col-md-3">
        <input type="date" name="to" value="{{ request('to') }}" class="form-control">
    </div>

    <div class="col-md-3">
        <button type="submit" class="btn btn-primary w-100">Filter</button>
    </div>
</form>

{{-- ================= INVENTORY ================= --}}
@if ($reportType === 'inventory')

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Product</th>
            <th>Previous Stock</th>
            <th>Purchased</th>
            <th>Sold</th>
            <th>Current Stock</th>
            <th>Selling Price</th>
            <th>Profit</th>
        </tr>
    </thead>

    <tbody>
        @php $totalProfit = 0; @endphp

        @forelse ($inventoryData as $item)
            @php
                $purchased = $item['purchased'] ?? 0;
                $sold = $item['sold'] ?? 0;
                $cost = $item['cost_price'] ?? 0;
                $price = $item['selling_price'] ?? 0;

                // safe previous stock calculation
                $previous = $item['previous_stock'] ?? 0;

                $profit = ($price - $cost) * $sold;
                $totalProfit += $profit;
            @endphp

            <tr>
                <td>{{ $item['name'] ?? 'N/A' }}</td>
                <td>{{ $previous }}</td>
                <td>{{ $purchased }}</td>
                <td>{{ $sold }}</td>
                <td>{{ $item['current_stock'] ?? 0 }}</td>
                <td>{{ number_format($price, 2) }}</td>
                <td>{{ number_format($profit, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7">No inventory data found</td>
            </tr>
        @endforelse
    </tbody>

    <tfoot>
        <tr>
            <th colspan="6">Total Profit</th>
            <th>{{ number_format($totalProfit, 2) }}</th>
        </tr>
    </tfoot>
</table>

{{-- ================= SALES ================= --}}
@elseif ($reportType === 'sales')

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Unit Price</th>
            <th>Total</th>
            <th>Profit</th>
        </tr>
    </thead>

    <tbody>
        @php $totalProfit = 0; @endphp

        @foreach($sales as $sale)
            @php
                $cost = $sale->product->cost_price ?? 0;
                $unitProfit = $sale->unit_price - $cost;
                $profit = $unitProfit * $sale->quantity;
                $totalProfit += $profit;
            @endphp

            <tr>
                <td>{{ $sale->product->name ?? 'Deleted Product' }}</td>
                <td>{{ $sale->quantity }}</td>
                <td>{{ number_format($sale->unit_price, 2) }}</td>
                <td>{{ number_format($sale->unit_price * $sale->quantity, 2) }}</td>
                <td>{{ number_format($profit, 2) }}</td>
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th colspan="4">Total Profit</th>
            <th>{{ number_format($totalProfit, 2) }}</th>
        </tr>
    </tfoot>
</table>

{{-- ================= PURCHASES ================= --}}
@elseif ($reportType === 'purchases')

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Unit Cost</th>
            <th>Date</th>
        </tr>
    </thead>

    <tbody>
        @forelse ($purchases as $purchase)
            <tr>
                <td>{{ $purchase->product->name ?? 'Deleted Product' }}</td>
                <td>{{ $purchase->quantity }}</td>
                <td>{{ number_format($purchase->unit_cost, 2) }}</td>
                <td>{{ optional($purchase->created_at)->format('Y-m-d') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4">No purchases found</td>
            </tr>
        @endforelse
    </tbody>
</table>

@endif

@endsection