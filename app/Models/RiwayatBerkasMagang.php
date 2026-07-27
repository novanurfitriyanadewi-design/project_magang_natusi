<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatBerkasMagang extends Model
{
    protected $table = 'riwayat_berkas_magang';

    protected $fillable = ['permintaan_id', 'jenis_berkas', 'path', 'versi'];

    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(PermintaanMagang::class, 'permintaan_id', 'id_permintaan');
    }
}
