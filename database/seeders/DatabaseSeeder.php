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
        // 1. Membuat/Update Super Admin
        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'nama' => 'Super Admin',
                'email' => 'superadmin@cvnatusi.com',
                'password' => Hash::make('admin123'),
                'role' => 'superadmin',
                'wajib_ganti_password' => false,
            ]
        );

        User::updateOrCreate(
            ['username' => 'adminkaryawan'],
            [
                'nama' => 'Admin Karyawan',
                'email' => 'adminkaryawan@cvnatusi.com',
                'password' => Hash::make('password'),
                'role' => 'admin_karyawan',
                'wajib_ganti_password' => false,
            ]
        );

        User::updateOrCreate(
            ['username' => 'adminpesertamagang'],
            [
                'nama' => 'Admin Peserta Magang',
                'email' => 'adminpesertamagang@cvnatusi.com',
                'password' => Hash::make('password'),
                'role' => 'admin_peserta',
                'wajib_ganti_password' => false,
            ]
        );

        // 2. Menjalankan PesertaMagangSeeder
        $this->call([
            PesertaMagangSeeder::class,
        ]);
    }
}
