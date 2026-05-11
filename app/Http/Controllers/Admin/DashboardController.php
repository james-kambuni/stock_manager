<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\User;
use App\Models\ServiceSale;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        $today = Carbon::today();

        // ================= SERVICE SALES (TODAY) =================
        $serviceTotal = ServiceSale::where('tenant_id', $tenantId)
            ->whereDate('created_at', $today)
            ->sum('amount');

        // ================= USERS =================
        $userCount = User::where('tenant_id', $tenantId)->count();

        // ================= SALES (TODAY) =================
        $salesCount = Sale::where('tenant_id', $tenantId)
            ->whereDate('sale_date', $today)
            ->count();

        $salesTotal = Sale::where('tenant_id', $tenantId)
            ->whereDate('sale_date', $today)
            ->sum('total');

        // ================= PURCHASES (TODAY) =================
        $purchasesCount = Purchase::where('tenant_id', $tenantId)
            ->whereDate('created_at', $today)
            ->count();

        $purchasesTotal = Purchase::where('tenant_id', $tenantId)
            ->whereDate('created_at', $today)
            ->sum(DB::raw('unit_cost * quantity'));

        // ================= MONTHLY PROFITS =================
        $months = [];
        $profits = [];

        for ($i = 3; $i >= 0; $i--) {
            $month = now()->subMonths($i);

            $monthlyProfit = DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->join('products', 'sale_items.product_id', '=', 'products.id')
                ->where('sales.tenant_id', $tenantId)
                ->whereMonth('sale_items.created_at', $month->month)
                ->whereYear('sale_items.created_at', $month->year)
                ->sum(DB::raw('(sale_items.unit_price - products.cost_price) * sale_items.quantity'));

            $months[] = $month->format('M');
            $profits[] = round($monthlyProfit ?? 0, 2);
        }

        // ================= RETURN VIEW =================
        return view('admin.dashboard', compact(
            'serviceTotal',
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