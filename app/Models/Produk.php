<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    // Nama tabel (opsionnal)
    protected $table = 'produks';

    // Kolom yang dapat diisi (mass assignable
    protected $fillable = [
        'kategori_id',
        'brand_id',
        'nama',
        'slug',
        'images',
        'deskripsi',
        'harga',
        'terjual',
        'stok',
        'rating',
        'berat',
        'is_available',
        'is_active',
    ];

    // Casting untuk tipe data kolom
    protected $casts = [
        'images' => 'array',
        'harga' => 'decimal:0',
        'rating' => 'decimal:1',
        'berat' => 'decimal:1',
        'is_available' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Tambahkan scope active
    public function scopeActive($query) {
        $query->where('is_active', true);
        return $query;
    }

    // Tambahkan scope available
    public function scopeAvailable($query) {
        $query->where('is_available', true);
        return $query;
    }

    // Relasi dengan kategori
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    // Relasi dengan brand
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    // Method yang digunakan untuk megkategorikan produk menjadi popular
    public function updatePopularity()
    {
        $this->is_popular = $this->terjual >= 50; // Produk populer jika terjual >= 50
        $this->save();
    }



}
