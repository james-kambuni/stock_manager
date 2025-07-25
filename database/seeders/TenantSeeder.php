<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = [
            ['name' => 'Alpha Ltd', 'email' => 'alpha@example.com'],
            ['name' => 'Beta Group', 'email' => 'beta@example.com'],
            ['name' => 'Gamma Solutions', 'email' => 'gamma@example.com'],
            ['name' => 'Junik Tenant', 'email' => 'junix@gmail.com'],

        ];

        foreach ($tenants as $data) {
            $tenant = Tenant::create([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);

            // Generate safe slug-based email domain for admin email
            $slug = Str::slug($data['name']);

            // Create an admin user for each tenant
            User::create([
                'name' => "{$data['name']} Admin",
                'email' => "admin@{$slug}.com",
                'password' => Hash::make('password'), // default password
                'role' => 'admin',
                'tenant_id' => $tenant->id,
            ]);
        }

        $this->command->info('✅ Tenants and admin users seeded successfully.');
    }
}
