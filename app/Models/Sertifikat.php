<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sertifikat extends Model
{
    protected $table = 'sertifikat';
    protected $primaryKey = 'id_sertifikat';

    protected $fillable = [
        'peserta_id',
        'divisi_id',
        'nomor_sertifikat',
        'judul',
        'predikat',
        'tanggal_terbit',
        'diterbitkan_oleh',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
    ];

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(PesertaMagang::class, 'peserta_id', 'id_peserta');
    }

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'divisi_id', 'id_divisi');
    }

    /**
     * Nama view Blade untuk cetak sertifikat, dipilih otomatis sesuai
     * jenjang jurusan peserta (SMK atau Kuliah/Universitas).
     * Default ke template Universitas kalau jenjang tidak diketahui.
     */
    public function viewCetak(): string
    {
        return $this->peserta?->jurusan?->tingkat === 'smk'
            ? 'sertifikat.cetak-smk'
            : 'sertifikat.cetak-universitas';
    }

    public function penerbit(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diterbitkan_oleh', 'id_user');
    }
}
