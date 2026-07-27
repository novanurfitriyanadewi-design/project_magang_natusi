<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranKaryawan extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_karyawan';
    protected $primaryKey = 'id_pembayaran'; // sesuaikan dengan nama PK di tabel
    public $timestamps = true;

    protected $fillable = [
        'karyawan_id',
        'periode',
        'nominal',
        'tanggal_bayar',
        'status',
        'keterangan',
        'bukti_transfer',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }
}
