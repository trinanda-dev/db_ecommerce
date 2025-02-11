<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvitationCodeController;
use App\Http\Controllers\KategoriController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route yang akan digunakan untuk memvalidasi kode undangan
Route::post('validate-invitation-code', [InvitationCodeController::class, 'validateInvitationCode']);

// Route yang digunakan untuk melakukan sign-up
Route::post('sign-up', [AuthController::class, 'signUp']);

// Route yang digunakan untuk melakukan login
Route::post('login', [AuthController::class, 'login']);

// Route yang digunakan untuk melakukan logout
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
});

// Route yang digunakan untuk melakukan get terhadap kategori
Route::get('kategori', [KategoriController::class, 'index']);

