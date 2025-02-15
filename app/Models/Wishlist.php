<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    // Kolom yang dapat diiisi (mass assignable)
    protected $fillable = [
        'pengguna_id',
        'produk_id',
    ];

    // Relasi ke model pengguna
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class);
    }

    // Relasi ke model produk
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
