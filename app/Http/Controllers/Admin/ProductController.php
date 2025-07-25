<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    // Show products for current tenant
    public function index()
    {
        $products = Product::where('tenant_id', auth()->user()->tenant_id)->get();
        return view('admin.products.index', compact('products'));
    }

    // Store new product for current tenant
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'stock' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'min_threshold' => 'nullable|integer|min:0',
            'max_threshold' => 'nullable|integer|min:0',
            'is_perishable' => 'required|boolean',
        ]);

        Product::create([
            'name' => $request->name,
            'stock' => $request->stock,
            'cost_price' => $request->cost_price,
            'selling_price' => $request->selling_price,
            'min_threshold' => $request->min_threshold,
            'max_threshold' => $request->max_threshold,
            'is_perishable' => $request->is_perishable,
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        return redirect()->back()->with('success', 'Product added successfully.');
    }

    // Update existing product
    public function update(Request $request, Product $product)
    {
        if ($product->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $request->validate([
            'stock' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'min_threshold' => 'nullable|integer|min:0',
            'max_threshold' => 'nullable|integer|min:0',
            'is_perishable' => 'required|boolean',
        ]);

        $product->update([
            'stock' => $request->stock,
            'cost_price' => $request->cost_price,
            'selling_price' => $request->selling_price,
            'min_threshold' => $request->min_threshold,
            'max_threshold' => $request->max_threshold,
            'is_perishable' => $request->is_perishable,
        ]);

        return back()->with('success', 'Product updated successfully.');
    }

    // Delete product
    public function destroy(Product $product)
    {
        if ($product->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $product->delete();
        return back()->with('success', 'Product deleted successfully.');
    }
}
