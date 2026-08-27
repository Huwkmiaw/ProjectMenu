<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin utama
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Kasir pertama
        User::firstOrCreate(
            ['username' => 'kasir1'],
            [
                'name' => 'Kasir Satu',
                'password' => Hash::make('kasir123'),
                'role' => 'cashier',
                'is_active' => true,
            ]
        );

        // Kasir kedua
        User::firstOrCreate(
            ['username' => 'kasir2'],
            [
                'name' => 'Kasir Dua',
                'password' => Hash::make('kasir123'),
                'role' => 'cashier',
                'is_active' => true,
            ]
        );
    }
}
