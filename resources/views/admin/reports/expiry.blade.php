@extends('layouts.admin')

@section('content')
<div class="container">
    <h3 class="mb-3">Expiring Stock Report</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Remaining Quantity</th>
                <th>Expiry Date</th>
                <th>Days Left</th>
            </tr>
        </thead>
        <tbody>
           <tbody>
            @foreach ($batches as $batch)
                <tr>
                    <td>{{ $batch->product?->name ?? 'Product Not Found' }}</td>
                    <td>{{ $batch->remaining }}</td>
                    <td>{{ $batch->expiry_date }}</td>
                    <td>{{ \Carbon\Carbon::now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($batch->expiry_date)->startOfDay(), false) }}</td>
                </tr>
            @endforeach
</tbody>

        </tbody>
    </table>
</div>
@endsection
