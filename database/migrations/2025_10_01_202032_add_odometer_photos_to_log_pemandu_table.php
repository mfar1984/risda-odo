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
            // Add odometer photo columns after 'odometer_masuk'
            $table->string('foto_odometer_keluar')->nullable()->after('odometer_keluar')->comment('Photo for Start Journey odometer');
            $table->string('foto_odometer_masuk')->nullable()->after('odometer_masuk')->comment('Photo for End Journey odometer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_pemandu', function (Blueprint $table) {
            $table->dropColumn(['foto_odometer_keluar', 'foto_odometer_masuk']);
        });
    }
};
