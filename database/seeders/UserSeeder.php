<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    // Admin user
    User::create([
    'name' => 'James',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'role' => 'admin',
]);


    // Regular user
    User::create([
        'name' => 'Normal User',
        'email' => 'user@example.com',
        'password' => Hash::make('password'),
        'is_admin' => false,
    ]);
}
}
