<?php

namespace App\Http\Controllers;

use App\Models\IntegrasiConfig;
use App\Models\EmailConfig;
use App\Models\WeatherConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class IntegrasiController extends Controller
{
    /**
     * Display integration management page with API, Weather, and Email tabs
     */
    public function index(Request $request)
    {
        $integrasi = IntegrasiConfig::get();
        $weatherConfig = WeatherConfig::getForCurrentUser();
        $emailConfig = EmailConfig::getForCurrentUser();

        return view('pengurusan.integrasi', [
            'integrasi' => $integrasi,
            'weatherConfig' => $weatherConfig,
            'emailConfig' => $emailConfig,
        ]);
    }

    /**
     * Generate new API token
     */
    public function generateApiToken(Request $request)
    {
        $integrasi = IntegrasiConfig::get();
        $integrasi->generateApiToken();

        $integrasi->update([
            'dikemaskini_oleh' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Token baru berjaya dijana',
            'token' => $integrasi->api_token,
        ]);
    }

    /**
     * Update CORS configuration
     */
    public function updateCors(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'api_allowed_origins' => 'nullable|string',
            'api_cors_allow_all' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $integrasi = IntegrasiConfig::get();
        
        // Process origins textarea to array
        $origins = [];
        if ($request->filled('api_allowed_origins')) {
            $lines = explode("\n", $request->api_allowed_origins);
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $origins[] = $line;
                }
            }
        }

        $integrasi->update([
            'api_allowed_origins' => $origins,
            'api_cors_allow_all' => $request->boolean('api_cors_allow_all'),
            'dikemaskini_oleh' => auth()->id(),
        ]);

        return redirect()->route('pengurusan.integrasi', ['tab' => 'api'])
            ->with('success', 'Konfigurasi CORS berjaya dikemaskini.');
    }

    /**
     * Update weather configuration (Multi-tenancy)
     */
    public function updateWeather(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'weather_api_key' => 'nullable|string|max:255',
            'weather_base_url' => 'nullable|string|max:255',
            'weather_default_location' => 'nullable|string|max:255',
            'weather_default_lat' => 'nullable|numeric|between:-90,90',
            'weather_default_long' => 'nullable|numeric|between:-180,180',
            'weather_units' => 'nullable|string|in:metric,imperial,standard',
            'weather_update_frequency' => 'nullable|integer|min:1|max:1440',
            'weather_cache_duration' => 'nullable|integer|min:1|max:1440',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = auth()->user();
        $weatherConfig = WeatherConfig::getForCurrentUser();
        
        // Multi-tenancy check (use loose comparison for organisasi_id to handle string/int type differences)
        if ($user->jenis_organisasi !== 'semua' && 
            ($weatherConfig->jenis_organisasi !== $user->jenis_organisasi || 
             $weatherConfig->organisasi_id != $user->organisasi_id)) {
            return redirect()->back()
                ->with('error', 'Anda tidak mempunyai kebenaran untuk kemaskini konfigurasi ini.');
        }
        
        $data = $request->only([
            'weather_api_key',
            'weather_base_url',
            'weather_default_location',
            'weather_default_lat',
            'weather_default_long',
            'weather_units',
            'weather_update_frequency',
            'weather_cache_duration',
        ]);

        $data['weather_provider'] = 'openweathermap';
        $data['dikemaskini_oleh'] = auth()->id();

        $weatherConfig->update($data);

        return redirect()->route('pengurusan.integrasi', ['tab' => 'cuaca'])
            ->with('success', 'Konfigurasi cuaca berjaya dikemaskini.');
    }

    /**
     * Update email configuration
     */
    public function updateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'smtp_host' => 'required|string|max:255',
            'smtp_port' => 'required|integer|min:1|max:65535',
            'smtp_encryption' => 'nullable|string|in:tls,ssl',
            'smtp_authentication' => 'boolean',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_from_address' => 'required|email|max:255',
            'smtp_from_name' => 'required|string|max:255',
            'smtp_reply_to' => 'nullable|email|max:255',
            'smtp_connection_timeout' => 'nullable|integer|min:1|max:300',
            'smtp_max_retries' => 'nullable|integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = auth()->user();
        $emailConfig = EmailConfig::getForCurrentUser();
        
        // Check if user can edit this config (use loose comparison for organisasi_id to handle string/int type differences)
        if ($user->jenis_organisasi !== 'semua' && 
            ($emailConfig->jenis_organisasi !== $user->jenis_organisasi || 
             $emailConfig->organisasi_id != $user->organisasi_id)) {
            return redirect()->back()
                ->with('error', 'Anda tidak mempunyai kebenaran untuk kemaskini konfigurasi ini.');
        }

        $data = $request->only([
            'smtp_host',
            'smtp_port',
            'smtp_encryption',
            'smtp_authentication',
            'smtp_username',
            'smtp_from_address',
            'smtp_from_name',
            'smtp_reply_to',
            'smtp_connection_timeout',
            'smtp_max_retries',
        ]);

        // Only update password if provided
        if ($request->filled('smtp_password')) {
            $data['smtp_password'] = $request->smtp_password;
        }

        $data['dikemaskini_oleh'] = auth()->id();

        $emailConfig->update($data);

        return redirect()->route('pengurusan.integrasi', ['tab' => 'email'])
            ->with('success', 'Konfigurasi email berjaya dikemaskini.');
    }
}


