<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermintaanMagangAnggota extends Model
{
    use HasFactory;

    protected $table = 'permintaan_magang_anggota';
    protected $primaryKey = 'id_anggota';

    protected $fillable = [
        'permintaan_id',
        'user_id',
        'peserta_id',
        'nama',
        'email',
        'no_induk',
        'jurusan',
        'no_hp',
        'cv_path',
        'surat_pengajuan_path',
        'is_ketua',
        'username_peserta',
        'password_awal',
    ];

    protected function casts(): array
    {
        return [
            'is_ketua' => 'boolean',
        ];
    }

    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(PermintaanMagang::class, 'permintaan_id', 'id_permintaan');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(PesertaMagang::class, 'peserta_id', 'id_peserta');
    }
}
