<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockBatch;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    public function create()
    {
        $products = Product::where('tenant_id', auth()->user()->tenant_id)->get();
        return view('admin.sales.create', compact('products'));
    }

    public function store(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|numeric|min:1',
    ]);

    $product = Product::where('id', $request->product_id)
        ->where('tenant_id', auth()->user()->tenant_id)
        ->firstOrFail();

    if ($product->stock < $request->quantity) {
        return back()->with('error', 'Insufficient stock.');
    }

    $remainingToSell = $request->quantity;
    $tenantId = auth()->user()->tenant_id;
    $totalCost = 0;

    $batches = StockBatch::where('product_id', $product->id)
        ->where('tenant_id', $tenantId)
        ->where('remaining_quantity', '>', 0)
        ->orderBy('expiry_date', 'asc')
        ->get();

    foreach ($batches as $batch) {
        if ($remainingToSell <= 0) break;

        $deductQty = min($batch->remaining_quantity, $remainingToSell);
        $batch->remaining_quantity -= $deductQty;
        $batch->save();

        $remainingToSell -= $deductQty;
        $totalCost += $deductQty * $batch->cost_price;
    }

    $sale = Sale::create([
        'product_id' => $product->id,
        'quantity' => $request->quantity,
        'total_price' => $totalCost,
        'tenant_id' => $tenantId,
    ]);

    $product->stock -= $request->quantity;
    $product->save();

    return redirect()->route('admin.sales.receipt', $sale->id);
}

public function printReceipt($saleId)
{
    $tenantId = auth()->user()->tenant_id;

    $sale = Sale::with('items.product')
                ->where('tenant_id', $tenantId)
                ->findOrFail($saleId);

    return view('sales.receipt', compact('sale'));
}

}
