<?php

namespace App\Http\Controllers;

use App\Models\Tuntutan;
use App\Models\LogPemandu;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\FirebaseService;
use Barryvdh\DomPDF\Facade\Pdf;

class TuntutanController extends Controller
{
    /**
     * Display a listing of claims (Laporan Tuntutan)
     */
    public function index(Request $request)
    {
        // Check permission
        if (!Auth::user()->adaKebenaran('laporan_tuntutan', 'lihat')) {
            abort(403, 'Akses dinafikan');
        }

        $query = Tuntutan::with([
            'logPemandu.pemandu.risdaStaf',
            'logPemandu.program',
            'diprosesOleh'
        ])->forCurrentUser();

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('tarikh_dari') && $request->filled('tarikh_hingga')) {
            $query->whereBetween('created_at', [
                $request->tarikh_dari . ' 00:00:00',
                $request->tarikh_hingga . ' 23:59:59',
            ]);
        }

        // Search by program name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('logPemandu.program', function ($q) use ($search) {
                $q->where('nama_program', 'like', "%{$search}%");
            });
        }

        $tuntutan = $query->orderBy('created_at', 'desc')->paginate(15);

        $kategori_list = [
            'tol' => 'Tol',
            'parking' => 'Parking',
            'f&b' => 'Makanan & Minuman',
            'accommodation' => 'Penginapan',
            'fuel' => 'Minyak',
            'car_maintenance' => 'Penyelenggaraan Kenderaan',
            'others' => 'Lain-lain',
        ];

        $status_list = [
            'pending' => 'Pending',
            'diluluskan' => 'Diluluskan',
            'ditolak' => 'Ditolak',
            'digantung' => 'Digantung',
        ];

        return view('laporan.laporan-tuntutan', compact(
            'tuntutan',
            'kategori_list',
            'status_list'
        ));
    }

    /**
     * Display the specified claim
     */
    public function show(Tuntutan $tuntutan)
    {
        // Check permission
        if (!Auth::user()->adaKebenaran('laporan_tuntutan', 'lihat')) {
            abort(403, 'Akses dinafikan');
        }

        $tuntutan->load([
            'logPemandu.pemandu.risdaStaf',
            'logPemandu.program.pemohon',
            'logPemandu.program.pemandu',
            'logPemandu.program.kenderaan',
            'logPemandu.kenderaan',
            'diprosesOleh.risdaStaf'
        ]);

        // Get all claims for the same program (to show related claims)
        $relatedClaims = Tuntutan::with(['logPemandu', 'diprosesOleh.risdaStaf'])
            ->whereHas('logPemandu', function($q) use ($tuntutan) {
                $q->where('program_id', $tuntutan->logPemandu->program_id);
            })
            ->where('id', '!=', $tuntutan->id) // Exclude current claim
            ->forCurrentUser()
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate stats for this program's claims
        $allProgramClaims = Tuntutan::whereHas('logPemandu', function($q) use ($tuntutan) {
            $q->where('program_id', $tuntutan->logPemandu->program_id);
        })
        ->forCurrentUser()
        ->get();

        $stats = [
            'jumlah_tuntutan' => $allProgramClaims->count(),
            'jumlah_keseluruhan' => (float) $allProgramClaims->sum('jumlah'),
            'pending' => $allProgramClaims->where('status', 'pending')->count(),
            'diluluskan' => $allProgramClaims->where('status', 'diluluskan')->count(),
            'ditolak' => $allProgramClaims->where('status', 'ditolak')->count(),
            'jumlah_diluluskan' => (float) $allProgramClaims->where('status', 'diluluskan')->sum('jumlah'),
        ];

        return view('laporan.laporan-tuntutan-show', compact('tuntutan', 'relatedClaims', 'stats'));
    }

    /**
     * Approve a claim
     */
    public function approve(Request $request, Tuntutan $tuntutan)
    {
        // Check permission
        if (!Auth::user()->adaKebenaran('laporan_tuntutan', 'terima')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak mempunyai kebenaran untuk meluluskan tuntutan'
            ], 403);
        }

        // Validate status
        if (!$tuntutan->canBeApproved()) {
            return response()->json([
                'success' => false,
                'message' => 'Tuntutan ini tidak boleh diluluskan (status semasa: ' . $tuntutan->status_label . ')'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $tuntutan->update([
                'status' => 'diluluskan',
                'diproses_oleh' => Auth::id(),
                'tarikh_diproses' => now(),
                'alasan_tolak' => null, // Clear rejection reason if any
            ]);

            // Send notification to driver
            $driverId = $tuntutan->logPemandu->pemandu_id;
            
            // Create database notification (for bell count)
            \App\Models\Notification::create([
                'user_id' => $driverId,
                'type' => 'claim_approved',
                'title' => 'Tuntutan Diluluskan',
                'message' => "Tuntutan anda sebanyak RM {$tuntutan->jumlah} untuk {$tuntutan->kategori_label} telah diluluskan.",
                'data' => [
                    'claim_id' => $tuntutan->id,
                    'amount' => $tuntutan->jumlah,
                    'category' => $tuntutan->kategori,
                ],
                'action_url' => "/laporan/laporan-tuntutan/{$tuntutan->id}",
            ]);

            // Send FCM push notification
            $firebaseService = app(FirebaseService::class);
            $firebaseService->sendToUser(
                $driverId,
                'Tuntutan Diluluskan',
                "Tuntutan anda sebanyak RM {$tuntutan->jumlah} untuk {$tuntutan->kategori_label} telah diluluskan.",
                [
                    'type' => 'claim_approved',
                    'claim_id' => $tuntutan->id,
                    'amount' => $tuntutan->jumlah,
                    'category' => $tuntutan->kategori,
                ]
            );

            // Log activity (tuntutan)
            activity('tuntutan')
                ->performedOn($tuntutan)
                ->causedBy(Auth::user())
                ->withProperties([
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'claim_id' => $tuntutan->id,
                    'amount' => $tuntutan->jumlah,
                    'category' => $tuntutan->kategori_label,
                    'driver_id' => $driverId,
                    'driver_name' => ($tuntutan->logPemandu->pemandu->risdaStaf->nama_penuh ?? $tuntutan->logPemandu->pemandu->name) ?? 'N/A',
                    'program_name' => $tuntutan->logPemandu->program->nama_program ?? 'N/A',
                    'old_status' => 'pending',
                    'new_status' => 'diluluskan',
                    'approval_code' => $request->input('approval_code'),
                ])
                ->event('approved')
                ->log("Tuntutan {$tuntutan->kategori_label} sebanyak RM {$tuntutan->jumlah} telah diluluskan");

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tuntutan berjaya diluluskan'
                ]);
            }

            return redirect()->route('laporan.laporan-tuntutan')
                ->with('success', 'Tuntutan berjaya diluluskan');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ralat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a claim with reason
     */
    public function reject(Request $request, Tuntutan $tuntutan)
    {
        // Check permission
        if (!Auth::user()->adaKebenaran('laporan_tuntutan', 'tolak')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak mempunyai kebenaran untuk menolak tuntutan'
            ], 403);
        }

        // Validate status
        if (!$tuntutan->canBeRejected()) {
            return response()->json([
                'success' => false,
                'message' => 'Tuntutan ini tidak boleh ditolak (status semasa: ' . $tuntutan->status_label . ')'
            ], 422);
        }

        // Validate reason
        $request->validate([
            'alasan_tolak' => 'required|string|min:10|max:1000',
        ], [
            'alasan_tolak.required' => 'Alasan penolakan wajib diisi',
            'alasan_tolak.min' => 'Alasan penolakan mestilah sekurang-kurangnya 10 aksara',
        ]);

        try {
            DB::beginTransaction();

            $tuntutan->update([
                'status' => 'ditolak',
                'alasan_tolak' => $request->alasan_tolak,
                'diproses_oleh' => Auth::id(),
                'tarikh_diproses' => now(),
            ]);

            // Send notification to driver
            $driverId = $tuntutan->logPemandu->pemandu_id;
            
            // Create database notification (for bell count)
            \App\Models\Notification::create([
                'user_id' => $driverId,
                'type' => 'claim_rejected',
                'title' => 'Tuntutan Ditolak',
                'message' => "Tuntutan anda sebanyak RM {$tuntutan->jumlah} untuk {$tuntutan->kategori_label} telah ditolak. Sila semak alasan dan kemukakan semula.",
                'data' => [
                    'claim_id' => $tuntutan->id,
                    'amount' => $tuntutan->jumlah,
                    'category' => $tuntutan->kategori,
                    'reason' => $request->alasan_tolak,
                ],
                'action_url' => "/laporan/laporan-tuntutan/{$tuntutan->id}",
            ]);

            // Send FCM push notification
            $firebaseService = app(FirebaseService::class);
            $firebaseService->sendToUser(
                $driverId,
                'Tuntutan Ditolak',
                "Tuntutan anda sebanyak RM {$tuntutan->jumlah} untuk {$tuntutan->kategori_label} telah ditolak. Sila semak alasan dan kemukakan semula.",
                [
                    'type' => 'claim_rejected',
                    'claim_id' => $tuntutan->id,
                    'amount' => $tuntutan->jumlah,
                    'category' => $tuntutan->kategori,
                    'reason' => $request->alasan_tolak,
                ]
            );

            // Log activity (tuntutan)
            activity('tuntutan')
                ->performedOn($tuntutan)
                ->causedBy(Auth::user())
                ->withProperties([
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'claim_id' => $tuntutan->id,
                    'amount' => $tuntutan->jumlah,
                    'category' => $tuntutan->kategori_label,
                    'driver_id' => $driverId,
                    'driver_name' => ($tuntutan->logPemandu->pemandu->risdaStaf->nama_penuh ?? $tuntutan->logPemandu->pemandu->name) ?? 'N/A',
                    'program_name' => $tuntutan->logPemandu->program->nama_program ?? 'N/A',
                    'old_status' => 'pending',
                    'new_status' => 'ditolak',
                    'rejection_reason' => $request->alasan_tolak,
                ])
                ->event('rejected')
                ->log("Tuntutan {$tuntutan->kategori_label} sebanyak RM {$tuntutan->jumlah} telah ditolak");

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tuntutan berjaya ditolak. Pemandu boleh mengedit dan menghantar semula.'
                ]);
            }

            return redirect()->route('laporan.laporan-tuntutan')
                ->with('success', 'Tuntutan berjaya ditolak. Pemandu boleh mengedit dan menghantar semula.');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ralat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a claim permanently with reason
     */
    public function cancel(Request $request, Tuntutan $tuntutan)
    {
        // Check permission
        if (!Auth::user()->adaKebenaran('laporan_tuntutan', 'gantung')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak mempunyai kebenaran untuk menggantung tuntutan'
            ], 403);
        }

        // Validate status
        if (!$tuntutan->canBeCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'Tuntutan ini tidak boleh digantung (status semasa: ' . $tuntutan->status_label . ')'
            ], 422);
        }

        // Validate reason
        $request->validate([
            'alasan_gantung' => 'required|string|min:10|max:1000',
        ], [
            'alasan_gantung.required' => 'Alasan pembatalan wajib diisi',
            'alasan_gantung.min' => 'Alasan pembatalan mestilah sekurang-kurangnya 10 aksara',
        ]);

        try {
            DB::beginTransaction();

            $tuntutan->update([
                'status' => 'digantung',
                'alasan_gantung' => $request->alasan_gantung,
                'diproses_oleh' => Auth::id(),
                'tarikh_diproses' => now(),
            ]);

            // Send notification to driver
            $driverId = $tuntutan->logPemandu->pemandu_id;
            
            // Create database notification (for bell count)
            \App\Models\Notification::create([
                'user_id' => $driverId,
                'type' => 'claim_cancelled',
                'title' => 'Tuntutan Dibatalkan',
                'message' => "Tuntutan anda sebanyak RM {$tuntutan->jumlah} untuk {$tuntutan->kategori_label} telah dibatalkan.",
                'data' => [
                    'claim_id' => $tuntutan->id,
                    'amount' => $tuntutan->jumlah,
                    'category' => $tuntutan->kategori,
                    'reason' => $request->alasan_gantung,
                ],
                'action_url' => "/laporan/laporan-tuntutan/{$tuntutan->id}",
            ]);

            // Send FCM push notification
            $firebaseService = app(FirebaseService::class);
            $firebaseService->sendToUser(
                $driverId,
                'Tuntutan Dibatalkan',
                "Tuntutan anda sebanyak RM {$tuntutan->jumlah} untuk {$tuntutan->kategori_label} telah dibatalkan.",
                [
                    'type' => 'claim_cancelled',
                    'claim_id' => $tuntutan->id,
                    'amount' => $tuntutan->jumlah,
                    'category' => $tuntutan->kategori,
                    'reason' => $request->alasan_gantung,
                ]
            );

            // Log activity (tuntutan)
            activity('tuntutan')
                ->performedOn($tuntutan)
                ->causedBy(Auth::user())
                ->withProperties([
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'claim_id' => $tuntutan->id,
                    'amount' => $tuntutan->jumlah,
                    'category' => $tuntutan->kategori_label,
                    'driver_id' => $driverId,
                    'driver_name' => ($tuntutan->logPemandu->pemandu->risdaStaf->nama_penuh ?? $tuntutan->logPemandu->pemandu->name) ?? 'N/A',
                    'program_name' => $tuntutan->logPemandu->program->nama_program ?? 'N/A',
                    'old_status' => $tuntutan->getOriginal('status'),
                    'new_status' => 'digantung',
                    'cancellation_reason' => $request->alasan_gantung,
                ])
                ->event('cancelled')
                ->log("Tuntutan {$tuntutan->kategori_label} sebanyak RM {$tuntutan->jumlah} telah digantung");

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tuntutan berjaya digantung. Pemandu tidak boleh mengedit tuntutan ini.'
                ]);
            }

            return redirect()->route('laporan.laporan-tuntutan')
                ->with('success', 'Tuntutan berjaya digantung. Pemandu tidak boleh mengedit tuntutan ini.');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ralat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Soft delete a claim
     */
    public function destroy(Request $request, Tuntutan $tuntutan)
    {
        // Check permission
        if (!Auth::user()->adaKebenaran('laporan_tuntutan', 'padam')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak mempunyai kebenaran untuk memadam tuntutan'
            ], 403);
        }

        try {
            // Store data for logging before deletion
            $claimId = $tuntutan->id;
            $claimAmount = $tuntutan->jumlah;
            $claimCategory = $tuntutan->kategori_label;
            $claimStatus = $tuntutan->status;
            $driverId = $tuntutan->logPemandu->pemandu_id ?? null;
            $driverName = ($tuntutan->logPemandu->pemandu->risdaStaf->nama_penuh ?? $tuntutan->logPemandu->pemandu->name) ?? 'N/A';
            $programName = $tuntutan->logPemandu->program->nama_program ?? 'N/A';

            $tuntutan->delete(); // Soft delete

            // Log activity (tuntutan)
            activity('tuntutan')
                ->causedBy(Auth::user())
                ->withProperties([
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'claim_id' => $claimId,
                    'amount' => $claimAmount,
                    'category' => $claimCategory,
                    'status' => $claimStatus,
                    'driver_id' => $driverId,
                    'driver_name' => $driverName,
                    'program_name' => $programName,
                    'delete_code' => $request->input('delete_code'),
                ])
                ->event('deleted')
                ->log("Tuntutan {$claimCategory} sebanyak RM {$claimAmount} telah dipadam");

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tuntutan berjaya dipadam'
                ]);
            }

            return redirect()->route('laporan.laporan-tuntutan')
                ->with('success', 'Tuntutan berjaya dipadam');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ralat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export claims to PDF
     */
    public function exportPdf(Request $request)
    {
        // Check permission
        if (!Auth::user()->adaKebenaran('laporan_tuntutan', 'lihat')) {
            abort(403, 'Akses dinafikan');
        }

        $query = Tuntutan::with([
            'logPemandu.pemandu.risdaStaf',
            'logPemandu.program',
            'diprosesOleh'
        ])->forCurrentUser();

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('tarikh_dari') && $request->filled('tarikh_hingga')) {
            $query->whereBetween('created_at', [
                $request->tarikh_dari . ' 00:00:00',
                $request->tarikh_hingga . ' 23:59:59',
            ]);
        }

        $tuntutan = $query->orderBy('created_at', 'desc')->get();

        // Calculate totals
        $total_diluluskan = $tuntutan->where('status', 'diluluskan')->sum('jumlah');
        $total_pending = $tuntutan->where('status', 'pending')->sum('jumlah');

        // Log activity (tuntutan)
        $filename = 'laporan-tuntutan-' . now()->format('Y-m-d') . '.pdf';
        activity('tuntutan')
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'filename' => $filename,
                'format' => 'pdf',
                'total_records' => $tuntutan->count(),
                'total_diluluskan' => $total_diluluskan,
                'total_pending' => $total_pending,
                'filters' => [
                    'status' => $request->status ?? 'semua',
                    'kategori' => $request->kategori ?? 'semua',
                    'tarikh_dari' => $request->tarikh_dari ?? null,
                    'tarikh_hingga' => $request->tarikh_hingga ?? null,
                ],
            ])
            ->event('exported')
            ->log("Laporan tuntutan telah dieksport ke PDF ({$tuntutan->count()} rekod)");

        $pdf = PDF::loadView('laporan.laporan-tuntutan-pdf', compact(
            'tuntutan',
            'total_diluluskan',
            'total_pending',
            'request'
        ));

        return $pdf->download($filename);
    }

    /**
     * Get dashboard report data (Tuntutan or Kenderaan)
     */
    public function getDashboardReport(Request $request)
    {
        $jenisLaporan = $request->input('jenis_laporan');

        if ($jenisLaporan === 'tuntutan') {
            return $this->getTuntutanReport($request);
        } elseif ($jenisLaporan === 'kenderaan') {
            return $this->getKenderaanReport($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Jenis laporan tidak sah'
        ], 400);
    }

    /**
     * Get Tuntutan report data
     */
    private function getTuntutanReport(Request $request)
    {
        // Check permission
        if (!Auth::user()->adaKebenaran('laporan_tuntutan', 'lihat')) {
            return response()->json([
                'success' => false,
                'message' => 'Akses dinafikan'
            ], 403);
        }

        $query = Tuntutan::with([
            'logPemandu.pemandu.risdaStaf',
            'logPemandu.program',
            'diprosesOleh.risdaStaf'
        ])->forCurrentUser();

        // Only show approved claims
        $query->where('status', 'diluluskan');

        // Apply date range filter
        if ($request->filled('tarikh_mula') && $request->filled('tarikh_akhir')) {
            $query->whereBetween('created_at', [
                $request->tarikh_mula . ' 00:00:00',
                $request->tarikh_akhir . ' 23:59:59',
            ]);
        }

        // Apply staff filter
        if ($request->filled('staf_id')) {
            $query->whereHas('logPemandu.pemandu', function ($q) use ($request) {
                $q->where('id', $request->staf_id);
            });
        }

        $tuntutan = $query->orderBy('created_at', 'desc')->get();

        // Format data for dashboard display
        $rows = $tuntutan->map(function ($t) {
            $logPemandu = $t->logPemandu;
            $program = $logPemandu ? $logPemandu->program : null;
            $pemandu = $logPemandu ? $logPemandu->pemandu : null;
            $staf = $pemandu ? $pemandu->risdaStaf : null;
            $diprosesOleh = $t->diprosesOleh ? $t->diprosesOleh->risdaStaf : null;

            $tarikhPerjalanan = $logPemandu && $logPemandu->tarikh_perjalanan 
                ? ($logPemandu->tarikh_perjalanan instanceof \Carbon\Carbon ? $logPemandu->tarikh_perjalanan : \Carbon\Carbon::parse($logPemandu->tarikh_perjalanan))
                : \Carbon\Carbon::parse($t->created_at);

            return [
                'tarikh' => $this->formatTarikhMelayu($tarikhPerjalanan),
                'program' => $program ? $program->nama_program : '-',
                'tarikhDituntut' => $this->formatTarikhMelayu(\Carbon\Carbon::parse($t->created_at)) . ', ' . \Carbon\Carbon::parse($t->created_at)->format('g:i A'),
                'jenis' => ucfirst($t->kategori_label),
                'diluluskanOleh' => $diprosesOleh ? $diprosesOleh->nama_penuh : '-',
                'jumlah' => (float) $t->jumlah,
            ];
        });

        $totalAmount = $tuntutan->sum('jumlah');

        return response()->json([
            'success' => true,
            'data' => [
                'rows' => $rows,
                'summary' => [
                    'totalAmount' => (float) $totalAmount,
                    'totalRecords' => $tuntutan->count(),
                ]
            ]
        ]);
    }

    /**
     * Helper to format date in Bahasa Melayu
     */
    private function formatTarikhMelayu($carbon)
    {
        if (!$carbon) return '-';
        
        $bulanMelayu = [
            'January' => 'Januari', 'February' => 'Februari', 'March' => 'Mac', 'April' => 'April',
            'May' => 'Mei', 'June' => 'Jun', 'July' => 'Julai', 'August' => 'Ogos',
            'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Disember'
        ];
        
        $formatted = $carbon->format('j F Y');
        foreach ($bulanMelayu as $eng => $bm) {
            $formatted = str_replace($eng, $bm, $formatted);
        }
        return $formatted;
    }

    /**
     * Get Kenderaan report data
     */
    private function getKenderaanReport(Request $request)
    {
        // Check permission
        if (!Auth::user()->adaKebenaran('laporan_kenderaan', 'lihat')) {
            return response()->json([
                'success' => false,
                'message' => 'Akses dinafikan'
            ], 403);
        }

        $kenderaanId = $request->input('kenderaan_id');
        if (!$kenderaanId) {
            return response()->json([
                'success' => false,
                'message' => 'Sila pilih kenderaan'
            ], 400);
        }

        // Get vehicle details
        $kenderaan = \App\Models\Kenderaan::find($kenderaanId);
        if (!$kenderaan) {
            return response()->json([
                'success' => false,
                'message' => 'Kenderaan tidak dijumpai'
            ], 404);
        }

        // Get logs for this vehicle
        $query = LogPemandu::with([
            'pemandu.risdaStaf',
            'program',
            'kenderaan'
        ])->where('kenderaan_id', $kenderaanId);

        // Apply date range filter
        if ($request->filled('tarikh_mula') && $request->filled('tarikh_akhir')) {
            $query->whereBetween('tarikh_perjalanan', [
                $request->tarikh_mula,
                $request->tarikh_akhir,
            ]);
        }

        // Only completed journeys
        $query->where('status', 'selesai');

        $logs = $query->orderBy('tarikh_perjalanan', 'desc')->get();

        // Format data for dashboard display
        $rows = $logs->map(function ($log) {
            $pemandu = $log->pemandu;
            $staf = $pemandu ? $pemandu->risdaStaf : null;
            $program = $log->program;

            // Tarikh sahaja (tanpa masa)
            $tarikh = $log->tarikh_perjalanan instanceof \Carbon\Carbon 
                ? $log->tarikh_perjalanan 
                : \Carbon\Carbon::parse($log->tarikh_perjalanan);

            // Masa check-in dan check-out
            $masaCheckin = $log->masa_keluar 
                ? \Carbon\Carbon::parse($log->masa_keluar)->format('g:i A')
                : '-';
            $masaCheckout = $log->masa_masuk 
                ? \Carbon\Carbon::parse($log->masa_masuk)->format('g:i A')
                : '-';

            return [
                'tarikhMasa' => $this->formatTarikhMelayu($tarikh),
                'pemandu' => $staf ? $staf->nama_penuh : ($pemandu ? $pemandu->name : '-'),
                'program' => $program ? $program->nama_program : '-',
                'daftarMasukLat' => $log->lokasi_checkin_lat ?? '0',
                'daftarMasukLong' => $log->lokasi_checkin_long ?? '0',
                'daftarMasukMasa' => $masaCheckin,
                'daftarKeluarLat' => $log->lokasi_checkout_lat ?? '0',
                'daftarKeluarLong' => $log->lokasi_checkout_long ?? '0',
                'daftarKeluarMasa' => $masaCheckout,
                'jarak' => (float) ($log->jarak ?? 0),
            ];
        });

        $totalDistance = $logs->sum('jarak');

        return response()->json([
            'success' => true,
            'data' => [
                'vehicle' => [
                    'noPlat' => $kenderaan->no_plat,
                    'jenama' => $kenderaan->jenama . ' ' . $kenderaan->model,
                    'noEnjin' => $kenderaan->no_enjin ?? '-',
                    'noCasis' => $kenderaan->no_casis ?? '-',
                    'cukaiTamat' => $kenderaan->cukai_tamat_tempoh 
                        ? $this->formatTarikhMelayu($kenderaan->cukai_tamat_tempoh)
                        : '-',
                ],
                'rows' => $rows,
                'summary' => [
                    'totalDistance' => (float) $totalDistance,
                    'totalRecords' => $logs->count(),
                ]
            ]
        ]);
    }
}
