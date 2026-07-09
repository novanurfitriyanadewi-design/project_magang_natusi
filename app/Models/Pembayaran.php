<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';
    protected $primaryKey = 'id_pembayaran';
    protected $fillable = [
        'id_bank',
        'nominal_id',
        'peserta_id',
        'nominal',
        'bukti_transfer',
        'tgl_bayar',
        'status',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tgl_bayar' => 'datetime',
            'nominal' => 'integer',
        ];
    }
    public function bank(): BelongsTo
    {
        return $this->belongsTo(
            Bank::class,
            'id_bank',
            'id_bank'
        );
    }
    public function nominalPembayaran(): BelongsTo
    {
        return $this->belongsTo(
            NominalPembayaran::class,
            'nominal_id',
            'id_nominal'
        );
    }
    public function peserta(): BelongsTo
    {
        return $this->belongsTo(
            PesertaMagang::class,
            'peserta_id',
            'id_peserta'
        );
    }
}