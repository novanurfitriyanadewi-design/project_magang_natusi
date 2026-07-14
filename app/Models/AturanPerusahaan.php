<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AturanPerusahaan extends Model
{
    use HasFactory;

    protected $table = 'aturan_perusahaan';
    protected $primaryKey = 'id_aturan';
    protected $fillable = [
        'nama',
        'deskripsi',
        'status',
    ];
}