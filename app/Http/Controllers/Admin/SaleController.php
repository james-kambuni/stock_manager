<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;

class SaleController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric',
        ]);

        $product = Product::find($request->product_id);

        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Not enough stock.');
        }

        $product->stock -= $request->quantity;
        $product->save();

        Sale::create([
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'unit_price' => $product->selling_price,
        ]);

        return back()->with('success', 'Sale recorded.');
    }
}
