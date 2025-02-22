<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('penggunas')->cascadeOnDelete();
            $table->foreignId('alamat_id')->constrained('alamat_tokos')->cascadeOnDelete();
            $table->decimal('total_harga', 10, 0);
            $table->decimal('ongkos_kirim', 10, 0)->nullable();
            $table->enum('status', ['Menunggu Validasi Admin', 'Menunggu Pembayaran', 'Kadaluarsa', 'Berhasil', 'Gagal'])->default('Menunggu Validasi Admin');
            $table->string('bukti_transfer')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};
