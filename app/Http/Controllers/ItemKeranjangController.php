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
    public function getCartDetails()
    {
        $user = Auth::user();
        $keranjang = Keranjang::with(['ItemKeranjangs.produk'])
            ->where('pengguna_id', $user->id)
            ->first();

        if (!$keranjang) {
            return response()->json([
                'success' => true,
                'data' => [],
                'total' => 0,
            ]);
        }

        $itemKeranjangs = $keranjang->ItemKeranjangs->map(function ($item) {
            return [
                'id' => $item->id,
                'produk_id' => $item->produk_id,
                'jumlah' => $item->jumlah,
                'produk' => [
                    'id' => $item->produk->id,
                    'nama' => $item->produk->nama,
                    'harga' => $item->produk->harga,
                    'stok' => $item->produk->stok,
                    'image_url' => !empty($item->produk->images)
                        ? asset('storage/' . $item->produk->images[0])
                        : asset('storage/default.png'),
                ],
            ];
        });

        $total = $itemKeranjangs->sum(fn($item) => $item['produk']['harga'] * $item['jumlah']);

        return response()->json([
            'success' => true,
            'data' => $itemKeranjangs,
            'total' => $total,
        ]);
    }

}
