<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';
    protected $fillable = [
        'nama',
        'email',
        'username',
        'password',
        'role',
        'university',
        'student_id',
        'major',
        'phone',
        'description',
        'wajib_ganti_password',
        'api_token',
        'foto_profil',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
        'foto_profil',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'wajib_ganti_password' => 'boolean',
        ];
    }

    public function isSuperadmin(): bool
    {
        return $this->role === 'superadmin';
    }
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    public function isPeserta(): bool
    {
        return $this->role === 'peserta';
    }

    public function permintaanMagang(): HasMany
    {
        return $this->hasMany(
            PermintaanMagang::class,
            'user_id',
            'id_user'
        );
    }
    public function pesertaMagang(): HasOne
    {
        return $this->hasOne(
            PesertaMagang::class,
            'user_id',
            'id_user'
        );
    }
    public function tugas(): HasMany
    {
        return $this->hasMany(
            Tugas::class,
            'user_id',
            'id_user'
        );
    }
    public function notifikasi(): HasMany
    {
        return $this->hasMany(
            Notifikasi::class,
            'user_id',
            'id_user'
        );
    }
}