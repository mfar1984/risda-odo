<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Media
{
    /**
     * Normalize a stored path to a public URL.
     */
    public static function url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $path = trim($path);
        if ($path === '') {
            return null;
        }

        // Already an absolute URL or absolute path
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }
        if (Str::startsWith($path, '/')) {
            return $path;
        }

        // Blade sometimes stores 'storage/...' (public symlink) or 'public/...'
        if (Str::startsWith($path, 'storage/')) {
            return asset($path);
        }
        if (Str::startsWith($path, 'public/')) {
            $path = Str::after($path, 'public/');
        }

        return Storage::url($path);
    }

    /**
     * Check if a stored path exists on the public disk.
     */
    public static function exists(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        // Remote URLs are assumed accessible
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return true;
        }

        // Normalize to relative path within public disk
        $relative = $path;
        if (Str::startsWith($relative, 'public/')) {
            $relative = Str::after($relative, 'public/');
        }
        if (Str::startsWith($relative, 'storage/')) {
            $relative = Str::after($relative, 'storage/');
        }

        return Storage::disk('public')->exists($relative);
    }
}


