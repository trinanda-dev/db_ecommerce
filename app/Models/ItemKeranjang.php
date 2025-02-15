<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemKeranjang extends Model
{
    use HasFactory;
    // Kolom yang dapat diisi
    protected $fillable = [
        'keranjang_id',
        'produk_id',
        'jumlah',
    ];

    // Relasi ke model keranjang
    public function keranjang()
    {
        return $this->belongsTo(Keranjang::class);
    }

    // Relasi ke model produk
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
