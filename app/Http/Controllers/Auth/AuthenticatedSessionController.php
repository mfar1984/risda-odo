<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    protected AuditTrailService $auditTrailService;

    public function __construct(AuditTrailService $auditTrailService)
    {
        $this->auditTrailService = $auditTrailService;
    }

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // Check if 2FA is enabled
        if ($user && $user->two_factor_enabled) {
            // Store user ID in session and logout
            $request->session()->put('2fa_user_id', $user->id);
            Auth::logout();
            
            return redirect()->route('two-factor.show-verify');
        }

        $request->session()->regenerate();

        // Update last login info
        if ($user) {
            $user->updateLastLogin($request->ip());
        }

        // Log successful login activity
        activity()
            ->causedBy($user)
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->event('login_success')
            ->log('Log masuk berjaya untuk ' . $user->name);

        // Record audit trail for login
        $this->auditTrailService->recordLogin($user, $request);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Record audit trail for logout BEFORE logout
        if ($user) {
            $this->auditTrailService->recordLogout($user, $request);
        }

        // Log logout activity BEFORE logout
        activity()
            ->causedBy($user)
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Log keluar');

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
