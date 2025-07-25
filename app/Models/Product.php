<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    protected $fillable = [
    'name', 'stock', 'cost_price', 'selling_price',
    'min_threshold', 'max_threshold', 'tenant_id',
    'is_perishable',
];


    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function saleItems()
    {
        return $this->hasMany(\App\Models\SaleItem::class);
    }
    protected $casts = [
        'is_perishable' => 'boolean',
    ];

    public function stockCount()
    {
        return $this->hasOne(StockCount::class)->latest();
    }

    // Global tenant scope
    protected static function booted()
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check() && !auth()->user()->is_admin) {
                $builder->where('tenant_id', auth()->user()->tenant_id);
            }
        });
    }
}
