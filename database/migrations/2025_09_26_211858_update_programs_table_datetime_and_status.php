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
        Schema::table('programs', function (Blueprint $table) {
            // Change date columns to datetime
            $table->datetime('tarikh_mula')->change();
            $table->datetime('tarikh_selesai')->change();

            // Update status enum to include new statuses
            $table->enum('status', ['draf', 'lulus', 'tolak', 'aktif', 'tertunda', 'selesai'])->default('draf')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            // Revert datetime columns back to date
            $table->date('tarikh_mula')->change();
            $table->date('tarikh_selesai')->change();

            // Revert status enum to original
            $table->enum('status', ['aktif', 'tertunda', 'selesai'])->default('aktif')->change();
        });
    }
};
