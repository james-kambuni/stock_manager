<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\ServiceSale;
use Carbon\Carbon;

class ReportController extends Controller
{
    // ================= ALL REPORTS =================
    public function index(Request $request)
    {
        $type = $request->input('type', 'inventory');
        $start = $request->input('start');
        $end = $request->input('end');
        $tenantId = auth()->user()->tenant_id;

        $reportData = collect();

        switch ($type) {

            case 'inventory':
                $reportData = Product::where('tenant_id', $tenantId)->get();
                break;

            case 'sales':
                $reportData = SaleItem::with('product', 'sale')
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
                $reportData = Purchase::with('product')
                    ->where('tenant_id', $tenantId)
                    ->when($start && $end, function ($q) use ($start, $end) {
                        $q->whereBetween('created_at', [$start, $end]);
                    })
                    ->latest()
                    ->get();
                break;

            case 'services':
                $reportData = ServiceSale::with('service')
                    ->where('tenant_id', $tenantId)
                    ->when($start && $end, function ($q) use ($start, $end) {
                        $q->whereBetween('date', [$start, $end]);
                    })
                    ->latest()
                    ->get();
                break;
        }

        return view('users.reports.index', compact('reportData', 'type'))
            ->with('reportType', $type);
    }

    // ================= TODAY REPORT =================
    public function today()
    {
        $today = Carbon::today();
        $tenantId = auth()->user()->tenant_id;

        $sales = SaleItem::with('product')
            ->whereHas('sale', function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->whereDate('created_at', $today)
            ->get();

        $purchases = Purchase::with('product')
            ->where('tenant_id', $tenantId)
            ->whereDate('created_at', $today)
            ->get();

        $expenses = Expense::where('tenant_id', $tenantId)
            ->whereDate('created_at', $today)
            ->get();

        $serviceSales = ServiceSale::with('service')
            ->where('tenant_id', $tenantId)
            ->whereDate('date', $today)
            ->get();

        return view('users.reports.today', compact(
            'sales',
            'purchases',
            'expenses',
            'serviceSales'
        ));
    }
}