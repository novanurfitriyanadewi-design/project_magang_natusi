<?php

namespace App\Models;

use App\Services\EmailNotificationService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';
    protected $primaryKey = 'id_notifikasi';

    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'kategori',
        'tipe',
        'referensi_id',
        'dibaca',
    ];

    protected $casts = [
        'dibaca' => 'boolean',
    ];


    protected static function booted(): void
    {
        static::created(function (Notifikasi $notifikasi): void {
            DB::afterCommit(function () use ($notifikasi): void {
                app(EmailNotificationService::class)->send($notifikasi);
            });
        });
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id_user'
        );
    }
}