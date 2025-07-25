<table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Invoice No</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($invoices as $invoice)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $invoice->invoice_number }}</td>
                <td>{{ $invoice->customer_name }}</td>
                <td>{{ number_format($invoice->total, 2) }}</td>
                <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
