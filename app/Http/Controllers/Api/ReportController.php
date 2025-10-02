<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LogPemandu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /**
     * Get Vehicle Report
     * Returns vehicle usage data with FULL DETAILS
     */
    public function vehicle(Request $request)
    {
        try {
            $user = Auth::user();
            
            $query = LogPemandu::with(['kenderaan', 'program', 'program.pemohon'])
                ->where('pemandu_id', $user->id)
                ->where('status', 'selesai');
            
            // Apply date filters if provided
            if ($request->filled('date_from')) {
                $query->whereDate('tarikh_perjalanan', '>=', $request->date_from);
            }
            
            if ($request->filled('date_to')) {
                $query->whereDate('tarikh_perjalanan', '>=', $request->date_to);
            }
            
            $logs = $query->orderBy('tarikh_perjalanan', 'desc')->get();
            
            $vehicleReport = $logs->map(function ($log) {
                return [
                    // Basic Info
                    'id' => $log->id,
                    'no_plat' => $log->kenderaan->no_plat ?? '-',
                    'program' => $log->program->nama_program ?? '-',
                    'location' => $log->program->lokasi_program ?? '-',
                    'distance' => $log->jarak ?? 0,
                    'date' => $log->tarikh_perjalanan,
                    
                    // FULL VEHICLE DETAILS
                    'vehicle_details' => [
                        'id' => $log->kenderaan_id,
                        'no_plat' => $log->kenderaan->no_plat ?? '-',
                        'jenama' => $log->kenderaan->jenama ?? '-',
                        'model' => $log->kenderaan->model ?? '-',
                        'jenis_bahan_api' => $log->kenderaan->jenis_bahan_api ?? '-',
                    ],
                    
                    // FULL PROGRAM DETAILS
                    'program_details' => [
                        'id' => $log->program_id,
                        'nama_program' => $log->program->nama_program ?? '-',
                        'lokasi_program' => $log->program->lokasi_program ?? '-',
                        'permohonan_dari' => $log->program->pemohon->nama_penuh ?? '-',
                    ],
                    
                    // FULL JOURNEY DETAILS
                    'journey_details' => [
                        'tarikh' => $log->tarikh_perjalanan,
                        'masa_keluar' => $log->masa_keluar,
                        'masa_masuk' => $log->masa_masuk,
                        'odometer_keluar' => $log->odometer_keluar,
                        'odometer_masuk' => $log->odometer_masuk,
                        'jarak' => $log->jarak,
                        'status' => $log->status,
                        'catatan' => $log->catatan,
                    ],
                    
                    // FUEL DETAILS
                    'fuel_details' => [
                        'kos_minyak' => $log->kos_minyak ? (float) $log->kos_minyak : null,
                        'liter_minyak' => $log->liter_minyak ? (float) $log->liter_minyak : null,
                        'stesen_minyak' => $log->stesen_minyak,
                    ],
                    
                    // IMAGES
                    'images' => [
                        'foto_odometer_keluar' => $log->foto_odometer_keluar ? Storage::url($log->foto_odometer_keluar) : null,
                        'foto_odometer_masuk' => $log->foto_odometer_masuk ? Storage::url($log->foto_odometer_masuk) : null,
                        'resit_minyak' => $log->resit_minyak ? Storage::url($log->resit_minyak) : null,
                    ],
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $vehicleReport
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ralat mendapatkan laporan kenderaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get Cost Report
     * Returns fuel cost data with FULL DETAILS
     */
    public function cost(Request $request)
    {
        try {
            $user = Auth::user();
            
            $query = LogPemandu::with(['kenderaan', 'program', 'program.pemohon'])
                ->where('pemandu_id', $user->id)
                ->where('status', 'selesai')
                ->whereNotNull('kos_minyak')
                ->where('kos_minyak', '>', 0);
            
            // Apply date filters if provided
            if ($request->filled('date_from')) {
                $query->whereDate('tarikh_perjalanan', '>=', $request->date_from);
            }
            
            if ($request->filled('date_to')) {
                $query->whereDate('tarikh_perjalanan', '<=', $request->date_to);
            }
            
            $logs = $query->orderBy('tarikh_perjalanan', 'desc')->get();
            
            $costReport = $logs->map(function ($log) {
                return [
                    // Basic Info
                    'id' => $log->id,
                    'date' => $log->tarikh_perjalanan,
                    'vehicle' => $log->kenderaan->no_plat ?? '-',
                    'program' => $log->program->nama_program ?? '-',
                    'amount' => (float) $log->kos_minyak,
                    'liters' => (float) $log->liter_minyak,
                    'station' => $log->stesen_minyak ?? '-',
                    
                    // FULL VEHICLE DETAILS
                    'vehicle_details' => [
                        'id' => $log->kenderaan_id,
                        'no_plat' => $log->kenderaan->no_plat ?? '-',
                        'jenama' => $log->kenderaan->jenama ?? '-',
                        'model' => $log->kenderaan->model ?? '-',
                    ],
                    
                    // FULL PROGRAM DETAILS
                    'program_details' => [
                        'id' => $log->program_id,
                        'nama_program' => $log->program->nama_program ?? '-',
                        'lokasi_program' => $log->program->lokasi_program ?? '-',
                        'permohonan_dari' => $log->program->pemohon->nama_penuh ?? '-',
                    ],
                    
                    // FULL JOURNEY DETAILS
                    'journey_details' => [
                        'tarikh' => $log->tarikh_perjalanan,
                        'masa_keluar' => $log->masa_keluar,
                        'masa_masuk' => $log->masa_masuk,
                        'odometer_keluar' => $log->odometer_keluar,
                        'odometer_masuk' => $log->odometer_masuk,
                        'jarak' => $log->jarak,
                        'status' => $log->status,
                    ],
                    
                    // FUEL DETAILS
                    'fuel_details' => [
                        'kos_minyak' => (float) $log->kos_minyak,
                        'liter_minyak' => (float) $log->liter_minyak,
                        'stesen_minyak' => $log->stesen_minyak,
                        'resit_minyak' => $log->resit_minyak ? Storage::url($log->resit_minyak) : null,
                    ],
                    
                    // IMAGES
                    'images' => [
                        'foto_odometer_keluar' => $log->foto_odometer_keluar ? Storage::url($log->foto_odometer_keluar) : null,
                        'foto_odometer_masuk' => $log->foto_odometer_masuk ? Storage::url($log->foto_odometer_masuk) : null,
                        'resit_minyak' => $log->resit_minyak ? Storage::url($log->resit_minyak) : null,
                    ],
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $costReport,
                'total_cost' => $logs->sum('kos_minyak'),
                'total_liters' => $logs->sum('liter_minyak')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ralat mendapatkan laporan kos',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get Driver Report
     * Returns driver's journey statistics with DETAILED TRIPS
     */
    public function driver(Request $request)
    {
        try {
            $user = Auth::user();
            
            $query = LogPemandu::with(['program', 'program.pemohon', 'kenderaan'])
                ->where('pemandu_id', $user->id);
            
            // Apply date filters if provided
            if ($request->filled('date_from')) {
                $query->whereDate('tarikh_perjalanan', '>=', $request->date_from);
            }
            
            if ($request->filled('date_to')) {
                $query->whereDate('tarikh_perjalanan', '<=', $request->date_to);
            }
            
            $logs = $query->orderBy('tarikh_perjalanan', 'desc')->get();
            
            // Group by program WITH DETAILED TRIPS
            $programStats = $logs->groupBy('program_id')->map(function ($programLogs) {
                $program = $programLogs->first()->program;
                
                // DETAILED TRIPS - ALL CHECK-IN/CHECK-OUT
                $trips = $programLogs->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'tarikh' => $log->tarikh_perjalanan,
                        'masa_keluar' => $log->masa_keluar,
                        'masa_masuk' => $log->masa_masuk,
                        'odometer_keluar' => $log->odometer_keluar,
                        'odometer_masuk' => $log->odometer_masuk,
                        'jarak' => $log->jarak,
                        'status' => $log->status,
                        'kenderaan' => $log->kenderaan->no_plat ?? '-',
                        'kos_minyak' => $log->kos_minyak ? (float) $log->kos_minyak : null,
                        'liter_minyak' => $log->liter_minyak ? (float) $log->liter_minyak : null,
                    ];
                });
                
                return [
                    'program_id' => $program->id ?? null,
                    'program_name' => $program->nama_program ?? '-',
                    'program_location' => $program->lokasi_program ?? '-',
                    'permohonan_dari' => $program->pemohon->nama_penuh ?? '-',
                    'total_trips' => $programLogs->count(),
                    'check_out_count' => $programLogs->where('masa_keluar', '!=', null)->count(),
                    'check_in_count' => $programLogs->where('masa_masuk', '!=', null)->count(),
                    'completed_count' => $programLogs->where('status', 'selesai')->count(),
                    'total_distance' => $programLogs->sum('jarak'),
                    'total_fuel_cost' => $programLogs->sum('kos_minyak'),
                    'status' => $programLogs->first()->status,
                    
                    // ALL TRIPS DETAILS
                    'trips' => $trips->values(),
                ];
            })->values();
            
            return response()->json([
                'success' => true,
                'data' => $programStats,
                'summary' => [
                    'total_programs' => $programStats->count(),
                    'total_trips' => $logs->count(),
                    'completed_trips' => $logs->where('status', 'selesai')->count(),
                    'total_distance' => $logs->sum('jarak'),
                    'total_fuel_cost' => $logs->sum('kos_minyak'),
                    'active_trips' => $logs->where('status', 'dalam_perjalanan')->count(),
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ralat mendapatkan laporan pemandu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
