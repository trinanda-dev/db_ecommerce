<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    // Kolom yang dapat diisi
    protected $fillable = [
        'nama',
        'slug',
        'image',
        'is_active',
    ];

    // Casting untuk kolom is active
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Scope untuk data aktif
    public function scopeActive($query) {
        $query->where('is_active', true);
        return $query;
    }

    // Relasi ke tabel produk
    public function produks()
    {
        return $this->hasMany(Produk::class);
    }

    // Asessor untuk gambar di dalam brands
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : asset('storage/default.png');
    }
}
