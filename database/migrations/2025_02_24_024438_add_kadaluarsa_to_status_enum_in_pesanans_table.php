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
        Schema::table('pesanans', function (Blueprint $table) {
            $table->enum('status', ['Menunggu Validasi Admin', 'Menunggu Pembayaran', 'Kadaluarsa', 'Berhasil', 'Gagal'])
                  ->default('Menunggu Validasi Admin')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->enum('status', ['Menunggu Validasi Admin', 'Menunggu Pembayaran', 'Berhasil', 'Gagal'])
                  ->default('Menunggu Validasi Admin')
                  ->change();
        });
    }
};
