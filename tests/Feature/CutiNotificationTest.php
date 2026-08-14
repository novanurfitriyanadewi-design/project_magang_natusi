<?php

use App\Models\Karyawan;
use App\Models\User;

it('notifies HRD when an employee submits a leave request', function () {
    $admin = User::create([
        'nama' => 'Admin Karyawan',
        'username' => 'admincuti',
        'email' => 'admincuti@example.com',
        'password' => bcrypt('password123'),
        'role' => 'admin_karyawan',
    ]);

    $karyawanUser = User::create([
        'nama' => 'Budi Santoso',
        'username' => 'budicuti',
        'email' => 'budicuti@example.com',
        'password' => bcrypt('password123'),
        'role' => 'karyawan',
    ]);

    Karyawan::create([
        'user_id' => $karyawanUser->id_user,
        'nip' => 'KRY-002',
        'nama_karyawan' => 'Budi Santoso',
        'email' => 'budi@company.test',
        'status' => 'aktif',
    ]);

    $this->actingAs($karyawanUser)
        ->post(route('karyawan.cuti.store'), [
            'jenis_cuti' => 'tahunan',
            'tanggal_mulai' => now()->addDay()->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(2)->format('Y-m-d'),
            'alasan' => 'Keperluan keluarga.',
        ])
        ->assertSessionHas('success');

    $this->assertDatabaseHas('notifikasi', [
        'user_id' => $admin->id_user,
        'judul' => 'Pengajuan Cuti Baru',
        'kategori' => 'cuti',
        'dibaca' => false,
    ]);
});
