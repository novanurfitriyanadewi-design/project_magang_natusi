<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatMetodePembayaran extends Model
{
    use HasFactory;

    protected $table = 'riwayat_metode_pembayaran';
    protected $primaryKey = 'id_riwayat';

    protected $fillable = [
        'user_id',
        'aksi',
        'entitas',
        'deskripsi',
        'data_lama',
        'data_baru',
    ];

    protected function casts(): array
    {
        return [
            'data_lama' => 'array',
            'data_baru' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id_user'
        );
    }
}
