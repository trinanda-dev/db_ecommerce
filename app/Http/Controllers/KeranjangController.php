<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KeranjangController extends Controller
{
    /**
     * Method yang digunakan untuk menambahkan keranjang
     */
    public function addToCart(Request $request)
    {
        // Validasi input tanpa variant_id
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'jumlah' => 'required|numeric|min:1',
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu untuk menambahkan ke keranjang',
            ], 401);
        }

        // Periksa apakah keranjang pengguna sudah ada
        $keranjang = Keranjang::firstOrCreate(['pengguna_id' => $user->id]);

        // Ambil produk
        $produk = Produk::findOrFail($request->produk_id);

        // Validasi stok produk
        if ($produk->stok < $request->jumlah) {
            return response()->json([
                'success' => false,
                'message' => 'Stok produk tidak mencukupi',
            ], 400);
        }

        // Kurangi stok produk
        $produk->decrement('stok', $request->jumlah);

        // Periksa apakah produk sudah ada di item keranjang
        $itemKeranjang = $keranjang->ItemKeranjangs()
            ->where('produk_id', $request->produk_id)
            ->first();

        if ($itemKeranjang) {
            // Jika item sudah ada, tambahkan jumlahnya
            $itemKeranjang->jumlah += $request->jumlah;
            $itemKeranjang->save();
        } else {
            // Jika belum ada, tambahkan item baru ke keranjang
            $keranjang->ItemKeranjangs()->create([
                'produk_id' => $request->produk_id,
                'jumlah' => $request->jumlah
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang',
        ]);
    }

    /**
     * Method yang digunakan untuk menampilkan item di keranjang pengguna
     */
    public function getCartItems()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu untuk melihat keranjang'
            ], 401);
        }

        $cartItems = Keranjang::with(['ItemKeranjangs.produk'])
            ->where('pengguna_id', $user->id)
            ->first();

        return response()->json([
            'success' => true,
            'data' => $cartItems ? $cartItems->ItemKeranjangs : [],
        ]);
    }

    /**
     * Method yang digunakan untuk menghapus item dari keranjang
     */
    public function removeFromCart(Request $request, $cartItemId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus login untuk menghapus item dari keranjang'
            ], 401);
        }

        // Validasi input hanya untuk produk_id saja
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
        ]);

        // Ambil keranjang berdasarkan pengguna
        $keranjang = Keranjang::where('pengguna_id', $user->id)->first();

        if (!$keranjang) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang tidak ditemukan'
            ], 404);
        }

        // Ambil item keranjang berdasarkan cartItemId dan produk_id
        $itemKeranjang = $keranjang->itemKeranjangs()
            ->where('id', $cartItemId)
            ->where('produk_id', $request->produk_id)
            ->first();

        if (!$itemKeranjang) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan di dalam keranjang'
            ], 404);
        }

        // Gunakan transaksi untuk memastikan konsistensi
        DB::beginTransaction();

        try {
            // Tambahkan kembali stok produk
            $produk = Produk::find($itemKeranjang->produk_id);
            if ($produk) {
                $produk->increment('stok', $itemKeranjang->jumlah);
            } else {
                throw new \Exception('Produk tidak ditemukan');
            }

            // Hapus item keranjang
            $itemKeranjang->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus dari keranjang',
                'data' => [
                    'cart_item_id' => $cartItemId,
                    'produk_id' => $request->produk_id,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus item dari keranjang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Method yang digunakan untuk mengupdate keranjang
     */
    public function updateCartItem(Request $request, $cartId)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:1',
            'produk_id' => 'required|exists:produks,id',
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus login untuk memperbarui keranjang',
            ], 401);
        }

        $keranjang = Keranjang::where('pengguna_id', $user->id)->first();

        if (!$keranjang) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang tidak ditemukan',
            ], 404);
        }

        $itemKeranjang = $keranjang->ItemKeranjangs()
            ->where('id', $cartId)
            ->where('produk_id', $request->produk_id)
            ->first();

        if (!$itemKeranjang) {
            return response()->json([
                'success' => false,
                'message' => 'Item keranjang tidak ditemukan',
            ], 404);
        }

        // Hitung selisih jumlah
        $selisihJumlah = $request->jumlah - $itemKeranjang->jumlah;

        // Validasi stok sebelum pembaruan
        $produk = Produk::findOrFail($request->produk_id);

        if ($selisihJumlah > 0 && $produk->stok < $selisihJumlah) {
            return response()->json([
                'success' => false,
                'message' => 'Stok produk tidak mencukupi',
            ], 400);
        }

        // Kurangi atau tambahkan stok produk berdasarkan selisih jumlah
        $produk->stok -= $selisihJumlah;
        $produk->save();

        // Perbarui jumlah item keranjang
        $itemKeranjang->jumlah = $request->jumlah;
        $itemKeranjang->save();

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil diperbarui',
            'data' => [
                'id' => $itemKeranjang->id,
                'produk_id' => $itemKeranjang->produk_id,
                'jumlah' => $itemKeranjang->jumlah,
            ],
        ]);
    }
}
