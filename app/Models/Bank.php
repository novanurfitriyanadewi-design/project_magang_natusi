<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $table = 'bank';
    protected $primaryKey = 'id_bank';

    protected $fillable = [
        'nama_bank',
        'nama_pemilik',
        'no_rekening',
    ];

    public function pembayaran()
    {
        return $this->hasMany(
            Pembayaran::class,
            'id_bank',
            'id_bank'
        );
    }
}