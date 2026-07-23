<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermintaanLamaran extends Model
{
    use HasFactory;

    protected $table = 'permintaan_lamaran';
    protected $primaryKey = 'id_permintaan';

    protected $fillable = [
        'user_id',
        'nama_pemohon',
        'email',
        'posisi',
        'tanggal_lamar',
        'no_hp',
        'pesan',
        'status',
        'cv_path',
        'portfolio_path',
    ];

    protected $casts = [
        'tanggal_lamar' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id_user'
        );
    }

    // Helper untuk mendapatkan inisial nama pemohon
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->nama_pemohon);
        $initials = '';
        foreach (array_slice($words, 0, 2) as $w) {
            $initials .= strtoupper($w[0] ?? '');
        }
        return $initials;
    }
}