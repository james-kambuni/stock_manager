
<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>
    <style>
        @media print {
            body {
                width: 58mm;
                font-family: 'Courier New', monospace;
                font-size: 12px;
            }
            .receipt {
                width: 100%;
                padding: 5px;
            }
            .center { text-align: center; }
            .right { text-align: right; }
            table { width: 100%; border-collapse: collapse; }
            td { padding: 2px 0; }
            hr { border-top: 1px dashed #000; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="receipt">
        <div class="center">
            <strong>Your Business Name</strong><br>
            0712345678<br>
            {{ date('Y-m-d H:i') }}
        </div>
        <hr>
        <table>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item->name }} (x{{ $item->quantity }})</td>
                    <td class="right">{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </table>
        <hr>
        <table>
            <tr>
                <td><strong>Total:</strong></td>
                <td class="right"><strong>Ksh {{ number_format($total, 2) }}</strong></td>
            </tr>
        </table>
        <hr>
        <div class="center">
            Thank you for your purchase!
        </div>
    </div>
</body>
</html>
