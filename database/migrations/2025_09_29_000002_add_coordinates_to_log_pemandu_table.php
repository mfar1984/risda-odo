<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('log_pemandu', function (Blueprint $table) {
            $table->decimal('lokasi_checkin_lat', 11, 8)->nullable()->after('destinasi');
            $table->decimal('lokasi_checkin_long', 11, 8)->nullable()->after('lokasi_checkin_lat');
            $table->decimal('lokasi_checkout_lat', 11, 8)->nullable()->after('lokasi_checkin_long');
            $table->decimal('lokasi_checkout_long', 11, 8)->nullable()->after('lokasi_checkout_lat');
        });
    }

    public function down(): void
    {
        Schema::table('log_pemandu', function (Blueprint $table) {
            $table->dropColumn([
                'lokasi_checkin_lat',
                'lokasi_checkin_long',
                'lokasi_checkout_lat',
                'lokasi_checkout_long',
            ]);
        });
    }
};
