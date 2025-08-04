<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\StockBatch;

class PurchaseController extends Controller
{
    public function create()
    {
        $products = Product::where('tenant_id', auth()->user()->tenant_id)->get();
        return view('admin.purchases', compact('products'));
    }

    public function store(Request $request)
    {
       $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'unit_cost' => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date',
        ]);

        $product = Product::where('id', $request->product_id)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->firstOrFail();

        // Create the purchase record
        $purchase = Purchase::create([
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'unit_cost' => $request->unit_cost,
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        // Create stock batch
        StockBatch::create([
            'product_id' => $product->id,
            'purchase_id' => $purchase->id,
            'quantity' => $request->quantity,
            'remaining' => $request->quantity,
            'expiry_date' => $request->expiry_date,
            'cost_price' => $request->unit_cost,
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        // Update product stock and cost price
        $product->stock += $request->quantity;
        $product->cost_price = $request->unit_cost;
        $product->save();

        return redirect()->route('admin.purchases.create')->with('success', 'Purchase recorded successfully.');
    }
}
