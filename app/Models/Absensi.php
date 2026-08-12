<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';
    protected $primaryKey = 'id_absensi';
    protected $fillable = [
        'absentable_id',
        'absentable_type',
        'tanggal',
        'jam',
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
