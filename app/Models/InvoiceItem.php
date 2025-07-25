<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
    'invoice_id',
    'tenant_id',
    'product_name',
    'quantity',
    'unit_price',
    'total_price',
];


    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}

