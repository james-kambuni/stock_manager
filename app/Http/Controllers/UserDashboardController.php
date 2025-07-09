<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\User;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserDashboardController extends Controller
{
    public function index()
    {
        $productCount = Product::count();

        // Fix: Sales today from sale_items
        $salesToday = SaleItem::whereDate('created_at', today())->sum('quantity');

        // Purchases today (still valid)
        $purchasesToday = Purchase::whereDate('created_at', today())->sum('quantity');

        $userCount = User::count();

        // Profits for last 4 months
        $months = [];
        $profits = [];

        for ($i = 3; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $label = $month->format('M');

            // Fix: sum total from sale_items (unit_price * quantity)
            $totalSales = DB::table('sale_items')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum(DB::raw('unit_price * quantity'));

            // Purchases calculation remains valid
            $totalPurchases = DB::table('purchases')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum(DB::raw('unit_cost * quantity'));

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
