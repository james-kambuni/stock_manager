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
    // Show reports based on selected type (sales, purchases, inventory)
    public function index(Request $request)
{
    $type = $request->input('type', 'inventory');
    $tenantId = auth()->user()->tenant_id;

    $sales = collect();
    $purchases = collect();
    $products = collect();

    $start = $request->input('start');
    $end = $request->input('end');

    // Validate dates if provided
    if ($start && $end && $start > $end) {
        return back()->withErrors(['end' => 'End date must be after start date']);
    }

    switch ($type) {
        case 'sales':
            $sales = SaleItem::with('product', 'sale')
                ->whereHas('sale', function ($q) use ($tenantId) {
                    $q->where('tenant_id', $tenantId);
                })
                ->when($start && $end, function ($q) use ($start, $end) {
                    $q->whereBetween('created_at', [$start, $end]);
                })
                ->latest()
                ->get();
            break;

        case 'purchases':
            $purchases = Purchase::with('product')
                ->where('tenant_id', $tenantId)
                ->when($start && $end, function ($q) use ($start, $end) {
                    $q->whereBetween('created_at', [$start, $end]);
                })
                ->latest()
                ->get();
            break;

        default: // inventory
            $products = Product::where('tenant_id', $tenantId)->get();
            break;
    }

    return view('users.reports.index', compact('sales', 'purchases', 'products', 'type', 'start', 'end'));
}


    // Show today's transactions (sales, purchases, expenses)
    public function today()
    {
        $today = Carbon::today();
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

        $expenses = Expense::whereDate('created_at', $today)
            ->where('tenant_id', $tenantId)
            ->get();

        return view('users.reports.today', compact('sales', 'purchases', 'expenses'));
    }
}
