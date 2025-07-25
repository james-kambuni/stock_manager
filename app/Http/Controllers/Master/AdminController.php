<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Purchase;

class AdminController extends Controller
{
    public function dashboard()
{
    $tenants = User::whereNotNull('tenant_id')->distinct('tenant_id')->get();

    $productCount = Product::withoutGlobalScopes()->count();
    $totalSales = Sale::withoutGlobalScopes()->sum('total');
    $totalPurchases = Purchase::withoutGlobalScopes()->sum(\DB::raw('quantity * unit_cost'));

    // For Chart
    $salesByTenant = Sale::withoutGlobalScopes()
        ->select('tenant_id', \DB::raw('SUM(total) as total'))
        ->groupBy('tenant_id')
        ->get();

    $salesChart = [
        'labels' => $salesByTenant->pluck('tenant_id'),
        'data' => $salesByTenant->pluck('total'),
    ];

    return view('master.dashboard', compact(
        'tenants', 'productCount', 'totalSales', 'totalPurchases', 'salesChart'
    ));
}

    public function tenants()
{
    $tenants = User::whereNotNull('tenant_id')
        ->select('tenant_id', 'name', 'email', 'created_at')
        ->groupBy('tenant_id', 'name', 'email', 'created_at')
        ->get();

    return view('master.tenants', compact('tenants'));
}


    public function tenantData($id)
    {
        $products = Product::withoutGlobalScopes()->where('tenant_id', $id)->get();
        $sales = Sale::withoutGlobalScopes()->where('tenant_id', $id)->get();
        $purchases = Purchase::withoutGlobalScopes()->where('tenant_id', $id)->get();

        return view('master.tenant-data', compact('id', 'products', 'sales', 'purchases'));
    }
    
}

