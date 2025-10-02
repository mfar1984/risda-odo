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
        Schema::create('weather_configs', function (Blueprint $table) {
            $table->id();
            $table->string('organisasi_id')->nullable(); // For multi-tenancy
            $table->string('jenis_organisasi')->nullable(); // 'semua', 'bahagian', 'stesen'

            $table->string('weather_provider')->default('openweathermap'); // Fixed to OpenWeatherMap
            $table->string('weather_api_key')->nullable();
            $table->string('weather_base_url')->nullable();
            $table->string('weather_default_location')->nullable();
            $table->decimal('weather_default_lat', 10, 8)->nullable();
            $table->decimal('weather_default_long', 11, 8)->nullable();
            $table->string('weather_units')->default('metric'); // metric, imperial, standard
            $table->integer('weather_update_frequency')->default(30); // in minutes
            $table->integer('weather_cache_duration')->default(60); // in minutes
            $table->timestamp('weather_last_update')->nullable();
            $table->json('weather_current_data')->nullable();

            $table->foreignId('dikemaskini_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['organisasi_id', 'jenis_organisasi'], 'org_weather_config_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_configs');
    }
};
