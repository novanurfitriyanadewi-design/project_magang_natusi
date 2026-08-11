<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranKaryawan extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_karyawan';
    protected $primaryKey = 'id_pembayaran';
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

    protected $casts = [
        'tanggal_bayar' => 'date',
        'nominal' => 'integer',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }

    public function getPeriodeLabelAttribute(): string
    {
        try {
            return \Carbon\Carbon::createFromFormat('Y-m', $this->periode)->translatedFormat('F Y');
        } catch (\Exception $e) {
            return $this->periode;
        }
    }

    public function statusMeta(): array
    {
        return match ($this->status) {
            'terbayar'       => ['label' => 'Terbayar', 'class' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200'],
            'belum_terbayar' => ['label' => 'Belum Terbayar', 'class' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200'],
            default          => ['label' => ucfirst(str_replace('_', ' ', $this->status)), 'class' => 'bg-slate-50 text-slate-600 ring-1 ring-slate-200'],
        };
    }
}