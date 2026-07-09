<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';
    protected $primaryKey = 'id_absensi';
    protected $fillable = [
        'peserta_id',
        'tanggal',
        'jam',
        'status',
        'latitude',
        'longitude',
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

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(
            PesertaMagang::class,
            'peserta_id',
            'id_peserta'
        );
    }
}