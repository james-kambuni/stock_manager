<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'email', 'phone', 'logo', 'is_active'];

    /**
     * Boot method to handle cascading deletes
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($tenant) {
            // Delete all users under this tenant
            $tenant->users()->delete();
        });
    }

    /**
     * Relationship: Tenant has many Users
     */
    public function user()
{
    return $this->belongsTo(User::class);
}

public function tenant()
{
    return $this->belongsTo(Tenant::class);
}

public function admin()
{
    return $this->belongsTo(User::class, 'admin_id'); // assuming admin is stored in users table
}
public function users()
{
    return $this->hasMany(User::class);
}

public function products()
{
    return $this->hasMany(Product::class);
}

public function purchases()
{
    return $this->hasMany(Purchase::class);
}

public function sales()
{
    return $this->hasMany(Sale::class);
}


}
