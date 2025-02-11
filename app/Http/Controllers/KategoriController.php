<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::where('is_active', true)->get();

        $kategoris->map(function ($kategori) {
            if ($kategori->image) {
                $kategori->image = asset('storage/' . $kategori->image);
            }
            return $kategori;
        });

        return response()->json([
            'success' => true,
            'data' => $kategoris,
        ]);
    }
}
