<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\Purchase;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type', 'inventory'); // default to inventory

        switch ($type) {
            case 'sales':
                $sales = SaleItem::with('product', 'sale')->latest()->get();
                return view('users.reports', compact('sales'))->with('reportType', 'sales');

            case 'purchases':
                $purchases = Purchase::with('product')->latest()->get();
                return view('users.reports', compact('purchases'))->with('reportType', 'purchases');

            default:
                $products = Product::all();
                return view('users.reports', compact('products'))->with('reportType', 'inventory');
        }
    }

    // Show today's sales and purchases
    public function today()
    {
        $today = Carbon::today();

        $sales = SaleItem::with('product', 'sale')
                    ->whereDate('created_at', $today)
                    ->get();

        $purchases = Purchase::with('product')
                    ->whereDate('created_at', $today)
                    ->get();

        return view('users.reports.today', compact('sales', 'purchases'));
    }
}
