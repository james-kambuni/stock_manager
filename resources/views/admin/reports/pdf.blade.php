<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ ucfirst($reportType) }} Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #eee; }
        .logo {
            text-align: center;
            margin-bottom: 10px;
        }
        .logo img {
            max-height: 80px;
        }
    </style>
</head>
<body>

    {{-- Tenant Logo --}}
    <div class="logo">
        @if(isset($tenant) && $tenant->logo)
            <img src="{{ public_path('storage/' . $tenant->logo) }}" alt="Logo">
        @endif
    </div>

    <h3 style="text-align: center;">{{ ucfirst($reportType) }} Report</h3>
    <p>From: {{ $from ?? 'N/A' }} | To: {{ $to ?? 'N/A' }}</p>

    @if ($reportType === 'inventory')
        @php $totalSales = 0; @endphp
        <table>
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
                @foreach ($inventoryData as $item)
                    @php
                        $profit = $item['sold'] * ($item['selling_price'] - $item['cost_price']);
                        $totalSales += $item['sold'] * $item['selling_price'];
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
                @endforeach
                <tr>
                    <td colspan="5"></td>
                    <th>Total Sales</th>
                    <th>{{ number_format($totalSales, 2) }}</th>
                </tr>
            </tbody>
        </table>

    @elseif ($reportType === 'sales')
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                    <th>Profit/Unit</th>
                    <th>Profit</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sales as $sale)
                    @php
                        $unitProfit = $sale->unit_price - $sale->product->cost_price;
                        $saleProfit = $unitProfit * $sale->quantity;
                    @endphp
                    <tr>
                        <td>{{ $sale->product->name ?? 'N/A' }}</td>
                        <td>{{ $sale->quantity }}</td>
                        <td>{{ number_format($sale->unit_price, 2) }}</td>
                        <td>{{ number_format($sale->quantity * $sale->unit_price, 2) }}</td>
                        <td>{{ number_format($unitProfit, 2) }}</td>
                        <td>{{ number_format($saleProfit, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @elseif ($reportType === 'purchases')
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity Purchased</th>
                    <th>Cost Price</th>
                    <th>Purchased At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchases as $purchase)
                    <tr>
                        <td>{{ $purchase->product->name }}</td>
                        <td>{{ $purchase->quantity }}</td>
                        <td>{{ $purchase->cost_price }}</td>
                        <td>{{ $purchase->created_at->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
