<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Invoice;
use App\Models\InvoiceItem;

class InvoiceController extends Controller
{
    public function create()
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;

        $products = Product::where('tenant_id', $tenantId)->get();
        $invoiceNumber = 'INV-' . now()->format('YmdHis') . rand(10, 99);

        return view('admin.invoice', compact('products', 'user', 'invoiceNumber'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_address' => 'nullable|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email',
            'items' => 'required|array',
            'items.*.name' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric',
            'vat' => 'required|numeric',
            'total' => 'required|numeric',
            'served_by' => 'required|string',
            'invoice_number' => 'required|string|unique:invoices,invoice_number'
        ]);

        $invoice = Invoice::create([
            'tenant_id'        => $tenantId,
            'invoice_number'   => $validated['invoice_number'],
            'customer_name'    => $validated['customer_name'],
            'customer_address' => $validated['customer_address'],
            'customer_phone'   => $validated['customer_phone'],
            'customer_email'   => $validated['customer_email'],
            'subtotal'         => $validated['subtotal'],
            'vat'              => $validated['vat'],
            'total'            => $validated['total'],
            'served_by'        => $validated['served_by'],
        ]);

        foreach ($validated['items'] as $item) {
            $invoice->items()->create([
                'tenant_id'    => $tenantId,
                'product_name' => $item['name'],
                'quantity'     => $item['qty'],
                'unit_price'   => $item['price'],
                'total_price'  => $item['qty'] * $item['price'],
            ]);
        }

        return response()->json([
            'success' => true,
            'invoice_id' => $invoice->id
        ]);
    }
}
