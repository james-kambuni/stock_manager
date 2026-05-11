<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Purchase;
use App\Models\StockBatch;
use App\Models\Payment;
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

    public function sell(Request $request)
    {
        $tenantId = $this->getTenantId();

        $request->validate([
            'product_id'      => 'required|exists:products,id',
            'quantity'        => 'required|numeric|min:1',
            'unit_price'      => 'required|numeric|min:0',
            'payment_method'  => 'required|in:cash,mpesa',
            'phone'           => 'required_if:payment_method,mpesa|regex:/^07\d{8}$/',
        ]);

        $product = Product::where('tenant_id', $tenantId)->findOrFail($request->product_id);

        if ($request->quantity > $product->stock) {
            return back()->with('error', 'Not enough stock.');
        }

        DB::beginTransaction();

        try {
            $previousStock = $product->stock;
            $subtotal = $request->unit_price * $request->quantity;

            $sale = Sale::create([
                'sale_date' => now(),
                'total'     => $subtotal,
                'tenant_id' => $tenantId,
            ]);

            SaleItem::create([
                'sale_id'        => $sale->id,
                'product_id'     => $product->id,
                'quantity'       => $request->quantity,
                'unit_price'     => $request->unit_price,
                'unit_cost'      => $product->cost_price,
                'total'          => $subtotal,
                'previous_stock' => $previousStock,
                'tenant_id'      => $tenantId,
            ]);

            $product->stock -= $request->quantity;
            $product->save();

            if ($request->payment_method === 'mpesa') {
                $payment = Payment::create([
                    'sale_id' => $sale->id,
                    'phone'   => $request->phone,
                    'amount'  => $subtotal,
                    'status'  => 'completed', // In real case, set to 'pending'
                    'mpesa_code' => 'MPESA' . rand(10000, 99999) // Simulated code
                ]);
            }

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
            'payment_method'        => 'required|in:cash,mpesa',
            'phone'                 => 'required_if:payment_method,mpesa|regex:/^07\d{8}$/',
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

            if ($request->payment_method === 'mpesa') {
                Payment::create([
                    'sale_id' => $sale->id,
                    'phone'   => $request->phone,
                    'amount'  => $total,
                    'status'  => 'completed',
                    'mpesa_code' => 'MPESA' . rand(10000, 99999),
                ]);
            }

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

        $sale = Sale::with(['items.product', 'payment'])->where('tenant_id', $tenantId)->findOrFail($saleId);
        $tenant = \App\Models\Tenant::find($tenantId);

        return view('sales.receipt', compact('sale', 'tenant'));
    }
    public function purchase(Request $request)
{
    $tenantId = $this->getTenantId();

    $data = $request->validate([
        'product_id'  => 'required|exists:products,id',
        'quantity'    => 'required|numeric|min:1',
        'cost_price'  => 'required|numeric|min:0',
        'expiry_date' => 'nullable|date|after:today',
    ]);

    $product = Product::where('id', $data['product_id'])
        ->where('tenant_id', $tenantId)
        ->firstOrFail();

    DB::beginTransaction();

    try {

        $previousStock = $product->stock;

        $product->stock += $data['quantity'];
        $product->save();

        $purchase = Purchase::create([
            'product_id'     => $product->id,
            'quantity'       => $data['quantity'],
            'unit_cost'      => $data['cost_price'],
            'expiry_date'    => $data['expiry_date'] ?? null,
            'tenant_id'      => $tenantId,
            'previous_stock' => $previousStock,
        ]);

        StockBatch::create([
            'product_id'  => $product->id,
            'purchase_id' => $purchase->id,
            'quantity'    => $data['quantity'],
            'remaining'   => $data['quantity'],
            'expiry_date' => $data['expiry_date'] ?? null,
            'cost_price'  => $data['cost_price'],
            'tenant_id'   => $tenantId,
        ]);

        DB::commit();

        return back()->with('success', 'Purchase recorded successfully.');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()->with('error', $e->getMessage());
    }
}

}
