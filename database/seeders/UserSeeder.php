<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // =====================================
        // ADMIN
        // =====================================

        User::create([
            'nama' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'Admin',
        ]);

        // =====================================
        // KETUA
        // =====================================
        User::create([
            'nama' => 'ketua',
            'email' => 'ketua@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'Ketua',
        ]);

        //cara call seeder: php artisan db:seed --class=UserSeeder
    }
}
