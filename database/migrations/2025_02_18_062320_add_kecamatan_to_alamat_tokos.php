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
        Schema::table('alamat_tokos', function (Blueprint $table) {
            $table->string('kecamatan', 100)->after('kota')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alamat_tokos', function (Blueprint $table) {
            $table->dropColumn('kecamatan');
        });
    }
};
