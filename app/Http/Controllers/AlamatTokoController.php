<?php

namespace App\Http\Controllers;

use App\Models\AlamatToko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlamatTokoController extends Controller
{
    // Mendapatkan daftar alamat toko
    public function index()
    {
        $alamatToko = AlamatToko::with([
            'pengguna' => function ($query) {
                $query->select('id', 'nama', 'nomor_hp');
            }
        ])
            -> where('pengguna_id', Auth::id())
            -> get();
        return response()->json([
            'success' => true,
            'data' => $alamatToko,
        ]);
    }

    // Menambahkan alamat toko baru
    public function store(Request $request)
    {
        $request->validate([
            'alamat_lengkap' => 'required|string',
            'kota' => 'required|string',
            'id_kota' => 'required|integer',
            'provinsi' => 'required|string',
            'id_provinsi' => 'required|integer',
            'kode_pos' => 'required|string',
            'is_utama' => 'required|boolean',
        ]);

        // Jika alamat di atur sebagai alamat utama, nonaktifkan alamat utama sebelumnya
        if ($request->is_utama) {
            AlamatToko::where('pengguna_id', Auth::id())->update(['is_utama' => false]);
        }

         $alamatToko = AlamatToko::create([
            'pengguna_id' => Auth::id(),
            'alamat_lengkap' => $request->alamat_lengkap,
            'kota' => $request->kota,
            'id_kota' => $request->id_kota,
            'provinsi' => $request->provinsi,
            'id_provinsi' => $request->id_provinsi,
            'kode_pos' => $request->kode_pos,
            'is_utama' => $request->is_utama,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alamat toko berhasil ditambahkan',
            'data' => $alamatToko,
        ]);
    }

    // Menghapus alamat toko
    public function destroy($id)
    {
        $alamatToko = AlamatToko::findOrFail($id)
            -> where('pengguna_id', Auth::id())
            -> firstOrFail();

        $alamatToko->delete();

        return response()->json([
            'success' => true,
            'message' => 'Alamat toko berhasil dihapus',
        ]);
    }

    // Mengatur alamat toko sebagai alamat utama
    public function update($id)
    {
        // Nonaktifkan semua alamat utama sebelumnya
        AlamatToko::where('pengguna_id', Auth::id())->update(['is_utama' => false]);

        // Set alamat yang dipilih sebagai alamat utama
        $alamatToko = AlamatToko::where('id', $id)
            ->where('pengguna_id', Auth::id())
            ->firstOrFail();

        $alamatToko->update([
            'is_utama' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alamat toko berhasil diatur sebagai utama',
            'data' => $alamatToko,
        ]);
    }

}
