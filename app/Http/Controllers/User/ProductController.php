<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Purchase;
use App\Models\StockBatch;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected function getTenantId()
    {
        return auth()->user()->tenant_id;
    }

    public function index()
    {
        $tenantId = $this->getTenantId();
        $products = Product::where('tenant_id', $tenantId)->get();
        return view('products.index', compact('products'));
    }
public function purchase(Request $request)
{
    $tenantId = $this->getTenantId();

    $request->validate([
        'product_id'   => 'required|exists:products,id',
        'quantity'     => 'required|numeric|min:1',
        'cost_price'   => 'required|numeric|min:0',
        'expiry_date'  => 'nullable|date|after:today',
    ]);

    $product = Product::where('tenant_id', $tenantId)->findOrFail($request->product_id);

    DB::beginTransaction();

    try {
        // ✅ Define previous stock before updating
        $previousStock = $product->stock;

        // Update product stock
        $product->stock += $request->quantity;
        $product->cost_price = $request->cost_price;
        $product->save();

        // Record purchase with previous_stock
        $purchase = Purchase::create([
            'product_id'     => $product->id,
            'quantity'       => $request->quantity,
            'unit_cost'      => $request->cost_price,
            'expiry_date'    => $request->expiry_date ?? null,
            'tenant_id'      => $tenantId,
            'previous_stock' => $previousStock, // ✅ now this will work
        ]);

        // Create stock batch
        StockBatch::create([
            'product_id'   => $product->id,
            'purchase_id'  => $purchase->id,
            'quantity'     => $purchase->quantity,
            'remaining'    => $purchase->quantity,
            'expiry_date'  => $purchase->expiry_date,
            'cost_price'   => $purchase->unit_cost,
            'tenant_id'    => $tenantId,
        ]);

        DB::commit();
        return back()->with('success', 'Purchase recorded successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Purchase failed: ' . $e->getMessage());
    }
}

    public function sell(Request $request)
{
    $tenantId = $this->getTenantId();

    $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity'   => 'required|numeric|min:1',
        'unit_price' => 'required|numeric|min:0',
    ]);

    // Fetch the product and ensure it's for the current tenant
    $product = Product::where('tenant_id', $tenantId)->where('id', $request->product_id)->first();

    if (!$product) {
        return back()->with('error', 'Product not found or does not belong to this tenant.');
    }

    if ($request->quantity > $product->stock) {
        return back()->with('error', 'Not enough stock.');
    }

    DB::beginTransaction();

    try {
        $previousStock = $product->stock;

        $subtotal = $request->unit_price * $request->quantity;

        $sale = Sale::create([
            'sale_date'  => now(),
            'total'      => $subtotal,
            'tenant_id'  => $tenantId,
        ]);

        SaleItem::create([
            'sale_id'        => $sale->id,
            'product_id'     => $product->id,
            'quantity'       => $request->quantity,
            'unit_price'     => $request->unit_price,
            'unit_cost'      => $product->cost_price,
            'total'          => $subtotal,
            'previous_stock' => $previousStock, // ✅ critical
            'tenant_id'      => $tenantId,
        ]);

        $product->stock -= $request->quantity;
        $product->save();

        DB::commit();
        return redirect()->route('user.receipt', $sale->id);
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Sale failed: ' . $e->getMessage());
    }
}

    public function sellMultiple(Request $request)
    {
        $tenantId = $this->getTenantId();

        $request->validate([
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.unit_price' => 'required|numeric|min:0',
            'products.*.quantity'   => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();

        try {
            $sale = Sale::create([
                'sale_date' => now(),
                'total'     => 0,
                'tenant_id' => $tenantId,
            ]);

            $total = 0;

            foreach ($request->products as $item) {
                $product = Product::where('tenant_id', $tenantId)->findOrFail($item['product_id']);

                if ($item['quantity'] > $product->stock) {
                    throw new \Exception("Not enough stock for product: {$product->name}");
                }

                $subtotal = $item['unit_price'] * $item['quantity'];
                $total += $subtotal;

                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'unit_cost'  => $product->cost_price ?? 0,
                    'total'      => $subtotal,
                    'tenant_id'  => $tenantId,
                ]);

                $product->stock -= $item['quantity'];
                $product->save();
            }

            $sale->update(['total' => $total]);

            DB::commit();

            return redirect()->route('user.receipt', $sale->id);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Sale failed: ' . $e->getMessage());
        }
    }

    public function printReceipt($saleId)
    {
        $tenantId = $this->getTenantId();

        $sale = Sale::with('items.product')
                    ->where('tenant_id', $tenantId)
                    ->findOrFail($saleId);

        return view('sales.receipt', compact('sale'));
    }
}
