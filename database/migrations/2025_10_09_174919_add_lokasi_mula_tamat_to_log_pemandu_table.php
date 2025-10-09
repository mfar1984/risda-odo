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
        Schema::table('log_pemandu', function (Blueprint $table) {
            $table->string('lokasi_mula_perjalanan')->nullable()->after('lokasi_checkout_long');
            $table->string('lokasi_tamat_perjalanan')->nullable()->after('lokasi_mula_perjalanan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_pemandu', function (Blueprint $table) {
            $table->dropColumn(['lokasi_mula_perjalanan', 'lokasi_tamat_perjalanan']);
        });
    }
};
