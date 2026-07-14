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
            ['username' => 'adminnatusi'],
            [
                'nama' => 'Admin CV Natusi',
                'email' => 'admin@cvnatusi.com',
                'role' => 'admin',
                'password' => Hash::make('password123'),
                'wajib_ganti_password' => true,
            ]
        );
    }
}
