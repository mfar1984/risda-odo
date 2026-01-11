<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FALaravel\Google2FA;

class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = app('pragmarx.google2fa');
    }

    /**
     * Show 2FA setup page
     */
    public function setup()
    {
        $user = Auth::user();
        
        if ($user->two_factor_enabled) {
            return redirect()->route('settings.index', ['tab' => 'keselamatan'])
                ->with('error', '2FA sudah diaktifkan.');
        }

        // Generate secret key
        $secret = $this->google2fa->generateSecretKey();
        
        // Store temporarily in session
        session(['2fa_secret' => $secret]);

        // Generate QR code URL (otpauth:// format)
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name', 'RISDA'),
            $user->email,
            $secret
        );

        // Generate QR code as SVG using BaconQrCode
        $renderer = new \BaconQrCode\Renderer\Image\SvgImageBackEnd();
        $imageRenderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
            $renderer
        );
        $writer = new \BaconQrCode\Writer($imageRenderer);
        $qrCodeSvg = $writer->writeString($qrCodeUrl);

        return view('settings.two-factor-setup', compact('secret', 'qrCodeUrl', 'qrCodeSvg'));
    }

    /**
     * Enable 2FA after verification
     */
    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        $secret = session('2fa_secret');

        if (!$secret) {
            return back()->with('error', 'Sesi tamat. Sila cuba lagi.');
        }

        // Verify the code
        $valid = $this->google2fa->verifyKey($secret, $request->code);

        if (!$valid) {
            return back()->with('error', 'Kod tidak sah. Sila cuba lagi.');
        }

        // Generate recovery codes
        $recoveryCodes = $this->generateRecoveryCodes();

        // Enable 2FA
        $user->enable2FA($secret, $recoveryCodes);

        // Clear session
        session()->forget('2fa_secret');

        // Log activity
        activity()
            ->causedBy($user)
            ->withProperties(['ip' => $request->ip()])
            ->event('2fa_enabled')
            ->log('2FA diaktifkan untuk ' . $user->name);

        return redirect()->route('settings.index', ['tab' => 'keselamatan'])
            ->with('success', '2FA berjaya diaktifkan!')
            ->with('recovery_codes', $recoveryCodes);
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = Auth::user();

        if (!$user->two_factor_enabled) {
            return back()->with('error', '2FA tidak diaktifkan.');
        }

        // Verify with current code or recovery code
        $secret = decrypt($user->two_factor_secret);
        $valid = $this->google2fa->verifyKey($secret, $request->code);

        // Check recovery codes if OTP failed
        if (!$valid) {
            $recoveryCodes = $user->two_factor_recovery_codes ?? [];
            if (in_array($request->code, $recoveryCodes)) {
                $valid = true;
            }
        }

        if (!$valid) {
            return back()->with('error', 'Kod tidak sah.');
        }

        // Disable 2FA
        $user->disable2FA();

        // Log activity
        activity()
            ->causedBy($user)
            ->withProperties(['ip' => $request->ip()])
            ->event('2fa_disabled')
            ->log('2FA dinyahaktifkan untuk ' . $user->name);

        return redirect()->route('settings.index', ['tab' => 'keselamatan'])
            ->with('success', '2FA berjaya dinyahaktifkan.');
    }

    /**
     * Verify 2FA during login
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $userId = session('2fa_user_id');
        
        if (!$userId) {
            return redirect()->route('login')
                ->with('error', 'Sesi tamat. Sila log masuk semula.');
        }

        $user = \App\Models\User::find($userId);
        
        if (!$user || !$user->two_factor_enabled) {
            return redirect()->route('login');
        }

        $secret = decrypt($user->two_factor_secret);
        $valid = $this->google2fa->verifyKey($secret, $request->code);

        // Check recovery codes if OTP failed
        if (!$valid) {
            $recoveryCodes = $user->two_factor_recovery_codes ?? [];
            $codeIndex = array_search($request->code, $recoveryCodes);
            
            if ($codeIndex !== false) {
                $valid = true;
                // Remove used recovery code
                unset($recoveryCodes[$codeIndex]);
                $user->update(['two_factor_recovery_codes' => array_values($recoveryCodes)]);
            }
        }

        if (!$valid) {
            return back()->with('error', 'Kod tidak sah. Sila cuba lagi.');
        }

        // Clear session and login
        session()->forget('2fa_user_id');
        
        Auth::login($user);
        $request->session()->regenerate();

        // Update last login
        $user->updateLastLogin($request->ip());

        // Log successful login
        activity()
            ->causedBy($user)
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->event('login_success')
            ->log('Log masuk berjaya (dengan 2FA) untuk ' . $user->name);

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Show 2FA verification page during login
     */
    public function showVerify()
    {
        if (!session('2fa_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-verify');
    }

    /**
     * Generate recovery codes
     */
    protected function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(4)));
        }
        return $codes;
    }
}
