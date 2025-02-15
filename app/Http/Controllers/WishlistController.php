<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Menambahkan produk ke wishlist
     */
    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
        ]);

        // Cek aoakah produk sudah ada di wishlist pengguna
        $wishlist = Wishlist::where('pengguna_id', Auth::id())
            ->where('produk_id', $request->produk_id)
            ->first();

        if ($wishlist) {
            return response()->json([
                'success' => false,
                'message' => 'Produk sudah ada di wishlist',
            ], 400);
        }

        // Tambahkan produk ke wishlist
        $wishlist = Wishlist::create([
            'pengguna_id' => Auth::id(),
            'produk_id' => $request->produk_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke wishlist',
        ]);
    }

    /**
     * Menghapus produk dari wishlist
     */
    public function destroy(Request $request)
    {
        $wishlist = Wishlist::where('pengguna_id', Auth::id())
            ->where('produk_id', $request->produk_id)
            ->first();

        if (!$wishlist) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan di wishlist',
            ], 404);
        }

        $wishlist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus dari wishlist',
        ]);
    }

    /**
     * Mendapatkan daftar wishlist pengguna
     */
    public function index()
    {
        $wishlist = Wishlist::with('produk')->where('pengguna_id', Auth::id())->get();

        $wishlist->map(function ($wishlist) {
            if (!empty($wishlist->produk->images)) {
                $wishlist->produk->images = array_map(function ($image) {
                    return asset('storage/' . $image);
                }, $wishlist->produk->images);
            } else {
                $wishlist->produk->images = [asset('storage/default.png')];
            }

            $wishlist->produk->image_url = $wishlist->produk->images[0];
        });

        return response()->json([
            'success' => true,
            'data' => $wishlist,
        ]);
    }
}
