<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
 use App\Models\Sale;
 use App\Models\Purchase;

class ProductController extends Controller
{
    // Show product list and forms
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    // Record a purchase
  

public function purchase(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|numeric|min:1',
        'cost_price' => 'required|numeric|min:0',
    ]);

    $product = Product::findOrFail($request->product_id);
    $product->stock += $request->quantity;
    $product->cost_price = $request->cost_price; // Optionally update cost
    $product->save();

    // Add to purchases table
    Purchase::create([
        'product_id' => $product->id,
        'quantity' => $request->quantity,
        'unit_cost' => $request->cost_price,
    ]);

    return back()->with('success', 'Purchase recorded.');
}




    // Record a sale
   

public function sell(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|numeric|min:1',
        'unit_price' => 'required|numeric|min:0',
    ]);

    $product = Product::find($request->product_id);

    if ($request->quantity > $product->stock) {
        return back()->with('error', 'Not enough stock.');
    }

    $product->stock -= $request->quantity;
    $product->save();

    Sale::create([
        'product_id' => $product->id,
        'quantity' => $request->quantity,
        'unit_price' => $request->unit_price,
    ]);

    return back()->with('success', 'Sale recorded.');
}

}
