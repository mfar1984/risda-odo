<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\IntegrasiConfig;

class ApiTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Validates the global API token from request header
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $config = IntegrasiConfig::get();

        // Check if API token exists in config
        if (!$config->api_token) {
            return response()->json([
                'success' => false,
                'message' => 'API token belum dikonfigurasi di sistem',
            ], 503);
        }

        // Get API key from request header
        $apiKey = $request->header('X-API-Key');

        // Check if API key is provided
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key diperlukan. Sila sertakan X-API-Key dalam header',
            ], 401);
        }

        // Validate API key
        if ($apiKey !== $config->api_token) {
            return response()->json([
                'success' => false,
                'message' => 'API key tidak sah',
            ], 401);
        }

        // Record API usage
        $config->recordTokenUsage();

        return $next($request);
    }
}
