<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Purchase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reportType = $request->query('type', 'inventory');
        $from = $request->query('from');
        $to = $request->query('to');

        $products = [];
        $sales = [];
        $purchases = [];

        if ($reportType === 'inventory') {
            $products = Product::all();

        } elseif ($reportType === 'sales') {
            $query = SaleItem::with('product', 'sale');
            if ($from && $to) {
                $query->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
            }
            $sales = $query->latest()->get();

        } elseif ($reportType === 'purchases') {
            $query = Purchase::with('product');
            if ($from && $to) {
                $query->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
            }
            $purchases = $query->latest()->get();
        }

        return view('admin.reports.index', compact('reportType', 'products', 'sales', 'purchases'));
    }

    public function profits()
{
    $profits = [];

    // Monthly Profit for past 4 months
    for ($i = 3; $i >= 0; $i--) {
        $month = now()->subMonths($i);
        $monthName = $month->format('F');

        $totalSales = DB::table('sale_items')
            ->whereMonth('created_at', $month->month)
            ->whereYear('created_at', $month->year)
            ->sum(DB::raw('quantity * unit_price'));

        $totalPurchases = DB::table('purchases')
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
        ->select('products.name as product', DB::raw('SUM((sale_items.unit_price - products.cost_price) * sale_items.quantity) as profit'))
        ->where('sale_items.created_at', '>=', Carbon::now()->subMonth())
        ->groupBy('products.name')
        ->orderByDesc('profit')
        ->take(10)
        ->get();

    // Top products profit - Last 1 Week
    $weeklyTopProducts = DB::table('sale_items')
        ->join('products', 'sale_items.product_id', '=', 'products.id')
        ->select('products.name as product', DB::raw('SUM((sale_items.unit_price - products.cost_price) * sale_items.quantity) as profit'))
        ->where('sale_items.created_at', '>=', Carbon::now()->subWeek())
        ->groupBy('products.name')
        ->orderByDesc('profit')
        ->take(10)
        ->get();

    // Top products profit - Yesterday
    $yesterdayTopProducts = DB::table('sale_items')
        ->join('products', 'sale_items.product_id', '=', 'products.id')
        ->select('products.name as product', DB::raw('SUM((sale_items.unit_price - products.cost_price) * sale_items.quantity) as profit'))
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


    public function today()
{
    $today = Carbon::today();

    $sales = SaleItem::with('product', 'sale')
                ->whereDate('created_at', $today)
                ->get();

    $purchases = Purchase::with('product')
                ->whereDate('created_at', $today)
                ->get();

    // Calculate totals
    $totalSales = $sales->sum(function ($sale) {
        return $sale->quantity * $sale->unit_price;
    });

    $totalPurchases = $purchases->sum(function ($purchase) {
        return $purchase->quantity * $purchase->unit_cost;
    });

    $profit = $totalSales - $totalPurchases;

    return view('admin.reports.today', compact(
        'sales',
        'purchases',
        'totalSales',
        'totalPurchases',
        'profit'
    ));
}


    public function generate(Request $request)
    {
        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();

        switch ($request->type) {
            case 'sales':
                $data = SaleItem::with('product', 'sale')
                        ->whereBetween('created_at', [$start, $end])
                        ->get();
                break;

            case 'purchases':
                $data = Purchase::with('product')
                        ->whereBetween('created_at', [$start, $end])
                        ->get();
                break;

            case 'inventory':
                $data = Product::all();
                break;

            default:
                $data = [];
        }

        return response()->json($data);
    }

    public function userReports()
    {
        $products = Product::all();
        return view('users.reports', compact('products'));
    }
}
