<?php

namespace App\Http\Controllers;

use App\Models\StatusPengiriman;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatusPengirimanController extends Controller
{
    /**
     * Method untuk melihat status pengiriman hanya untuk pemilik pesanan.
     */
    public function getStatus($pesanan_id)
    {
        // Ambil pengguna yang sedang login
        $user = Auth::user();

        // Jika tidak login, kembalikan error
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus login untuk dapat melihat status pengiriman',
            ], 401);
        }

        // Cek apakah pesanan benar-benar milik user yang login
        $pesanan = Pesanan::where('id', $pesanan_id)
                          ->where('pengguna_id', $user->id) // Hanya pesanan milik user ini
                          ->first();

        // Jika pesanan tidak ditemukan atau bukan milik user, beri respon error
        if (!$pesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan atau bukan milik Anda',
            ], 403);
        }

        // Ambil status pengiriman berdasarkan pesanan
        $statusPengiriman = StatusPengiriman::where('pesanan_id', $pesanan_id)->first();

        // Jika status pengiriman tidak ditemukan
        if (!$statusPengiriman) {
            return response()->json([
                'success' => false,
                'message' => 'Status pengiriman tidak ditemukan',
            ], 404);
        }

        // Kembalikan data status pengiriman
        return response()->json([
            'success' => true,
            'message' => 'Status pengiriman ditemukan',
            'data' => $statusPengiriman
        ]);
    }
}
