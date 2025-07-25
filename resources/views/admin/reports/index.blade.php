@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
    <h4>{{ ucfirst($reportType) }} Report</h4>

    <div class="mb-3">
        <a href="{{ route('admin.reports.index', ['type' => 'inventory']) }}" class="btn btn-secondary">Inventory</a>
        <a href="{{ route('admin.reports.index', ['type' => 'sales']) }}" class="btn btn-success">Sales</a>
        <a href="{{ route('admin.reports.index', ['type' => 'purchases']) }}" class="btn btn-info">Purchases</a>
    </div>

    <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-2 mb-4">
        <input type="hidden" name="type" value="{{ $reportType }}">

        <div class="col-md-3">
            <label for="from" class="form-label">From</label>
            <input type="date" name="from" id="from" value="{{ request('from') }}" class="form-control">
        </div>

        <div class="col-md-3">
            <label for="to" class="form-label">To</label>
            <input type="date" name="to" id="to" value="{{ request('to') }}" class="form-control">
        </div>

        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    {{-- Print & PDF Export Buttons --}}
    <div class="mb-3 d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline-dark">🖨️ Print Report</button>

        <form method="GET" action="{{ route('admin.reports.export') }}">
            <input type="hidden" name="type" value="{{ $reportType }}">
            <input type="hidden" name="from" value="{{ request('from') }}">
            <input type="hidden" name="to" value="{{ request('to') }}">
            <button type="submit" class="btn btn-outline-primary">📄 Export to PDF</button>
        </form>
    </div>

    {{-- Report Tables --}}
    @if ($reportType === 'inventory')
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th>Previous Stock</th>
                    <th>Purchased</th>
                    <th>Sold</th>
                    <th>Current Stock</th>
                    <th>Selling Price (Ksh)</th>
                    <th>Profit (Ksh)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalRevenue = 0;
                    $totalProfit = 0;
                @endphp
                @forelse ($inventoryData as $item)
                    @php
                        $revenue = $item['sold'] * $item['selling_price'];
                        $profit = $item['sold'] * ($item['selling_price'] - $item['cost_price']);
                        $totalRevenue += $revenue;
                        $totalProfit += $profit;
                    @endphp
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['previous_stock'] }}</td>
                        <td>{{ $item['purchased'] }}</td>
                        <td>{{ $item['sold'] }}</td>
                        <td>{{ $item['current_stock'] }}</td>
                        <td>{{ number_format($item['selling_price'], 2) }}</td>
                        <td>{{ number_format($profit, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7">No inventory data found.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6" class="text-end">Total Profit</th>
                    <th>{{ number_format($totalProfit, 2) }} Ksh</th>
                </tr>
                <tr>
                    <th colspan="6" class="text-end">Total Sales Amount</th>
                    <th>{{ number_format($totalRevenue, 2) }} Ksh</th>
                </tr>
                <tr>
                    <th colspan="6" class="text-end">Total Expenses</th>
                    <th>{{ number_format($totalExpenses ?? 0, 2) }} Ksh</th>
                </tr>
            </tfoot>
        </table>

    @elseif ($reportType === 'sales')
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price (Ksh)</th>
                    <th>Total</th>
                    <th>Profit/Unit</th>
                    <th>Profit</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @php $totalProfit = 0; @endphp
                @foreach($sales as $sale)
                    @php
                        $unitProfit = $sale->unit_price - $sale->product->cost_price;
                        $saleProfit = $unitProfit * $sale->quantity;
                        $totalProfit += $saleProfit;
                    @endphp
                    <tr>
                        <td>{{ $sale->product->name ?? 'N/A' }}</td>
                        <td>{{ $sale->quantity }}</td>
                        <td>{{ number_format($sale->unit_price, 2) }}</td>
                        <td>{{ number_format($sale->quantity * $sale->unit_price, 2) }}</td>
                        <td>{{ number_format($unitProfit, 2) }}</td>
                        <td>{{ number_format($saleProfit, 2) }}</td>
                        <td>{{ $sale->created_at->format('h:i A') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-end">Total Profit</th>
                    <th colspan="2" class="text-start">{{ number_format($totalProfit, 2) }} Ksh</th>
                </tr>
            </tfoot>
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
