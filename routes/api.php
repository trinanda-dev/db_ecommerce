<?php

use App\Http\Controllers\AlamatTokoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\DiscountBannerController;
use App\Http\Controllers\InvitationCodeController;
use App\Http\Controllers\ItemKeranjangController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\WishlistController;
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

// Route yang akan digunakan untuk melakukan get terhadap brand
Route::get('brand', [BrandController::class, 'index']);

// Route yang digunakan untuk melakukan get terhadap produk
Route::get('produk', [ProdukController::class, 'index']);

// Route yang digunakan untuk berinteraksi dengan wishlist
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/wishlist', [WishlistController::class, 'store']);
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy']);
});

// Route yang digunakan untuk menampilkan produk berdasarkan brand
Route::get('/produk/brand/{id}', [ProdukController::class, 'getProductByBrand']);

// Route yang digunakan untuk menampilkan produk berdasarkan kategori
Route::get('/produk/kategori/{id}', [ProdukController::class, 'getProductByCategory']);

// Route yang digunakan untuk beriteraksi dengan keranjang
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/keranjang/add', [KeranjangController::class, 'addToCart']);
    Route::get('/keranjang', [KeranjangController::class, 'getCartItems']);
    Route::delete('/keranjang/{cartItemId}', [KeranjangController::class, 'removeFromCart']);
    Route::put('/keranjang/{cartItemId}', [KeranjangController::class, 'updateCartItem']);
});

// Route yang digunakan untuk untuk memanggil endpoit cart detail
Route::middleware('auth:sanctum')->get('/detail-keranjang', [ItemKeranjangController::class, 'getCartDetails']);

// Route yang digunakan untuk berinteraksi dengan alamat toko
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/alamat-toko', [AlamatTokoController::class, 'index']);
    Route::post('/alamat-toko', [AlamatTokoController::class, 'store']);
    Route::delete('/alamat-toko/{id}', [AlamatTokoController::class, 'destroy']);
    Route::put('/alamat-toko/{id}', [AlamatTokoController::class, 'update']);
});

// Route yang digunakan untuk menampilkan data pengguna
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [PenggunaController::class, 'show']);
    Route::put('/profile', [PenggunaController::class, 'update']);
});

// Route yang digunakan untuk berinteraksi dengan discount banner
Route::get('/discount-banner', [DiscountBannerController::class, 'index']);
