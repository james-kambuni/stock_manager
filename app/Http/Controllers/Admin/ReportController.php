<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockBatch;
use App\Models\Expense;
use App\Models\Purchase;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reportType = $request->query('type', 'inventory');
        $from = $request->query('from');
        $to = $request->query('to');
        $tenantId = auth()->user()->tenant_id;

        $products = $sales = $purchases = $inventoryData = [];
        $totalExpenses = 0;

        $fromDate = $from ? Carbon::parse($from)->startOfDay() : now()->startOfMonth();
        $toDate = $to ? Carbon::parse($to)->endOfDay() : now();

        if ($reportType === 'inventory') {
            $inventoryData = $this->calculateInventoryData($fromDate, $toDate);
            $totalExpenses = Expense::where('tenant_id', $tenantId)
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->sum('amount');

        } elseif ($reportType === 'sales') {
            $sales = SaleItem::with('product', 'sale')
                ->whereHas('sale', fn($q) => $q->where('tenant_id', $tenantId))
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->latest()->get();

        } elseif ($reportType === 'purchases') {
            $purchases = Purchase::with('product')
                ->where('tenant_id', $tenantId)
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->latest()->get();
        }

        return view('admin.reports.index', compact(
            'reportType', 'products', 'sales', 'purchases', 'inventoryData',
            'totalExpenses', 'from', 'to'
        ));
    }

    private function calculateInventoryData($fromDate, $toDate)
{
    $tenantId = auth()->user()->tenant_id;
    $products = Product::where('tenant_id', $tenantId)->get();
    $inventoryData = [];

    foreach ($products as $product) {
        // Get latest Purchase before or on fromDate
        $latestPurchase = Purchase::where('product_id', $product->id)
            ->where('tenant_id', $tenantId)
            ->whereDate('created_at', '<=', $fromDate)
            ->orderByDesc('created_at')
            ->first();

        // Get latest SaleItem before or on fromDate using Sale's created_at
        $latestSaleItem = SaleItem::where('product_id', $product->id)
            ->whereHas('sale', function ($q) use ($tenantId, $fromDate) {
                $q->where('tenant_id', $tenantId)
                  ->whereDate('created_at', '<=', $fromDate);
            })
            ->with(['sale' => function ($q) {
                $q->select('id', 'created_at'); // required for ordering
            }])
            ->get()
            ->sortByDesc(fn($item) => $item->sale->created_at ?? now())
            ->first();

        // Pick latest of the two based on created_at
        $previousStock = 0;

        if ($latestPurchase && $latestSaleItem) {
            $previousStock = $latestPurchase->created_at > $latestSaleItem->sale->created_at
                ? $latestPurchase->previous_stock
                : $latestSaleItem->previous_stock;
        } elseif ($latestPurchase) {
            $previousStock = $latestPurchase->previous_stock;
        } elseif ($latestSaleItem) {
            $previousStock = $latestSaleItem->previous_stock;
        }

        // Get purchases in range
        $purchased = Purchase::where('product_id', $product->id)
            ->where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->sum('quantity');

        // Get sales in range
        $sold = SaleItem::where('product_id', $product->id)
            ->whereHas('sale', function ($q) use ($tenantId, $fromDate, $toDate) {
                $q->where('tenant_id', $tenantId)
                  ->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->sum('quantity');

        $inventoryData[] = [
            'name' => $product->name,
            'previous_stock' => $previousStock,
            'purchased' => $purchased,
            'sold' => $sold,
            'current_stock' => $product->stock,
            'cost_price' => $product->cost_price,
            'selling_price' => $product->selling_price,
        ];
    }

    return $inventoryData;
}

    public function export(Request $request)
    {
        $reportType = $request->type;
        $from = $request->from;
        $to = $request->to;
        $tenant = auth()->user()->tenant;

        $fromDate = Carbon::parse($from)->startOfDay();
        $toDate = Carbon::parse($to)->endOfDay();

        $data = [
            'reportType' => $reportType,
            'from' => $from,
            'to' => $to,
            'tenant' => $tenant,
        ];

        if ($reportType === 'inventory') {
            $data['inventoryData'] = $this->calculateInventoryData($fromDate, $toDate);
        } elseif ($reportType === 'sales') {
            $data['sales'] = SaleItem::with('product', 'sale')
                ->whereHas('sale', fn($q) => $q->where('tenant_id', $tenant->id))
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->get();
        } elseif ($reportType === 'purchases') {
            $data['purchases'] = Purchase::with('product')
                ->where('tenant_id', $tenant->id)
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->get();
        }

        $pdf = Pdf::loadView('admin.reports.pdf', $data)->setPaper('A4', 'portrait');
        return $pdf->download("{$reportType}_report.pdf");
    }

    public function profits()
    {
        $tenantId = auth()->user()->tenant_id;
        $profits = [];

        // Monthly Profit for past 4 months
        for ($i = 3; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthName = $month->format('F');

            $totalSales = DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->where('sales.tenant_id', $tenantId)
                ->whereMonth('sale_items.created_at', $month->month)
                ->whereYear('sale_items.created_at', $month->year)
                ->sum(DB::raw('quantity * unit_price'));

            $totalPurchases = DB::table('purchases')
                ->where('tenant_id', $tenantId)
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum(DB::raw('quantity * unit_cost'));

            $profit = $totalSales - $totalPurchases;

            $profits[] = [
                'month' => $monthName,
                'profit' => $profit,
            ];
        }

        // Top products profit - Last 1 Month
        $monthlyTopProducts = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->select('products.name as product', DB::raw('SUM((sale_items.unit_price - products.cost_price) * sale_items.quantity) as profit'))
            ->where('sales.tenant_id', $tenantId)
            ->where('sale_items.created_at', '>=', Carbon::now()->subMonth())
            ->groupBy('products.name')
            ->orderByDesc('profit')
            ->take(10)
            ->get();

        // Top products profit - Last 1 Week
        $weeklyTopProducts = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->select('products.name as product', DB::raw('SUM((sale_items.unit_price - products.cost_price) * sale_items.quantity) as profit'))
            ->where('sales.tenant_id', $tenantId)
            ->where('sale_items.created_at', '>=', Carbon::now()->subWeek())
            ->groupBy('products.name')
            ->orderByDesc('profit')
            ->take(10)
            ->get();

        // Top products profit - Yesterday
        $yesterdayTopProducts = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->select('products.name as product', DB::raw('SUM((sale_items.unit_price - products.cost_price) * sale_items.quantity) as profit'))
            ->where('sales.tenant_id', $tenantId)
            ->whereDate('sale_items.created_at', Carbon::yesterday())
            ->groupBy('products.name')
            ->orderByDesc('profit')
            ->take(10)
            ->get();

        return view('admin.reports.profits', compact(
            'profits',
            'monthlyTopProducts',
            'weeklyTopProducts',
            'yesterdayTopProducts'
        ));
    }
public function expiryReport()
    {
        $tenantId = auth()->user()->tenant_id;

        $batches = StockBatch::where('tenant_id', $tenantId)
            ->where('remaining', '>', 0)
            ->orderBy('expiry_date')
            ->get();

        return view('admin.reports.expiry', compact('batches'));
    }

public function today()
{
    $tenantId = auth()->user()->tenant_id;
    $today = \Carbon\Carbon::today();

    $sales = SaleItem::with(['product', 'sale'])
        ->whereDate('created_at', $today)
        ->whereHas('sale', fn($q) => $q->where('tenant_id', $tenantId))
        ->get();

    $purchases = Purchase::with('product')
        ->whereDate('created_at', $today)
        ->where('tenant_id', $tenantId)
        ->get();

    $expenses = Expense::where('tenant_id', $tenantId)
        ->whereDate('date', $today)
        ->get();

    $totalSales = $sales->sum(fn($s) => $s->quantity * $s->unit_price);
    $totalPurchases = $purchases->sum(fn($p) => $p->quantity * $p->unit_cost);
    $totalExpenses = $expenses->sum('amount');

    $grossProfit = $sales->sum(fn($s) => ($s->unit_price - ($s->product->cost_price ?? 0)) * $s->quantity);
    $netProfit = $grossProfit - $totalExpenses;

    return view('admin.reports.today', compact(
        'sales', 'purchases', 'expenses',
        'totalSales', 'totalPurchases', 'grossProfit', 'totalExpenses', 'netProfit'
    ));
}

    public function monthly()
{
    $startOfMonth = Carbon::now()->startOfMonth();
    $endOfMonth = Carbon::now()->endOfMonth();

    // If you're using multi-tenancy:
    $tenantId = auth()->user()->tenant_id ?? null;

    $purchases = \App\Models\Purchase::whereBetween('created_at', [$startOfMonth, $endOfMonth])
        ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
        ->get();

    $totalPurchases = $purchases->sum(fn($p) => $p->quantity * $p->unit_cost); // or unit_cost if used

   $totalSales = Sale::whereBetween('created_at', [$startOfMonth, $endOfMonth])
    ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
    ->sum('total');

    $totalExpenses = \App\Models\Expense::whereBetween('created_at', [$startOfMonth, $endOfMonth])
        ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
        ->sum('amount');

    $netProfit = $totalSales - $totalPurchases - $totalExpenses;

    return view('admin.reports.monthly', compact(
        'totalPurchases',
        'totalSales',
        'totalExpenses',
        'netProfit'
    ));
}

    public function generate(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();

        switch ($request->type) {
            case 'sales':
                $data = SaleItem::with('product', 'sale')
                    ->whereBetween('created_at', [$start, $end])
                    ->whereHas('sale', function ($q) use ($tenantId) {
                        $q->where('tenant_id', $tenantId);
                    })
                    ->get();
                break;

            case 'purchases':
                $data = Purchase::with('product')
                    ->whereBetween('created_at', [$start, $end])
                    ->where('tenant_id', $tenantId)
                    ->get();
                break;

            case 'inventory':
                $data = Product::where('tenant_id', $tenantId)->get();
                break;

            default:
                $data = [];
        }

        return response()->json($data);
    }

    public function userReports()
    {
        $tenantId = auth()->user()->tenant_id;
        $products = Product::where('tenant_id', $tenantId)->get();
        return view('users.reports', compact('products'));
    }
}
