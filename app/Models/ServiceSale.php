<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceSale extends Model
{
    protected $fillable = [
        'service_id',
        'tenant_id',
        'quantity',
        'unit_price',
        'amount',
        'date'
    ];

    /**
     * Service relationship
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Auto-calculate total if needed
     */
    public function getTotalAttribute()
    {
        return $this->quantity * $this->unit_price;
    }

     public $timestamps = true;
}