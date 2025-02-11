<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Pengguna extends Authenticatable
{
    use HasApiTokens, HasFactory;

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
}
