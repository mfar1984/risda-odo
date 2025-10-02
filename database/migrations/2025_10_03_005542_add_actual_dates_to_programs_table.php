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
            $table->dateTime('tarikh_kelulusan')->nullable()->after('status')->comment('Actual approval date/time');
            $table->dateTime('tarikh_mula_aktif')->nullable()->after('tarikh_kelulusan')->comment('When program became active (first journey)');
            $table->dateTime('tarikh_sebenar_selesai')->nullable()->after('tarikh_mula_aktif')->comment('Actual completion date/time (last end journey or auto-close)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['tarikh_kelulusan', 'tarikh_mula_aktif', 'tarikh_sebenar_selesai']);
        });
    }
};
