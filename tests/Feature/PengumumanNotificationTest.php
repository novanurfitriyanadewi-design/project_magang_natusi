<?php

use App\Models\Karyawan;
use App\Models\User;

it('notifies every active employee when an announcement is published', function () {
    $admin = User::create([
        'nama' => 'Admin Karyawan',
        'username' => 'adminpengumuman',
        'email' => 'adminpengumuman@example.com',
        'password' => bcrypt('password123'),
        'role' => 'admin_karyawan',
    ]);

    $employees = collect(['Budi', 'Siti'])->map(function (string $name, int $index) {
        $user = User::create([
            'nama' => $name,
            'username' => strtolower($name) . 'pengumuman',
            'email' => strtolower($name) . 'pengumuman@example.com',
            'password' => bcrypt('password123'),
            'role' => 'karyawan',
        ]);

        Karyawan::create([
            'user_id' => $user->id_user,
            'nip' => 'KRY-' . str_pad((string) ($index + 10), 3, '0', STR_PAD_LEFT),
            'nama_karyawan' => $name,
            'email' => strtolower($name) . '@company.test',
            'status' => 'aktif',
        ]);

        return $user;
    });

    $this->actingAs($admin)
        ->post(route('admin-karyawan.pengumuman.store'), [
            'judul' => 'Libur Nasional',
            'isi' => 'Kantor libur pada hari Senin.',
            'kategori' => 'umum',
            'target' => 'umum',
            'aktif' => true,
        ])
        ->assertSessionHas('success');

    foreach ($employees as $employee) {
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $employee->id_user,
            'judul' => 'Libur Nasional',
            'kategori' => 'pengumuman',
            'tipe' => 'info',
            'dibaca' => false,
        ]);
    }
});
