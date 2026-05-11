@extends('layouts.users')

@section('title', 'Reports')

@section('content')

<h4 class="mb-3">{{ ucfirst($reportType) }} Report</h4>

{{-- INVENTORY --}}
@if ($reportType === 'inventory')
    <table class="table table-bordered table-striped table-sm">
        <thead>
            <tr>
                <th>Product</th>
                <th>Stock</th>
                <th>Cost Price</th>
                <th>Selling Price</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reportData as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>Ksh {{ number_format($product->cost_price, 2) }}</td>
                    <td>Ksh {{ number_format($product->selling_price, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="4">No products found.</td></tr>
            @endforelse
        </tbody>
    </table>

{{-- SALES --}}
@elseif ($reportType === 'sales')
    <table class="table table-bordered table-striped table-sm">
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
            @forelse ($reportData as $sale)
                <tr>
                    <td>{{ $sale->product->name ?? 'N/A' }}</td>
                    <td>{{ $sale->quantity }}</td>
                    <td>Ksh {{ number_format($sale->unit_price, 2) }}</td>
                    <td>Ksh {{ number_format($sale->quantity * $sale->unit_price, 2) }}</td>
                    <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                </tr>
            @empty
                <tr><td colspan="5">No sales found.</td></tr>
            @endforelse
        </tbody>
    </table>

{{-- PURCHASES --}}
@elseif ($reportType === 'purchases')
    <table class="table table-bordered table-striped table-sm">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Unit Cost</th>
                <th>Total</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reportData as $purchase)
                <tr>
                    <td>{{ $purchase->product->name ?? 'N/A' }}</td>
                    <td>{{ $purchase->quantity }}</td>
                    <td>Ksh {{ number_format($purchase->unit_cost, 2) }}</td>
                    <td>Ksh {{ number_format($purchase->quantity * $purchase->unit_cost, 2) }}</td>
                    <td>{{ $purchase->created_at->format('Y-m-d') }}</td>
                </tr>
            @empty
                <tr><td colspan="5">No purchases found.</td></tr>
            @endforelse
        </tbody>
    </table>

{{-- SERVICES (ADD THIS) --}}
@elseif ($reportType === 'services')
    <table class="table table-bordered table-striped table-sm">
        <thead>
            <tr>
                <th>Service</th>
                <th>Amount</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reportData as $service)
                <tr>
                    <td>{{ $service->service->name ?? 'N/A' }}</td>
                    <td>Ksh {{ number_format($service->amount, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($service->date)->format('Y-m-d') }}</td>
                </tr>
            @empty
                <tr><td colspan="3">No services found.</td></tr>
            @endforelse
        </tbody>
    </table>

@endif

@endsection