<?php

namespace App\Services;

use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AuditTrailService
{
    /**
     * Sensitive fields that should never be stored in audit trail
     */
    protected array $sensitiveFields = [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        'token',
        'secret',
        '_token',
        'api_key',
        'api_secret',
        'credit_card',
        'cvv',
        'pin',
    ];

    /**
     * Record a page view event.
     */
    public function recordPageView(Request $request): ?AuditTrail
    {
        if (!auth()->check()) {
            return null;
        }

        try {
            return AuditTrail::create([
                'user_id' => auth()->id(),
                'action_type' => AuditTrail::TYPE_PAGE_VIEW,
                'action_name' => $this->getPageTitle($request),
                'url' => $request->fullUrl(),
                'route_name' => $request->route()?->getName(),
                'http_method' => $request->method(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'properties' => [
                    'referer' => $request->header('referer'),
                ],
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('AuditTrail: Failed to record page view', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            return null;
        }
    }

    /**
     * Record a button click event.
     */
    public function recordButtonClick(string $buttonId, string $actionName, Request $request): ?AuditTrail
    {
        if (!auth()->check()) {
            return null;
        }

        try {
            return AuditTrail::create([
                'user_id' => auth()->id(),
                'action_type' => AuditTrail::TYPE_BUTTON_CLICK,
                'action_name' => $actionName,
                'url' => $request->input('url', $request->fullUrl()),
                'route_name' => $request->input('route_name'),
                'http_method' => 'CLICK',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'properties' => [
                    'button_id' => $buttonId,
                ],
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('AuditTrail: Failed to record button click', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            return null;
        }
    }

    /**
     * Record a form submission event.
     */
    public function recordFormSubmission(string $formName, bool $success, Request $request, array $additionalData = []): ?AuditTrail
    {
        if (!auth()->check()) {
            return null;
        }

        try {
            // Filter out sensitive data
            $sanitizedData = $this->sanitizeData($additionalData);

            return AuditTrail::create([
                'user_id' => auth()->id(),
                'action_type' => AuditTrail::TYPE_FORM_SUBMIT,
                'action_name' => $formName,
                'url' => $request->fullUrl(),
                'route_name' => $request->route()?->getName(),
                'http_method' => $request->method(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'properties' => array_merge($sanitizedData, [
                    'success' => $success,
                ]),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('AuditTrail: Failed to record form submission', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            return null;
        }
    }

    /**
     * Record a successful login event.
     */
    public function recordLogin(User $user, Request $request): ?AuditTrail
    {
        try {
            return AuditTrail::create([
                'user_id' => $user->id,
                'action_type' => AuditTrail::TYPE_LOGIN,
                'action_name' => 'Log masuk berjaya',
                'url' => $request->fullUrl(),
                'route_name' => 'login',
                'http_method' => $request->method(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'properties' => [
                    'email' => $user->email,
                    'login_time' => now()->toIso8601String(),
                ],
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('AuditTrail: Failed to record login', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            return null;
        }
    }

    /**
     * Record a logout event.
     */
    public function recordLogout(User $user, Request $request): ?AuditTrail
    {
        try {
            // Calculate session duration from last login
            $lastLogin = AuditTrail::where('user_id', $user->id)
                ->where('action_type', AuditTrail::TYPE_LOGIN)
                ->latest('created_at')
                ->first();

            $sessionDuration = null;
            if ($lastLogin) {
                $sessionDuration = now()->diffInMinutes($lastLogin->created_at);
            }

            return AuditTrail::create([
                'user_id' => $user->id,
                'action_type' => AuditTrail::TYPE_LOGOUT,
                'action_name' => 'Log keluar',
                'url' => $request->fullUrl(),
                'route_name' => 'logout',
                'http_method' => $request->method(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'properties' => [
                    'email' => $user->email,
                    'logout_time' => now()->toIso8601String(),
                    'session_duration_minutes' => $sessionDuration,
                ],
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('AuditTrail: Failed to record logout', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            return null;
        }
    }

    /**
     * Record a failed login attempt.
     */
    public function recordFailedLogin(string $email, string $reason, Request $request): ?AuditTrail
    {
        try {
            // Try to find user by email
            $user = User::where('email', $email)->first();

            return AuditTrail::create([
                'user_id' => $user?->id,
                'action_type' => AuditTrail::TYPE_LOGIN_FAILED,
                'action_name' => 'Log masuk gagal',
                'url' => $request->fullUrl(),
                'route_name' => 'login',
                'http_method' => $request->method(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'properties' => [
                    'email' => $email,
                    'reason' => $reason,
                    'attempt_time' => now()->toIso8601String(),
                ],
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('AuditTrail: Failed to record failed login', [
                'error' => $e->getMessage(),
                'email' => $email,
            ]);
            return null;
        }
    }

    /**
     * Get audit trail for a specific user within date range.
     */
    public function getAuditTrail(int $userId, Carbon $dateFrom, Carbon $dateTo): Collection
    {
        return AuditTrail::with('user')
            ->forUser($userId)
            ->dateRange($dateFrom, $dateTo)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get paginated audit trail for a specific user within date range.
     */
    public function getAuditTrailPaginated(int $userId, Carbon $dateFrom, Carbon $dateTo, int $perPage = 20)
    {
        return AuditTrail::with('user')
            ->forUser($userId)
            ->dateRange($dateFrom, $dateTo)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Cleanup old audit trail records.
     */
    public function cleanupOldRecords(int $daysToKeep = 30): int
    {
        $count = AuditTrail::olderThan($daysToKeep)->count();
        
        AuditTrail::olderThan($daysToKeep)->delete();

        Log::info('AuditTrail: Cleanup completed', [
            'records_deleted' => $count,
            'days_kept' => $daysToKeep,
        ]);

        return $count;
    }

    /**
     * Sanitize data by removing sensitive fields.
     */
    public function sanitizeData(array $data): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            // Skip sensitive fields
            if ($this->isSensitiveField($key)) {
                continue;
            }

            // Recursively sanitize nested arrays
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeData($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Check if a field name is sensitive.
     */
    protected function isSensitiveField(string $fieldName): bool
    {
        $lowerField = strtolower($fieldName);

        // Check exact matches
        if (in_array($lowerField, $this->sensitiveFields)) {
            return true;
        }

        // Check if field contains 'password'
        if (str_contains($lowerField, 'password')) {
            return true;
        }

        // Check if field contains 'secret'
        if (str_contains($lowerField, 'secret')) {
            return true;
        }

        // Check if field contains 'token'
        if (str_contains($lowerField, 'token')) {
            return true;
        }

        return false;
    }

    /**
     * Get human-readable page title from request.
     */
    protected function getPageTitle(Request $request): string
    {
        $routeName = $request->route()?->getName();

        if (!$routeName) {
            return 'Halaman: ' . $request->path();
        }

        // Map route names to human-readable titles
        $routeTitles = [
            'dashboard' => 'Dashboard',
            'program.index' => 'Senarai Program',
            'program.create' => 'Cipta Program',
            'program.show' => 'Butiran Program',
            'program.edit' => 'Kemaskini Program',
            'pengurusan.senarai-pengguna' => 'Senarai Pengguna',
            'pengurusan.show-pengguna' => 'Butiran Pengguna',
            'pengurusan.aktiviti-log' => 'Aktiviti Log',
            'pengurusan.senarai-kumpulan' => 'Senarai Kumpulan',
            'settings.index' => 'Tetapan',
            'profile.edit' => 'Profil',
            'laporan.senarai-program' => 'Laporan Program',
            'laporan.laporan-tuntutan' => 'Laporan Tuntutan',
            'laporan.laporan-kos' => 'Laporan Kos',
        ];

        return $routeTitles[$routeName] ?? 'Halaman: ' . $routeName;
    }
}
