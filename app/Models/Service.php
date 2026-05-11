<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
    'service_id',
    'quantity',
    'unit_price',
    'amount',
    'date',
    'tenant_id',
];
public function sales()
{
    return $this->hasMany(ServiceSale::class);
}
}
