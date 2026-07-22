<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PengumpulanTugas;

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
        'file_tugas',
        'instansi',
        'status',
    ];

    protected $casts = [
        'pengumpulan' => 'datetime',
        'minggu_ke'   => 'integer',
    ];

    /**
     * Relasi ke user pembuat tugas
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    /**
     * Relasi ke data pengumpulan tugas
     */
    public function pengumpulanTugas()
    {
        return $this->hasMany(PengumpulanTugas::class, 'tugas_id', 'id_tugas');
    }

    public function penugasanPeserta()
    {
        return $this->hasMany(PenugasanPeserta::class, 'tugas_id', 'id_tugas');
    }
}
