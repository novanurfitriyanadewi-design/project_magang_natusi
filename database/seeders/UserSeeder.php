<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Admin CV Natusi',
            'email'    => 'admin@cvnatusi.com',
            'role'     => 'admin',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'name'     => 'Nova Pelamar',
            'email'    => 'pelamar@cvnatusi.com',
            'role'     => 'pelamar',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'name'     => 'Karyawan Test',
            'email'    => 'karyawan@cvnatusi.com',
            'role'     => 'karyawan',
            'password' => Hash::make('password123'),
        ]);
    }
}