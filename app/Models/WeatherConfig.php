<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherConfig extends Model
{
    use HasFactory;

    protected $table = 'weather_configs';

    protected $fillable = [
        'organisasi_id',
        'jenis_organisasi',
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
        'weather_default_lat' => 'decimal:8',
        'weather_default_long' => 'decimal:8',
        'weather_update_frequency' => 'integer',
        'weather_cache_duration' => 'integer',
        'weather_last_update' => 'datetime',
        'weather_current_data' => 'array',
    ];

    /**
     * Get the weather configuration for the current user's organization.
     * Creates a new record if none exists for the organization.
     */
    public static function getForCurrentUser()
    {
        $user = auth()->user();

        if ($user->jenis_organisasi === 'semua') {
            // Admin can manage a default/global config
            return static::firstOrCreate([
                'jenis_organisasi' => 'semua',
                'organisasi_id' => null, // Global for admin
            ], [
                'weather_provider' => 'openweathermap',
                'weather_base_url' => 'https://api.openweathermap.org/data/2.5',
                'weather_units' => 'metric',
                'weather_update_frequency' => 30,
                'weather_cache_duration' => 60,
            ]);
        } else {
            return static::firstOrCreate([
                'jenis_organisasi' => $user->jenis_organisasi,
                'organisasi_id' => $user->organisasi_id,
            ], [
                'weather_provider' => 'openweathermap',
                'weather_base_url' => 'https://api.openweathermap.org/data/2.5',
                'weather_units' => 'metric',
                'weather_update_frequency' => 30,
                'weather_cache_duration' => 60,
            ]);
        }
    }

    public function dikemaskiniOleh()
    {
        return $this->belongsTo(User::class, 'dikemaskini_oleh');
    }

    public function getCurrentWeatherAttribute()
    {
        if (!$this->weather_current_data) {
            return null;
        }
        return $this->weather_current_data;
    }

    public function isWeatherCacheValid(): bool
    {
        if (!$this->weather_last_update || !$this->weather_cache_duration) {
            return false;
        }
        $expiresAt = $this->weather_last_update->addMinutes($this->weather_cache_duration);
        return now()->lessThan($expiresAt);
    }

    /**
     * Get badge color based on cache validity
     */
    public function getCacheBadgeAttribute()
    {
        if (!$this->weather_last_update) {
            return 'gray';
        }
        return $this->isWeatherCacheValid() ? 'green' : 'yellow';
    }
}

