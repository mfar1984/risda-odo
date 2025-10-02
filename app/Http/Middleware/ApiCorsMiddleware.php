<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\IntegrasiConfig;

class ApiCorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get CORS configuration
        $config = IntegrasiConfig::get();
        
        // Get origin from request
        $origin = $request->header('Origin');

        // Handle preflight requests
        if ($request->isMethod('OPTIONS')) {
            return $this->handlePreflight($request, $config, $origin);
        }

        // Process the request
        $response = $next($request);

        // Add CORS headers to response
        if ($origin) {
            if ($config->api_cors_allow_all || $config->isOriginAllowed($origin)) {
                $response->headers->set('Access-Control-Allow-Origin', $origin);
                $response->headers->set('Access-Control-Allow-Credentials', 'true');
                $response->headers->set('Access-Control-Expose-Headers', 'Authorization, X-Total-Count, X-Page, X-Per-Page');
            }
        }

        return $response;
    }

    /**
     * Handle preflight OPTIONS request
     */
    protected function handlePreflight(Request $request, $config, $origin): Response
    {
        $response = response('', 200);

        // Check if origin is allowed
        if ($origin && ($config->api_cors_allow_all || $config->isOriginAllowed($origin))) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-API-Key, Accept');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Max-Age', '86400'); // Cache preflight for 24 hours
        }

        return $response;
    }
}
