<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'tanggal_lamar',
        'no_hp',
        'pesan',
        'status',
        'akun_dibuat',
        'cv_path',
        'portfolio_path',
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