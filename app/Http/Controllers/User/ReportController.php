<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\Purchase;
use App\Models\Expense;
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

    // ✅ Show today's sales, purchases, and expenses
   public function today()
{
    $today = \Carbon\Carbon::today();
    $tenantId = auth()->user()->tenant_id;

    $sales = SaleItem::with('product', 'sale')
        ->whereDate('created_at', $today)
        ->whereHas('sale', function ($q) use ($tenantId) {
            $q->where('tenant_id', $tenantId);
        })
        ->get();

    $purchases = Purchase::with('product')
        ->whereDate('created_at', $today)
        ->where('tenant_id', $tenantId)
        ->get();

    $expenses = \App\Models\Expense::whereDate('created_at', $today)
        ->where('tenant_id', $tenantId) 
        ->get();

    return view('users.reports.today', compact('sales', 'purchases', 'expenses'));
}
}
