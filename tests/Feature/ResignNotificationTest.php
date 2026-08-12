<?php

use App\Models\Karyawan;
use App\Models\Notifikasi;
use App\Models\Resign;
use App\Models\User;

it('sends notifications to admin on submission and to karyawan on decision', function () {
    $admin = User::create([
        'nama' => 'Admin Karyawan',
        'username' => 'adminresign',
        'email' => 'adminresign@example.com',
        'password' => bcrypt('password123'),
        'role' => 'admin_karyawan',
    ]);

    $karyawanUser = User::create([
        'nama' => 'Budi Santoso',
        'username' => 'budisantoso',
        'email' => 'budisantoso@example.com',
        'password' => bcrypt('password123'),
        'role' => 'karyawan',
    ]);

    Karyawan::create([
        'user_id' => $karyawanUser->id_user,
        'nip' => 'KRY-001',
        'nama_karyawan' => 'Budi Santoso',
        'email' => 'budi@company.test',
        'status' => 'aktif',
    ]);

    $this->actingAs($karyawanUser)
        ->post(route('karyawan.resign.store'), [
            'tanggal_efektif' => now()->addDay()->format('Y-m-d'),
            'alasan' => 'Saya ingin resign karena alasan keluarga yang mendesak.',
        ])
        ->assertRedirect();

    $resign = Resign::first();

    expect($resign)->not->toBeNull();
    $this->assertDatabaseHas('notifikasi', [
        'user_id' => $admin->id_user,
        'judul' => 'Pengajuan Resign Baru',
    ]);

    $this->actingAs($admin)
        ->patch(route('admin-karyawan.resign.approve', $resign))
        ->assertRedirect();

    $this->assertDatabaseHas('notifikasi', [
        'user_id' => $karyawanUser->id_user,
        'judul' => 'Pengajuan Resign Diterima',
    ]);
});
