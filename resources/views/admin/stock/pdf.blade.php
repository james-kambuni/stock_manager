<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Reconciliation PDF</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Stock Reconciliation Report</h2>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>System Stock</th>
                <th>Physical Stock</th>
                <th>Discrepancy</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                @php
                    $physical = $product->stockCount->physical_stock ?? 0;
                    $discrepancy = $physical - $product->stock;
                @endphp
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>{{ $physical }}</td>
                    <td>{{ $discrepancy }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
