<?php

namespace App\Http\Controllers;

use App\Models\AlamatToko;
use App\Models\InvitationCode;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Signup controller
     */
    public function signUp(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:50',
            'email' => 'required|email|max:50|unique:penggunas,email',
            'password' => 'required|string|min:8|confirmed',
            'nomor_hp' => 'nullable|string|max:15|unique:penggunas,nomor_hp',
            'kode_undangan' => 'required|exists:invitation_codes,code',
            'nama_toko' => 'nullable|string|max:100',
            'alamat_lengkap' => 'required|string|max:255',
            'id_kota' => 'nullable|string|max:10',
            'kota' => 'required|string|max:100',
            'id_provinsi' => 'nullable|string|max:10',
            'provinsi' => 'required|string|max:100',
            'kode_pos' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], 422);
        }

        // Periksa validitas kode undangan
        $invitationCode = InvitationCode::where('code', $request->kode_undangan)->first();

        if (!$invitationCode || !$invitationCode->is_active || $invitationCode->is_used) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode undangan tidak valid atau sudah digunakan',
            ], 403);
        }

        // Tandai kode undangan sebagai sudah digunakan
        $invitationCode->update([
            'is_used' => true,
        ]);

        // Buat pengguna baru
        $pengguna = Pengguna::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => $request->password,
            'nomor_hp' => $request->nomor_hp,
            'nama_toko' => $request->nama_toko,
        ]);

        // Buat alamat toko baru
        AlamatToko::create([
            'pengguna_id' => $pengguna->id,
            'alamat_lengkap' => $request->alamat_lengkap,
            'id_kota' => $request->id_kota,
            'kota' => $request->kota,
            'id_provinsi' => $request->id_provinsi,
            'provinsi' => $request->provinsi,
            'kode_pos' => $request->kode_pos,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Sign up berhasil.',
        ]);
    }

    /**
     * Login controller
     */
    public function login(Request $request)
    {
        // Validator input
        $validator = Validator::make($request->all(), [
            'email_or_phone' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan validasi.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Cek login berdasarkan emaila atau nomor hp
        $pengguna = Pengguna::where(function($query) use ($request){
            $query->where('email', $request->email_or_phone)
                ->orWhere('nomor_hp', $request->email_or_phone);
        })->first();

        if (!$pengguna || !Hash::check($request->password, $pengguna->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email atau kata sandi salah.',
            ], 402);
        }

        // Pastikan pengguna aktif
        if (!$pengguna->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akun pengguna tidak aktif.',
            ], 403);
        }

        // Buat token untuk pengguna
        $token = $pengguna->createToken('authToken')->plainTextToken;

        // Perbarui waktu terakhir login
        $pengguna->update(['terakhir_login' => now()]);

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil.',
            'token' => $token,
        ]);
    }

    /**
     * Logout controller
     */
    public function logout(Request $request)
    {
        // Revoke token yang sedang digunakan
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil.',
        ]);
    }
}
