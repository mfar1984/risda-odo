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
        
        // Get manual cuti overrides
        $cutiOverrides = \App\Models\CutiUmumOverride::orderBy('tarikh_mula', 'desc')->get();

        return view('pengurusan.integrasi', [
            'integrasi' => $integrasi,
            'weatherConfig' => $weatherConfig,
            'emailConfig' => $emailConfig,
            'cutiOverrides' => $cutiOverrides,
        ]);
    }

    /**
     * Generate new API token
     */
    public function generateApiToken(Request $request)
    {
        $integrasi = IntegrasiConfig::get();
        
        // Store old token (masked for security)
        $oldToken = $integrasi->api_token ? substr($integrasi->api_token, 0, 10) . '...' . substr($integrasi->api_token, -10) : null;
        
        $integrasi->generateApiToken();

        $integrasi->update([
            'dikemaskini_oleh' => auth()->id(),
        ]);

        // Mask new token for logging
        $newTokenMasked = substr($integrasi->api_token, 0, 10) . '...' . substr($integrasi->api_token, -10);

        // Log activity
        activity('integrasi')
            ->performedOn($integrasi)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'old_token_masked' => $oldToken,
                'new_token_masked' => $newTokenMasked,
            ])
            ->event('generated_token')
            ->log("API Token baharu telah dijana");

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
        
        // Store old values for logging
        $oldOrigins = $integrasi->api_allowed_origins ?? [];
        $oldAllowAll = $integrasi->api_cors_allow_all;
        
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

        // Log activity
        activity('integrasi')
            ->performedOn($integrasi)
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'old_origins' => $oldOrigins,
                'new_origins' => $origins,
                'old_allow_all' => $oldAllowAll,
                'new_allow_all' => $integrasi->api_cors_allow_all,
            ])
            ->event('updated_cors')
            ->log("Konfigurasi CORS telah dikemaskini");

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
        
        // Store old values for logging
        $oldLocation = $weatherConfig->weather_default_location;
        $oldUnits = $weatherConfig->weather_units;
        $oldApiKey = $weatherConfig->weather_api_key;
        
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

        // Detect changes
        $changes = [];
        if ($oldLocation != $weatherConfig->weather_default_location) {
            $changes['location'] = ['old' => $oldLocation, 'new' => $weatherConfig->weather_default_location];
        }
        if ($oldUnits != $weatherConfig->weather_units) {
            $changes['units'] = ['old' => $oldUnits, 'new' => $weatherConfig->weather_units];
        }
        // Mask API key for security
        $apiKeyChanged = $oldApiKey != $weatherConfig->weather_api_key;

        // Log activity
        activity('integrasi')
            ->performedOn($weatherConfig)
            ->causedBy($user)
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'changes' => $changes,
                'api_key_changed' => $apiKeyChanged,
                'total_changes' => count($changes) + ($apiKeyChanged ? 1 : 0),
            ])
            ->event('updated_weather')
            ->log("Konfigurasi cuaca telah dikemaskini (" . (count($changes) + ($apiKeyChanged ? 1 : 0)) . " medan diubah)");

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

        // Store old values for logging
        $oldHost = $emailConfig->smtp_host;
        $oldPort = $emailConfig->smtp_port;
        $oldFromAddress = $emailConfig->smtp_from_address;

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

        // Track password change
        $passwordChanged = false;
        // Only update password if provided
        if ($request->filled('smtp_password')) {
            $data['smtp_password'] = $request->smtp_password;
            $passwordChanged = true;
        }

        $data['dikemaskini_oleh'] = auth()->id();

        $emailConfig->update($data);

        // Detect changes
        $changes = [];
        if ($oldHost != $emailConfig->smtp_host) {
            $changes['smtp_host'] = ['old' => $oldHost, 'new' => $emailConfig->smtp_host];
        }
        if ($oldPort != $emailConfig->smtp_port) {
            $changes['smtp_port'] = ['old' => $oldPort, 'new' => $emailConfig->smtp_port];
        }
        if ($oldFromAddress != $emailConfig->smtp_from_address) {
            $changes['smtp_from_address'] = ['old' => $oldFromAddress, 'new' => $emailConfig->smtp_from_address];
        }

        // Log activity (NEVER log actual password!)
        activity('integrasi')
            ->performedOn($emailConfig)
            ->causedBy($user)
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'changes' => $changes,
                'password_changed' => $passwordChanged,
                'total_changes' => count($changes) + ($passwordChanged ? 1 : 0),
            ])
            ->event('updated_email')
            ->log("Konfigurasi email telah dikemaskini (" . (count($changes) + ($passwordChanged ? 1 : 0)) . " medan diubah)");

        return redirect()->route('pengurusan.integrasi', ['tab' => 'email'])
            ->with('success', 'Konfigurasi email berjaya dikemaskini.');
    }

    /**
     * Preview cuti umum from package (AJAX)
     */
    public function cutiUmumPreview(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $mode = $request->input('mode', 'all'); // 'all' or 'single'
        $negeri = $request->input('negeri', 'Sarawak');

        try {
            if ($mode === 'all') {
                // Get holidays from all states and merge
                $allStates = ['Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan', 'Pahang', 
                             'Penang', 'Perak', 'Perlis', 'Sabah', 'Sarawak', 'Selangor', 
                             'Terengganu', 'Kuala Lumpur', 'Labuan', 'Putrajaya'];
                
                $allHolidays = [];
                foreach ($allStates as $state) {
                    $data = \Holiday\MalaysiaHoliday::make()->fromState($state)->ofYear($year)->get();
                    if ($data['status']) {
                        foreach ($data['data'][0]['collection'][0]['data'] as $h) {
                            if ($h['is_holiday']) {
                                if (!isset($allHolidays[$h['date']])) {
                                    $allHolidays[$h['date']] = [
                                        'date' => $h['date'],
                                        'day' => $h['day'],
                                        'name' => $h['name'],
                                        'states' => []
                                    ];
                                }
                                $allHolidays[$h['date']]['states'][] = $state;
                            }
                        }
                    }
                }
                
                ksort($allHolidays);
                
                return response()->json([
                    'success' => true,
                    'mode' => 'all',
                    'data' => array_values($allHolidays)
                ]);
            } else {
                // Get holidays for single state
                $data = \Holiday\MalaysiaHoliday::make()->fromState($negeri)->ofYear($year)->get();
                
                if ($data['status']) {
                    $holidays = collect($data['data'][0]['collection'][0]['data'])
                        ->where('is_holiday', true)
                        ->map(function ($h) {
                            return [
                                'date' => $h['date'],
                                'day' => $h['day'],
                                'name' => $h['name'],
                                'type' => $h['type']
                            ];
                        })
                        ->values();
                    
                    return response()->json([
                        'success' => true,
                        'mode' => 'single',
                        'negeri' => $negeri,
                        'data' => $holidays
                    ]);
                }
            }
            
            return response()->json(['success' => false, 'message' => 'Gagal mendapatkan data cuti'], 500);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Add custom holiday override
     */
    public function tambahCutiKhas(Request $request)
    {
        $validated = $request->validate([
            'tarikh_mula' => 'required|date',
            'tarikh_akhir' => 'required|date|after_or_equal:tarikh_mula',
            'nama_cuti' => 'required|string|max:255',
            'negeri' => 'nullable|array',
            'semuaNegeri' => 'boolean',
            'catatan' => 'nullable|string',
        ]);

        // Determine which states to apply
        $negeriList = [];
        if ($request->input('semuaNegeri', false)) {
            $negeriList = ['Semua'];
        } else {
            $negeriList = $validated['negeri'] ?? [];
        }

        if (empty($negeriList)) {
            return response()->json([
                'success' => false,
                'message' => 'Sila pilih sekurang-kurangnya satu negeri atau "Semua Negeri"'
            ], 400);
        }

        // Create one record for each selected state
        $created = [];
        foreach ($negeriList as $negeri) {
            $cuti = \App\Models\CutiUmumOverride::create([
                'tarikh_mula' => $validated['tarikh_mula'],
                'tarikh_akhir' => $validated['tarikh_akhir'],
                'nama_cuti' => $validated['nama_cuti'],
                'negeri' => $negeri,
                'catatan' => $validated['catatan'] ?? null,
                'aktif' => true,
                'dicipta_oleh' => auth()->id(),
            ]);
            $created[] = $cuti;
        }

        // Log activity
        $negeriStr = implode(', ', $negeriList);
        activity('integrasi')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'tarikh_mula' => $validated['tarikh_mula'],
                'tarikh_akhir' => $validated['tarikh_akhir'],
                'nama_cuti' => $validated['nama_cuti'],
                'negeri' => $negeriStr,
                'count' => count($created),
            ])
            ->event('created_holiday_override')
            ->log("Cuti khas '{$validated['nama_cuti']}' telah ditambah untuk {$negeriStr} (" . count($created) . " rekod)");

        return response()->json([
            'success' => true,
            'message' => 'Cuti khas berjaya ditambah untuk ' . count($created) . ' negeri',
            'data' => $created
        ]);
    }

    /**
     * Update custom holiday override
     */
    public function updateCutiKhas(Request $request, $id)
    {
        $cuti = \App\Models\CutiUmumOverride::findOrFail($id);

        $validated = $request->validate([
            'tarikh_mula' => 'required|date',
            'tarikh_akhir' => 'required|date|after_or_equal:tarikh_mula',
            'nama_cuti' => 'required|string|max:255',
            'negeri' => 'required|string',
            'catatan' => 'nullable|string',
            'aktif' => 'boolean',
        ]);

        $cuti->update($validated);

        // Log activity
        activity('integrasi')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'cuti_id' => $cuti->id,
                'nama_cuti' => $cuti->nama_cuti,
            ])
            ->event('updated_holiday_override')
            ->log("Cuti khas '{$cuti->nama_cuti}' telah dikemaskini");

        return response()->json([
            'success' => true,
            'message' => 'Cuti khas berjaya dikemaskini',
            'data' => $cuti
        ]);
    }

    /**
     * Delete custom holiday override
     */
    public function deleteCutiKhas($id)
    {
        $cuti = \App\Models\CutiUmumOverride::findOrFail($id);
        $namaCuti = $cuti->nama_cuti;
        
        $cuti->delete();

        // Log activity
        activity('integrasi')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'cuti_id' => $id,
                'nama_cuti' => $namaCuti,
            ])
            ->event('deleted_holiday_override')
            ->log("Cuti khas '{$namaCuti}' telah dipadam");

        return response()->json([
            'success' => true,
            'message' => 'Cuti khas berjaya dipadam'
        ]);
    }
}


