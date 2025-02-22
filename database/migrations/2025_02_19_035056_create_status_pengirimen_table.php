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
        Schema::create('status_pengiriman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanans')->cascadeOnDelete();
            $table->enum('status', ['Diproses', 'Dikirim', 'Sampai di Gudang', 'Dalam Pengantaran', 'Diterima', 'Gagal']);
            $table->text('catatan')->nullable();
            $table->dateTime('tanggal_dikirim')->nullable();
            $table->string('nomor_resi')->nullable();
            $table->string('ekspedisi')->nullable();
            $table->timestamp('waktu_update')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_pengiriman');
    }
};
