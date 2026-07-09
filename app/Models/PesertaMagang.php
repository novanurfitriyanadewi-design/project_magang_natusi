<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PesertaMagang extends Model
{
    use HasFactory;

    protected $table = 'peserta_magang';
    protected $primaryKey = 'id_peserta';
    protected $fillable = [
        'user_id',
        'permintaan_id',
        'alamat',
        'tingkat_pendidikan',
        'kelas',
        'tgl_mulai',
        'tgl_selesai',
        'durasi_magang',
        'nama_guru',
        'no_hpguru',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tgl_mulai' => 'date',
            'tgl_selesai' => 'date',
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
    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(
            PermintaanMagang::class,
            'permintaan_id',
            'id_permintaan'
        );
    }
    public function absensi(): HasMany
    {
        return $this->hasMany(
            Absensi::class,
            'peserta_id',
            'id_peserta'
        );
    }
    public function laporanMingguan(): HasMany
    {
        return $this->hasMany(
            LaporanMingguan::class,
            'peserta_id',
            'id_peserta'
        );
    }
    public function pembayaran(): HasMany
    {
        return $this->hasMany(
            Pembayaran::class,
            'peserta_id',
            'id_peserta'
        );
    }
    public function pengumpulanTugas(): HasMany
    {
        return $this->hasMany(
            PengumpulanTugas::class,
            'peserta_id',
            'id_peserta'
        );
    }
}