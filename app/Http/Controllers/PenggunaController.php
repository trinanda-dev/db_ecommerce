<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfilePenggunaResource;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class PenggunaController extends Controller
{
    /**
     * Menampilkan data pengguna yang sedang terauntetikasi
     */
    public function show()
    {
        // Ambil pengguna yang sedang terautentikasi
        $user = Auth::user();

        // Jika pengguna tidak ditemukan, kembalikan respon 404
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus login untuk dapat melihat profile',
            ], 404);
        }

        // Kembalikan data pengguna dalam bentuk JSON menggunakan PenggunaResource
        return new ProfilePenggunaResource($user);
    }

    /**
     * Update data pengguna yang sedang terautentikasi
     */
    public function update(Request $request)
    {
        // Validasi data input
        $request->validate([
            'nama' => 'sometimes|string|max:50',
            'nomor_hp' => 'sometimes|string|max:20',
            'nama_toko' => 'sometimes|string|max:100',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,webp|max:2048', // Perbaikan validasi
        ]);

        // Ambil pengguna yang sedang login
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu untuk memperbarui profile',
            ], 401);
        }

        $pengguna = Pengguna::findOrFail($user->id);

        // Cek apakah ada gambar yang diupload
        if ($request->hasFile('image')) {
            Log::info("Gambar diterima: " . $request->file('image')->getClientOriginalName());

            // Hapus gambar lama jika ada
            if ($pengguna->image && file_exists(storage_path('app/public/penggunas/' . $pengguna->image))) {
                unlink(storage_path('app/public/penggunas/' . $pengguna->image));
            }

            // Simpan gambar baru
            $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
            $request->image->storeAs('public/penggunas', $imageName);

            // Update database
            $pengguna->image = $imageName;
        } else {
            Log::warning("Tidak ada gambar yang diterima dalam request.");
        }

        // Update data pengguna
        $pengguna->update($request->only([
            'nama',
            'nomor_hp',
            'nama_toko',
        ]));

        // Kirim response
        return response()->json([
            'success' => true,
            'data' => new ProfilePenggunaResource($pengguna),
        ]);
    }

}
