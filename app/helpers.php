<?php

use App\Support\UserSettingsHelper;

if (!function_exists('formatTarikh')) {
    function formatTarikh($date, $userId = null)
    {
        return UserSettingsHelper::formatTarikh($date, $userId);
    }
}

if (!function_exists('formatMasa')) {
    function formatMasa($time, $userId = null)
    {
        return UserSettingsHelper::formatMasa($time, $userId);
    }
}

if (!function_exists('formatTarikhMasa')) {
    function formatTarikhMasa($datetime, $userId = null)
    {
        return UserSettingsHelper::formatTarikhMasa($datetime, $userId);
    }
}

if (!function_exists('formatNombor')) {
    function formatNombor($number, $decimals = 0, $userId = null)
    {
        return UserSettingsHelper::formatNombor($number, $decimals, $userId);
    }
}

if (!function_exists('formatWang')) {
    function formatWang($amount, $userId = null)
    {
        return UserSettingsHelper::formatWang($amount, $userId);
    }
}
