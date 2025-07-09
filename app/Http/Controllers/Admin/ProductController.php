<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    // Show product list
    public function index()
    {
        $products = Product::all();
        return view('admin.products.index', compact('products'));
    }

    // Store new product
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

    // Update product details
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'stock' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
        ]);

        $product->update($request->only(['stock', 'cost_price', 'selling_price']));

        return back()->with('success', 'Product updated.');
    }

    // Delete product
    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Product deleted.');
    }
}
