@extends('layouts.users')

@section('title', 'Reports')

@section('content')
    <h4>{{ ucfirst($reportType) }} Report</h4>

    @if ($reportType === 'inventory')
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Stock</th>
                    <th>Cost Price</th>
                    <th>Selling Price</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->stock }}</td>
                        <td>{{ $product->cost_price }}</td>
                        <td>{{ $product->selling_price }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4">No products found.</td></tr>
                @endforelse
            </tbody>
        </table>

    @elseif ($reportType === 'sales')
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity Sold</th>
                    <th>Sold At</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $sale)
                    <tr>
                        <td>{{ $sale->product->name }}</td>
                        <td>{{ $sale->quantity }}</td>
                        <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3">No sales found.</td></tr>
                @endforelse
            </tbody>
        </table>

    @elseif ($reportType === 'purchases')
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity Purchased</th>
                    <th>Cost Price</th>
                    <th>Purchased At</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($purchases as $purchase)
                    <tr>
                        <td>{{ $purchase->product->name }}</td>
                        <td>{{ $purchase->quantity }}</td>
                        <td>{{ $purchase->cost_price }}</td>
                        <td>{{ $purchase->created_at->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4">No purchases found.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif
@endsection
