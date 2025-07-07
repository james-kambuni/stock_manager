<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;

class InvoiceController extends Controller
{
    public function create()
{
    $products = Product::all();
    $user = auth()->user();
    $invoiceNumber = 'INV-' . now()->format('YmdHis') . rand(10, 99); // e.g., INV-20250703180101

    return view('admin.invoice', compact('products', 'user', 'invoiceNumber'));
}

}



