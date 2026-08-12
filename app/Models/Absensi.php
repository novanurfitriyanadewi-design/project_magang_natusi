<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\User;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';
    protected $primaryKey = 'id_absensi';
    protected $fillable = [
        'user_id',
        'absentable_id',
        'absentable_type',
        'tanggal',
        'jam',
        'jam_masuk',
        'jam_keluar',
        'status',
        'latitude',
        'longitude',
        'alamat',
        'foto',
        'jarak_meter',
        'surat_izin',
        'surat_sakit',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jam_masuk' => 'datetime:H:i:s',
            'jam_keluar' => 'datetime:H:i:s',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'jarak_meter' => 'decimal:2',
        ];
    }

    /**
     * Relasi polymorphic ke pemilik absensi (PesertaMagang atau Karyawan).
     */
    public function absentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Relasi ke tabel users (pemilik akun yang melakukan absensi).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    /**
     * Dipertahankan untuk kompatibilitas kode lama yang memanggil
     * $absensi->peserta / whereHas('peserta', ...). Hanya cocok untuk
     * baris yang absentable_type-nya PesertaMagang; kalau dipakai untuk
     * query, pastikan query induknya sudah difilter
     * where('absentable_type', PesertaMagang::class) supaya tidak
     * tercampur dengan data absensi karyawan.
     */
    public function peserta(): BelongsTo
    {
        return $this->belongsTo(
            PesertaMagang::class,
            'absentable_id',
            'id_peserta'
        );
    }
}
