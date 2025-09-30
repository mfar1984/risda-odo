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
        Schema::table('tetapan_umums', function (Blueprint $table) {
            if (!Schema::hasColumn('tetapan_umums', 'map_provider')) {
                $table->string('map_provider')->nullable()->after('masa_tamat_sesi_minit');
            }

            if (!Schema::hasColumn('tetapan_umums', 'map_api_key')) {
                $table->string('map_api_key')->nullable()->after('map_provider');
            }

            if (!Schema::hasColumn('tetapan_umums', 'map_style_url')) {
                $table->string('map_style_url')->nullable()->after('map_api_key');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tetapan_umums', function (Blueprint $table) {
            if (Schema::hasColumn('tetapan_umums', 'map_style_url')) {
                $table->dropColumn('map_style_url');
            }

            if (Schema::hasColumn('tetapan_umums', 'map_api_key')) {
                $table->dropColumn('map_api_key');
            }

            if (Schema::hasColumn('tetapan_umums', 'map_provider')) {
                $table->dropColumn('map_provider');
            }
        });
    }
};
