<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class UserDashboardController extends Controller
{
    public function index()
{
    $tenantId = auth()->user()->tenant_id;

    // ✅ Show welcome message only once per session
    if (!session()->has('welcome_shown')) {
        session(['welcome_shown' => true]);
    }

    // Tenant-specific data
    $productCount = Product::where('tenant_id', $tenantId)->count();
    $products = Product::where('tenant_id', $tenantId)->get();

    $salesToday = SaleItem::whereDate('sale_items.created_at', today())
        ->whereHas('sale', function ($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        })
        ->sum('quantity');

    $purchasesToday = Purchase::where('tenant_id', $tenantId)
        ->whereDate('created_at', today())
        ->sum('quantity');

    $userCount = User::where('tenant_id', $tenantId)->count();

    // Profits for last 4 months
    $months = [];
    $profits = [];

    for ($i = 3; $i >= 0; $i--) {
        $month = now()->subMonths($i);
        $label = $month->format('M');

        $totalSales = SaleItem::whereMonth('sale_items.created_at', $month->month)
            ->whereYear('sale_items.created_at', $month->year)
            ->whereHas('sale', function ($query) use ($tenantId) {
                $query->where('tenant_id', $tenantId);
            })
            ->sum(DB::raw('unit_price * quantity'));

        $totalPurchases = Purchase::where('tenant_id', $tenantId)
            ->whereMonth('created_at', $month->month)
            ->whereYear('created_at', $month->year)
            ->sum(DB::raw('unit_cost * quantity'));

        $months[] = $label;
        $profits[] = round($totalSales - $totalPurchases, 2);
    }

    return view('dashboard', compact(
        'productCount',
        'products',
        'salesToday',
        'purchasesToday',
        'userCount',
        'months',
        'profits'
    ));
}
}
