<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Kenderaan;
use App\Models\LogPemandu;
use App\Models\SelenggaraKenderaan;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class KenderaanController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $allowedStatuses = $this->eligibleProgramStatuses();

        $baseVehicleQuery = Kenderaan::query()
            ->with(['bahagian', 'stesen'])
            ->whereHas('programs', function ($programQuery) use ($user, $allowedStatuses) {
                $programQuery->whereIn('status', $allowedStatuses)
                    ->whereHas('logPemandu', function ($logQuery) use ($user, $allowedStatuses) {
                        $this->applyLogScope($logQuery, $user);
                        $this->applyEligibleProgramFilterToLog($logQuery, $user, $allowedStatuses);
                    });
                $this->applyProgramScope($programQuery, $user);
            });
        $this->applyVehicleScope($baseVehicleQuery, $user);

        $filteredVehicleQuery = clone $baseVehicleQuery;

        if ($search = $request->get('search')) {
            $filteredVehicleQuery->where(function ($query) use ($search) {
                $query->where('no_plat', 'like', "%{$search}%")
                    ->orWhere('jenama', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhereHas('bahagian', function ($subQuery) use ($search) {
                        $subQuery->where('nama_bahagian', 'like', "%{$search}%");
                    })
                    ->orWhereHas('stesen', function ($subQuery) use ($search) {
                        $subQuery->where('nama_stesen', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = $request->get('status')) {
            $filteredVehicleQuery->where('status', $status);
        }

        if ($fuel = $request->get('jenis_bahan_api')) {
            $filteredVehicleQuery->where('jenis_bahan_api', $fuel);
        }

        $vehicles = (clone $filteredVehicleQuery)
            ->orderByDesc('created_at')
            ->paginate(5)
            ->withQueryString();

        $vehicleIds = (clone $filteredVehicleQuery)->pluck('id');

        $logQuery = LogPemandu::query();
        $this->applyLogScope($logQuery, $user);
        $this->applyEligibleProgramFilterToLog($logQuery, $user, $allowedStatuses);
        $logQuery->when($vehicleIds->isNotEmpty(), fn ($query) => $query->whereIn('kenderaan_id', $vehicleIds));

        $overallLogQuery = LogPemandu::query();
        $this->applyLogScope($overallLogQuery, $user);
        $this->applyEligibleProgramFilterToLog($overallLogQuery, $user, $allowedStatuses);

        $overallStats = [
            'total_kenderaan' => (clone $baseVehicleQuery)->count(),
            'total_log' => (clone $overallLogQuery)->count(),
            'total_pemandu' => (clone $overallLogQuery)->distinct('pemandu_id')->count('pemandu_id'),
            'total_program' => (clone $overallLogQuery)->distinct('program_id')->count('program_id'),
            'jumlah_jarak' => (float) (clone $overallLogQuery)->sum('jarak'),
            'jumlah_kos' => (float) (clone $overallLogQuery)->sum('kos_minyak'),
        ];

        $logAggregates = (clone $logQuery)
            ->selectRaw('
                kenderaan_id,
                COUNT(*) AS jumlah_log,
                SUM(CASE WHEN status = "dalam_perjalanan" THEN 1 ELSE 0 END) AS jumlah_aktif,
                SUM(CASE WHEN status = "selesai" THEN 1 ELSE 0 END) AS jumlah_selesai,
                SUM(CASE WHEN status = "tertunda" THEN 1 ELSE 0 END) AS jumlah_tertunda,
                SUM(CASE WHEN masa_keluar IS NOT NULL THEN 1 ELSE 0 END) AS jumlah_checkin,
                SUM(CASE WHEN masa_masuk IS NOT NULL THEN 1 ELSE 0 END) AS jumlah_checkout,
                SUM(jarak) AS jumlah_jarak,
                SUM(kos_minyak) AS jumlah_kos,
                COUNT(DISTINCT pemandu_id) AS jumlah_pemandu,
                COUNT(DISTINCT program_id) AS jumlah_program
            ')
            ->groupBy('kenderaan_id')
            ->get()
            ->keyBy('kenderaan_id');

        $vehicleData = $vehicles->getCollection()->map(function (Kenderaan $vehicle) use ($logAggregates) {
            $stats = $logAggregates->get($vehicle->id);

            return [
                'id' => $vehicle->id,
                'jumlah_log' => $stats->jumlah_log ?? 0,
                'jumlah_aktif' => $stats->jumlah_aktif ?? 0,
                'jumlah_selesai' => $stats->jumlah_selesai ?? 0,
                'jumlah_tertunda' => $stats->jumlah_tertunda ?? 0,
                'jumlah_checkin' => $stats->jumlah_checkin ?? 0,
                'jumlah_checkout' => $stats->jumlah_checkout ?? 0,
                'jumlah_jarak' => (float) ($stats->jumlah_jarak ?? 0),
                'jumlah_kos' => (float) ($stats->jumlah_kos ?? 0),
                'jumlah_pemandu' => $stats->jumlah_pemandu ?? 0,
                'jumlah_program' => $stats->jumlah_program ?? 0,
            ];
        })->keyBy('id');

        return view('laporan.laporan-kenderaan', [
            'vehicles' => $vehicles,
            'vehicleData' => $vehicleData,
            'overallStats' => $overallStats,
        ]);
    }

    public function show(Request $request, Kenderaan $kenderaan)
    {
        $user = $request->user();
        $this->ensureVehicleAccessible($kenderaan, $user);

        $kenderaan->load(['bahagian', 'stesen', 'pencipta']);

        $logQuery = LogPemandu::query()
            ->with(['pemandu', 'program'])
            ->where('kenderaan_id', $kenderaan->id);

        $this->applyLogScope($logQuery, $user);
        $this->applyEligibleProgramFilterToLog($logQuery, $user, $this->eligibleProgramStatuses());

        $logCollection = $logQuery
            ->orderByDesc('tarikh_perjalanan')
            ->orderByDesc('created_at')
            ->get();

        // Get maintenance records for this vehicle
        $maintenanceQuery = SelenggaraKenderaan::query()
            ->with(['kategoriKos', 'pelaksana'])
            ->where('kenderaan_id', $kenderaan->id)
            ->forCurrentUser($user);

        $maintenanceCollection = $maintenanceQuery
            ->orderByDesc('tarikh_selesai')
            ->get();

        $stats = [
            'jumlah_log' => $logCollection->count(),
            'jumlah_aktif' => $logCollection->where('status', 'dalam_perjalanan')->count(),
            'jumlah_selesai' => $logCollection->where('status', 'selesai')->count(),
            'jumlah_tertunda' => $logCollection->where('status', 'tertunda')->count(),
            'jumlah_checkin' => $logCollection->whereNotNull('masa_keluar')->count(),
            'jumlah_checkout' => $logCollection->whereNotNull('masa_masuk')->count(),
            'jumlah_pemandu' => $logCollection->pluck('pemandu_id')->filter()->unique()->count(),
            'jumlah_program' => $logCollection->pluck('program_id')->filter()->unique()->count(),
            'jarak' => (float) $logCollection->sum('jarak'),
            'kos_minyak' => (float) $logCollection->sum('kos_minyak'),
            'jumlah_selenggara' => $maintenanceCollection->count(),
            'kos_selenggara' => (float) $maintenanceCollection->sum('jumlah_kos'),
            'kos' => (float) $logCollection->sum('kos_minyak') + (float) $maintenanceCollection->sum('jumlah_kos'),
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

        $programSummary = $logCollection
            ->filter(fn ($log) => $log->program)
            ->groupBy('program_id')
            ->map(function (Collection $logs) {
                $log = $logs->first();

                return [
                    'nama_program' => $log->program->nama_program ?? '-',
                    'status' => $log->program->status ?? '-',
                    'tarikh_mula' => optional($log->program->tarikh_mula)->format('d/m/Y'),
                    'tarikh_selesai' => optional($log->program->tarikh_selesai)->format('d/m/Y'),
                    'jumlah_log' => $logs->count(),
                    'jarak' => (float) $logs->sum('jarak'),
                ];
            })
            ->values();

        return view('laporan.laporan-kenderaan-show', [
            'kenderaan' => $kenderaan,
            'logs' => $logCollection,
            'maintenance' => $maintenanceCollection,
            'stats' => $stats,
            'pemanduSummary' => $pemanduSummary,
            'programSummary' => $programSummary,
        ]);
    }

    public function pdf(Request $request, Kenderaan $kenderaan)
    {
        $user = $request->user();
        $this->ensureVehicleAccessible($kenderaan, $user);

        $kenderaan->load(['bahagian', 'stesen']);

        $logQuery = LogPemandu::query()
            ->with(['pemandu', 'program'])
            ->where('kenderaan_id', $kenderaan->id);

        $this->applyLogScope($logQuery, $user);
        $this->applyEligibleProgramFilterToLog($logQuery, $user, $this->eligibleProgramStatuses());

        $logCollection = $logQuery
            ->orderByDesc('tarikh_perjalanan')
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'jumlah_log' => $logCollection->count(),
            'jumlah_pemandu' => $logCollection->pluck('pemandu_id')->filter()->unique()->count(),
            'jumlah_program' => $logCollection->pluck('program_id')->filter()->unique()->count(),
            'jumlah_checkin' => $logCollection->whereNotNull('masa_keluar')->count(),
            'jumlah_checkout' => $logCollection->whereNotNull('masa_masuk')->count(),
            'jarak' => (float) $logCollection->sum('jarak'),
            'kos' => (float) $logCollection->sum('kos_minyak'),
        ];

        $pdf = Pdf::loadView('laporan.pdf.kenderaan', [
            'kenderaan' => $kenderaan,
            'logs' => $logCollection,
            'stats' => $stats,
        ])->setPaper('a4', 'portrait');

        $filename = 'laporan-kenderaan-' . str($kenderaan->no_plat)->slug('-') . '.pdf';

        // Log activity (export vehicle report)
        activity('kenderaan')
            ->performedOn($kenderaan)
            ->causedBy($user)
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'vehicle_plate' => $kenderaan->no_plat,
                'filename' => $filename,
                'format' => 'pdf',
            ])
            ->event('exported')
            ->log("Laporan kenderaan {$kenderaan->no_plat} dieksport ke PDF");

        return $pdf->download($filename);
    }

    private function applyVehicleScope($query, ?User $user): void
    {
        if (!$user || $user->jenis_organisasi === 'semua') {
            return;
        }

        if ($user->jenis_organisasi === 'stesen') {
            $query->where(function ($inner) use ($user) {
                $inner->where('stesen_id', (string) $user->organisasi_id)
                    ->orWhere('bahagian_id', (string) $user->organisasi_id);
            });
            return;
        }

        if ($user->jenis_organisasi === 'bahagian') {
            $stesenIds = $this->getStesenIdsForBahagian($user->organisasi_id, $user->stesen_akses_ids);

            $query->where(function ($inner) use ($user, $stesenIds) {
                $inner->where('bahagian_id', (string) $user->organisasi_id);

                if ($stesenIds->isNotEmpty()) {
                    $inner->orWhereIn('stesen_id', $stesenIds->map(fn ($id) => (string) $id)->all());
                }
            });

            return;
        }

        $query->where(function ($inner) use ($user) {
            $inner->where('bahagian_id', (string) $user->organisasi_id)
                ->orWhere('stesen_id', (string) $user->organisasi_id)
                ->orWhereNull('stesen_id');
        });
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

            if ($stesenIds->isNotEmpty()) {
                $query->where(function ($q) use ($user, $stesenIds) {
                    $q->where('organisasi_id', (string) $user->organisasi_id)
                      ->orWhereIn('organisasi_id', $stesenIds->map(fn ($id) => (string) $id)->all());
                });
            } else {
                $query->where('organisasi_id', (string) $user->organisasi_id);
            }

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

    private function ensureVehicleAccessible(Kenderaan $kenderaan, ?User $user): void
    {
        if (!$user) {
            abort(403, 'Sesi pengguna tidak sah.');
        }

        if ($user->jenis_organisasi === 'semua') {
            return;
        }

        if ($user->jenis_organisasi === 'stesen') {
            if ((string) $kenderaan->stesen_id !== (string) $user->organisasi_id) {
                abort(403, 'Anda tidak mempunyai akses kepada kenderaan ini.');
            }
            return;
        }

        if ($user->jenis_organisasi === 'bahagian') {
            $stesenIds = $this->getStesenIdsForBahagian($user->organisasi_id, $user->stesen_akses_ids);

            $isBahagian = (string) $kenderaan->bahagian_id === (string) $user->organisasi_id;
            $isStesen = $stesenIds->contains((int) $kenderaan->stesen_id);
            $isTanpaStesen = !$kenderaan->stesen_id;

            if (!$isBahagian && !$isStesen && !$isTanpaStesen) {
                abort(403, 'Anda tidak mempunyai akses kepada kenderaan ini.');
            }

            return;
        }

        if ((string) $kenderaan->bahagian_id !== (string) $user->organisasi_id && $kenderaan->stesen_id) {
            abort(403, 'Anda tidak mempunyai akses kepada kenderaan ini.');
        }
    }
}


