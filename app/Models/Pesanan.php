<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    // Nama tabel, opsional sesuai kovensi Laravel
    protected $table = 'pesanans';

    protected $fillable = [
        'pengguna_id',
        'alamat_id',
        'total_harga',
        'ongkos_kirim',
        'grand_total',
        'status',
        'catatan',
        'bukti_transfer',
    ];

    // Casting untuk tipe data kolom
    protected $casts = [
        'total_harga' => 'decimal:0',
        'ongkos_kirim' => 'decimal:0',
    ];


    // Relasi dengan model pengguna
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class);
    }

    // Relasi dengan model alamat toko
    public function alamat()
    {
        return $this->belongsTo(AlamatToko::class);
    }

    // Relasi dengan model status pengiriman
    public function statusPengiriman()
    {
        return $this->hasOne(StatusPengiriman::class);
    }

    // Accessor menghitung grand total
    public function getGrandTotalAttribute()
    {
        return $this->total_harga + ($this->ongkos_kirim ?? 0);
    }

    // Relasi ke model pesanan item
    public function items()
    {
        return $this->hasMany(PesananItem::class);
    }
}
