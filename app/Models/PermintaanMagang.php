<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'jenjang',
        'tipe_pengajuan',
        'jumlah_anggota',
        'no_hp',
        'pesan',
        'cv_path',
        'surat_pengajuan_path',
        'status',
        'username_peserta',
        'password_awal',
        'akun_dibuat',
        'alasan_penolakan',
        'catatan_revisi',
    ];

    protected function casts(): array
    {
        return [
            'akun_dibuat' => 'boolean',
            'jumlah_anggota' => 'integer',
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

    public function pesertas(): HasMany
    {
        return $this->hasMany(
            PesertaMagang::class,
            'permintaan_id',
            'id_permintaan'
        );
    }


    public function anggota(): HasMany
    {
        return $this->hasMany(
            PermintaanMagangAnggota::class,
            'permintaan_id',
            'id_permintaan'
        )->orderByDesc('is_ketua')->orderBy('id_anggota');
    }

    public function riwayatBerkas(): HasMany
    {
        return $this->hasMany(RiwayatBerkasMagang::class, 'permintaan_id', 'id_permintaan');
    }
}
