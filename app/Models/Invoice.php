<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
   protected $fillable = [
    'invoice_number', 'customer_name', 'customer_address', 'customer_phone',
    'customer_email', 'subtotal', 'vat', 'total', 'served_by', 'tenant_id'
];

public function items()
{
    return $this->hasMany(InvoiceItem::class);
}

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
