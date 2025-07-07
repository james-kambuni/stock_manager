<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserDashboardController extends Controller
{
    public function index()
    {
        $productCount = Product::count();
        $salesToday = Sale::whereDate('created_at', today())->sum('quantity');
        $purchasesToday = Purchase::whereDate('created_at', today())->sum('quantity');
        $userCount = User::count();

        // Profits for last 4 months
        $months = [];
        $profits = [];

        for ($i = 3; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $label = $month->format('M');
            $totalSales = Sale::join('products', 'sales.product_id', '=', 'products.id')
    ->whereMonth('sales.created_at', $month->month)
    ->whereYear('sales.created_at', $month->year)
    ->sum(DB::raw('sales.quantity * products.selling_price'));


            $totalPurchases = Purchase::join('products', 'purchases.product_id', '=', 'products.id')
    ->whereMonth('purchases.created_at', $month->month)
    ->whereYear('purchases.created_at', $month->year)
    ->sum(DB::raw('purchases.quantity * products.cost_price'));


            $months[] = $label;
            $profits[] = round($totalSales - $totalPurchases, 2);
        }

        return view('dashboard', compact(
            'productCount',
            'salesToday',
            'purchasesToday',
            'userCount',
            'months',
            'profits'
        ));
    }
}
