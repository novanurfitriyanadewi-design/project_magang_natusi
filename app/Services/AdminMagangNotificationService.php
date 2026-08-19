<?php

namespace App\Services;

use App\Models\Notifikasi;
use App\Models\User;

class AdminMagangNotificationService
{
    public function notify(
        string $judul,
        string $pesan,
        string $kategori = 'pengajuan',
        ?int $referensiId = null,
        string $tipe = 'info'
    ): void {
        $admins = User::query()
            ->whereIn('role', ['admin', 'admin_peserta'])
            ->get(['id_user']);

        foreach ($admins as $admin) {
            Notifikasi::query()->create([
                'user_id' => $admin->id_user,
                'judul' => $judul,
                'pesan' => $pesan,
                'kategori' => $kategori,
                'tipe' => $tipe,
                'referensi_id' => $referensiId,
                'dibaca' => false,
            ]);
        }
    }
}
