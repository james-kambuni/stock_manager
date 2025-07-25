<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin'          => 'boolean',
        'tenant_id'         => 'integer',
        'is_active'         => 'boolean',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
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

}
