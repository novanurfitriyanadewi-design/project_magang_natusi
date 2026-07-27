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

     public function statusMeta(): array
    {
        return match ($this->status) {
            'aktif'    => ['label' => 'Aktif', 'dot' => 'bg-emerald-500', 'text' => 'text-emerald-700'],
            'nonaktif' => ['label' => 'Non-Aktif', 'dot' => 'bg-rose-500', 'text' => 'text-rose-700'],
            default    => ['label' => ucfirst($this->status), 'dot' => 'bg-slate-400', 'text' => 'text-slate-600'],
        };
    }

    public function initials(): string
    {
        $words = preg_split('/\s+/', trim($this->nama_karyawan));
        return mb_strtoupper(mb_substr($words[0] ?? '', 0, 1) . mb_substr($words[1] ?? '', 0, 1));

    }
    public function absensi(): MorphMany
    {
        return $this->morphMany(Absensi::class, 'absentable');

    }
}