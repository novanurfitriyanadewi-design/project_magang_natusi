<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplateLaporan extends Model
{
    use HasFactory;

    protected $table = 'template_laporan';
    protected $primaryKey = 'id_template_laporan';

    protected $fillable = [
        'user_id',
        'instansi',
        'judul',
        'file_word',
        'ketentuan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function penugasanPeserta(): HasMany
    {
        return $this->hasMany(
            PenugasanPeserta::class,
            'template_laporan_id',
            'id_template_laporan'
        );
    }
}
