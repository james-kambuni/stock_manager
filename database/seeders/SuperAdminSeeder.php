<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'vkambuni@gmail.com'],
            [
                'name'      => 'Super Admin',
                'password'  => Hash::make('james@1812'),
                'role'      => 'superadmin',
                'is_admin' => true,
                'tenant_id' => null,
            ]
        );
    }
}
