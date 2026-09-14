<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'nama' => 'Admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_aktif' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'kasir@gmail.com'],
            [
                'nama' => 'Kasir',
                'password' => Hash::make('kasir123'),
                'role' => 'kasir',
                'is_aktif' => true,
            ]
        );
    }
}
