<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Method yang digunakan untuk menampilkan brand
     */
    public function index() {
        $brands = Brand::where('is_active', true)
        ->withCount('produks') // Menghitung jumlah produk yang terkait dengan brand
        ->get();

        $brands->map(function ($brand) {
            if ($brand->image) {
                $brand->image = asset('storage/' . $brand->image);
            }
            return $brand;
        });

        return response()->json([
            'success' => true,
            'data' => $brands,
        ]);
    }
}
