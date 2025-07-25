@extends('layouts.users')

@section('title', 'Receipt')

@section('content')
<style>
    @media print {
        body {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }

        /* Hide everything except the receipt container */
        body * {
            visibility: hidden;
        }

        .receipt-container, .receipt-container * {
            visibility: visible;
        }

        .receipt-container {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 58mm;
            padding: 8px;
        }

        .print-btn {
            display: none;
        }

        .receipt-line {
            border-top: 1px dashed #000;
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td, th {
            padding: 2px 0;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }
    }

    /* Screen styles */
    .receipt-container {
        width: 250px;
        margin: 0 auto;
        font-family: 'Courier New', monospace;
        font-size: 12px;
        background: #fff;
        padding: 10px;
        border: 1px solid #ccc;
    }

    .receipt-line {
        border-top: 1px dashed #000;
        margin: 5px 0;
    }

    .right {
        text-align: right;
    }

    .center {
        text-align: center;
    }

    .bold {
        font-weight: bold;
    }

    .print-btn {
        margin-top: 15px;
        text-align: center;
    }

    table {
        width: 100%;
    }

    td, th {
        padding: 2px 0;
    }
</style>

<div class="receipt-container">
    <div class="center bold">
        Junik Drip<br>
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
                    <td>{{ Str::limit($item->product->name, 10) }}</td>
                    <td class="right">{{ $item->quantity }}</td>
                    <td class="right">{{ number_format($item->unit_price, 0) }}</td>
                    <td class="right">{{ number_format($item->total, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="receipt-line"></div>

    <table>
        <tr>
            <td class="bold">TOTAL</td>
            <td class="right bold" colspan="3">{{ number_format($sale->total, 0) }}</td>
        </tr>
    </table>

    <div class="receipt-line"></div>

    <div class="center">
        Thank you for your purchase!<br>
        No returns after 7 days.
    </div>

    <div class="print-btn">
        <button onclick="window.print()">🖨️ Print Receipt</button>
    </div>
</div>
@endsection
