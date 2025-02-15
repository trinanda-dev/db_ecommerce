<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    /**
     * Method yang digunakan untuk memanggil semua produk
     */
    public function index()
    {
        $produks = Produk::with(['kategori:id,nama', 'brand:id,nama',])
            ->active()
            ->available()
            ->get();

        $produks->map(function ($produk) {
            // Ubah semua path images menjadi URL penuh
            if (!empty($produk->images)) {
                $produk->images = array_map(function ($image) {
                    return asset('storage/' . $image);
                }, $produk->images);
            } else {
                $produk->images = [asset('storage/default.png')]; // Jika tidak ada gambar
            }

            // Tambahkan image_url dari gambar pertama
            $produk->image_url = $produk->images[0];

            // Set is_popular berdasarkan jumlah terjual
            $produk->is_popular = $produk->terjual >= 50;

            return $produk;
        });

        return response()->json([
            'success' => true,
            'data' => $produks,
        ]);
    }

    /**
     * Method yang digunakan untuk memangggil produk berdasarkan filter brand
     */
    public function getProductByBrand($brandId)
    {
        $produks = Produk::with(['kategori:id,nama', 'brand:id,nama'])
            ->where('brand_id', $brandId)
            ->active()
            ->available()
            ->get();



        $produks->map(function ($produk) {
            // Ubah semua path images menjadi url penuh
            if (!empty($produk->images)) {
                $produk->images = array_map(function ($image) {
                    return asset('storage/' . $image);
                }, $produk->images);
            } else {
                $produk->images = [asset('storage/default.png')]; // Jika tidak ada gambar)]
            }

            // Tambahkan image_url dari gambar pertama
            $produk->image_url = $produk->images[0];

            return $produk;
        });

        return response()->json([
            'success' => true,
            'data' => $produks,
        ]);
    }

    /**
     * Method yang digunakan untuk memanggil produk berdasarkan filter kategori
     */
    public function getProductByCategory($kategoriId)
    {
        $produks = Produk::with(['kategori:id,nama', 'brand:id,nama'])
            ->where('kategori_id', $kategoriId)
            ->active()
            ->available()
            ->get();

        $produks->map(function ($produk) {
            // Ubah semua path images menjadi url penuh
            if(!empty($produk->images)) {
                $produk->images = array_map(function ($image) {
                    return asset('storage/' . $image);
                }, $produk->images);
            } else{
                $produk->images = [asset('storage/default.png')];
            }

            // Tambahkan image_url dari gambar pertama
            $produk->image_url = $produk->images[0];

            // Memperbaharui gambar untuk setiap varint
            $produk->image_url = $produk->images[0];

            return $produk;
        });

        return response()->json([
            'success' => true,
            'data' => $produks,
        ]);
    }

    /**
     * Method yang digunakan untuk mengembalikan jumlah stok dari produk setelah di tambah ke keranjang
     */
    public function getStock($id)
    {
        $produk = Produk::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $produk->stok,
        ]);
    }
}
