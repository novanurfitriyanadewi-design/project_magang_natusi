<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PermintaanMagang extends Model
{
    use HasFactory;

    protected $table = 'permintaan_magang';
    protected $primaryKey = 'id_permintaan';

    protected $fillable = [
        'user_id',
        'nama_pemohon',
        'email',
        'nama_sekolah',
        'no_induk',
        'jurusan',
        'no_hp',
        'pesan',
        'status',
        'username_peserta',
        'password_awal',
        'akun_dibuat',
    ];

    protected function casts(): array
    {
        return [
            'akun_dibuat' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id_user'
        );
    }

    public function peserta(): HasOne
    {
        return $this->hasOne(
            PesertaMagang::class,
            'permintaan_id',
            'id_permintaan'
        );
    }
}