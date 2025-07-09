<?php
// app/Models/SaleItem.php
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
    ];

    public function sale()
{
    return $this->belongsTo(Sale::class);
}

public function product()
{
    return $this->belongsTo(Product::class);
}

}
