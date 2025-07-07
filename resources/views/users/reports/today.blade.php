<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Today's Reports</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            padding: 15px;
        }

        .card {
            margin-bottom: 25px;
        }

        h2, h5 {
            font-weight: 600;
        }

        @media (max-width: 576px) {
            h2 {
                font-size: 1.3rem;
            }

            h5 {
                font-size: 1rem;
            }

            .card-header, .card-body {
                padding: 1rem;
            }

            .table td, .table th {
                font-size: 0.75rem;
                padding: 0.4rem;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid px-2 px-sm-3">
    <div class="text-center mb-4">
        <h2 class="text-primary">Today's Report</h2>
        <p class="text-muted">{{ now()->format('l, F j, Y') }}</p>
    </div>

    <!-- Sales Report -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Sales Report</h5>
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
                                <td>{{ number_format($sale->unit_price, 2) }}</td>
                                <td>{{ number_format($sale->quantity * $sale->unit_price, 2) }}</td>
                                <td>{{ $sale->created_at->format('h:i A') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Purchase Report -->
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Purchase Report</h5>
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
                                <td>{{ number_format($purchase->unit_cost, 2) }}</td>
                                <td>{{ number_format($purchase->quantity * $purchase->unit_cost, 2) }}</td>
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
