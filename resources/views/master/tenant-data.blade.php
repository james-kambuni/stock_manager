@extends('layouts.master')

@section('title', 'Tenant Data')

@section('content')
    <h3>Data for Tenant ID: {{ $id }}</h3>

    <h4>Products</h4>
    <ul>
        @foreach($products as $product)
            <li>{{ $product->name }} - Stock: {{ $product->stock }}</li>
        @endforeach
    </ul>

    <h4>Sales</h4>
    <ul>
        @foreach($sales as $sale)
            <li>Total: {{ $sale->total }} on {{ $sale->sale_date }}</li>
        @endforeach
    </ul>

    <h4>Purchases</h4>
    <ul>
        @foreach($purchases as $purchase)
            <li>Product ID: {{ $purchase->product_id }} - Quantity: {{ $purchase->quantity }}</li>
        @endforeach
    </ul>
@endsection
