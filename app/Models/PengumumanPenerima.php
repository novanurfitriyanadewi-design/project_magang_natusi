<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengumumanPenerima extends Model
{
    protected $table = 'pengumuman_penerima';

    protected $fillable = [
        'id_pengumuman',
        'tipe_penerima',
        'id_penerima',
    ];

    public function pengumuman()
    {
        return $this->belongsTo(
            Pengumuman::class,
            'id_pengumuman',
            'id_pengumuman'
        );
    }

    public function karyawan()
    {
        return $this->belongsTo(
            Karyawan::class,
            'id_penerima',
            'id_karyawan'
        );
    }
}