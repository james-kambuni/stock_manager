<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        return view('admin.products.index', [
            'products' => Product::all()
        ]);
    }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'stock' => 'required|numeric|min:0',
        'cost_price' => 'required|numeric|min:0',
        'selling_price' => 'required|numeric|min:0',
    ]);

    Product::create([
        'name' => $request->name,
        'stock' => $request->stock,
        'cost_price' => $request->cost_price,
        'selling_price' => $request->selling_price,
    ]);

    return redirect()->back()->with('success', 'Product added successfully.');
}


    public function update(Request $request, Product $product)
    {
        $product->update($request->only(['stock', 'cost_price', 'selling_price']));
        return back()->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Product deleted.');
    }
    

public function purchase(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|numeric|min:1',
        'cost_price' => 'required|numeric|min:0',
    ]);

    $product = Product::find($request->product_id);
    $product->stock += $request->quantity;
    $product->cost_price = $request->cost_price; // optionally update cost
    $product->save();

    return back()->with('success', 'Purchase recorded.');
}

public function sell(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|numeric|min:1',
    ]);

    $product = Product::find($request->product_id);

    if ($request->quantity > $product->stock) {
        return back()->with('error', 'Not enough stock.');
    }

    $product->stock -= $request->quantity;
    $product->save();

    return back()->with('success', 'Sale recorded.');
}

}
