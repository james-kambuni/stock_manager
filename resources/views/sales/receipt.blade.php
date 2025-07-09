<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receipt</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            width: 250px; /* 58mm printers typically */
            margin: 0 auto;
            padding: 10px;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .receipt-line {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        table {
            width: 100%;
        }

        td {
            padding: 2px 0;
        }

        .right {
            text-align: right;
        }

        .print-btn {
            margin-top: 20px;
            text-align: center;
        }

        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="center bold">
        Junik drip<br>
        +254 700 123 456<br>
        ------------------------------<br>
        <span class="bold">SALES RECEIPT</span><br>
        {{ now()->format('Y-m-d H:i') }}
    </div>

    <div class="receipt-line"></div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="right">Qty</th>
                <th class="right">Price</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td class="right">{{ $item->quantity }}</td>
                    <td class="right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="right">{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="receipt-line"></div>

    <table>
        <tr>
            <td class="bold">TOTAL</td>
            <td class="right bold" colspan="3">{{ number_format($sale->total, 2) }}</td>
        </tr>
    </table>

    <div class="center">
        Thank you for your purchase!<br>
        No returns after 7 days
    </div>

    <div class="print-btn">
        <button onclick="window.print()">🖨️ Print Receipt</button>
    </div>

</body>
</html>
