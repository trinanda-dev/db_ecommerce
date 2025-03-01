<?php

namespace App\Http\Controllers\Auth;

use App\Mail\ResetPasswordMail;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class ResetPasswordController
{
    /**
     * Menghandle request pengiriman email reset password
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:penggunas,email']);

        $user = Pengguna::where('email', $request->email)->first();

        // Buat token reset password
        $token = Password::createToken($user);

        // Simpan token di database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => $token, 'created_at' => now()]
        );

        // Kirim email dengan token
        $resetUrl = "$token";

        Mail::to($user->email)->send(new ResetPasswordMail([
            'name' => $user->nama,
            'reset_url' => $resetUrl
        ]));

        return response()->json([
            'message' => 'Link reset password telah dikirim.',
            'token' => $token, // Kirim token untuk digunakan di aplikasi Android
        ]);
    }

    /**
     * Method yang digunakan apakah token rest password valid
     */
    public function verifyResetToken(Request $request)
    {
        $request->validate(['email' => 'required|email', 'token' => 'required']);

        $exists = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->exists();

        return $exists
            ? response()->json(['message' => 'Token valid.'])
            : response()->json(['message' => 'Token tidak valid atau sudah kedaluwarsa.'], 400);
    }

    /**
     * Menghandle proses reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        // Cek apakah token valid
        $resetRequest = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetRequest) {
            return response()->json(['message' => 'Token tidak valid atau sudah kedaluwarsa.'], 400);
        }

        // Update password pengguna
        $user = Pengguna::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Pengguna tidak ditemukan.'], 404);
        }

        // **Bypass Mutator**
        $user->setRawAttributes(['password' => Hash::make($request->password)]);
        $user->save();

        // Hapus token setelah digunakan
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password berhasil diperbarui.']);
    }
}
