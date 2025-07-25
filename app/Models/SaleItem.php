<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'unit_cost',
        'total',
        'previous_stock',
    ];

    protected static function booted()
    {
        // Automatically set previous_stock from product stock at time of sale
        static::creating(function ($item) {
            if ($item->product_id) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $item->previous_stock = $product->stock;
                }
            }
        });
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
