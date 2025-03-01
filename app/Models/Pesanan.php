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

    public function getStatusAttribute($value)
    {
        return ucfirst($value); // Ubah jadi huruf besar di awal (opsional)
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = strtolower($value); // Simpan dalam lowercase (opsional)
    }

    // Relasi ke model pesanan item
    public function items()
    {
        return $this->hasMany(PesananItem::class);
    }

    // Method yang digunakan untuk melakukan perubahan status pengiriman
    protected static function boot()
    {
        parent::boot();

        static::updated(function ($pesanan) {
            // Jika status berubah menjadi "Berhasil", ubah status pengiriman menjadi "Diproses"
            if ($pesanan->isDirty('status') && $pesanan->status === 'Berhasil') {
                // Pastikan ada status pengiriman, jika tidak buat baru
                $pesanan->statusPengiriman()->updateOrCreate(
                    ['pesanan_id' => $pesanan->id],
                    ['status' => 'Diproses']
                );
            }
        });
    }
}
