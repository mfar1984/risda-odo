<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProgramController extends Controller
{
    /**
     * Get programs for authenticated driver
     * Supports filtering by: current, ongoing, past
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get user's staf_id to find assigned programs
        $stafId = $user->staf_id;
        
        if (!$stafId) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak mempunyai data staf yang berkaitan',
                'data' => []
            ], 400);
        }

        // Base query - programs assigned to this driver
        $query = Program::where('pemandu_id', $stafId)
                        ->forCurrentUser(); // Apply multi-tenancy

        // Filter by status parameter (current, ongoing, past)
        $status = $request->get('status');
        
        if ($status) {
            $query = $this->applyStatusFilter($query, $status);
        }

        // Get programs with relationships
        $programs = $query->with([
                            'pemohon:id,no_pekerja,nama_penuh,no_telefon',
                            'pemandu:id,no_pekerja,nama_penuh,no_telefon',
                            'kenderaan:id,no_plat,jenama,model,status',
                            'logPemandu:id,program_id,status,masa_keluar,masa_masuk'
                        ])
                        ->orderBy('tarikh_mula', 'desc')
                        ->get()
                        ->map(function ($program) {
                            return $this->formatProgramData($program);
                        });

        return response()->json([
            'success' => true,
            'data' => $programs,
            'meta' => [
                'total' => $programs->count(),
                'filter' => $status ?? 'all',
            ]
        ], 200);
    }

    /**
     * Apply status filter to query
     * 
     * @param $query
     * @param string $status
     * @return mixed
     */
    private function applyStatusFilter($query, $status)
    {
        $today = Carbon::today();
        
        switch ($status) {
            case 'current':
                // Programs scheduled for today
                return $query->whereDate('tarikh_mula', '<=', $today)
                            ->whereDate('tarikh_selesai', '>=', $today)
                            ->whereIn('status', ['aktif', 'lulus']);
                
            case 'ongoing':
                // Active programs (status = aktif)
                return $query->where('status', 'aktif');
                
            case 'past':
                // Completed programs or past due date
                return $query->where(function ($q) use ($today) {
                    $q->where('status', 'selesai')
                      ->orWhere('tarikh_selesai', '<', $today);
                });
                
            default:
                return $query;
        }
    }

    /**
     * Format program data for API response
     * 
     * @param Program $program
     * @return array
     */
    private function formatProgramData($program)
    {
        return [
            'id' => $program->id,
            'nama_program' => $program->nama_program,
            'status' => $program->status,
            'status_label' => $program->status_label,
            'tarikh_mula' => $program->tarikh_mula->format('Y-m-d H:i:s'),
            'tarikh_mula_formatted' => $program->tarikh_mula->format('d/m/Y'),
            'tarikh_selesai' => $program->tarikh_selesai ? $program->tarikh_selesai->format('Y-m-d H:i:s') : null,
            'tarikh_selesai_formatted' => $program->tarikh_selesai ? $program->tarikh_selesai->format('d/m/Y') : null,
            'lokasi_program' => $program->lokasi_program,
            'lokasi_lat' => $program->lokasi_lat,
            'lokasi_long' => $program->lokasi_long,
            'jarak_anggaran' => (float) $program->jarak_anggaran,
            'penerangan' => $program->penerangan,
            
            // Requestor (Pemohon)
            'permohonan_dari' => $program->pemohon ? [
                'id' => $program->pemohon->id,
                'no_pekerja' => $program->pemohon->no_pekerja,
                'nama_penuh' => $program->pemohon->nama_penuh,
                'no_telefon' => $program->pemohon->no_telefon,
            ] : null,
            
            // Driver (Pemandu)
            'pemandu' => $program->pemandu ? [
                'id' => $program->pemandu->id,
                'no_pekerja' => $program->pemandu->no_pekerja,
                'nama_penuh' => $program->pemandu->nama_penuh,
                'no_telefon' => $program->pemandu->no_telefon,
            ] : null,
            
            // Vehicle (Kenderaan)
            'kenderaan' => $program->kenderaan ? [
                'id' => $program->kenderaan->id,
                'no_plat' => $program->kenderaan->no_plat,
                'jenama' => $program->kenderaan->jenama,
                'model' => $program->kenderaan->model,
                'status' => $program->kenderaan->status,
                'latest_odometer' => $program->kenderaan->latest_odometer, // Latest odometer from completed journeys
            ] : null,
            
            // Logs Statistics
            'logs' => [
                'total' => $program->logPemandu->count(),
                'active' => $program->logPemandu->where('status', 'aktif')->count(),
                'completed' => $program->logPemandu->where('status', 'selesai')->count(),
            ],
            
            // Timestamps
            'created_at' => $program->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $program->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get single program details
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $stafId = $user->staf_id;
        
        if (!$stafId) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak mempunyai data staf yang berkaitan'
            ], 400);
        }

        // Get program assigned to this driver
        $program = Program::where('id', $id)
                        ->where('pemandu_id', $stafId)
                        ->forCurrentUser()
                        ->with([
                            'pemohon:id,no_pekerja,nama_penuh,no_telefon',
                            'pemandu:id,no_pekerja,nama_penuh,no_telefon',
                            'kenderaan:id,no_plat,jenama,model,status',
                            'logPemandu'
                        ])
                        ->first();

        if (!$program) {
            return response()->json([
                'success' => false,
                'message' => 'Program tidak dijumpai atau tidak diberikan akses'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatProgramData($program)
        ], 200);
    }
}
