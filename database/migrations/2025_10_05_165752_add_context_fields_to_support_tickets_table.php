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
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->string('ip_address')->nullable()->after('source');
            $table->string('device')->nullable()->after('ip_address');
            $table->string('platform')->nullable()->after('device');
            $table->decimal('latitude', 10, 7)->nullable()->after('platform');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropColumn(['ip_address', 'device', 'platform', 'latitude', 'longitude']);
        });
    }
};
