<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    public function purchase(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'cost_price' => 'required|numeric|min:0',
        ]);

        $product = Product::findOrFail($request->product_id);
        $product->stock += $request->quantity;
        $product->cost_price = $request->cost_price;
        $product->save();

        Purchase::create([
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'unit_cost' => $request->cost_price,
        ]);

        return back()->with('success', 'Purchase recorded.');
    }

    public function sell(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($request->quantity > $product->stock) {
            return back()->with('error', 'Not enough stock.');
        }

        DB::beginTransaction();

        try {
            $subtotal = $request->unit_price * $request->quantity;

            $sale = Sale::create([
                'sale_date' => now(),
                'total' => $subtotal,
            ]);

            SaleItem::create([
                'sale_id'    => $sale->id,
                'product_id' => $product->id,
                'quantity'   => $request->quantity,
                'unit_price' => $request->unit_price,
                'unit_cost'  => $product->cost_price ?? 0,
                'total'      => $subtotal,
            ]);

            $product->stock -= $request->quantity;
            $product->save();

            DB::commit();

            return redirect()->route('print.receipt', $sale->id);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Sale failed: ' . $e->getMessage());
        }
    }

    public function sellMultiple(Request $request)
    {
        $request->validate([
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.unit_price' => 'required|numeric|min:0',
            'products.*.quantity' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();

        try {
            $sale = Sale::create([
                'sale_date' => now(),
                'total' => 0,
            ]);

            $total = 0;

            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($item['quantity'] > $product->stock) {
                    throw new \Exception("Not enough stock for product: {$product->name}");
                }

                $subtotal = $item['unit_price'] * $item['quantity'];
                $total += $subtotal;

                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $product->id,
                    'unit_price' => $item['unit_price'],
                    'unit_cost'  => $product->cost_price ?? 0, // ✅ pulled from DB
                    'quantity'   => $item['quantity'],
                    'total'      => $subtotal,
                ]);

                $product->stock -= $item['quantity'];
                $product->save();
            }

            $sale->update(['total' => $total]);

            DB::commit();

            return redirect()->route('print.receipt', $sale->id);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Sale failed: ' . $e->getMessage());
        }
    }

    public function printReceipt($saleId)
    {
        $sale = Sale::with('items.product')->findOrFail($saleId);
        return view('sales.receipt', compact('sale'));
    }
}
