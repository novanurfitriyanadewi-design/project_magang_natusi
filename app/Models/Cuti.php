<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    use HasFactory;

    protected $table = 'cutis';

    protected $fillable = [
        'karyawan_id',
        'jenis_cuti',
        'tanggal_mulai',
        'tanggal_selesai',
        'alasan',
        'bukti_pendukung',
        'status',
        'catatan_hrd',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function getJumlahHariAttribute(): int
    {
        return $this->tanggal_mulai->diffInDays($this->tanggal_selesai) + 1;
    }

    public function getJenisLabelAttribute(): string
    {
        return match ($this->jenis_cuti) {
            'tahunan'    => 'Cuti Tahunan',
            'sakit'      => 'Cuti Sakit',
            'melahirkan' => 'Cuti Melahirkan',
            'penting'    => 'Cuti Alasan Penting',
            default      => 'Lainnya',
        };
    }

    public function statusMeta(): array
    {
        return match ($this->status) {
            'disetujui' => ['label' => 'Disetujui', 'class' => 'bg-emerald-100 text-emerald-700'],
            'ditolak'   => ['label' => 'Ditolak', 'class' => 'bg-rose-100 text-rose-700'],
            default     => ['label' => 'Menunggu', 'class' => 'bg-amber-100 text-amber-700'],
        };
    }
}