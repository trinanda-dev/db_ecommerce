<?php

namespace App\Models;

use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\Passwords\CanResetPassword as ResetPasswordTrait;

class Pengguna extends Authenticatable implements MustVerifyEmail, CanResetPassword
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable, ResetPasswordTrait;

    // Nama tabel, opsional sesuai kovensi Laravel
    protected $table = 'penggunas';

    // Kolom yang bisa diisi (mass assignable)
    protected $fillable = [
        'nama',
        'email',
        'password',
        'image',
        'nomor_hp',
        'nama_toko',
        'tanggal_bergabung',
        'terakhir_login',
        'is_active',
    ];

    // Casting untuk tipe data kolom
    protected $casts = [
        'tanggal_bergabung' => 'datetime',
        'terakhir_login' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Scope untuk data aktif
    public function scopeActive($query) {
        $query->where('is_active', true);
        return $query;
    }

    // Mutator untuk hashing password
    public function setPasswordAttribute($value) {
        $this->attributes['password'] = bcrypt($value);
    }

    // Relasi tambahan dengan alamat toko
    public function toko()
    {
        return $this->hasOne(AlamatToko::class, 'pengguna_id');
    }

    // Relasi dengan model pesanan
    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }

    // Relasi dengan alamat toko
    public function alamatTokos()
    {
        return $this->hasMany(AlamatToko::class);
    }

    public function getAlamatUtamaAttribute()
    {
        return $this->alamatTokos()->where('is_utama', true)->first();
    }

}
