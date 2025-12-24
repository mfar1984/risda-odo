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
        
        return view('settings.index', compact('settings'));
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
}

