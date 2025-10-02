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
            // Remove SMTP fields (moving to email_configs table)
            $table->dropColumn([
                'smtp_host',
                'smtp_port',
                'smtp_encryption',
                'smtp_username',
                'smtp_password',
                'smtp_from_address',
                'smtp_from_name',
            ]);
            
            // Add Weather API Configuration
            $table->string('weather_provider', 50)->nullable()->default('openweathermap')->after('api_token_usage_count');
            $table->string('weather_api_key')->nullable()->after('weather_provider');
            $table->string('weather_base_url')->nullable()->default('https://api.openweathermap.org/data/2.5')->after('weather_api_key');
            $table->string('weather_default_location')->nullable()->after('weather_base_url');
            $table->decimal('weather_default_lat', 10, 8)->nullable()->after('weather_default_location');
            $table->decimal('weather_default_long', 11, 8)->nullable()->after('weather_default_lat');
            $table->string('weather_units', 20)->nullable()->default('metric')->after('weather_default_long'); // metric, imperial, standard
            $table->integer('weather_update_frequency')->nullable()->default(30)->after('weather_units'); // minutes
            $table->integer('weather_cache_duration')->nullable()->default(60)->after('weather_update_frequency'); // minutes
            $table->timestamp('weather_last_update')->nullable()->after('weather_cache_duration');
            $table->text('weather_current_data')->nullable()->after('weather_last_update'); // JSON
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('integrasi_config', function (Blueprint $table) {
            // Add back SMTP fields
            $table->string('smtp_host')->nullable();
            $table->integer('smtp_port')->nullable()->default(587);
            $table->string('smtp_encryption', 10)->nullable()->default('tls');
            $table->string('smtp_username')->nullable();
            $table->text('smtp_password')->nullable();
            $table->string('smtp_from_address')->nullable();
            $table->string('smtp_from_name')->nullable();
            
            // Remove Weather API fields
            $table->dropColumn([
                'weather_provider',
                'weather_api_key',
                'weather_base_url',
                'weather_default_location',
                'weather_default_lat',
                'weather_default_long',
                'weather_units',
                'weather_update_frequency',
                'weather_cache_duration',
                'weather_last_update',
                'weather_current_data',
            ]);
        });
    }
};
