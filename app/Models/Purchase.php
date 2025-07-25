<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Purchase extends Model
{
    protected $fillable = [
        'product_id', 'quantity', 'unit_cost',
        'expiry_date', 'tenant_id','previous_stock', 'remaining',
    ];

    protected $dates = ['expiry_date'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Global scope + auto tenant_id assign on creation
    protected static function booted()
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check() && !auth()->user()->is_admin) {
                $builder->where('tenant_id', auth()->user()->tenant_id);
            }
        });

        static::creating(function ($purchase) {
            if (auth()->check() && !auth()->user()->is_admin) {
                $purchase->tenant_id = auth()->user()->tenant_id;
            }
        });
    }
}
