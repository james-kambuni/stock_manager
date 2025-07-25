<?php

// app/Models/Expense.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
    'user_id',
    'tenant_id',
    'category',
    'amount',
    'notes',
    'date',
];

public function user() {
    return $this->belongsTo(User::class);
}


}

