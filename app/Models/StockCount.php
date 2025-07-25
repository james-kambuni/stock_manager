<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/StockCount.php
class StockCount extends Model
{
    protected $fillable = ['tenant_id', 'product_id', 'physical_stock', 'note'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
