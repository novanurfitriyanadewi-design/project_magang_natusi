<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resign extends Model
{
    use HasFactory;

    protected $table = 'resigns';

    // Jika primary key Anda bukan "id", ubah sesuai nama kolom.
    // Contoh:
    // protected $primaryKey = 'id_resign';

    protected $fillable = [
        'karyawan_id',
        'alasan',
        'tanggal_efektif',
        'status',
        'catatan_hrd',
        'surat_resign_path',
        'surat_resign_original_name',
    ];

    protected $casts = [
        'tanggal_efektif' => 'date',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}
