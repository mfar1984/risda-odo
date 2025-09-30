<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PermissionAnyMiddleware
{
    public function handle(Request $request, Closure $next, string $permissions)
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        $pairs = array_filter(explode(';', $permissions));

        foreach ($pairs as $pair) {
            [$module, $action] = array_pad(array_map('trim', explode(',', $pair, 2)), 2, null);

            if ($module && $action && $user->adaKebenaran($module, $action)) {
                return $next($request);
            }
        }

        abort(403);
    }
}
