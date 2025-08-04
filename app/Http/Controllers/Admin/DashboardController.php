<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
{
    $tenantId = auth()->user()->tenant_id;

    $productCount = Product::where('tenant_id', $tenantId)->count();
    $userCount = User::where('tenant_id', $tenantId)->count();

    $today = Carbon::today();

    // Today's Sales
    $salesToday = Sale::where('tenant_id', $tenantId)
        ->whereDate('sale_date', $today)
        ->get();

    $salesCount = $salesToday->count();
    $salesTotal = $salesToday->sum('total');

    // Today's Purchases
    $purchasesToday = Purchase::where('tenant_id', $tenantId)
        ->whereDate('created_at', $today)
        ->get();

    $purchasesCount = $purchasesToday->count();

    $purchasesTotal = $purchasesToday->sum(function ($purchase) {
        return $purchase->unit_cost * $purchase->quantity;
    });

    // Monthly Gross Profits (last 4 months)
    $months = [];
    $profits = [];

    for ($i = 3; $i >= 0; $i--) {
        $month = now()->subMonths($i);
        $label = $month->format('M');

        $monthlyProfit = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.tenant_id', $tenantId)
            ->whereMonth('sale_items.created_at', $month->month)
            ->whereYear('sale_items.created_at', $month->year)
            ->select(DB::raw('SUM((sale_items.unit_price - products.cost_price) * sale_items.quantity) as gross_profit'))
            ->value('gross_profit');

        $months[] = $label;
        $profits[] = round($monthlyProfit ?? 0, 2);
    }

    return view('admin.dashboard', compact(
        'productCount',
        'salesCount',
        'salesTotal',
        'purchasesCount',
        'purchasesTotal',
        'userCount',
        'months',
        'profits'
    ));
}

}
