<?php

// app/Models/Sale.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = ['sale_date','total'];

   public function items()
{
    return $this->hasMany(SaleItem::class);
}

}
