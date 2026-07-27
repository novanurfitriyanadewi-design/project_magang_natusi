<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';
    protected $primaryKey = 'id_karyawan';

    protected $fillable = [
        'user_id',
        'permintaan_id',
        'nip',
        'nama_karyawan',
        'email',
        'no_hp',
        'alamat',
        'jabatan',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function permintaanLamaran(): BelongsTo
    {
        return $this->belongsTo(PermintaanLamaran::class, 'permintaan_id', 'id_permintaan');
    }

    public function absensi(): MorphMany
    {
        return $this->morphMany(Absensi::class, 'absentable');
    }
}