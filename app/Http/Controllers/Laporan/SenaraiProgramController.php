<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\LogPemandu;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;

class SenaraiProgramController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $allowedStatuses = $this->eligibleProgramStatuses();

        $baseProgramQuery = Program::query()
            ->with(['pemohon', 'pemandu', 'kenderaan'])
            ->whereIn('status', $allowedStatuses)
            ->whereHas('logPemandu', function ($logQuery) use ($user) {
                $this->applyLogScope($logQuery, $user);
            });

        $this->applyProgramScope($baseProgramQuery, $user);

        $filteredProgramQuery = clone $baseProgramQuery;

        if ($status = $request->get('status')) {
            if (in_array($status, $allowedStatuses, true)) {
                $filteredProgramQuery->where('status', $status);
            } else {
                $filteredProgramQuery->whereRaw('0 = 1');
            }
        }

        if ($search = $request->get('search')) {
            $filteredProgramQuery->where(function ($q) use ($search) {
                $q->where('nama_program', 'like', "%{$search}%")
                    ->orWhere('lokasi_program', 'like', "%{$search}%")
                    ->orWhereHas('pemohon', function ($sub) use ($search) {
                        $sub->where('nama_penuh', 'like', "%{$search}%");
                    });
            });
        }

        $programs = (clone $filteredProgramQuery)
            ->orderByDesc('created_at')
            ->paginate(5)
            ->withQueryString();

        $programIdsForStats = (clone $filteredProgramQuery)->pluck('id');

        $logQuery = LogPemandu::query();
        $this->applyLogScope($logQuery, $user);
        $this->applyEligibleProgramFilterToLog($logQuery, $user, $allowedStatuses);
        $logQuery->when($programIdsForStats->isNotEmpty(), fn ($query) => $query->whereIn('program_id', $programIdsForStats));

        $overallLogQuery = LogPemandu::query();
        $this->applyLogScope($overallLogQuery, $user);
        $this->applyEligibleProgramFilterToLog($overallLogQuery, $user, $allowedStatuses);

        $overallStats = [
            'total_program' => (clone $baseProgramQuery)->count(),
            'total_pemandu' => (clone $overallLogQuery)->distinct('pemandu_id')->count('pemandu_id'),
            'total_kenderaan' => (clone $overallLogQuery)->distinct('kenderaan_id')->count('kenderaan_id'),
            'total_log' => (clone $overallLogQuery)->count(),
            'total_checkin' => (clone $overallLogQuery)->whereNotNull('masa_keluar')->count(),
            'total_checkout' => (clone $overallLogQuery)->whereNotNull('masa_masuk')->count(),
        ];

        $logAggregates = (clone $logQuery)
            ->selectRaw('
                program_id,
                COUNT(*) AS jumlah_log,
                SUM(CASE WHEN status = "dalam_perjalanan" THEN 1 ELSE 0 END) AS jumlah_aktif,
                SUM(CASE WHEN status = "selesai" THEN 1 ELSE 0 END) AS jumlah_selesai,
                SUM(CASE WHEN status = "tertunda" THEN 1 ELSE 0 END) AS jumlah_tertunda,
                SUM(CASE WHEN masa_keluar IS NOT NULL THEN 1 ELSE 0 END) AS jumlah_checkin,
                SUM(CASE WHEN masa_masuk IS NOT NULL THEN 1 ELSE 0 END) AS jumlah_checkout,
                COUNT(DISTINCT pemandu_id) AS jumlah_pemandu,
                COUNT(DISTINCT kenderaan_id) AS jumlah_kenderaan
            ')
            ->groupBy('program_id')
            ->get()
            ->keyBy('program_id');

        $programData = $programs->getCollection()->map(function (Program $program) use ($logAggregates) {
            $stats = $logAggregates->get($program->id);

            return [
                'id' => $program->id,
                'jumlah_log' => $stats->jumlah_log ?? 0,
                'jumlah_aktif' => $stats->jumlah_aktif ?? 0,
                'jumlah_selesai' => $stats->jumlah_selesai ?? 0,
                'jumlah_tertunda' => $stats->jumlah_tertunda ?? 0,
                'jumlah_checkin' => $stats->jumlah_checkin ?? 0,
                'jumlah_checkout' => $stats->jumlah_checkout ?? 0,
                'jumlah_pemandu' => $stats->jumlah_pemandu ?? 0,
                'jumlah_kenderaan' => $stats->jumlah_kenderaan ?? 0,
            ];
        });

        return view('laporan.senarai-program', [
            'programs' => $programs,
            'programData' => $programData,
            'overallStats' => $overallStats,
        ]);
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

    public function show(Request $request, Program $program)
    {
        $user = $request->user();
        $this->ensureProgramAccessible($program, $user);

        $program->load([
            'pemohon',
            'pemandu',
            'kenderaan',
            'logPemandu' => function ($query) {
                $query->with(['pemandu', 'kenderaan'])
                    ->orderByDesc('masa_keluar')
                    ->orderByDesc('created_at');
            },
        ]);

        $logCollection = $program->logPemandu
            ->filter(fn ($log) => in_array($program->status, $this->eligibleProgramStatuses(), true));

        $stats = [
            'jumlah_log' => $logCollection->count(),
            'jumlah_pemandu' => $logCollection->pluck('pemandu_id')->filter()->unique()->count(),
            'jumlah_kenderaan' => $logCollection->pluck('kenderaan_id')->filter()->unique()->count(),
            'jumlah_checkin' => $logCollection->whereNotNull('masa_keluar')->count(),
            'jumlah_checkout' => $logCollection->whereNotNull('masa_masuk')->count(),
            'jarak_km' => (float) $logCollection->sum('jarak'),
            'kos' => (float) $logCollection->sum('kos_minyak'),
        ];

        $statusSummary = [
            'dalam_perjalanan' => $logCollection->where('status', 'dalam_perjalanan')->count(),
            'selesai' => $logCollection->where('status', 'selesai')->count(),
            'tertunda' => $logCollection->where('status', 'tertunda')->count(),
        ];

        $pemanduSummary = $logCollection
            ->filter(fn ($log) => $log->pemandu)
            ->groupBy('pemandu_id')
            ->map(function (Collection $logs) {
                $log = $logs->first();

                return [
                    'nama' => $log->pemandu->name ?? '-',
                    'jumlah_log' => $logs->count(),
                    'jarak' => (float) $logs->sum('jarak'),
                ];
            })
            ->values();

        $kenderaanSummary = $logCollection
            ->filter(fn ($log) => $log->kenderaan)
            ->groupBy('kenderaan_id')
            ->map(function (Collection $logs) {
                $log = $logs->first();

                return [
                    'no_plat' => $log->kenderaan->no_plat ?? '-',
                    'jenis' => $log->kenderaan->jenama ?? '-',
                    'model' => $log->kenderaan->model ?? '-',
                    'jumlah_log' => $logs->count(),
                    'jarak' => (float) $logs->sum('jarak'),
                ];
            })
            ->values();

        return view('laporan.senarai-program-show', [
            'program' => $program,
            'stats' => $stats,
            'statusSummary' => $statusSummary,
            'pemanduSummary' => $pemanduSummary,
            'kenderaanSummary' => $kenderaanSummary,
            'logs' => $logCollection,
        ]);
    }

    public function pdf(Request $request, Program $program)
    {
        $user = $request->user();
        $this->ensureProgramAccessible($program, $user);

        $program->load([
            'pemohon',
            'pemandu',
            'kenderaan',
            'logPemandu' => fn ($query) => $query->with(['pemandu', 'kenderaan'])->orderBy('masa_keluar'),
        ]);

        $logCollection = $program->logPemandu;

        $stats = [
            'jumlah_log' => $logCollection->count(),
            'jumlah_pemandu' => $logCollection->pluck('pemandu_id')->filter()->unique()->count(),
            'jumlah_kenderaan' => $logCollection->pluck('kenderaan_id')->filter()->unique()->count(),
            'jumlah_checkin' => $logCollection->whereNotNull('masa_keluar')->count(),
            'jumlah_checkout' => $logCollection->whereNotNull('masa_masuk')->count(),
            'jarak_km' => (float) $logCollection->sum('jarak'),
            'kos' => (float) $logCollection->sum('kos_minyak'),
        ];

        $pdf = Pdf::loadView('laporan.pdf.program', [
            'program' => $program,
            'stats' => $stats,
            'logs' => $logCollection,
        ])->setPaper('a4', 'portrait');

        $filename = 'laporan-program-' . str($program->nama_program)->slug('-') . '.pdf';

        return $pdf->download($filename);
    }

    private function applyProgramScope($query, ?User $user): void
    {
        if (!$user || $user->jenis_organisasi === 'semua') {
            return;
        }

        if ($user->jenis_organisasi === 'stesen') {
            $query->where('jenis_organisasi', 'stesen')
                ->where('organisasi_id', $user->organisasi_id);
            return;
        }

        if ($user->jenis_organisasi === 'bahagian') {
            $stesenIds = collect($user->stesen_akses_ids ?? [])
                ->map(fn ($id) => (string) $id)
                ->filter();

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

        $query->where(function ($inner) use ($user) {
            $inner->where('jenis_organisasi', $user->jenis_organisasi)
                ->where('organisasi_id', $user->organisasi_id);
        });
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
            $stesenIds = collect($user->stesen_akses_ids ?? [])
                ->map(fn ($id) => (string) $id)
                ->filter();

            if ($stesenIds->isNotEmpty()) {
                $query->whereIn('organisasi_id', $stesenIds->all());
            } else {
                $query->where('organisasi_id', (string) $user->organisasi_id);
            }
        }
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
            $stesenIds = collect($user->stesen_akses_ids ?? [])
                ->map(fn ($id) => (string) $id)
                ->filter();

            $isSameBahagian = $program->jenis_organisasi === 'bahagian'
                && (string) $program->organisasi_id === (string) $user->organisasi_id;

            $isWithinStesen = $program->jenis_organisasi === 'stesen'
                && ($stesenIds->isNotEmpty() && $stesenIds->contains((string) $program->organisasi_id));

            if (!$isSameBahagian && !$isWithinStesen) {
                abort(403, 'Anda tidak mempunyai kebenaran untuk melihat program ini.');
            }

            return;
        }

        if ($program->jenis_organisasi !== $user->jenis_organisasi || (string) $program->organisasi_id !== (string) $user->organisasi_id) {
            abort(403, 'Anda tidak mempunyai kebenaran untuk melihat program ini.');
        }
    }
}

