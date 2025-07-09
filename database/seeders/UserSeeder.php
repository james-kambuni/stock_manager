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
    'name' => 'Junik agri-suppliers',
    'email' => 'junix@gmail.com',
    'password' => Hash::make('junik123'),
    'role' => 'admin',
]);


    // Regular user
    User::create([
        'name' => 'Normal user',
        'email' => 'user@example.com',
        'password' => Hash::make('password'),
        'is_admin' => false,
    ]);
}
}
