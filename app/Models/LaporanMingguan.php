<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanMingguan extends Model
{
    use HasFactory;

    protected $table = 'laporan_mingguan';

    protected $primaryKey = 'id_laporan';

    protected $fillable = [
        'peserta_id',
        'minggu_ke',
        'laporan',
        'dikumpulkan_pada',
    ];

    protected $casts = [
        'dikumpulkan_pada' => 'datetime',
    ];

    public function peserta()
    {
        return $this->belongsTo(
            PesertaMagang::class,
            'peserta_id',
            'id_peserta'
        );
    }
}