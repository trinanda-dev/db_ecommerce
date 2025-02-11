<?php

namespace App\Http\Controllers;

use App\Models\InvitationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvitationCodeController extends Controller
{
    /**
     * Method yang digunakan untuk memriksa validitas kode undangan
     */
    public function validateInvitationCode(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'kode_undangan' => 'required|exists:invitation_codes,code',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode undangan tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Periksa apakah kode undangan aktif dan belum digunakan
        $invitationCode = InvitationCode::where('code', $request->kode_undangan)->first();

        if (!$invitationCode || !$invitationCode->is_active || $invitationCode->is_used) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode undangan tidak valid atau sudah digunakan.',
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Kode undangan valid.',
        ]);
    }
}
