<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LogPemandu;
use App\Models\Program;
use App\Models\Kenderaan;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class LogPemanduController extends Controller
{
    /**
     * Get all logs for authenticated driver
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $stafId = $user->staf_id;
        
        if (!$stafId) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak mempunyai data staf yang berkaitan',
                'data' => []
            ], 400);
        }

        // Get logs for this driver
        $query = LogPemandu::where('pemandu_id', $user->id)
                           ->with([
                               'program:id,nama_program,lokasi_program',
                               'kenderaan:id,no_plat,jenama,model',
                           ])
                           ->orderBy('created_at', 'desc');

        // Filter by status if provided
        $status = $request->get('status');
        if ($status) {
            if ($status === 'aktif') {
                $query->where('status', 'dalam_perjalanan');
            } elseif ($status === 'selesai') {
                $query->where('status', 'selesai');
            }
        }

        $logs = $query->get()->map(function ($log) {
            return $this->formatLogData($log);
        });

        return response()->json([
            'success' => true,
            'data' => $logs,
            'meta' => [
                'total' => $logs->count(),
                'filter' => $status ?? 'all',
            ]
        ], 200);
    }

    /**
     * Get active journey (if any)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveJourney(Request $request)
    {
        $user = $request->user();
        
        $activeLog = LogPemandu::where('pemandu_id', $user->id)
                               ->where('status', 'dalam_perjalanan')
                               ->with([
                                   'program:id,nama_program,lokasi_program,lokasi_lat,lokasi_long,jarak_anggaran,permohonan_dari',
                                   'program.pemohon:id,nama_penuh,no_pekerja',
                                   'kenderaan:id,no_plat,jenama,model',
                               ])
                               ->first();

        if (!$activeLog) {
            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Tiada perjalanan aktif'
            ], 200);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatLogData($activeLog)
        ], 200);
    }

    // show() for single log via API was intentionally removed per request

    /**
     * Start Journey (Check-Out)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function startJourney(Request $request)
    {
        $user = $request->user();

        // Check if driver already has active journey
        $activeJourney = LogPemandu::where('pemandu_id', $user->id)
                                   ->where('status', 'dalam_perjalanan')
                                   ->first();

        if ($activeJourney) {
            return response()->json([
                'success' => false,
                'message' => 'Anda masih mempunyai perjalanan aktif. Sila tamatkan perjalanan semasa terlebih dahulu.',
                'data' => $this->formatLogData($activeJourney)
            ], 400);
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'program_id' => 'required|exists:programs,id',
            'kenderaan_id' => 'required|exists:kenderaans,id',
            'odometer_keluar' => 'required|numeric|min:0',
            'lokasi_keluar_lat' => 'nullable|numeric',
            'lokasi_keluar_long' => 'nullable|numeric',
            'lokasi_mula_perjalanan' => 'nullable|string',
            'catatan' => 'nullable|string',
            'foto_odometer_keluar' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify program belongs to this driver
        $program = Program::find($request->program_id);
        if ($program->pemandu_id != $user->staf_id) {
            return response()->json([
                'success' => false,
                'message' => 'Program ini tidak diperuntukkan kepada anda'
            ], 403);
        }

        // Upload odometer photo if provided
        $fotoOdometerKeluar = null;
        if ($request->hasFile('foto_odometer_keluar')) {
            $fotoOdometerKeluar = $request->file('foto_odometer_keluar')
                                         ->store('odometer_photos', 'public');  // Specify 'public' disk
        }

        // Create log pemandu
        $log = LogPemandu::create([
            'program_id' => $request->program_id,
            'pemandu_id' => $user->id,
            'kenderaan_id' => $request->kenderaan_id,
            'tarikh_perjalanan' => Carbon::now()->toDateString(),
            'destinasi' => $program->lokasi_program, // Get from program
            'masa_keluar' => Carbon::now(),
            'odometer_keluar' => $request->odometer_keluar,
            'lokasi_checkout_lat' => $request->lokasi_keluar_lat,
            'lokasi_checkout_long' => $request->lokasi_keluar_long,
            'lokasi_mula_perjalanan' => $request->lokasi_mula_perjalanan,
            'foto_odometer_keluar' => $fotoOdometerKeluar,
            'catatan' => $request->catatan,
            'status' => 'dalam_perjalanan',
        ]);

        // Activity: journey started
        activity('log_pemandu')
            ->performedOn($log)
            ->causedBy($user)
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'log_id' => $log->id,
                'driver_name' => $user->name,
                'vehicle_plate' => $log->kenderaan->no_plat ?? 'N/A',
                'program_name' => $program->nama_program,
                'tarikh_perjalanan' => $log->tarikh_perjalanan,
                'masa_keluar' => $log->masa_keluar,
                'odometer_keluar' => $log->odometer_keluar,
                'status' => $log->status,
            ])
            ->event('created')
            ->log("Perjalanan bermula untuk program '{$program->nama_program}'");

        // Auto-update Program status: LULUS → AKTIF (when first journey starts)
        if ($program->status === 'lulus') {
            $program->update([
                'status' => 'aktif',
                'tarikh_mula_aktif' => now(), // Set actual start date/time
            ]);
        }

        // Load relationships
        $log->load([
            'program:id,nama_program,lokasi_program,lokasi_lat,lokasi_long,jarak_anggaran',
            'kenderaan:id,no_plat,jenama,model',
        ]);

        // Create notification for admin (backend bell icon)
        Notification::create([
            'user_id' => null, // Global notification for all admins
            'type' => 'journey_started',
            'title' => 'Perjalanan Bermula',
            'message' => "{$user->name} telah memulakan perjalanan untuk program '{$program->nama_program}'.",
            'data' => [
                'log_id' => $log->id,
                'driver_id' => $user->id,
                'driver_name' => $user->name,
                'program_id' => $program->id,
                'program_name' => $program->nama_program,
                'vehicle_id' => $log->kenderaan_id,
                'vehicle_plate' => $log->kenderaan->no_plat ?? 'N/A',
            ],
            'action_url' => "/log-pemandu/{$log->id}",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perjalanan dimulakan',
            'data' => $this->formatLogData($log)
        ], 201);
    }

    /**
     * End Journey (Check-In)
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function endJourney(Request $request, $id)
    {
        $user = $request->user();

        // Find log
        $log = LogPemandu::where('id', $id)
                        ->where('pemandu_id', $user->id)
                        ->where('status', 'dalam_perjalanan')
                        ->first();

        if (!$log) {
            return response()->json([
                'success' => false,
                'message' => 'Log perjalanan tidak dijumpai atau sudah tamat'
            ], 404);
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'odometer_masuk' => 'required|numeric|min:' . $log->odometer_keluar,
            'lokasi_checkin_lat' => 'nullable|numeric',
            'lokasi_checkin_long' => 'nullable|numeric',
            'lokasi_tamat_perjalanan' => 'nullable|string',
            'no_resit' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
            'foto_odometer_masuk' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            // Fuel fields (optional)
            'liter_minyak' => 'nullable|numeric|min:0',
            'kos_minyak' => 'nullable|numeric|min:0',
            'stesen_minyak' => 'nullable|string',
            'resit_minyak' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Upload odometer photo if provided
        $fotoOdometerMasuk = null;
        if ($request->hasFile('foto_odometer_masuk')) {
            $fotoOdometerMasuk = $request->file('foto_odometer_masuk')
                                        ->store('odometer_photos', 'public');  // Specify 'public' disk
        }

        // Upload fuel receipt photo if provided
        $resitMinyak = null;
        if ($request->hasFile('resit_minyak')) {
            $resitMinyak = $request->file('resit_minyak')
                                      ->store('fuel_receipts', 'public');  // Specify 'public' disk
        }

        // Update log
        $log->update([
            'masa_masuk' => Carbon::now(),
            'odometer_masuk' => $request->odometer_masuk,
            'lokasi_checkin_lat' => $request->lokasi_checkin_lat,
            'lokasi_checkin_long' => $request->lokasi_checkin_long,
            'lokasi_tamat_perjalanan' => $request->lokasi_tamat_perjalanan,
            'foto_odometer_masuk' => $fotoOdometerMasuk,
            'catatan' => $request->catatan ?? $log->catatan,
            'liter_minyak' => $request->liter_minyak,
            'kos_minyak' => $request->kos_minyak,
            'stesen_minyak' => $request->stesen_minyak,
            'resit_minyak' => $resitMinyak,
            'no_resit' => $request->no_resit ?? $log->no_resit,
            'status' => 'selesai',
        ]);

        // Activity: journey ended
        activity('log_pemandu')
            ->performedOn($log)
            ->causedBy($user)
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'log_id' => $log->id,
                'driver_name' => $user->name,
                'vehicle_plate' => optional($log->kenderaan)->no_plat,
                'program_name' => optional($log->program)->nama_program,
                'masa_masuk' => $log->masa_masuk,
                'odometer_keluar' => $log->odometer_keluar,
                'odometer_masuk' => $log->odometer_masuk,
                'jarak' => $log->jarak,
                'liter_minyak' => $log->liter_minyak,
                'kos_minyak' => $log->kos_minyak,
                'stesen_minyak' => $log->stesen_minyak,
                'status' => 'selesai',
                'old_status' => 'dalam_perjalanan',
                'new_status' => 'selesai',
            ])
            ->event('updated')
            ->log('Perjalanan ditamatkan');

        // Reload relationships
        $log->refresh();
        $log->load([
            'program:id,nama_program,lokasi_program',
            'kenderaan:id,no_plat,jenama,model',
        ]);

        // Update program's tarikh_sebenar_selesai to LAST end journey time
        $program = $log->program;
        if ($program) {
            // Get latest end journey time for this program
            $latestEndJourney = LogPemandu::where('program_id', $program->id)
                ->where('status', 'selesai')
                ->orderBy('masa_masuk', 'desc')
                ->first();
            
            if ($latestEndJourney) {
                $program->update([
                    'tarikh_sebenar_selesai' => $latestEndJourney->masa_masuk,
                ]);
            }
        }

        // Create notification for admin (backend bell icon)
        $user = Auth::user();
        $program = $log->program;
        Notification::create([
            'user_id' => null, // Global notification for all admins
            'type' => 'journey_ended',
            'title' => 'Perjalanan Selesai',
            'message' => "{$user->name} telah menamatkan perjalanan untuk program '{$program->nama_program}'.",
            'data' => [
                'log_id' => $log->id,
                'driver_id' => $user->id,
                'driver_name' => $user->name,
                'program_id' => $program->id,
                'program_name' => $program->nama_program,
                'vehicle_id' => $log->kenderaan_id,
                'vehicle_plate' => $log->kenderaan->no_plat ?? 'N/A',
                'distance' => $log->jarak_perjalanan ?? 0,
                'fuel_cost' => $log->kos_minyak ?? 0,
            ],
            'action_url' => "/log-pemandu/{$log->id}",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perjalanan berjaya ditamatkan',
            'data' => $this->formatLogData($log)
        ], 200);
    }

    /**
     * Update only textual start/end locations for a log (owner driver or admin)
     */
    public function updateLokasi(Request $request, $id)
    {
        $user = $request->user();

        $data = $request->validate([
            'lokasi_mula_perjalanan' => 'nullable|string|max:255',
            'lokasi_tamat_perjalanan' => 'nullable|string|max:255',
        ]);

        $log = LogPemandu::find($id);
        if (!$log) {
            return response()->json([
                'success' => false,
                'message' => 'Log tidak dijumpai',
            ], 404);
        }

        // Only the owner driver or admin can update
        $isOwner = (string) $log->pemandu_id === (string) $user->id;
        $isAdmin = ($user->jenis_organisasi ?? null) === 'semua';
        if (!$isOwner && !$isAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Akses dinafikan',
            ], 403);
        }

        $log->fill($data);
        $log->save();

        return response()->json([
            'success' => true,
            'message' => 'Lokasi berjaya dikemaskini',
            'data' => $this->formatLogData($log->fresh()),
        ]);
    }

    /**
     * Format log data for API response
     * 
     * @param LogPemandu $log
     * @return array
     */
    private function formatLogData(LogPemandu $log)
    {
        return [
            'id' => $log->id,
            'program_id' => $log->program_id,
            'program' => $log->program ? [
                'id' => $log->program->id,
                'nama_program' => $log->program->nama_program, // Fixed: use nama_program
                'lokasi_program' => $log->program->lokasi_program, // Fixed: use lokasi_program
                'lokasi_lat' => $log->program->lokasi_lat,
                'lokasi_long' => $log->program->lokasi_long,
                'jarak_anggaran' => $log->program->jarak_anggaran,
                'arahan_khas_pengguna_kenderaan' => $log->program->arahan_khas_pengguna_kenderaan,
                'permohonan_dari' => $log->program->pemohon ? [
                    'id' => $log->program->pemohon->id,
                    'nama_penuh' => $log->program->pemohon->nama_penuh, // Fixed: use nama_penuh
                    'no_pekerja' => $log->program->pemohon->no_pekerja,
                ] : null,
            ] : null,
            'kenderaan' => $log->kenderaan ? [
                'id' => $log->kenderaan->id,
                'no_plat' => $log->kenderaan->no_plat,
                'jenama' => $log->kenderaan->jenama,
                'model' => $log->kenderaan->model,
            ] : null,
            'status' => $log->status,
            'status_label' => $log->getStatusLabelAttribute(),
            'tarikh_perjalanan' => $log->tarikh_perjalanan, // Added: missing date field
            'masa_keluar' => $log->masa_keluar,
            'masa_masuk' => $log->masa_masuk,
            'destinasi' => $log->destinasi,
            'odometer_keluar' => $log->odometer_keluar,
            'odometer_masuk' => $log->odometer_masuk,
            'jarak' => $log->jarak,
            'lokasi_checkout_lat' => $log->lokasi_checkout_lat,
            'lokasi_checkout_long' => $log->lokasi_checkout_long,
            'lokasi_checkin_lat' => $log->lokasi_checkin_lat,
            'lokasi_checkin_long' => $log->lokasi_checkin_long,
            'lokasi_mula_perjalanan' => $log->lokasi_mula_perjalanan,
            'lokasi_tamat_perjalanan' => $log->lokasi_tamat_perjalanan,
            'foto_odometer_keluar' => $log->foto_odometer_keluar, // Return relative path only
            'foto_odometer_masuk' => $log->foto_odometer_masuk, // Return relative path only
            'liter_minyak' => $log->liter_minyak,
            'kos_minyak' => $log->kos_minyak,
            'stesen_minyak' => $log->stesen_minyak,
            'no_resit' => $log->no_resit,
            'resit_minyak' => $log->resit_minyak, // Return relative path only
            'catatan' => $log->catatan,
            'created_at' => $log->created_at?->toISOString(),
            'updated_at' => $log->updated_at?->toISOString(),
        ];
    }
}

