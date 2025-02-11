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
        Schema::create('penggunas', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50);
            $table->string('email', 50)->unique();
            $table->string('password', 255);
            $table->string('image')->nullable();
            $table->string('nomor_hp', 15)->nullable();
            $table->string('nama_toko', 100)->nullable();
            $table->dateTime('tanggal_bergabung')->default(now());
            $table->dateTime('terakhir_login')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penggunas');
    }
};
