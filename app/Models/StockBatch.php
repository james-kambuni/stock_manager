<?php

// app/Models/StockBatch.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockBatch extends Model
{
    protected $fillable = [
    'product_id',
    'purchase_id',
    'quantity',
    'remaining',
    'expiry_date',
    'cost_price',
    'tenant_id',
];


    public $timestamps = true;

    protected $dates = ['expiry_date'];
    public function product()
{
    return $this->belongsTo(Product::class);
}

}

