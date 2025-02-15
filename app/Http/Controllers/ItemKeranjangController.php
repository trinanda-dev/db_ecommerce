<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemKeranjangController extends Controller
{
    /**
     * Method yang digunakan untuk mendapatkan detail keranjnag
     */
    public function getCartDetails(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu untuk melihat keranjang',
            ], 401);
        }

        // Ambil keranjang pengguna dengan relasi item keranjang dan produk (tanpa varian)
        $keranjang = Keranjang::with(['ItemKeranjangs.produk' => function ($query) {
            $query->select('id', 'nama', 'harga', 'stok', 'images');
        }])->where('pengguna_id', $user->id)->first();

        if (!$keranjang) {
            return response()->json([
                'success' => true,
                'data' => [],
                'total' => 0,
            ]);
        }

        // Filter item berdasarkan item_ids jika diberikan
        $selectedItems = $request->has('item_ids') ? $request->item_ids : [];

        $itemKeranjangs = $keranjang->ItemKeranjangs->filter(function ($item) use ($selectedItems) {
            return empty($selectedItems) || in_array($item->id, $selectedItems);
        })->map(function ($item) {
            // Ambil stok produk
            $stok = $item->produk->stok;

            // Format data produk dan tambahkan image_url
            return [
                'id' => $item->id,
                'produk_id' => $item->produk_id,
                'jumlah' => $item->jumlah,
                'produk' => [
                    'id' => $item->produk->id,
                    'nama' => $item->produk->nama,
                    'harga' => $item->produk->harga,
                    'stok' => $stok,
                    'image_url' => !empty($item->produk->images)
                        ? asset('storage/' . $item->produk->images[0])
                        : asset('storage/default.png'),
                ],
            ];
        });

        // Hitung total harga dari semua item yang dipilih (atau semua item jika tidak ada filter)
        $total = $itemKeranjangs->sum(function ($item) {
            return $item['produk']['harga'] * $item['jumlah'];
        });

        return response()->json([
            'success' => true,
            'data' => $itemKeranjangs->values(), // Reset indeks menjadi array numerik
            'total' => $total,
        ]);
    }

}
