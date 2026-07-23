<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Karyawan extends Model
{
    use HasFactory;

    // Menyesuaikan nama tabel di database
    protected $table = 'karyawan';

    // Menyesuaikan primary key (default Laravel adalah 'id')
    protected $primaryKey = 'id_karyawan';

    // Kolom yang diizinkan untuk diisi secara massal (mass assignment)
    protected $fillable = [
        'permintaan_id',
        'nip',
        'nama_karyawan',
        'email',
        'no_hp',
        'alamat',
        'jabatan',
        'status',
    ];

    /**
     * Relasi ke tabel permintaan_lamaran (Inverse One-to-One atau One-to-Many)
     */
    public function permintaanLamaran(): BelongsTo
    {
        // (NamaModel::class, 'foreign_key', 'owner_key')
        return $this->belongsTo(PermintaanLamaran::class, 'permintaan_id', 'id_permintaan');
    }
}