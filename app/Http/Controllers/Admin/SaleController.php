<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleItem;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    // Show the sale creation form
    public function create()
    {
        $products = Product::all(); 
        return view('sales.create', compact('products'));
    }

    // Store sale and items
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Create the Sale
        $sale = Sale::create([
            'sale_date' => now(),
            'total' => 0, // will be updated
        ]);

        $total = 0;
        $itemsData = [];

        foreach ($validated['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);

            $subtotal = $item['quantity'] * $item['unit_price'];
            $total += $subtotal;

            $itemsData[] = new SaleItem([
                'product_id' => $product->id,
                'quantity'   => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'unit_cost'  => $product->cost_price ?? 0,
                'total'      => $subtotal,
            ]);

            // Decrease stock
            $product->stock -= $item['quantity'];
            $product->save();
        }

        // Save items in bulk
        $sale->items()->saveMany($itemsData);

        // Update sale total
        $sale->update(['total' => $total]);

        return redirect()->route('sales.receipt', $sale->id);
    }

    // Show receipt
    public function receipt(Sale $sale)
    {
        $sale->load('items.product');
        return view('sales.receipt', compact('sale'));
    }
}
