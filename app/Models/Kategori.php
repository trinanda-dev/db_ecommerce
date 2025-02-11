<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'kategoris';

    // Kolom yang dapat diisi (mass assignable)

    protected $fillable = [
        'nama',
        'slug',
        'image',
        'is_active',
    ];

    // Casting untuk tipe data kolom
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Scope untuk data aktif
    public function scopeActive($query) {
        $query->where('is_active', true);
        return $query;
    }
}
