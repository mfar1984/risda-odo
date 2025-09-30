<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->decimal('lokasi_lat', 11, 8)->nullable()->after('lokasi_program');
            $table->decimal('lokasi_long', 11, 8)->nullable()->after('lokasi_lat');
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['lokasi_lat', 'lokasi_long']);
        });
    }
};
