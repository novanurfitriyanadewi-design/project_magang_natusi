<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'nama' => 'Direktur Utama',
                'email' => 'superadmin@cvnatusi.com',
                'password' => Hash::make('admin123'),
                'role' => 'superadmin',
                'wajib_ganti_password' => false,
            ]
        );
    }
}
