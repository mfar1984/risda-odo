<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdministrator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Check if user is authenticated and is Administrator
        if (!$user || $user->jenis_organisasi !== 'semua') {
            abort(403, 'Access denied. Administrator privileges required.');
        }

        return $next($request);
    }
}
