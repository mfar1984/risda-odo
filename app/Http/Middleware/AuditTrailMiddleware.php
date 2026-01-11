<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\AuditTrailService;

class AuditTrailMiddleware
{
    protected AuditTrailService $auditTrailService;

    /**
     * Routes to exclude from audit trail tracking
     */
    protected array $excludedRoutes = [
        'api.audit-trail.click',  // Don't track the tracking endpoint itself
        'livewire.*',
        'debugbar.*',
        'telescope.*',
        'horizon.*',
    ];

    /**
     * URL patterns to exclude
     */
    protected array $excludedPatterns = [
        '/livewire/',
        '/_debugbar/',
        '/telescope/',
        '/horizon/',
        '/api/audit-trail/',
    ];

    public function __construct(AuditTrailService $auditTrailService)
    {
        $this->auditTrailService = $auditTrailService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track for authenticated users
        if (!auth()->check()) {
            return $response;
        }

        // Only track GET requests (page views)
        if (!$request->isMethod('GET')) {
            return $response;
        }

        // Skip AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return $response;
        }

        // Skip excluded routes
        if ($this->shouldExclude($request)) {
            return $response;
        }

        // Skip if response is not successful (4xx, 5xx)
        if ($response->getStatusCode() >= 400) {
            return $response;
        }

        // Record the page view
        try {
            $this->auditTrailService->recordPageView($request);
        } catch (\Exception $e) {
            // Log error but don't break the request
            \Log::error('AuditTrailMiddleware: Failed to record page view', [
                'error' => $e->getMessage(),
                'url' => $request->fullUrl(),
            ]);
        }

        return $response;
    }

    /**
     * Check if the request should be excluded from tracking.
     */
    protected function shouldExclude(Request $request): bool
    {
        // Check route name
        $routeName = $request->route()?->getName();
        if ($routeName) {
            foreach ($this->excludedRoutes as $pattern) {
                if (fnmatch($pattern, $routeName)) {
                    return true;
                }
            }
        }

        // Check URL patterns
        $path = $request->path();
        foreach ($this->excludedPatterns as $pattern) {
            if (str_contains($path, trim($pattern, '/'))) {
                return true;
            }
        }

        return false;
    }
}
