<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockCount;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;

class StockReconciliationController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        $products = Product::where('tenant_id', $tenantId)->with('stockCount')->get();

        return view('admin.stock.reconciliation', compact('products'));
    }

    public function store(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        foreach ($request->counts as $productId => $physicalStock) {
            StockCount::updateOrCreate(
                ['tenant_id' => $tenantId, 'product_id' => $productId],
                ['physical_stock' => $physicalStock]
            );
        }

        return redirect()->back()->with('success', 'Physical stock recorded successfully.');
    }

    public function reconcile()
    {
        $tenantId = auth()->user()->tenant_id;

        $counts = StockCount::where('tenant_id', $tenantId)->get();

        foreach ($counts as $count) {
            $product = Product::where('id', $count->product_id)->where('tenant_id', $tenantId)->first();
            if ($product) {
                $product->stock = $count->physical_stock;
                $product->save();
            }
        }

        return back()->with('success', 'System stock updated successfully.');
    }

    public function updateSystemStock(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $data = json_decode($request->counts_json, true);

        foreach ($data as $productId => $physicalStock) {
            $product = Product::where('id', $productId)->where('tenant_id', $tenantId)->first();
            if ($product) {
                $product->stock = $physicalStock;
                $product->save();
            }
        }

        return redirect()->route('admin.stock-reconciliation.index')
            ->with('success', 'System stock updated successfully with physical counts.');
    }

    public function exportPdf()
    {
        $tenantId = auth()->user()->tenant_id;
        $products = Product::where('tenant_id', $tenantId)->with('stockCount')->get();

        $pdf = Pdf::loadView('admin.stock.pdf', compact('products'));
        return $pdf->download('stock_reconciliation.pdf');
    }
}
