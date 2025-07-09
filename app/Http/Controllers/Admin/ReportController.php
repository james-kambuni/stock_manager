<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Product;
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
            $query = Sale::with('items.product');
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

        for ($i = 3; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthName = $month->format('F');

            // Total sales from sale_items
            $totalSales = DB::table('sale_items')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->select(DB::raw('SUM(quantity * unit_price) as total_sales'))
                ->value('total_sales') ?? 0;

            // Total purchases
            $totalPurchases = DB::table('purchases')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->select(DB::raw('SUM(quantity * unit_cost) as total_purchases'))
                ->value('total_purchases') ?? 0;

            $profit = $totalSales - $totalPurchases;

            $profits[] = [
                'month' => $monthName,
                'profit' => $profit,
            ];
        }

        return view('admin.reports.profits', compact('profits'));
    }

    public function today()
    {
        $today = Carbon::today();

        $sales = Sale::with('items.product')
            ->whereDate('created_at', $today)
            ->get();

        $purchases = Purchase::with('product')
            ->whereDate('created_at', $today)
            ->get();

        return view('admin.reports.today', compact('sales', 'purchases'));
    }

    public function generate(Request $request)
    {
        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();

        switch ($request->type) {
            case 'sales':
                $data = Sale::with('items.product')
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
