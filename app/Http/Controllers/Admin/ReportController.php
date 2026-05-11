<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\StockBatch;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\ServiceSale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // ================= TODAY REPORT =================
    public function today()
    {
        $tenantId = auth()->user()->tenant_id;
        $today = Carbon::today();

        $sales = SaleItem::with(['product', 'sale'])
            ->whereHas('sale', function ($q) use ($tenantId, $today) {
                $q->where('tenant_id', $tenantId)
                  ->whereDate('sale_date', $today);
            })
            ->get();

        $purchases = Purchase::with('product')
            ->where('tenant_id', $tenantId)
            ->whereDate('created_at', $today)
            ->get();

        $expenses = Expense::where('tenant_id', $tenantId)
            ->whereDate('created_at', $today)
            ->get();

        $serviceSales = ServiceSale::where('tenant_id', $tenantId)
            ->whereDate('created_at', $today)
            ->get();

        // ================= TOTALS =================
        $totalSales = $sales->sum(fn($s) => $s->quantity * $s->unit_price);

        $totalServiceSales = $serviceSales->sum('amount');

        $totalPurchases = $purchases->sum(fn($p) => $p->quantity * $p->unit_cost);

        $totalExpenses = $expenses->sum('amount');

        // ================= PROFITS =================
        $productProfit = $sales->sum(function ($s) {
            return ($s->unit_price - ($s->product->cost_price ?? 0)) * $s->quantity;
        });

        $serviceProfit = $totalServiceSales;

        $grossProfit = $productProfit + $serviceProfit;

        $netProfit = $grossProfit - $totalExpenses;

        return view('admin.reports.today', compact(
            'sales',
            'purchases',
            'expenses',
            'serviceSales',
            'totalSales',
            'totalServiceSales',
            'totalPurchases',
            'totalExpenses',
            'productProfit',
            'serviceProfit',
            'grossProfit',
            'netProfit'
        ));
    }

    // ================= MONTHLY REPORT =================
    public function monthly()
    {
        $tenantId = auth()->user()->tenant_id;

        $start = Carbon::now()->startOfMonth()->startOfDay();
        $end = Carbon::now()->endOfMonth()->endOfDay();

        $sales = SaleItem::with(['product', 'sale'])
            ->whereHas('sale', function ($q) use ($tenantId, $start, $end) {
                $q->where('tenant_id', $tenantId)
                  ->whereBetween('sale_date', [$start, $end]);
            })
            ->get();

        $serviceSales = ServiceSale::where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $purchases = Purchase::with('product')
            ->where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $expenses = Expense::where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        // ================= TOTALS =================
        $totalSales = $sales->sum(fn($s) => $s->quantity * $s->unit_price);

        $totalServiceSales = $serviceSales->sum('amount');

        $totalPurchases = $purchases->sum(fn($p) => $p->quantity * $p->unit_cost);

        $totalExpenses = $expenses->sum('amount');

        // ================= PROFITS =================
        $productProfit = $sales->sum(function ($s) {
            return ($s->unit_price - ($s->product->cost_price ?? 0)) * $s->quantity;
        });

        $serviceProfit = $totalServiceSales;

        $netProfit = ($productProfit + $serviceProfit) - $totalExpenses;

        return view('admin.reports.monthly', compact(
            'sales',
            'purchases',
            'expenses',
            'serviceSales',
            'totalSales',
            'totalServiceSales',
            'totalPurchases',
            'totalExpenses',
            'productProfit',
            'serviceProfit',
            'netProfit'
        ));
    }

    // ================= MAIN REPORT PAGE =================
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $reportType = $request->type ?? 'inventory';

        $from = $request->from;
        $to = $request->to;

        $fromDate = $from
            ? Carbon::parse($from)->startOfDay()
            : now()->startOfMonth()->startOfDay();

        $toDate = $to
            ? Carbon::parse($to)->endOfDay()
            : now()->endOfMonth()->endOfDay();

        // ================= INVENTORY =================
        $inventoryData = $this->calculateInventoryData($fromDate, $toDate);

        // ================= SALES =================
        $sales = SaleItem::with(['product', 'sale'])
            ->whereHas('sale', function ($q) use ($tenantId, $fromDate, $toDate) {

                $q->where('tenant_id', $tenantId)
                  ->whereBetween('sale_date', [$fromDate, $toDate]);

            })
            ->get();

        // ================= PURCHASES =================
        $purchases = Purchase::with('product')
            ->where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->get();

        return view('admin.reports.index', compact(
            'reportType',
            'inventoryData',
            'sales',
            'purchases',
            'from',
            'to'
        ));
    }

    // ================= INVENTORY CALCULATION =================
    private function calculateInventoryData($fromDate, $toDate)
    {
        $tenantId = auth()->user()->tenant_id;

        $products = Product::where('tenant_id', $tenantId)->get();

        $inventoryData = [];

        foreach ($products as $product) {

            // ================= PURCHASED IN RANGE =================
            $purchased = Purchase::where('tenant_id', $tenantId)
                ->where('product_id', $product->id)
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->sum('quantity');

            // ================= SOLD IN RANGE =================
            $sold = SaleItem::where('product_id', $product->id)
                ->whereHas('sale', function ($q) use ($tenantId, $fromDate, $toDate) {

                    $q->where('tenant_id', $tenantId)
                      ->whereBetween('sale_date', [$fromDate, $toDate]);

                })
                ->sum('quantity');

            // ================= PREVIOUS STOCK =================
            $previousStock = ($product->stock - $purchased) + $sold;

            $inventoryData[] = [
                'name' => $product->name,
                'previous_stock' => $previousStock,
                'purchased' => $purchased,
                'sold' => $sold,
                'current_stock' => $product->stock,
                'cost_price' => $product->cost_price ?? 0,
                'selling_price' => $product->selling_price ?? 0,
            ];
        }

        return $inventoryData;
    }

    // ================= PROFITS REPORT =================
    public function profits()
    {
        $tenantId = auth()->user()->tenant_id;

        $profits = collect();

        for ($i = 3; $i >= 0; $i--) {

            $month = now()->subMonths($i);

            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $profit = DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->join('products', 'sale_items.product_id', '=', 'products.id')
                ->where('sales.tenant_id', $tenantId)
                ->whereBetween('sales.sale_date', [$start, $end])
                ->sum(DB::raw('(sale_items.unit_price - products.cost_price) * sale_items.quantity'));

            $profits->push([
                'month' => $month->format('F'),
                'profit' => $profit,
            ]);
        }

        return view('admin.reports.profits', compact('profits'));
    }

    // ================= EXPIRY REPORT =================
    public function expiryReport()
    {
        $tenantId = auth()->user()->tenant_id;

        $batches = StockBatch::where('tenant_id', $tenantId)
            ->where('remaining', '>', 0)
            ->orderBy('expiry_date')
            ->get();

        return view('admin.reports.expiry', compact('batches'));
    }
}