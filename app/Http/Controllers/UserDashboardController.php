<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use App\Models\SaleItem;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserDashboardController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        // ================= SERVICES =================
        $services = Service::where('tenant_id', $tenantId)->get();

        // ================= PRODUCTS =================
        $productCount = Product::where('tenant_id', $tenantId)->count();
        $products = Product::where('tenant_id', $tenantId)->get();

        // ================= TODAY SALES =================
        $salesToday = SaleItem::whereDate('created_at', today())
            ->whereHas('sale', function ($query) use ($tenantId) {
                $query->where('tenant_id', $tenantId);
            })
            ->sum('quantity');

        // ================= TODAY PURCHASES (FIXED) =================
        $purchasesToday = Purchase::where('tenant_id', $tenantId)
            ->whereDate('created_at', today())
            ->sum('quantity');

        // ================= USERS =================
        $userCount = User::where('tenant_id', $tenantId)->count();

        // ================= PROFIT CHART =================
        $months = [];
        $profits = [];

        for ($i = 3; $i >= 0; $i--) {

            $month = Carbon::now()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            // ================= PRODUCT PROFIT =================
            $productProfit = SaleItem::join('products', 'sale_items.product_id', '=', 'products.id')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->where('sales.tenant_id', $tenantId)
                ->whereBetween('sale_items.created_at', [$start, $end])
                ->sum(DB::raw('(sale_items.unit_price - products.cost_price) * sale_items.quantity'));

            // ================= SERVICE PROFIT =================
            $serviceProfit = DB::table('service_sales')
                ->where('tenant_id', $tenantId)
                ->whereBetween('created_at', [$start, $end])
                ->sum('amount');

            $months[] = $month->format('M');

            // ================= FINAL PROFIT =================
            $profits[] = round($productProfit + $serviceProfit, 2);
        }

        return view('dashboard', compact(
            'productCount',
            'products',
            'salesToday',
            'purchasesToday',
            'userCount',
            'months',
            'profits',
            'services'
        ));
    }
}