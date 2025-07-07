<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Product;

class PurchaseController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric',
            'unit_cost' => 'required|numeric',
        ]);

        $product = Product::find($request->product_id);
        $product->stock += $request->quantity;
        $product->cost_price = $request->unit_cost;
        $product->save();

        Purchase::create([
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'unit_cost' => $request->unit_cost,
        ]);

        return back()->with('success', 'Purchase recorded.');
    }
}
