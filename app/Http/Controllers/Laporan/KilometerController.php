<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\LogPemandu;
use App\Models\Program;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class KilometerController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $allowedStatuses = $this->eligibleProgramStatuses();

        $programQuery = Program::query()
            ->with(['pemohon', 'pemandu', 'kenderaan'])
            ->whereIn('status', $allowedStatuses)
            ->whereHas('logPemandu', function ($logQuery) use ($user, $allowedStatuses) {
                $this->applyLogScope($logQuery, $user);
                $this->applyEligibleProgramFilterToLog($logQuery, $user, $allowedStatuses);
            });
        $this->applyProgramScope($programQuery, $user);

        $this->applyProgramFilters($programQuery, $request, $allowedStatuses);

        $programs = (clone $programQuery)
            ->orderByDesc('tarikh_mula')
            ->paginate(5)
            ->withQueryString();

        $programIds = $programs->isEmpty() ? collect() : $programs->getCollection()->pluck('id');

        $logQuery = LogPemandu::query();
        $this->applyLogScope($logQuery, $user);
        $this->applyLogFilters($logQuery, $request);
        $this->applyEligibleProgramFilterToLog($logQuery, $user, $allowedStatuses);

        if ($programIds->isNotEmpty()) {
            $logQuery->whereIn('program_id', $programIds);
        }

        $overallLogQuery = LogPemandu::query();
        $this->applyLogScope($overallLogQuery, $user);
        $this->applyLogFilters($overallLogQuery, $request);
        $this->applyEligibleProgramFilterToLog($overallLogQuery, $user, $allowedStatuses);

        $overallStats = [
            'total_program' => (clone $programQuery)->count(),
            'total_log' => (clone $overallLogQuery)->count(),
            'jumlah_jarak' => (float) (clone $overallLogQuery)->sum('jarak'),
            'purata_jarak_log' => $this->calculateAverageDistance($overallLogQuery),
            'jumlah_kos' => (float) (clone $overallLogQuery)->sum('kos_minyak'),
            'jumlah_checkin' => (clone $overallLogQuery)->whereNotNull('masa_keluar')->count(),
            'jumlah_checkout' => (clone $overallLogQuery)->whereNotNull('masa_masuk')->count(),
        ];

        $logAggregates = (clone $logQuery)
            ->selectRaw('
                program_id,
                COUNT(*) AS jumlah_log,
                SUM(jarak) AS jumlah_jarak,
                SUM(kos_minyak) AS jumlah_kos,
                SUM(CASE WHEN masa_keluar IS NOT NULL THEN 1 ELSE 0 END) AS jumlah_checkin,
                SUM(CASE WHEN masa_masuk IS NOT NULL THEN 1 ELSE 0 END) AS jumlah_checkout
            ')
            ->groupBy('program_id')
            ->get()
            ->keyBy('program_id');

        $programData = $programs->getCollection()->map(function (Program $program) use ($logAggregates) {
            $stats = $logAggregates->get($program->id);

            $jarakDirekod = (float) ($stats->jumlah_jarak ?? 0);
            $anggaran = (float) ($program->jarak_anggaran ?? 0);

            return [
                'id' => $program->id,
                'jumlah_log' => $stats->jumlah_log ?? 0,
                'jumlah_jarak' => $jarakDirekod,
                'jarak_anggaran' => $anggaran,
                'perbezaan_jarak' => $jarakDirekod && $anggaran ? $jarakDirekod - $anggaran : $jarakDirekod,
                'jumlah_kos' => (float) ($stats->jumlah_kos ?? 0),
                'jumlah_checkin' => $stats->jumlah_checkin ?? 0,
                'jumlah_checkout' => $stats->jumlah_checkout ?? 0,
            ];
        })->keyBy('id');

        return view('laporan.laporan-kilometer', [
            'programs' => $programs,
            'programData' => $programData,
            'overallStats' => $overallStats,
        ]);
    }

    public function show(Request $request, Program $program)
    {
        $user = $request->user();
        $this->ensureProgramAccessible($program, $user);

        $logQuery = $program->logPemandu()->with(['pemandu', 'kenderaan']);
        $this->applyLogScope($logQuery, $user);
        $this->applyLogFilters($logQuery, $request);
        $this->applyEligibleProgramFilterToLog($logQuery, $user, $this->eligibleProgramStatuses());

        $logs = $logQuery
            ->orderByDesc('tarikh_perjalanan')
            ->orderByDesc('masa_keluar')
            ->get();

        $stats = [
            'jumlah_log' => $logs->count(),
            'jumlah_jarak' => (float) $logs->sum('jarak'),
            'jarak_anggaran' => (float) ($program->jarak_anggaran ?? 0),
            'jumlah_kos' => (float) $logs->sum('kos_minyak'),
            'jumlah_checkin' => $logs->whereNotNull('masa_keluar')->count(),
            'jumlah_checkout' => $logs->whereNotNull('masa_masuk')->count(),
        ];

        $pemanduSummary = $logs
            ->filter(fn ($log) => $log->pemandu)
            ->groupBy('pemandu_id')
            ->map(function (Collection $items) {
                $first = $items->first();

                return [
                    'nama' => $first->pemandu->name ?? '-',
                    'jumlah_log' => $items->count(),
                    'jumlah_jarak' => (float) $items->sum('jarak'),
                ];
            })
            ->values();

        $kenderaanSummary = $logs
            ->filter(fn ($log) => $log->kenderaan)
            ->groupBy('kenderaan_id')
            ->map(function (Collection $items) {
                $first = $items->first();

                return [
                    'no_plat' => $first->kenderaan->no_plat ?? '-',
                    'nama' => trim(($first->kenderaan->jenama ?? '') . ' ' . ($first->kenderaan->model ?? '')) ?: '-',
                    'jumlah_log' => $items->count(),
                    'jumlah_jarak' => (float) $items->sum('jarak'),
                ];
            })
            ->values();

        return view('laporan.laporan-kilometer-show', [
            'program' => $program->load(['pemohon', 'pemandu', 'kenderaan']),
            'logs' => $logs,
            'stats' => $stats,
            'pemanduSummary' => $pemanduSummary,
            'kenderaanSummary' => $kenderaanSummary,
        ]);
    }

    public function pdf(Request $request, Program $program)
    {
        $user = $request->user();
        $this->ensureProgramAccessible($program, $user);

        $logQuery = $program->logPemandu()->with(['pemandu', 'kenderaan']);
        $this->applyLogScope($logQuery, $user);
        $this->applyLogFilters($logQuery, $request);
        $this->applyEligibleProgramFilterToLog($logQuery, $user, $this->eligibleProgramStatuses());

        $logs = $logQuery
            ->orderByDesc('tarikh_perjalanan')
            ->orderByDesc('masa_keluar')
            ->get();

        $stats = [
            'jumlah_log' => $logs->count(),
            'jumlah_jarak' => (float) $logs->sum('jarak'),
            'jarak_anggaran' => (float) ($program->jarak_anggaran ?? 0),
            'jumlah_kos' => (float) $logs->sum('kos_minyak'),
        ];

        $pdf = Pdf::loadView('laporan.pdf.kilometer', [
            'program' => $program->load(['pemohon', 'pemandu', 'kenderaan']),
            'logs' => $logs,
            'stats' => $stats,
        ])->setPaper('a4', 'portrait');

        $filename = 'laporan-kilometer-' . str($program->nama_program)->slug('-') . '.pdf';

        // Log activity (export kilometer report)
        activity('program')
            ->performedOn($program)
            ->causedBy($user)
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'program_name' => $program->nama_program,
                'filename' => $filename,
                'format' => 'pdf',
            ])
            ->event('exported')
            ->log("Laporan kilometer '{$program->nama_program}' dieksport ke PDF");

        return $pdf->download($filename);
    }

    private function applyProgramFilters($query, Request $request, array $allowedStatuses = []): void
    {
        if ($status = $request->get('status')) {
            if ($allowedStatuses && !in_array($status, $allowedStatuses, true)) {
                $query->whereRaw('0 = 1');
            } else {
                $query->where('status', $status);
            }
        }

        if ($search = $request->get('search')) {
            $query->where(function ($inner) use ($search) {
                $inner->where('nama_program', 'like', "%{$search}%")
                    ->orWhere('lokasi_program', 'like', "%{$search}%")
                    ->orWhereHas('pemohon', function ($sub) use ($search) {
                        $sub->where('nama_penuh', 'like', "%{$search}%");
                    })
                    ->orWhereHas('pemanduStaf', function ($sub) use ($search) {
                        $sub->where('nama_penuh', 'like', "%{$search}%");
                    })
                    ->orWhereHas('kenderaan', function ($sub) use ($search) {
                        $sub->where('no_plat', 'like', "%{$search}%");
                    });
            });
        }

        if ($start = $request->get('tarikh_dari')) {
            $query->whereDate('tarikh_mula', '>=', $start);
        }

        if ($end = $request->get('tarikh_hingga')) {
            $query->whereDate('tarikh_selesai', '<=', $end);
        }
    }

    private function applyLogFilters($query, Request $request): void
    {
        if ($status = $request->get('status_log')) {
            $query->where('status', $status);
        }

        if ($start = $request->get('tarikh_dari')) {
            $query->whereDate('tarikh_perjalanan', '>=', $start);
        }

        if ($end = $request->get('tarikh_hingga')) {
            $query->whereDate('tarikh_perjalanan', '<=', $end);
        }
    }

    private function applyProgramScope($query, ?User $user): void
    {
        if (!$user || $user->jenis_organisasi === 'semua') {
            return;
        }

        if ($user->jenis_organisasi === 'stesen') {
            $query->where('organisasi_id', (string) $user->organisasi_id)
                ->where('jenis_organisasi', 'stesen');

            return;
        }

        if ($user->jenis_organisasi === 'bahagian') {
            $stesenIds = $this->getStesenIdsForBahagian($user->organisasi_id, $user->stesen_akses_ids);

            $query->where(function ($inner) use ($user, $stesenIds) {
                $inner->where(function ($q) use ($user) {
                    $q->where('jenis_organisasi', 'bahagian')
                        ->where('organisasi_id', $user->organisasi_id);
                });

                if ($stesenIds->isNotEmpty()) {
                    $inner->orWhere(function ($q) use ($stesenIds) {
                        $q->where('jenis_organisasi', 'stesen')
                            ->whereIn('organisasi_id', $stesenIds->all());
                    });
                }
            });

            return;
        }

        $query->where('organisasi_id', (string) $user->organisasi_id)
            ->where('jenis_organisasi', $user->jenis_organisasi);
    }

    /**
     * Get stesen IDs for a bahagian. If stesen_akses_ids is empty, returns ALL stesen under bahagian.
     */
    private function getStesenIdsForBahagian($bahagianId, $stesenAksesIds = null)
    {
        $userStesenIds = collect($stesenAksesIds ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter();

        if ($userStesenIds->isNotEmpty()) {
            return $userStesenIds;
        }

        return \App\Models\RisdaStesen::where('risda_bahagian_id', $bahagianId)->pluck('id');
    }

    private function applyLogScope($query, ?User $user): void
    {
        if (!$user || $user->jenis_organisasi === 'semua') {
            return;
        }

        if ($user->jenis_organisasi === 'stesen') {
            $query->where('organisasi_id', (string) $user->organisasi_id);
            return;
        }

        if ($user->jenis_organisasi === 'bahagian') {
            $stesenIds = $this->getStesenIdsForBahagian($user->organisasi_id, $user->stesen_akses_ids);

            $query->where(function ($q) use ($user, $stesenIds) {
                $q->where('organisasi_id', (string) $user->organisasi_id);
                
                if ($stesenIds->isNotEmpty()) {
                    $q->orWhereIn('organisasi_id', $stesenIds->map(fn ($id) => (string) $id)->all());
                }
            });

            return;
        }

        $query->where('organisasi_id', (string) $user->organisasi_id);
    }

    private function eligibleProgramStatuses(): array
    {
        return ['aktif', 'selesai'];
    }

    private function applyEligibleProgramFilterToLog($query, ?User $user, array $allowedStatuses): void
    {
        $query->whereHas('program', function ($programQuery) use ($user, $allowedStatuses) {
            $programQuery->whereIn('status', $allowedStatuses);
            $this->applyProgramScope($programQuery, $user);
        });
    }

    private function ensureProgramAccessible(Program $program, ?User $user): void
    {
        if (!$user) {
            abort(403, 'Sesi pengguna tidak sah.');
        }

        if ($user->jenis_organisasi === 'semua') {
            return;
        }

        if ($user->jenis_organisasi === 'stesen') {
            if ($program->jenis_organisasi !== 'stesen' || (string) $program->organisasi_id !== (string) $user->organisasi_id) {
                abort(403, 'Anda tidak mempunyai kebenaran untuk melihat program ini.');
            }

            return;
        }

        if ($user->jenis_organisasi === 'bahagian') {
            $stesenIds = $this->getStesenIdsForBahagian($user->organisasi_id, $user->stesen_akses_ids);

            $isBahagian = $program->jenis_organisasi === 'bahagian' && (string) $program->organisasi_id === (string) $user->organisasi_id;
            $isStesen = $program->jenis_organisasi === 'stesen' && $stesenIds->contains((int) $program->organisasi_id);

            if (!$isBahagian && !$isStesen) {
                abort(403, 'Anda tidak mempunyai kebenaran untuk melihat program ini.');
            }

            return;
        }

        if ($program->jenis_organisasi !== $user->jenis_organisasi || (string) $program->organisasi_id !== (string) $user->organisasi_id) {
            abort(403, 'Anda tidak mempunyai kebenaran untuk melihat program ini.');
        }
    }

    private function calculateAverageDistance($logQuery): float
    {
        $count = (clone $logQuery)->count();

        if ($count === 0) {
            return 0.0;
        }

        $sum = (clone $logQuery)->sum('jarak');

        return round((float) $sum / $count, 2);
    }
}


