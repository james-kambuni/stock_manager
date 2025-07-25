@extends('layouts.users')

@section('title', "Today's Reports")

@section('content')
<div class="container px-2 px-sm-4 py-3">
    <div class="text-center mb-4">
        <h2 class="text-primary">Today's Report</h2>
        <p class="text-muted">{{ now()->format('l, F j, Y') }}</p>
    </div>

    {{-- Sales Report --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Sales Report</h5>
        </div>
        <div class="card-body">
            @if($sales->isEmpty())
                <p class="text-muted">No sales recorded today.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales as $sale)
                            <tr>
                                <td>{{ $sale->product->name ?? 'N/A' }}</td>
                                <td>{{ $sale->quantity }}</td>
                                <td>Ksh {{ number_format($sale->unit_price, 2) }}</td>
                                <td>Ksh {{ number_format($sale->quantity * $sale->unit_price, 2) }}</td>
                                <td>{{ $sale->created_at->format('h:i A') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Purchase Report --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Purchase Report</h5>
        </div>
        <div class="card-body">
            @if($purchases->isEmpty())
                <p class="text-muted">No purchases recorded today.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Unit Cost</th>
                                <th>Total</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchases as $purchase)
                            <tr>
                                <td>{{ $purchase->product->name ?? 'N/A' }}</td>
                                <td>{{ $purchase->quantity }}</td>
                                <td>Ksh {{ number_format($purchase->unit_cost, 2) }}</td>
                                <td>Ksh {{ number_format($purchase->quantity * $purchase->unit_cost, 2) }}</td>
                                <td>{{ $purchase->created_at->format('h:i A') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Expenses Report --}}
    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">Expenses Report</h5>
        </div>
        <div class="card-body">
            @if($expenses->isEmpty())
                <p class="text-muted">No expenses recorded today.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th>Notes</th>
                                <th>Amount</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expenses as $expense)
                            <tr>
                                <td>{{ $expense->category }}</td>
                                <td>{{ $expense->notes ?? '-' }}</td>
                                <td>Ksh {{ number_format($expense->amount, 2) }}</td>
                                <td>{{ $expense->created_at->format('h:i A') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
