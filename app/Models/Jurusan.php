<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    protected $table = 'jurusan';
    protected $primaryKey = 'id_jurusan';

    protected $fillable = [
        'nama_jurusan',
        'tingkat',
        'kode',
        'durasi_min_bulan',
        'durasi_max_bulan',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'durasi_min_bulan' => 'integer',
        'durasi_max_bulan' => 'integer',
    ];

    public function pesertaMagang()
    {
        return $this->hasMany(PesertaMagang::class, 'jurusan_id', 'id_jurusan');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Target penugasan/sheet Excel dibentuk dari kode jurusan, misal
     * "smk_rpl" atau "kuliah_ti", supaya konsisten dipakai di
     * PenugasanTemplateService tanpa perlu daftar hardcode lagi.
     */
    public function getTargetPesertaAttribute(): string
    {
        return $this->tingkat.'_'.\Illuminate\Support\Str::lower($this->kode);
    }

    /**
     * Nama sheet resmi pada template Excel tugas mingguan.
     */
    public function getNamaSheetAttribute(): string
    {
        return $this->tingkat === 'smk'
            ? 'SMK '.$this->kode
            : $this->nama_jurusan;
    }
}
