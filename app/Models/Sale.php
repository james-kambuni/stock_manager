<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = ['sale_date', 'total', 'tenant_id'];

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    protected static function booted()
    {
        static::addGlobalScope('tenant', function (Builder $query) {
            if (auth()->check() && !auth()->user()->is_admin) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            }
        });

        static::creating(function ($sale) {
            if (auth()->check() && !auth()->user()->is_admin) {
                $sale->tenant_id = auth()->user()->tenant_id;
            }
        });
    }
}
