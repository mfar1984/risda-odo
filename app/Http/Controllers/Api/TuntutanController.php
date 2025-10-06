<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tuntutan;
use App\Models\LogPemandu;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TuntutanController extends Controller
{
    /**
     * Get all claims for authenticated driver
     * GET /api/tuntutan
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Get all claims for this driver's logs (pemandu_id can be either user_id or staf_id)
            $query = Tuntutan::with([
                'logPemandu.program.pemohon',
                'logPemandu.kenderaan',
                'diprosesOleh.risdaStaf'
            ])
            ->whereHas('logPemandu', function ($q) use ($user) {
                $q->where(function($q2) use ($user) {
                    $q2->where('pemandu_id', $user->id)
                       ->orWhere('pemandu_id', $user->staf_id);
                });
            });

            // Filter by status if provided
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $tuntutan = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $tuntutan->map(function ($item) {
                    return $this->formatTuntutanData($item);
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ralat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single claim by ID
     * GET /api/tuntutan/{id}
     */
    public function show($id)
    {
        try {
            $user = Auth::user();
            
            $tuntutan = Tuntutan::with([
                'logPemandu.program.pemohon',
                'logPemandu.kenderaan',
                'logPemandu',
                'diprosesOleh.risdaStaf'
            ])->find($id);

            if (!$tuntutan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tuntutan tidak dijumpai'
                ], 404);
            }

            // Check if this claim belongs to the authenticated driver (pemandu_id can be either user_id or staf_id)
            $pemanduId = $tuntutan->logPemandu->pemandu_id;
            if ($pemanduId !== $user->id && $pemanduId !== $user->staf_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak mempunyai akses ke tuntutan ini'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $this->formatTuntutanData($tuntutan)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ralat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new claim
     * POST /api/tuntutan
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'log_pemandu_id' => 'required|exists:log_pemandu,id',
                'kategori' => 'required|in:tol,parking,f&b,accommodation,fuel,car_maintenance,others',
                'jumlah' => 'required|numeric|min:0.01',
                'keterangan' => 'nullable|string|max:1000',
                'resit' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
            ], [
                'log_pemandu_id.required' => 'Log perjalanan wajib dipilih',
                'log_pemandu_id.exists' => 'Log perjalanan tidak sah',
                'kategori.required' => 'Kategori tuntutan wajib dipilih',
                'kategori.in' => 'Kategori tuntutan tidak sah',
                'jumlah.required' => 'Jumlah tuntutan wajib diisi',
                'jumlah.numeric' => 'Jumlah tuntutan mestilah nombor',
                'jumlah.min' => 'Jumlah tuntutan mestilah lebih dari RM 0.00',
                'resit.image' => 'Resit mestilah dalam format gambar',
                'resit.mimes' => 'Resit mestilah dalam format JPEG, PNG atau JPG',
                'resit.max' => 'Saiz resit mestilah kurang dari 5MB',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();

            // Verify the log belongs to this driver
            $log = LogPemandu::find($request->log_pemandu_id);
            if (!$log) {
                return response()->json([
                    'success' => false,
                    'message' => 'Log perjalanan tidak dijumpai'
                ], 404);
            }
            
            // Check if log belongs to this user (pemandu_id can be either user_id or staf_id)
            if ($log->pemandu_id !== $user->id && $log->pemandu_id !== $user->staf_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak mempunyai akses ke log perjalanan ini'
                ], 403);
            }

            // Handle receipt upload
            $resitPath = null;
            if ($request->hasFile('resit')) {
                $resitPath = $request->file('resit')->store('claim_receipts', 'public');
            }

            // Create claim
            $tuntutan = Tuntutan::create([
                'log_pemandu_id' => $request->log_pemandu_id,
                'kategori' => $request->kategori,
                'jumlah' => $request->jumlah,
                'keterangan' => $request->keterangan,
                'resit' => $resitPath,
                'status' => 'pending',
            ]);

            $tuntutan->load(['logPemandu.program', 'logPemandu.kenderaan']);

            // Create notification for admin (backend bell icon)
            Notification::create([
                'user_id' => null, // Global notification for all admins
                'type' => 'claim_created',
                'title' => 'Tuntutan Baru',
                'message' => "{$user->name} telah mengemukakan tuntutan baru sebanyak RM {$tuntutan->jumlah} untuk {$tuntutan->kategori_label}.",
                'data' => [
                    'claim_id' => $tuntutan->id,
                    'driver_id' => $user->id,
                    'driver_name' => $user->name,
                    'amount' => $tuntutan->jumlah,
                    'category' => $tuntutan->kategori,
                    'program_id' => $log->program_id,
                    'program_name' => $log->program->nama_program ?? 'N/A',
                ],
                'action_url' => "/laporan/laporan-tuntutan/{$tuntutan->id}",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tuntutan berjaya dihantar',
                'data' => $this->formatTuntutanData($tuntutan)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ralat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update claim (only if status = ditolak)
     * PUT /api/tuntutan/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            $tuntutan = Tuntutan::find($id);

            if (!$tuntutan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tuntutan tidak dijumpai'
                ], 404);
            }

            // Check ownership (pemandu_id can be either user_id or staf_id)
            $pemanduId = $tuntutan->logPemandu->pemandu_id;
            if ($pemanduId !== $user->id && $pemanduId !== $user->staf_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak mempunyai akses ke tuntutan ini'
                ], 403);
            }

            // Only allow edit if status is ditolak
            if (!$tuntutan->canBeEditedByDriver()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tuntutan ini tidak boleh diedit (Status: ' . $tuntutan->status_label . ')'
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'kategori' => 'required|in:tol,parking,f&b,accommodation,fuel,car_maintenance,others',
                'jumlah' => 'required|numeric|min:0.01',
                'keterangan' => 'nullable|string|max:1000',
                'resit' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Handle receipt upload
            $resitPath = $tuntutan->resit;
            if ($request->hasFile('resit')) {
                // Delete old receipt
                if ($resitPath) {
                    Storage::disk('public')->delete($resitPath);
                }
                $resitPath = $request->file('resit')->store('claim_receipts', 'public');
            }

            // Update claim and reset to pending
            $tuntutan->update([
                'kategori' => $request->kategori,
                'jumlah' => $request->jumlah,
                'keterangan' => $request->keterangan,
                'resit' => $resitPath,
                'status' => 'pending', // Reset to pending
                'alasan_tolak' => null, // Clear rejection reason
                'diproses_oleh' => null,
                'tarikh_diproses' => null,
            ]);

            $tuntutan->load(['logPemandu.program', 'logPemandu.kenderaan']);

            // Create notification for admin (backend bell icon) - RESUBMISSION
            Notification::create([
                'user_id' => null, // Global notification for all admins
                'type' => 'claim_resubmitted',
                'title' => 'Tuntutan Dikemuka Semula',
                'message' => "{$user->name} telah mengemukakan semula tuntutan sebanyak RM {$tuntutan->jumlah} untuk {$tuntutan->kategori_label}.",
                'data' => [
                    'claim_id' => $tuntutan->id,
                    'driver_id' => $user->id,
                    'driver_name' => $user->name,
                    'amount' => $tuntutan->jumlah,
                    'category' => $tuntutan->kategori,
                    'program_id' => $tuntutan->logPemandu->program_id,
                    'program_name' => $tuntutan->logPemandu->program->nama_program ?? 'N/A',
                ],
                'action_url' => "/laporan/laporan-tuntutan/{$tuntutan->id}",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tuntutan berjaya dikemaskini dan dihantar semula',
                'data' => $this->formatTuntutanData($tuntutan)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ralat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format tuntutan data for API response
     */
    private function formatTuntutanData(Tuntutan $tuntutan)
    {
        return [
            'id' => $tuntutan->id,
            'log_pemandu_id' => $tuntutan->log_pemandu_id,
            'kategori' => $tuntutan->kategori,
            'kategori_label' => $tuntutan->kategori_label,
            'jumlah' => (float) $tuntutan->jumlah,
            'keterangan' => $tuntutan->keterangan,
            'resit' => $tuntutan->resit, // Return relative path only (without full URL)
            'status' => $tuntutan->status,
            'status_label' => $tuntutan->status_label,
            'status_badge_color' => $tuntutan->status_badge_color,
            'alasan_tolak' => $tuntutan->alasan_tolak,
            'alasan_gantung' => $tuntutan->alasan_gantung,
            'can_edit' => $tuntutan->canBeEditedByDriver(),
            'diproses_oleh' => $tuntutan->diprosesOleh ? [
                'id' => $tuntutan->diprosesOleh->id,
                'name' => $tuntutan->diprosesOleh->name,
                'nama_penuh' => $tuntutan->diprosesOleh->risdaStaf->nama_penuh ?? $tuntutan->diprosesOleh->name,
            ] : null,
            'tarikh_diproses' => $tuntutan->tarikh_diproses ? $tuntutan->tarikh_diproses->format('Y-m-d H:i:s') : null,
            'program' => $tuntutan->logPemandu && $tuntutan->logPemandu->program ? [
                'id' => $tuntutan->logPemandu->program->id,
                'nama_program' => $tuntutan->logPemandu->program->nama_program,
                'lokasi_program' => $tuntutan->logPemandu->program->lokasi_program,
                'permohonan_dari' => $tuntutan->logPemandu->program->pemohon->nama_penuh ?? 'N/A',
            ] : null,
            'log_pemandu' => $tuntutan->logPemandu ? [
                'id' => $tuntutan->logPemandu->id,
                'tarikh_perjalanan' => $tuntutan->logPemandu->tarikh_perjalanan,
                'masa_keluar' => $tuntutan->logPemandu->masa_keluar,
                'tarikh_masa_perjalanan' => $tuntutan->logPemandu->masa_keluar_label,
            ] : null,
            'kenderaan' => $tuntutan->logPemandu && $tuntutan->logPemandu->kenderaan ? [
                'id' => $tuntutan->logPemandu->kenderaan->id,
                'no_plat' => $tuntutan->logPemandu->kenderaan->no_plat,
                'jenama' => $tuntutan->logPemandu->kenderaan->jenama,
                'model' => $tuntutan->logPemandu->kenderaan->model,
            ] : null,
            'created_at' => $tuntutan->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $tuntutan->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
