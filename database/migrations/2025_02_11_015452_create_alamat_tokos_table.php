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
        Schema::create('alamat_tokos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('penggunas')->cascadeOnDelete();
            $table->string('alamat_lengkap', 255)->comment('Alamat lengkap toko');
            $table->string('kota', 100)->comment('Kota lokasi toko');
            $table->unsignedBigInteger('id_kota')->nullable();
            $table->string('provinsi', 100)->comment('Provinsi lokasi toko');
            $table->unsignedBigInteger('id_provinsi')->nullable();
            $table->string('kode_pos', 10)->comment('Kode pos lokasi toko');
            $table->boolean('is_utama')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alamat_tokos');
    }
};
