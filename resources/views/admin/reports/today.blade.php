@extends('layouts.admin')

@section('title', 'Today\'s Reports')

@section('content')
<div class="container">
    <div class="card mb-4 shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Sales Report for {{ now()->format('F j, Y') }}</h5>
        </div>
        <div class="card-body">
            @if($sales->isEmpty())
                <p>No sales recorded today.</p>
            @else
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Unit Price (Ksh)</th>
                            <th>Total</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                        <tr>
                            <td>{{ $sale->product->name ?? 'N/A' }}</td>
                            <td>{{ $sale->quantity }}</td>
                            <td>{{ number_format($sale->unit_price, 2) }}</td>
                            <td>{{ number_format($sale->quantity * $sale->unit_price, 2) }}</td>
                            <td>{{ $sale->created_at->format('h:i A') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Purchase Report for {{ now()->format('F j, Y') }}</h5>
        </div>
        <div class="card-body">
            @if($purchases->isEmpty())
                <p>No purchases recorded today.</p>
            @else
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Unit Cost (Ksh)</th>
                            <th>Total</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchases as $purchase)
                        <tr>
                            <td>{{ $purchase->product->name ?? 'N/A' }}</td>
                            <td>{{ $purchase->quantity }}</td>
                            <td>{{ number_format($purchase->unit_cost, 2) }}</td>
                            <td>{{ number_format($purchase->quantity * $purchase->unit_cost, 2) }}</td>
                            <td>{{ $purchase->created_at->format('h:i A') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
    <div class="card mt-4 shadow">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0">Financial Summary for {{ now()->format('F j, Y') }}</h5>
    </div>
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-4 mb-3">
                <h6>Gross Profit</h6>
                <p class="text-success fw-bold">Ksh {{ number_format($grossProfit, 2) }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <h6>Total Expenses</h6>
                <p class="text-danger fw-bold">Ksh {{ number_format($totalExpenses, 2) }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <h6>Net Profit</h6>
                <p class="fw-bold {{ $netProfit >= 0 ? 'text-primary' : 'text-danger' }}">
                    Ksh {{ number_format($netProfit, 2) }}
                </p>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
