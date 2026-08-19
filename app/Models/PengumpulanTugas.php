<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengumpulanTugas extends Model
{
    use HasFactory;

    protected $table = 'pengumpulan_tugas';
    protected $primaryKey = 'id_pengumpulan';
    protected $fillable = [
        'tugas_id',
        'peserta_id',
        'file_jawaban',
        'catatan',
        'link_external',
        'dikumpulkan_pada',
        'status',
        'status_review',
        'catatan_revisi',
        'revisi_ke',
        'reviewed_at',
        'reviewed_by',
    ];

    protected function casts(): array
    {
        return [
            'dikumpulkan_pada' => 'datetime',
            'reviewed_at' => 'datetime',
            'revisi_ke' => 'integer',
        ];
    }
    public function tugas(): BelongsTo
    {
        return $this->belongsTo(
            Tugas::class,
            'tugas_id',
            'id_tugas'
        );
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