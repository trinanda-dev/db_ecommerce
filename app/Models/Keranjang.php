<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    use HasFactory;

    // Kolom yang dapat diisi
    protected $fillable = [
        'pengguna_id',
        'jumlah',
    ];

    // Relasi ke model pengguna
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class);
    }

    // Relasi ke model item keranjang
    public function itemKeranjangs()
    {
        return $this->hasMany(ItemKeranjang::class);
    }
}
