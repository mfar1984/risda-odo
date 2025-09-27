<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $module  The module to check permission for
     * @param  string  $action  The action to check permission for (default: 'lihat')
     */
    public function handle(Request $request, Closure $next, string $module, string $action = 'lihat'): Response
    {
        $user = auth()->user();

        // Check if user is authenticated
        if (!$user) {
            abort(401, 'Authentication required.');
        }

        // Administrator bypass (jenis_organisasi = 'semua')
        if ($user->jenis_organisasi === 'semua') {
            return $next($request);
        }

        // Check specific permission
        if (!$user->adaKebenaran($module, $action)) {
            abort(403, 'Access denied. You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
