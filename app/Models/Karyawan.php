<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';
    protected $primaryKey = 'id_karyawan';

    protected $fillable = [
        'permintaan_id',
        'nip',
        'nama_karyawan',
        'email',
        'no_hp',
        'alamat',
        'jabatan',
        'divisi_id',
        'tanggal_bergabung',
        'status',
        'user_id',
    ];

    /**
     * Relasi ke Tabel divisi
     */
    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id', 'id');
    }

    /**
     * Relasi ke Tabel permintaan_lamaran
     */
    public function permintaanLamaran()
    {
        return $this->belongsTo(PermintaanLamaran::class, 'permintaan_id', 'id_permintaan');
    }
}