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
     * Get dashboard report data (Tuntutan, Kenderaan, or OT)
     */
    public function getDashboardReport(Request $request)
    {
        // Check permission for generating reports
        if (!Auth::user()->adaKebenaran('dashboard', 'jana')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak mempunyai kebenaran untuk menjana laporan'
            ], 403);
        }

        $jenisLaporan = $request->input('jenis_laporan');

        if ($jenisLaporan === 'tuntutan') {
            return $this->getTuntutanReport($request);
        } elseif ($jenisLaporan === 'kenderaan') {
            return $this->getKenderaanReport($request);
        } elseif ($jenisLaporan === 'penggunaan_kenderaan') {
            return $this->getPenggunaanKenderaanReport($request);
        } elseif ($jenisLaporan === 'ot') {
            return $this->getOTReport($request);
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

        // Get vehicle details (with relations for bahagian/stesen)
        $kenderaan = \App\Models\Kenderaan::with(['bahagian', 'stesen'])->find($kenderaanId);
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

    /**
     * Get OT (Kerja Lebih Masa) report data
     */
    private function getOTReport(Request $request)
    {
        try {
            $user = Auth::user();

            // Get negeri for holiday detection
            // Priority: selected staf's negeri (pemandu), then current viewer
            if ($request->filled('staf_id')) {
                $negeri = $this->getNegeriByPemanduId($request->staf_id) ?: $this->getUserNegeri($user);
            } else {
                $negeri = $this->getUserNegeri($user);
            }

            // Get holidays for this negeri
            $year = $request->filled('tarikh_mula') 
                ? \Carbon\Carbon::parse($request->tarikh_mula)->year 
                : date('Y');

            $cutiUmumList = $this->getCutiUmumList($negeri, $year);

            // Get logs for OT calculation
            $query = LogPemandu::with([
                'pemandu.risdaStaf',
                'program',
            ]);

            // Apply user scope (data isolation)
            if ($user->jenis_organisasi === 'stesen') {
                $query->whereHas('pemandu', function ($q) use ($user) {
                    $q->where('organisasi_id', $user->organisasi_id)
                      ->where('jenis_organisasi', 'stesen');
                });
            } elseif ($user->jenis_organisasi === 'bahagian') {
                $stesenIds = \App\Models\RisdaStesen::where('risda_bahagian_id', $user->organisasi_id)->pluck('id');
                if ($stesenIds->isNotEmpty()) {
                    $query->whereHas('pemandu', function ($q) use ($stesenIds, $user) {
                        $q->where(function($inner) use ($stesenIds, $user) {
                            // Pemandu yang ada di stesen-stesen bawah bahagian ini
                            $inner->whereIn('organisasi_id', $stesenIds)
                                  ->where('jenis_organisasi', 'stesen');
                        })->orWhere(function($inner) use ($user) {
                            // Atau pemandu yang terus di bawah bahagian
                            $inner->where('organisasi_id', $user->organisasi_id)
                                  ->where('jenis_organisasi', 'bahagian');
                        });
                    });
                } else {
                    // Tiada stesen, cari pemandu terus di bawah bahagian
                    $query->whereHas('pemandu', function ($q) use ($user) {
                        $q->where('organisasi_id', $user->organisasi_id)
                          ->where('jenis_organisasi', 'bahagian');
                    });
                }
            }
            // Admin (jenis_organisasi = 'semua') - no filter, see all

        // Filter by date range (date-only to avoid time issues)
        if ($request->filled('tarikh_mula') && $request->filled('tarikh_akhir')) {
            $start = $request->tarikh_mula;
            $end = $request->tarikh_akhir;
            $query->where(function ($q) use ($start, $end) {
                $q->whereDate('tarikh_perjalanan', '>=', $start)
                  ->whereDate('tarikh_perjalanan', '<=', $end)
                  // Fallback: include legacy logs where tarikh_perjalanan might be null but created_at is within range
                  ->orWhere(function ($qq) use ($start, $end) {
                      $qq->whereNull('tarikh_perjalanan')
                         ->whereBetween('created_at', [
                             $start . ' 00:00:00',
                             $end . ' 23:59:59',
                         ]);
                  });
            });
        }

        // Filter by staff
        if ($request->filled('staf_id')) {
            $query->where('pemandu_id', $request->staf_id);
        }

        // Only completed journeys
        $query->where('status', 'selesai');

        $logs = $query->orderBy('tarikh_perjalanan', 'desc')->get();

        // Helper to compute one OT row
        $computeRow = function ($log) use ($cutiUmumList) {
            $pemandu = $log->pemandu;
            $staf = $pemandu ? $pemandu->risdaStaf : null;
            $program = $log->program;

            $tarikh = $log->tarikh_perjalanan instanceof \Carbon\Carbon 
                ? $log->tarikh_perjalanan 
                : \Carbon\Carbon::parse($log->tarikh_perjalanan);

            // Bind time-only fields to the same date as tarikh_perjalanan to avoid cross-day/timezone issues
            $masaKeluar = null;
            $masaMasuk = null;
            if ($log->masa_keluar) {
                $keluarRaw = method_exists($log, 'getRawOriginal') ? $log->getRawOriginal('masa_keluar') : (string) $log->masa_keluar;
                $formatKeluar = strlen($keluarRaw) === 5 ? 'H:i' : 'H:i:s';
                $masaKeluar = \Carbon\Carbon::createFromFormat($formatKeluar, $keluarRaw, config('app.timezone', 'Asia/Kuala_Lumpur'))
                    ->setDate($tarikh->year, $tarikh->month, $tarikh->day);
            }
            if ($log->masa_masuk) {
                $masukRaw = method_exists($log, 'getRawOriginal') ? $log->getRawOriginal('masa_masuk') : (string) $log->masa_masuk;
                $formatMasuk = strlen($masukRaw) === 5 ? 'H:i' : 'H:i:s';
                $masaMasuk = \Carbon\Carbon::createFromFormat($formatMasuk, $masukRaw, config('app.timezone', 'Asia/Kuala_Lumpur'))
                    ->setDate($tarikh->year, $tarikh->month, $tarikh->day);
            }

            if (!$masaKeluar || !$masaMasuk) {
                return null;
            }

            // Determine day type and multiplier
            $tarikhStr = $tarikh->format('Y-m-d');
            $isCutiUmum = in_array($tarikhStr, $cutiUmumList);
            $isWeekend = $tarikh->isWeekend();

            if ($isCutiUmum) {
                $jenisHari = 'cuti_umum';
                $multiplier = 3.0;
                $bgColor = 'bg-red-50';
                $allHoursAreOT = true;
            } elseif ($isWeekend) {
                $jenisHari = 'hujung_minggu';
                $multiplier = 2.0;
                $bgColor = 'bg-yellow-50';
                $allHoursAreOT = true;
            } else {
                $jenisHari = 'hari_bekerja';
                $multiplier = 1.5;
                $bgColor = '';
                $allHoursAreOT = false;
            }

            // Normalize possible cross-midnight (checkout earlier than checkin)
            if ($masaMasuk && $masaKeluar && $masaMasuk->lessThan($masaKeluar)) {
                $masaMasuk = $masaMasuk->copy()->addDay();
            }

            // Calculate OT hours
            if ($allHoursAreOT) {
                // Weekends/Public Holidays: all hours are OT
                $otMinutes = $masaMasuk->diffInMinutes($masaKeluar, true);
            } else {
                // Weekdays: only time AFTER 17:00 counts as OT.
                // Compute overlap between [masaKeluar, masaMasuk] and [17:00, end-of-day]
                $startOfOT = \Carbon\Carbon::parse($tarikhStr . ' 17:00:00');
                // If checkout is before or at 17:00, no OT
                if ($masaMasuk->lessThanOrEqualTo($startOfOT)) {
                    return null;
                }

                // OT starts at the later of check-in or 17:00
                $otStart = $masaKeluar->greaterThan($startOfOT) ? $masaKeluar : $startOfOT;
                // If the computed OT start is not before checkout, no OT
                if ($otStart->greaterThanOrEqualTo($masaMasuk)) {
                    return null;
                }

                $otMinutes = $otStart->diffInMinutes($masaMasuk, true);
            }

            if ($otMinutes <= 0) {
                return null;
            }

            $otHours = floor($otMinutes / 60);
            $otMins = $otMinutes % 60;
            $jamText = $otHours . 'jam ' . $otMins . 'min';

            return [
                'tarikh' => $this->formatTarikhMelayu($tarikh),
                'program' => $program ? $program->nama_program : '-',
                'mula' => $masaKeluar->format('g:i A'),
                'tamat' => $masaMasuk->format('g:i A'),
                'jamText' => $jamText,
                'jam' => round($otMinutes / 60, 2),
                'multiplier' => $multiplier,
                'jenisHari' => $jenisHari,
                'bgColor' => $bgColor,
            ];
        };

        // If specific staff selected -> single-table mode
        if ($request->filled('staf_id')) {
            $rows = $logs->map($computeRow)->filter()->values();
            return response()->json([
                'success' => true,
                'data' => [
                    'rows' => $rows,
                    'summary' => [
                        'totalRecords' => $rows->count(),
                    ]
                ]
            ]);
        }

        // Grouped mode for "Semua Staf"
        $groups = $logs->groupBy('pemandu_id')->map(function ($group) use ($computeRow) {
            $first = $group->first();
            $user = $first ? $first->pemandu : null;
            $staf = $user ? $user->risdaStaf : null;
            $rows = $group->map($computeRow)->filter()->values();
            return [
                'profile' => [
                    'namaPenuh' => $staf ? $staf->nama_penuh : ($user ? $user->name : '-'),
                    'noPekerja' => $staf ? ($staf->no_pekerja ?? '-') : '-',
                    'ic' => $staf ? ($staf->no_kad_pengenalan ?? '-') : '-',
                    'tel' => $staf ? ($staf->no_telefon ?? '-') : '-',
                ],
                'rows' => $rows,
                'summary' => [
                    'totalRecords' => $rows->count(),
                ],
            ];
        })->values();

        // Also provide flattened rows for backward-compatible single-table UI
        $allRows = $groups->flatMap(function ($g) {
            return collect($g['rows']);
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'groups' => $groups,
                'rows' => $allRows,
                'summary' => [
                    'totalRecords' => $allRows->count(),
                ],
            ]
        ]);
        } catch (\Exception $e) {
            \Log::error('OT Report Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'jenis_organisasi' => Auth::user()->jenis_organisasi ?? 'unknown',
                'organisasi_id' => Auth::user()->organisasi_id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ralat menjana laporan OT: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get negeri for user based on their stesen/bahagian
     */
    private function getUserNegeri($user)
    {
        if ($user->jenis_organisasi === 'stesen') {
            $stesen = \App\Models\RisdaStesen::find($user->organisasi_id);
            $bahagian = $stesen ? $stesen->bahagian : null;
        } elseif ($user->jenis_organisasi === 'bahagian') {
            $bahagian = \App\Models\RisdaBahagian::find($user->organisasi_id);
        } else {
            return 'Selangor';
        }

        return $bahagian ? $bahagian->negeri : 'Selangor';
    }

    /**
     * Get negeri by pemandu (user) id
     */
    private function getNegeriByPemanduId($pemanduId)
    {
        $user = \App\Models\User::find($pemanduId);
        if (!$user) return null;

        if ($user->jenis_organisasi === 'stesen') {
            $stesen = \App\Models\RisdaStesen::find($user->organisasi_id);
            $bahagian = $stesen ? $stesen->bahagian : null;
        } elseif ($user->jenis_organisasi === 'bahagian') {
            $bahagian = \App\Models\RisdaBahagian::find($user->organisasi_id);
        } else {
            return null;
        }
        return $bahagian ? $bahagian->negeri : null;
    }

    /**
     * Get list of cuti umum dates for a negeri and year
     */
    private function getCutiUmumList($negeri, $year)
    {
        // Get from package
        try {
            $packageHolidays = \Holiday\MalaysiaHoliday::make()
                ->fromState($negeri)
                ->ofYear($year)
                ->get();

            $cutiList = collect($packageHolidays['data'][0]['collection'][0]['data'])
                ->where('is_holiday', true)
                ->pluck('date')
                ->toArray();
        } catch (\Exception $e) {
            $cutiList = [];
        }

        // Add manual overrides
        $manualCuti = \App\Models\CutiUmumOverride::aktif()
            ->forYear($year)
            ->forNegeri($negeri)
            ->get()
            ->flatMap(function ($c) {
                $dates = [];
                $current = $c->tarikh_mula instanceof \Carbon\Carbon ? $c->tarikh_mula : \Carbon\Carbon::parse($c->tarikh_mula);
                $end = $c->tarikh_akhir instanceof \Carbon\Carbon ? $c->tarikh_akhir : \Carbon\Carbon::parse($c->tarikh_akhir);
                
                while ($current <= $end) {
                    $dates[] = $current->format('Y-m-d');
                    $current->addDay();
                }
                return $dates;
            })
            ->toArray();

        return array_unique(array_merge($cutiList, $manualCuti));
    }

    /**
     * Get Penggunaan Kenderaan report data (detailed monthly usage)
     */
    private function getPenggunaanKenderaanReport(Request $request)
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
            'program.pemohon', // Pelulus (pemohon program is User model)
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

        $logs = $query->orderBy('tarikh_perjalanan', 'asc')->get();

        // Format data for display
        $rows = $logs->map(function ($log) {
            $pemandu = $log->pemandu;
            $staf = $pemandu ? $pemandu->risdaStaf : null;
            $program = $log->program;
            $pemohonUser = $program ? $program->pemohon : null;
            $pelulus = $pemohonUser ? $pemohonUser->risdaStaf : null;

            $tarikh = $log->tarikh_perjalanan instanceof \Carbon\Carbon 
                ? $log->tarikh_perjalanan 
                : \Carbon\Carbon::parse($log->tarikh_perjalanan);

            // Time formatting
            $masaMulai = $log->masa_keluar;
            $masaHingga = $log->masa_masuk;

            // Format fuel data - No. Resit, RM, Liter
            // For Penggunaan Kenderaan, ONLY show explicit no_resit; do not fallback to filename
            $resitNo = $log->no_resit ?: '-';
            
            $resitRM = $log->kos_minyak ? number_format($log->kos_minyak, 2) : '-';

            return [
                'tarikh' => $tarikh->format('j.n.Y'),
                'masaMulai' => $masaMulai ? \Carbon\Carbon::parse($masaMulai)->format('g:i A') : '-',
                'masaHingga' => $masaHingga ? \Carbon\Carbon::parse($masaHingga)->format('g:i A') : '-',
                'pemandu' => $staf ? $staf->nama_penuh : ($pemandu ? $pemandu->name : '-'),
                'tujuan' => $program ? $program->nama_program : '-',
                // Tujuan & Destinasi (dari — ke): ambil dari lokasi_mula_perjalanan dan lokasi_tamat_perjalanan
                'destinasiDari' => $log->lokasi_mula_perjalanan
                    ?? ($program ? ($program->lokasi_program ?? '-') : '-'),
                'destinasiKe' => $log->lokasi_tamat_perjalanan
                    ?? ($program ? ($program->lokasi_program ?? '-') : '-'),
                'pelulus' => $pelulus ? $pelulus->nama_penuh : ($pemohonUser ? $pemohonUser->name : '-'),
                'pengguna' => $staf ? $staf->nama_penuh : ($pemandu ? $pemandu->name : '-'),
                'odometerKeluar' => $log->odometer_keluar ? number_format($log->odometer_keluar, 0) : '-',
                'odometerMasuk' => $log->odometer_masuk ? number_format($log->odometer_masuk, 0) : '-',
                'jarakPerjalanan' => $log->jarak ? number_format($log->jarak, 0) : '-',
                'resitNo' => $resitNo,
                'resitRM' => $resitRM,
                'liter' => $log->liter_minyak ? number_format($log->liter_minyak, 2) : '-',
                'arahanKhas' => ($program && $program->arahan_khas_pengguna_kenderaan) ? $program->arahan_khas_pengguna_kenderaan : ($log->catatan ?? '-'),
                // Raw values for calculation
                'jarak_raw' => (float) ($log->jarak ?? 0),
                'liter_raw' => (float) ($log->liter_minyak ?? 0),
                'kos_raw' => (float) ($log->kos_minyak ?? 0),
            ];
        });

        // Calculate summary
        $totalJarak = $logs->sum('jarak');
        $totalLiter = $logs->sum('liter_minyak');
        $totalKos = $logs->sum('kos_minyak');
        $kadarPenggunaan = $totalLiter > 0 ? round($totalJarak / $totalLiter, 2) : 0;

        // Get month from date range
        $bulan = $request->filled('tarikh_mula') 
            ? \Carbon\Carbon::parse($request->tarikh_mula)->format('n') 
            : date('n');

        return response()->json([
            'success' => true,
            'data' => [
                'vehicle' => [
                    // No. Pendaftaran
                    'noPlat' => $kenderaan->no_plat,
                    // Jenis Kenderaan (gabungan Jenama + Model)
                    'jenis' => trim(((string) ($kenderaan->jenama ?? '')) . ' ' . ((string) ($kenderaan->model ?? ''))) ?: ((string) ($kenderaan->jenama ?? '-')),
                    // Bahagian/Unit: utamakan stesen jika wujud, jika tidak bahagian, selain itu '-'
                    'bahagian' => $kenderaan->stesen
                        ? ('Stesen: ' . ($kenderaan->stesen->nama_stesen ?? '-'))
                        : ($kenderaan->bahagian
                            ? ('Bahagian: ' . ($kenderaan->bahagian->nama_bahagian ?? '-'))
                            : '-'),
                ],
                'rows' => $rows,
                'summary' => [
                    'bulan' => $bulan,
                    'totalJarak' => (float) $totalJarak,
                    'totalLiter' => (float) $totalLiter,
                    'totalKos' => (float) $totalKos,
                    'kadarPenggunaan' => (float) $kadarPenggunaan,
                    'totalRecords' => $logs->count(),
                    'disahkanOleh' => [
                        'nama' => 'Ahmad Bin Abdullah',
                        'jawatan' => 'Pengurus Bahagian',
                    ],
                ]
            ]
        ]);
    }
}
