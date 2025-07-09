<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $productCount = Product::count();

        // ✅ Correct: Get total quantity from sale_items
        $salesToday = DB::table('sale_items')
                        ->whereDate('created_at', today())
                        ->sum('quantity');

        $purchasesToday = Purchase::whereDate('created_at', today())->sum('quantity');

        $userCount = User::count();

        // Profits for last 4 months
        $months = [];
        $profits = [];

        for ($i = 3; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $label = $month->format('M');

            // ✅ Correct: Use sale_items table for sales totals
            $totalSales = DB::table('sale_items')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum(DB::raw('unit_price * quantity'));

            $totalPurchases = DB::table('purchases')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum(DB::raw('unit_cost * quantity'));

            $months[] = $label;
            $profits[] = round($totalSales - $totalPurchases, 2);
        }

        return view('admin.dashboard', compact(
            'productCount',
            'salesToday',
            'purchasesToday',
            'userCount',
            'months',
            'profits'
        ));
    }
}
