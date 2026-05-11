<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceSale;

class ServiceSaleController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'quantity'   => 'required|numeric|min:1',
            'amount'     => 'nullable|numeric|min:0',
        ]);

        $service = Service::findOrFail($request->service_id);

        $unitPrice = $service->price;
        $quantity  = $request->quantity;

        // auto calculate total if not manually overridden
        $totalAmount = $request->amount ?? ($unitPrice * $quantity);

        ServiceSale::create([
            'service_id' => $service->id,
            'quantity'   => $quantity,
            'unit_price' => $unitPrice,   // ✅ THIS FIXES YOUR ERROR
            'amount'     => $totalAmount,
            'date'       => $request->date ?? now(),
            'tenant_id'  => auth()->user()->tenant_id,
        ]);

        return back()->with('success', 'Service recorded successfully');
    }
}