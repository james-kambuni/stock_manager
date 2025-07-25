<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\StockBatch;

class TransactionController extends Controller
{
    public function create()
    {
        $products = Product::where('tenant_id', auth()->user()->tenant_id)->get();
        return view('admin.transactions.index', compact('products'));
    }

    public function storePurchase(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|numeric|min:1',
        'unit_cost' => 'required|numeric|min:0', // ✅ Correct field name
        'expiry_date' => 'nullable|date',
    ]);

    $tenantId = auth()->user()->tenant_id;

    $product = Product::where('id', $request->product_id)
        ->where('tenant_id', $tenantId)
        ->firstOrFail();

    $purchase = Purchase::create([
        'product_id' => $product->id,
        'quantity' => $request->quantity,
        'unit_cost' => $request->unit_cost, // ✅ Match field name
        'tenant_id' => $tenantId,
    ]);

    StockBatch::create([
        'product_id' => $product->id,
        'purchase_id' => $purchase->id,
        'quantity' => $request->quantity,
        'remaining' => $request->quantity,
        'expiry_date' => $request->expiry_date,
        'cost_price' => $request->unit_cost,
        'tenant_id' => $tenantId,
    ]);

    $product->stock += $request->quantity;
    $product->cost_price = $request->unit_cost;
    $product->save();

    return redirect()->back()->with('success', 'Purchase recorded successfully.');
}


    public function storeSale(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
        ]);

        $tenantId = auth()->user()->tenant_id;

        $product = Product::where('id', $request->product_id)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Insufficient stock.');
        }

        $remainingToSell = $request->quantity;
        $totalCost = 0;

        $batches = StockBatch::where('product_id', $product->id)
            ->where('tenant_id', $tenantId)
            ->where('remaining', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->get();

        foreach ($batches as $batch) {
            if ($remainingToSell <= 0) break;

            $deductQty = min($batch->remaining, $remainingToSell);
            $batch->remaining -= $deductQty;
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

        return redirect()->route('admin.transactions.receipt', $sale->id)->with('success', 'Sale recorded successfully.');
    }

public function storeSaleMultiple(Request $request)
{
    \Log::info("SALE MULTIPLE SUBMITTED: " . json_encode($request->all()));

    DB::beginTransaction();
    try {
        // Step 1: Create Sale record
        $sale = Sale::create([
            'sale_date' => now(),
            'total' => 0,
        ]);

        $total = 0;

        foreach ($request->products as $item) {
            $product = Product::findOrFail($item['product_id']);

            $quantity = $item['quantity'];
            $unitPrice = $item['unit_price'];
            $unitCost = $product->cost_price ?? 0;

            $subtotal = $unitPrice * $quantity;
            $total += $subtotal;

            // Step 2: Create SaleItem
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'unit_cost' => $unitCost,
                'total' => $subtotal,
                'sale_date' => now(),
            ]);

            // Step 3: Deduct product stock
            $product->stock -= $quantity;
            $product->save();

            // Optional: reduce from batches (FEFO) here
        }

        // Step 4: Update total on sale
        $sale->update(['total' => $total]);

        DB::commit();

        return back()->with('success', 'Sale completed successfully!');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("SALE ERROR: " . $e->getMessage());
        return back()->with('error', 'Error processing sale.');
    }
}


    public function printReceipt($saleId)
    {
        $tenantId = auth()->user()->tenant_id;

        $sale = Sale::with('product')
            ->where('tenant_id', $tenantId)
            ->findOrFail($saleId);

        return view('sales.receipt', compact('sale'));
    }
}
