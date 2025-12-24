<?php

namespace App\Support;

use App\Models\UserSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class UserSettingsHelper
{
    /**
     * Get user settings (with caching)
     */
    public static function getUserSettings($userId = null)
    {
        $userId = $userId ?? auth()->id();
        
        if (!$userId) {
            return self::getDefaultSettings();
        }

        // Cache for 1 hour
        return Cache::remember("user_settings_{$userId}", 3600, function () use ($userId) {
            $settings = UserSetting::where('user_id', $userId)->first();
            
            if (!$settings) {
                return self::getDefaultSettings();
            }
            
            return $settings;
        });
    }

    /**
     * Get default settings
     */
    private static function getDefaultSettings()
    {
        return (object) [
            'format_eksport' => UserSetting::DEFAULT_FORMAT_EKSPORT,
            'format_tarikh' => UserSetting::DEFAULT_FORMAT_TARIKH,
            'format_masa' => UserSetting::DEFAULT_FORMAT_MASA,
            'format_nombor' => UserSetting::DEFAULT_FORMAT_NOMBOR,
            'mata_wang' => UserSetting::DEFAULT_MATA_WANG,
        ];
    }

    /**
     * Clear user settings cache
     */
    public static function clearCache($userId = null)
    {
        $userId = $userId ?? auth()->id();
        Cache::forget("user_settings_{$userId}");
    }

    /**
     * Format date according to user preference
     */
    public static function formatTarikh($date, $userId = null)
    {
        if (!$date) {
            return '-';
        }

        // Convert to Carbon if not already
        if (!$date instanceof Carbon) {
            try {
                $date = Carbon::parse($date);
            } catch (\Exception $e) {
                return $date;
            }
        }

        $settings = self::getUserSettings($userId);
        
        switch ($settings->format_tarikh) {
            case 'DD/MM/YYYY':
                return $date->format('d/m/Y');
            case 'DD-MM-YYYY':
                return $date->format('d-m-Y');
            case 'YYYY-MM-DD':
                return $date->format('Y-m-d');
            case 'DD MMM YYYY':
                return $date->translatedFormat('d M Y');
            default:
                return $date->format('d/m/Y');
        }
    }

    /**
     * Format time according to user preference
     */
    public static function formatMasa($time, $userId = null)
    {
        if (!$time) {
            return '-';
        }

        // Convert to Carbon if not already
        if (!$time instanceof Carbon) {
            try {
                $time = Carbon::parse($time);
            } catch (\Exception $e) {
                return $time;
            }
        }

        $settings = self::getUserSettings($userId);
        
        if ($settings->format_masa === '12') {
            return $time->format('h:i A');
        } else {
            return $time->format('H:i');
        }
    }

    /**
     * Format datetime according to user preference
     */
    public static function formatTarikhMasa($datetime, $userId = null)
    {
        if (!$datetime) {
            return '-';
        }

        // Convert to Carbon if not already
        if (!$datetime instanceof Carbon) {
            try {
                $datetime = Carbon::parse($datetime);
            } catch (\Exception $e) {
                return $datetime;
            }
        }

        $tarikh = self::formatTarikh($datetime, $userId);
        $masa = self::formatMasa($datetime, $userId);
        
        return "{$tarikh} {$masa}";
    }

    /**
     * Format number according to user preference
     */
    public static function formatNombor($number, $decimals = 0, $userId = null)
    {
        if ($number === null || $number === '') {
            return '-';
        }

        $settings = self::getUserSettings($userId);
        
        switch ($settings->format_nombor) {
            case '1,234.56':
                return number_format($number, $decimals, '.', ',');
            case '1.234,56':
                return number_format($number, $decimals, ',', '.');
            case '1 234.56':
                return number_format($number, $decimals, '.', ' ');
            default:
                return number_format($number, $decimals, '.', ',');
        }
    }

    /**
     * Format currency according to user preference
     */
    public static function formatWang($amount, $userId = null)
    {
        if ($amount === null || $amount === '') {
            return '-';
        }

        $settings = self::getUserSettings($userId);
        $formatted = self::formatNombor($amount, 2, $userId);
        
        return "{$settings->mata_wang} {$formatted}";
    }

    /**
     * Get export format preference
     */
    public static function getFormatEksport($userId = null)
    {
        $settings = self::getUserSettings($userId);
        return $settings->format_eksport;
    }
}
