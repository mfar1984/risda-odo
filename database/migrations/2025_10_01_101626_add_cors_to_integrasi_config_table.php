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
        Schema::table('integrasi_config', function (Blueprint $table) {
            $table->json('api_allowed_origins')->nullable()->after('api_token_usage_count');
            $table->boolean('api_cors_allow_all')->default(false)->after('api_allowed_origins');
        });

        // Set default CORS origins
        DB::table('integrasi_config')->update([
            'api_allowed_origins' => json_encode([
                'http://localhost',
                'http://localhost:8000',
                '*.jara.my',
                '*.jara.com.my'
            ]),
            'api_cors_allow_all' => false,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('integrasi_config', function (Blueprint $table) {
            $table->dropColumn(['api_allowed_origins', 'api_cors_allow_all']);
        });
    }
};
