<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NominalPembayaran extends Model
{
    use HasFactory;

    protected $table = 'nominal_pembayaran';
    protected $primaryKey = 'id_nominal';
    protected $fillable = [
        'jumlah_nominal',
    ];

    public function pembayaran()
    {
        return $this->hasMany(
            Pembayaran::class,
            'nominal_id',
            'id_nominal'
        );
    }
}