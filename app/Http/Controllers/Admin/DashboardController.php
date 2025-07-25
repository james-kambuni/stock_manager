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

        // Get today's sales
        $salesToday = Sale::where('tenant_id', $tenantId)
            ->whereDate('sale_date', $today)
            ->get();

        $salesCount = $salesToday->count();
        $salesTotal = $salesToday->sum('total');

        // Get today's purchases
        $purchasesToday = Purchase::where('tenant_id', $tenantId)
            ->whereDate('created_at', $today)
            ->get();

        $purchasesCount = $purchasesToday->count();

        // Calculate total from unit_cost * quantity
        $purchasesTotal = $purchasesToday->sum(function ($purchase) {
            return $purchase->unit_cost * $purchase->quantity;
        });

        // Monthly profits (last 4 months)
        $months = [];
        $profits = [];

        for ($i = 3; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $label = $month->format('M');

            $totalSales = DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->whereMonth('sale_items.created_at', $month->month)
                ->whereYear('sale_items.created_at', $month->year)
                ->where('sales.tenant_id', $tenantId)
                ->sum(DB::raw('sale_items.unit_price * sale_items.quantity'));

            $totalPurchases = DB::table('purchases')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->where('tenant_id', $tenantId)
                ->sum(DB::raw('unit_cost * quantity'));

            $months[] = $label;
            $profits[] = round($totalSales - $totalPurchases, 2);
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
