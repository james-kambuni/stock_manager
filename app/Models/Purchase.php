<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Purchase extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'unit_cost',
        'expiry_date',
        'tenant_id',
        'previous_stock',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'previous_stock' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockBatches()
    {
        return $this->hasMany(StockBatch::class);
    }

    /*
    |--------------------------------------------------------------------------
    | TENANT GLOBAL SCOPE
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::addGlobalScope('tenant', function (Builder $builder) {

            if (
                auth()->check() &&
                isset(auth()->user()->tenant_id) &&
                !auth()->user()->is_admin
            ) {
                $builder->where(
                    'tenant_id',
                    auth()->user()->tenant_id
                );
            }
        });

        static::creating(function ($purchase) {

            if (
                auth()->check() &&
                isset(auth()->user()->tenant_id) &&
                empty($purchase->tenant_id)
            ) {
                $purchase->tenant_id = auth()->user()->tenant_id;
            }
        });
    }
}