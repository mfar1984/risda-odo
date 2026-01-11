<?php

namespace App\Http\Controllers;

use App\Models\UserSetting;
use App\Support\UserSettingsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        $user = auth()->user();
        $settings = UserSetting::getOrCreateForUser($user->id);
        
        // Get security data
        $twoFactorEnabled = $user->two_factor_enabled ?? false;
        
        // Get active sessions
        $activeSessions = \DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) {
                return (object) [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'last_activity' => $session->last_activity,
                    'is_current' => $session->id === session()->getId(),
                ];
            });
        
        // Get login history from activity log
        // Login events use 'event' field and 'causer_id' (not log_name and subject_id)
        $loginHistory = \Spatie\Activitylog\Models\Activity::where('causer_type', \App\Models\User::class)
            ->where('causer_id', $user->id)
            ->whereIn('event', ['login_success', 'login_failed', 'login_blocked'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('settings.index', compact('settings', 'twoFactorEnabled', 'activeSessions', 'loginHistory'));
    }

    /**
     * Update Data & Eksport settings
     */
    public function updateDataEksport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'format_eksport' => 'required|in:pdf,excel,csv',
            'format_tarikh' => 'required|in:DD/MM/YYYY,DD-MM-YYYY,YYYY-MM-DD,DD MMM YYYY',
            'format_masa' => 'required|in:24,12',
            'format_nombor' => 'required|in:1,234.56,1.234,56,1 234.56',
            'mata_wang' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = auth()->user();
        $settings = UserSetting::getOrCreateForUser($user->id);
        
        $settings->update([
            'format_eksport' => $request->format_eksport,
            'format_tarikh' => $request->format_tarikh,
            'format_masa' => $request->format_masa,
            'format_nombor' => $request->format_nombor,
            'mata_wang' => $request->mata_wang,
        ]);

        // Clear cache
        UserSettingsHelper::clearCache($user->id);

        return back()->with('success', 'Tetapan Data & Eksport berjaya dikemaskini.');
    }

    /**
     * Reset Data & Eksport settings to defaults
     */
    public function resetDataEksport()
    {
        $user = auth()->user();
        $settings = UserSetting::getOrCreateForUser($user->id);
        
        $settings->update([
            'format_eksport' => UserSetting::DEFAULT_FORMAT_EKSPORT,
            'format_tarikh' => UserSetting::DEFAULT_FORMAT_TARIKH,
            'format_masa' => UserSetting::DEFAULT_FORMAT_MASA,
            'format_nombor' => UserSetting::DEFAULT_FORMAT_NOMBOR,
            'mata_wang' => UserSetting::DEFAULT_MATA_WANG,
        ]);

        // Clear cache
        UserSettingsHelper::clearCache($user->id);

        return back()->with('success', 'Tetapan Data & Eksport telah dikembalikan ke nilai asal.');
    }

    /**
     * Logout a specific session
     */
    public function logoutSession(Request $request)
    {
        $sessionId = $request->input('session_id');
        
        if (!$sessionId) {
            return back()->with('error', 'Session ID tidak sah.');
        }
        
        // Delete the session
        \DB::table('sessions')
            ->where('id', $sessionId)
            ->where('user_id', auth()->id())
            ->delete();
        
        return back()->with('success', 'Sesi berjaya dilog keluar.');
    }

    /**
     * Logout all other sessions
     */
    public function logoutOtherSessions(Request $request)
    {
        $currentSessionId = session()->getId();
        
        // Delete all sessions except current
        \DB::table('sessions')
            ->where('user_id', auth()->id())
            ->where('id', '!=', $currentSessionId)
            ->delete();
        
        return back()->with('success', 'Semua sesi lain berjaya dilog keluar.');
    }
}

