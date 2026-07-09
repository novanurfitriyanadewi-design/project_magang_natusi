<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tugas extends Model
{
    use HasFactory;

    protected $table = 'tugas';
    protected $primaryKey = 'id_tugas';
    protected $fillable = [
        'user_id',
        'judul',
        'materi',
        'jenis_tugas',
        'minggu_ke',
        'file_tugas',
        'pengumpulan',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'minggu_ke' => 'integer',
            'pengumpulan' => 'datetime',
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
    public function pengumpulanTugas(): HasMany
    {
        return $this->hasMany(
            PengumpulanTugas::class,
            'tugas_id',
            'id_tugas'
        );
    }
}