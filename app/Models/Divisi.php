<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    protected $table = 'divisi';
    protected $primaryKey = 'id_divisi';

    protected $fillable = ['nama_divisi', 'keterangan'];

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class, 'divisi_id', 'id_divisi');
    }
}