<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class IntegrasiConfig extends Model
{
    use HasFactory;

    protected $table = 'integrasi_config';

    protected $fillable = [
        'api_token',
        'api_token_created_at',
        'api_token_last_used',
        'api_token_usage_count',
        'api_allowed_origins',
        'api_cors_allow_all',
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
        'dikemaskini_oleh',
    ];

    protected $casts = [
        'api_token_created_at' => 'datetime',
        'api_token_last_used' => 'datetime',
        'api_token_usage_count' => 'integer',
        'api_allowed_origins' => 'array',
        'api_cors_allow_all' => 'boolean',
        'weather_default_lat' => 'decimal:8',
        'weather_default_long' => 'decimal:8',
        'weather_update_frequency' => 'integer',
        'weather_cache_duration' => 'integer',
        'weather_last_update' => 'datetime',
        'weather_current_data' => 'array',
    ];

    /**
     * Get the configuration (singleton pattern - only 1 record)
     */
    public static function get()
    {
        $config = static::first();

        if (!$config) {
            $config = static::create([
                'api_token' => null,
                'api_allowed_origins' => ['localhost', 'localhost:8000', '*.jara.my', '*.jara.com.my'],
                'api_cors_allow_all' => false,
            ]);
        }

        return $config;
    }

    /**
     * Generate new API token
     */
    public function generateApiToken(): string
    {
        $token = 'rsk_' . Str::random(64);
        
        $this->update([
            'api_token' => $token,
            'api_token_created_at' => now(),
            'api_token_last_used' => null,
            'api_token_usage_count' => 0,
        ]);

        return $token;
    }

    /**
     * Record API token usage
     */
    public function recordTokenUsage(): void
    {
        $this->increment('api_token_usage_count');
        $this->update(['api_token_last_used' => now()]);
    }

    /**
     * Get current weather data
     */
    public function getCurrentWeatherAttribute()
    {
        if (!$this->weather_current_data) {
            return null;
        }

        return $this->weather_current_data;
    }

    /**
     * Check if weather cache is still valid
     */
    public function isWeatherCacheValid(): bool
    {
        if (!$this->weather_last_update || !$this->weather_cache_duration) {
            return false;
        }

        $expiresAt = $this->weather_last_update->addMinutes($this->weather_cache_duration);
        return now()->lessThan($expiresAt);
    }

    /**
     * Check if origin is allowed based on CORS configuration
     */
    public function isOriginAllowed($origin): bool
    {
        // If allow all is enabled
        if ($this->api_cors_allow_all) {
            return true;
        }

        // If no allowed origins configured, deny
        if (!$this->api_allowed_origins || !is_array($this->api_allowed_origins)) {
            return false;
        }

        // Check exact match or wildcard match
        foreach ($this->api_allowed_origins as $allowedOrigin) {
            // Exact match
            if ($origin === $allowedOrigin) {
                return true;
            }

            // Wildcard match (e.g., *.jara.my)
            if (strpos($allowedOrigin, '*') !== false) {
                $pattern = str_replace(['*', '.'], ['.*', '\.'], $allowedOrigin);
                if (preg_match('/^' . $pattern . '$/', $origin)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get CORS origins as string (one per line)
     */
    public function getCorsOriginsTextAttribute(): string
    {
        if (!$this->api_allowed_origins || !is_array($this->api_allowed_origins)) {
            return '';
        }
        return implode("\n", $this->api_allowed_origins);
    }
}

