<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // ✅ Fetch existing Junik Tenant (don't create again)
        $tenant = Tenant::where('name', 'Junik Tenant')->first();

        // ❌ If not found, log a warning and skip user creation
        if (!$tenant) {
            $this->command->warn('Junik Tenant not found. Skipping user creation.');
            return;
        }

        // Admin user
        User::create([
            'name' => 'Junik agri-suppliers',
            'email' => 'junix@gmail.com',
            'password' => Hash::make('junik123'),
            'role' => 'admin',
            'tenant_id' => $tenant->id,
        ]);

        // Regular user
        User::create([
            'name' => 'Junik staff user',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'tenant_id' => $tenant->id,
        ]);
    }
}
