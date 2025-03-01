<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\Pesanan;
use App\Models\PesananItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PesananController extends Controller
{
    /**
     * Method yang digunakan agar user dapat membuat pesanan
     */
    public function checkout()
    {
        $user = Auth::user();

        // Ambil keranjang berdasarkan pengguna
        $keranjang = Keranjang::with('ItemKeranjangs.produk')
                    ->where('pengguna_id', $user->id)
                    ->first();

        if (!$keranjang || $keranjang->itemKeranjangs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang kosong, tidak dapat melakukan checkout',
            ], 400);
        }

        // Ambil alamat utama pengguna (jika ada)
        $alamatUtama = $user->alamatUtama;
        if (!$alamatUtama) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan atur alamat utama sebelum checkout.',
            ], 400);
        }

        // Hitung total harga dari semua item di keranjang
        $totalHarga = $keranjang->itemKeranjangs->sum(fn($item) => $item->produk->harga * $item->jumlah);

        // Buat pesanan baru
        $pesanan = Pesanan::create([
            'pengguna_id' => $user->id,
            'alamat_id' => $alamatUtama->id,
            'total_harga' => $totalHarga,
            'ongkos_kirim' => 0, // Akan divalidasi oleh admin
            'grand_total' => $totalHarga, // Karena ongkos kirim belum ditentukan
            'status' => 'Menunggu Validasi Admin',
        ]);

        // Pindahkan item dari keranjang ke pesanan_items
        foreach ($keranjang->itemKeranjangs as $item) {
            PesananItem::create([
                'pesanan_id' => $pesanan->id,
                'produk_id' => $item->produk_id,
                'jumlah' => $item->jumlah,
                'harga_saat_checkout' => $item->produk->harga,
            ]);
        }

        // Hapus semua item di keranjang setelah checkout
        $keranjang->itemKeranjangs()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibuat',
            'data' => $pesanan->load('items.produk'),
        ], 201);
    }


    /**
     * Method yang digunakan untuk mengambil data pesanan
     */
    public function show($id)
    {
        $pesanan = Pesanan::where('id', $id)
                ->where('pengguna_id', Auth::id())
                ->with(['alamat', 'items.produk']) // Tidak perlu nested query di with()
                ->first();

        if (!$pesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan atau bukan milik anda',
            ], 403);
        }

        // Ubah path relative menjadi URL lengkap
        foreach ($pesanan->items as $item) {
            if (!empty($item->produk->images)) {
                // Pastikan images adalah array (bukan string JSON)
                $images = is_array($item->produk->images) ? $item->produk->images : json_decode($item->produk->images, true);

                // Perbarui URL setiap gambar
                if (is_array($images)) {
                    foreach ($images as $index => $image) {
                        $images[$index] = asset('storage/' . $image);
                    }
                    $item->produk->images = $images;
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => $pesanan,
        ]);
    }


    /**
     * Method yang digunakan untuk mengunggah bukti transfer
     */
    public function uploadBuktiTransfer(Request $request, $id)
    {
        try {
            $request->validate([
                'bukti_transfer' => 'required|image|mimes:jpeg,png,jpg,webp|max:20048',
            ]);

            $pesanan = Pesanan::where('id', $id)
                              ->where('pengguna_id', Auth::id()) // Pastikan hanya pemilik yang bisa upload
                              ->first();

            if (!$pesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan atau bukan milik Anda',
                ], 403);
            }

            if ($request->hasFile('bukti_transfer')) {
                // Hapus bukti transfer lama jika ada
                if ($pesanan->bukti_transfer) {
                    Storage::disk('public')->delete($pesanan->bukti_transfer);
                }

                // Simpan bukti transfer baru
                $filePath = $request->file('bukti_transfer')->store('bukti_transfer', 'public');
                $pesanan->update(['bukti_transfer' => $filePath]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Bukti transfer berhasil diunggah',
                'data' => [
                    'id' => $pesanan->id,
                    'bukti_transfer_url' => asset('storage/' . $pesanan->bukti_transfer),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengunggah bukti transfer',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Method yang digunakan untuk mendapaykan daftar pesanan milik user yang sedang login
     */
    public function index()
    {
        $batasWaktu = now()->subHours(24);

        // Update status pesanan yang belum dibayar lebih dari 24 jam menjadi "Kadaluarsa"
        Pesanan::where('pengguna_id', Auth::id())
            ->where('status', 'Menunggu Pembayaran')
            ->where('created_at', '<', $batasWaktu)
            ->update(['status' => 'Kadaluarsa']);

        // Ambil hanya pesanan yang statusnya bukan "Kadaluarsa"
        $pesanan = Pesanan::where('pengguna_id', Auth::id())
            ->whereNotIn('status', ['Kadaluarsa'])
            ->with([
                'statusPengiriman' => function ($query) {
                    $query->select('id', 'pesanan_id', 'status', 'catatan', 'tanggal_dikirim', 'nomor_resi', 'ekspedisi');
                },
                'items.produk'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pesanan->map(function ($item) {
                return [
                    'id' => $item->id,
                    'pengguna_id' => $item->pengguna_id,
                    'alamat_id' => $item->alamat_id,
                    'total_harga' => $item->total_harga,
                    'ongkos_kirim' => $item->ongkos_kirim,
                    'grand_total' => $item->grand_total,
                    'status' => $item->status,
                    'catatan' => $item->catatan,
                    'bukti_transfer' => $item->bukti_transfer ? asset('storage/' . $item->bukti_transfer) : null,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                    'ekspedisi' => optional($item->statusPengiriman)->ekspedisi,
                    'no_resi' => optional($item->statusPengiriman)->nomor_resi,
                    'tanggal_dikirim' => optional($item->statusPengiriman)->tanggal_dikirim,
                    'status_pengiriman' => optional($item->statusPengiriman)->status,
                    'catatan_pengiriman' => optional($item->statusPengiriman)->catatan,
                    'produk' => $item->items->map(function ($pesananItem) {
                        return [
                            'produk_id' => $pesananItem->produk->id ?? null,
                            'nama_produk' => $pesananItem->produk->nama ?? null,
                            'harga_produk' => $pesananItem->harga_saat_checkout ?? null,
                            'jumlah' => $pesananItem->jumlah,
                            'images' => collect($pesananItem->produk->images)->map(function ($image) {
                                return asset('storage/' . $image);
                            }),
                        ];
                    }),
                ];
            }),
        ]);
    }
}
