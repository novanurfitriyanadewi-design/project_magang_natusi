<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    use HasFactory;

    protected $table = 'tugas';

    protected $primaryKey = 'id_tugas';

    protected $fillable = [
        'user_id',
        'judul',
        'materi',
        'jenis_tugas',   // 'harian' | 'mingguan' | 'akhir'
        'file_tugas',
        'instansi',
        'status',        // 'aktif' | 'nonaktif' | 'selesai'
    ];

    protected $casts = [
        'pengumpulan' => 'datetime',
        'minggu_ke'   => 'integer',
    ];

    /**
     * Relasi ke user pembuat tugas (admin/pembimbing).
     * Sesuaikan nama Model User & primary key-nya jika berbeda.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}