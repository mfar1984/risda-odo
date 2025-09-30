<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tetapan_umums', function (Blueprint $table) {
            $table->decimal('map_default_lat', 10, 7)->nullable()->after('map_style_url');
            $table->decimal('map_default_long', 10, 7)->nullable()->after('map_default_lat');
        });
    }

    public function down(): void
    {
        Schema::table('tetapan_umums', function (Blueprint $table) {
            $table->dropColumn(['map_default_lat', 'map_default_long']);
        });
    }
};

