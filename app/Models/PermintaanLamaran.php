<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PermintaanLamaran extends Model
{
    use HasFactory;

    protected $table = 'permintaan_lamaran';
    protected $primaryKey = 'id_permintaan';

    protected $fillable = [
        'user_id',
        'nama_pemohon',
        'email',
        'nik',                  // Menggunakan 'nik'
        'pendidikan_terakhir',  // Menggunakan 'pendidikan_terakhir'
        'posisi',
        'bidang_keahlian',      // <-- FIX: kolom baru, tadinya belum ada di fillable
        'tanggal_lamar',
        'no_hp',
        'pesan',
        'status',
        'username_karyawan',
        'password_karyawan',
        'jadwal_interview',
        'lokasi_interview',
        'akun_dibuat',
        'cv_path',
        'surat_lamaran_path',
        'ijazah_path',
        'ktp_path',
        'pas_foto_path',
        'skck_path',
        'portfolio_path',        // FIX: duplikat dihapus (sebelumnya muncul 2x)
        'pengalaman_kerja_path',
        'alasan_penolakan',
        'catatan_revisi',
    ];

    protected $casts = [
        'tanggal_lamar' => 'date',
        'akun_dibuat'   => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id_user'
        );
    }

    public function karyawan(): HasOne
    {
        return $this->hasOne(Karyawan::class, 'permintaan_id', 'id_permintaan');
    }

    public function getInitialsAttribute(): string
    {
        if (empty($this->nama_pemohon)) {
            return 'P';
        }

        $words = array_filter(explode(' ', trim($this->nama_pemohon)));
        $initials = '';

        foreach (array_slice($words, 0, 2) as $w) {
            $initials .= strtoupper(mb_substr($w, 0, 1));
        }

        return $initials ?: 'P';
    }
}