<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenugasanPeserta extends Model
{
    use HasFactory;

    protected $table = 'penugasan_peserta';
    protected $primaryKey = 'id_penugasan';

    protected $fillable = [
        'tugas_id',
        'peserta_id',
        'template_laporan_id',
        'tersedia_pada',
        'deadline',
        'status',
        'keterangan',
        'ketentuan_laporan',
    ];

    protected function casts(): array
    {
        return [
            'tersedia_pada' => 'datetime',
            'deadline' => 'datetime',
        ];
    }

    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class, 'tugas_id', 'id_tugas');
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(PesertaMagang::class, 'peserta_id', 'id_peserta');
    }

    public function templateLaporan(): BelongsTo
    {
        return $this->belongsTo(
            TemplateLaporan::class,
            'template_laporan_id',
            'id_template_laporan'
        );
    }
}
