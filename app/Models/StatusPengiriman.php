<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusPengiriman extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'status_pengiriman';

    protected $fillable = [
        'pesanan_id',
        'status',
        'catatan',
        'tanggal_dikirim',
        'nomor_resi',
        'ekspedisi',
        'waktu_update',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }
}
