<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlamatToko extends Model
{
    use HasFactory;

    // Nama tabel, opsional sesuai kovensi Laravel
    protected $table = 'alamat_tokos';

    // Kolom yang dapat diisi (mass assignable)
    protected $fillable = [
        'pengguna_id',
        'alamat_lengkap',
        'kota',
        'id_kota',
        'provinsi',
        'id_provinsi',
        'kode_pos',
        'is_utama',
    ];

    // Relasi dengan model pengguna
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class);
    }
}
