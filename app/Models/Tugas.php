<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PengumpulanTugas;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tugas extends Model
{
    use HasFactory;

    protected $table = 'tugas';

    protected $primaryKey = 'id_tugas';

    protected $fillable = [
        'user_id',
        'kode_tugas',
        'judul',
        'materi',
        'kategori_tugas',
        'jenis_tugas',
        'minggu_ke',
        'file_tugas',
        'pengumpulan',
        'instansi',
        'target_peserta',
        'hari_tampil',
        'hari_deadline',
        'jam_deadline',
        'rilis_hari_ke',
        'deadline_hari_ke',
        'hari_mulai',
        'keterangan',
        'template_batch',
        'status',
    ];

    protected $casts = [
        'pengumpulan' => 'datetime',
        'minggu_ke' => 'integer',
        'rilis_hari_ke' => 'integer',
        'deadline_hari_ke' => 'integer',
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

    public function penugasanPeserta(): HasMany
    {
        return $this->hasMany(PenugasanPeserta::class, 'tugas_id', 'id_tugas');
    }
}
