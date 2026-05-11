@extends('layouts.users')

@section('title', "Today's Reports")

@section('content')
<div class="container px-2 px-sm-4 py-3">

    <!-- Header -->
    <div class="text-center mb-3">
        <h4 class="text-primary">Today's Report</h4>
        <small class="text-muted">{{ now()->format('l, F j, Y') }}</small>
    </div>

    <!-- Tabs -->
    <div class="d-flex justify-content-between mb-3">
        <button class="btn btn-sm btn-outline-primary w-100 me-1" onclick="showTab('sales')">Sales</button>
        <button class="btn btn-sm btn-outline-success w-100 mx-1" onclick="showTab('purchases')">Purchases</button>
        <button class="btn btn-sm btn-outline-info w-100 mx-1" onclick="showTab('services')">Services</button>
        <button class="btn btn-sm btn-outline-danger w-100 ms-1" onclick="showTab('expenses')">Expenses</button>
    </div>

    <!-- ================= SALES ================= -->
    <div id="salesTab" class="report-tab">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">Sales Report</h6>
            </div>
            <div class="card-body">

                @if($sales->isEmpty())
                    <p class="text-muted">No sales recorded today.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Price</th>
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
    </div>

    <!-- ================= PURCHASES ================= -->
    <div id="purchasesTab" class="report-tab d-none">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">Purchase Report</h6>
            </div>
            <div class="card-body">

                @if($purchases->isEmpty())
                    <p class="text-muted">No purchases recorded today.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Cost</th>
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
    </div>

    <!-- ================= SERVICES ================= -->
    <div id="servicesTab" class="report-tab d-none">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">Service Report</h6>
            </div>
            <div class="card-body">

                @if($serviceSales->isEmpty())
                    <p class="text-muted">No services recorded today.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Service</th>
                                    <th>Amount</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($serviceSales as $service)
                                <tr>
                                    <td>{{ $service->service->name ?? 'N/A' }}</td>
                                    <td>Ksh {{ number_format($service->amount, 2) }}</td>
                                    <td>{{ $service->created_at->timezone(config('app.timezone'))->format('h:i A') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 alert alert-info">
                        <strong>Total Service Profit:</strong>
                        Ksh {{ number_format($serviceSales->sum('amount'), 2) }}
                    </div>
                @endif

            </div>
        </div>
    </div>

    <!-- ================= EXPENSES ================= -->
    <div id="expensesTab" class="report-tab d-none">
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">Expenses Report</h6>
            </div>
            <div class="card-body">

                @if($expenses->isEmpty())
                    <p class="text-muted">No expenses recorded today.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
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

</div>

<!-- ================= TAB SCRIPT ================= -->
<script>
function showTab(tab) {
    const tabs = ['sales', 'purchases', 'services', 'expenses'];

    tabs.forEach(t => {
        document.getElementById(t + 'Tab').classList.add('d-none');
    });

    document.getElementById(tab + 'Tab').classList.remove('d-none');
}
</script>

@endsection