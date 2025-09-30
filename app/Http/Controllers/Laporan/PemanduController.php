<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\LogPemandu;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PemanduController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $allowedStatuses = $this->eligibleProgramStatuses();

        $driverQuery = User::query()
            ->whereHas('programsSebagaiPemandu', function ($programQuery) use ($user, $allowedStatuses) {
                $programQuery->whereIn('status', $allowedStatuses)
                    ->whereHas('logPemandu', function ($logQuery) use ($user, $allowedStatuses) {
                        $this->applyLogScope($logQuery, $user);
                        $this->applyEligibleProgramFilterToLog($logQuery, $user, $allowedStatuses);
                    });
                $this->applyProgramScope($programQuery, $user);
            })
            ->with(['stesen', 'bahagian']);

        $this->applyDriverScope($driverQuery, $user);
        $this->applyDriverFilters($driverQuery, $request);

        $drivers = (clone $driverQuery)
            ->orderBy('name')
            ->paginate(5)
            ->withQueryString();

        $driverIds = $drivers->isEmpty() ? collect() : $drivers->getCollection()->pluck('id');

        $logQuery = LogPemandu::query()->with(['kenderaan', 'program']);
        $this->applyLogScope($logQuery, $user);
        $this->applyLogFilters($logQuery, $request);
        $this->applyEligibleProgramFilterToLog($logQuery, $user, $allowedStatuses);
        if ($driverIds->isNotEmpty()) {
            $logQuery->whereIn('pemandu_id', $driverIds);
        }

        $overallLogQuery = LogPemandu::query();
        $this->applyLogScope($overallLogQuery, $user);
        $this->applyLogFilters($overallLogQuery, $request);
        $this->applyEligibleProgramFilterToLog($overallLogQuery, $user, $allowedStatuses);

        $overallStats = [
            'total_pemandu' => (clone $driverQuery)->count(),
            'total_log' => (clone $overallLogQuery)->count(),
            'jumlah_jarak' => (float) (clone $overallLogQuery)->sum('jarak'),
            'jumlah_kos' => (float) (clone $overallLogQuery)->sum('kos_minyak'),
            'purata_jarak_log' => $this->average((clone $overallLogQuery)->sum('jarak'), (clone $overallLogQuery)->count()),
            'purata_kos_log' => $this->average((clone $overallLogQuery)->sum('kos_minyak'), (clone $overallLogQuery)->count()),
        ];

        $logAggregates = (clone $logQuery)
            ->selectRaw('
                pemandu_id,
                COUNT(*) AS jumlah_log,
                SUM(jarak) AS jumlah_jarak,
                SUM(kos_minyak) AS jumlah_kos,
                SUM(liter_minyak) AS jumlah_liter,
                SUM(CASE WHEN status = "dalam_perjalanan" THEN 1 ELSE 0 END) AS jumlah_aktif,
                SUM(CASE WHEN status = "selesai" THEN 1 ELSE 0 END) AS jumlah_selesai,
                SUM(CASE WHEN status = "tertunda" THEN 1 ELSE 0 END) AS jumlah_tertunda
            ')
            ->groupBy('pemandu_id')
            ->get()
            ->keyBy('pemandu_id');

        $driverData = $drivers->getCollection()->map(function (User $driver) use ($logAggregates) {
            $stats = $logAggregates->get($driver->id);

            return [
                'id' => $driver->id,
                'jumlah_log' => $stats->jumlah_log ?? 0,
                'jumlah_jarak' => (float) ($stats->jumlah_jarak ?? 0),
                'jumlah_kos' => (float) ($stats->jumlah_kos ?? 0),
                'jumlah_liter' => (float) ($stats->jumlah_liter ?? 0),
                'jumlah_aktif' => $stats->jumlah_aktif ?? 0,
                'jumlah_selesai' => $stats->jumlah_selesai ?? 0,
                'jumlah_tertunda' => $stats->jumlah_tertunda ?? 0,
                'purata_jarak' => $this->average($stats->jumlah_jarak ?? 0, $stats->jumlah_log ?? 0),
                'purata_kos' => $this->average($stats->jumlah_kos ?? 0, $stats->jumlah_log ?? 0),
            ];
        })->keyBy('id');

        return view('laporan.laporan-pemandu', [
            'drivers' => $drivers,
            'driverData' => $driverData,
            'overallStats' => $overallStats,
        ]);
    }

    public function show(Request $request, User $driver)
    {
        $this->ensureDriverAccessible($driver, $request->user());

        $logQuery = LogPemandu::query()
            ->with(['kenderaan', 'program'])
            ->where('pemandu_id', $driver->id);

        $this->applyLogScope($logQuery, $request->user());
        $this->applyLogFilters($logQuery, $request);
        $this->applyEligibleProgramFilterToLog($logQuery, $request->user(), $this->eligibleProgramStatuses());

        $logs = $logQuery
            ->orderByDesc('tarikh_perjalanan')
            ->orderByDesc('masa_keluar')
            ->get();

        $stats = [
            'jumlah_log' => $logs->count(),
            'jumlah_jarak' => (float) $logs->sum('jarak'),
            'jumlah_kos' => (float) $logs->sum('kos_minyak'),
            'jumlah_liter' => (float) $logs->sum('liter_minyak'),
            'purata_jarak' => $this->average($logs->sum('jarak'), $logs->count()),
            'purata_kos' => $this->average($logs->sum('kos_minyak'), $logs->count()),
        ];

        $programSummary = $logs
            ->filter(fn ($log) => $log->program)
            ->groupBy('program_id')
            ->map(function (Collection $items) {
                $first = $items->first();

                return [
                    'nama_program' => $first->program->nama_program ?? '-',
                    'status' => $first->program->status ?? '-',
                    'jumlah_log' => $items->count(),
                    'jumlah_jarak' => (float) $items->sum('jarak'),
                    'jumlah_kos' => (float) $items->sum('kos_minyak'),
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
                    'jumlah_kos' => (float) $items->sum('kos_minyak'),
                ];
            })
            ->values();

        return view('laporan.laporan-pemandu-show', [
            'driver' => $driver->load(['stesen', 'bahagian']),
            'logs' => $logs,
            'stats' => $stats,
            'programSummary' => $programSummary,
            'kenderaanSummary' => $kenderaanSummary,
        ]);
    }

    public function pdf(Request $request, User $driver)
    {
        $this->ensureDriverAccessible($driver, $request->user());

        $logQuery = LogPemandu::query()
            ->with(['kenderaan', 'program'])
            ->where('pemandu_id', $driver->id);

        $this->applyLogScope($logQuery, $request->user());
        $this->applyLogFilters($logQuery, $request);
        $this->applyEligibleProgramFilterToLog($logQuery, $request->user(), $this->eligibleProgramStatuses());

        $logs = $logQuery
            ->orderByDesc('tarikh_perjalanan')
            ->orderByDesc('masa_keluar')
            ->get();

        $stats = [
            'jumlah_log' => $logs->count(),
            'jumlah_jarak' => (float) $logs->sum('jarak'),
            'jumlah_kos' => (float) $logs->sum('kos_minyak'),
            'jumlah_liter' => (float) $logs->sum('liter_minyak'),
        ];

        $pdf = Pdf::loadView('laporan.pdf.pemandu', [
            'driver' => $driver->load(['stesen', 'bahagian']),
            'logs' => $logs,
            'stats' => $stats,
        ])->setPaper('a4', 'portrait');

        $filename = 'laporan-pemandu-' . str($driver->name)->slug('-') . '.pdf';

        return $pdf->download($filename);
    }

    private function applyDriverFilters($query, Request $request): void
    {
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('stesen', fn ($sub) => $sub->where('nama_stesen', 'like', "%{$search}%"))
                    ->orWhereHas('bahagian', fn ($sub) => $sub->where('nama_bahagian', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
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

    private function applyDriverScope($query, ?User $user): void
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

            $query->where(function ($inner) use ($user, $stesenIds) {
                $inner->where('organisasi_id', (string) $user->organisasi_id);

                if ($stesenIds->isNotEmpty()) {
                    $inner->orWhereIn('organisasi_id', $stesenIds->all());
                }
            });

            return;
        }

        $query->where('organisasi_id', (string) $user->organisasi_id);
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

            return;
        }

        $query->where('organisasi_id', (string) $user->organisasi_id);
    }

    private function ensureDriverAccessible(User $driver, ?User $viewer): void
    {
        if (!$viewer) {
            abort(403, 'Sesi pengguna tidak sah.');
        }

        if ($viewer->jenis_organisasi === 'semua') {
            return;
        }

        if ($viewer->jenis_organisasi === 'stesen') {
            if ((string) $driver->organisasi_id !== (string) $viewer->organisasi_id) {
                abort(403, 'Anda tidak mempunyai kebenaran untuk melihat pemandu ini.');
            }

            return;
        }

        if ($viewer->jenis_organisasi === 'bahagian') {
            $stesenIds = collect($viewer->stesen_akses_ids ?? [])
                ->map(fn ($id) => (string) $id)
                ->filter();

            $isBahagian = (string) $driver->organisasi_id === (string) $viewer->organisasi_id;
            $isStesen = $stesenIds->contains((string) $driver->organisasi_id);

            if (!$isBahagian && !$isStesen) {
                abort(403, 'Anda tidak mempunyai kebenaran untuk melihat pemandu ini.');
            }

            return;
        }

        if ((string) $driver->organisasi_id !== (string) $viewer->organisasi_id) {
            abort(403, 'Anda tidak mempunyai kebenaran untuk melihat pemandu ini.');
        }
    }

    private function average($sum, $count): float
    {
        $sum = (float) $sum;
        $count = (int) $count;

        if ($count === 0) {
            return 0.0;
        }

        return round($sum / $count, 2);
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

        $query->where('organisasi_id', (string) $user->organisasi_id)
            ->where('jenis_organisasi', $user->jenis_organisasi);
    }
}


