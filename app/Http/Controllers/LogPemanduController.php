<?php

namespace App\Http\Controllers;

use App\Models\Kenderaan;
use App\Models\LogPemandu;
use App\Models\User;
use Illuminate\Http\Request;

class LogPemanduController extends Controller
{
    /**
     * Display a listing of driver logs with tabbed status view.
     */
    public function index(Request $request)
    {
        $activeTab = $this->resolveActiveTab($request->get('tab'), $request->user());
        $user = $request->user();

        $baseQuery = LogPemandu::query();
        $this->applyOrganisationScope($baseQuery, $user);

        $tabCounts = [
            'semua' => (clone $baseQuery)->count(),
            'aktif' => (clone $baseQuery)->where('status', 'dalam_perjalanan')->count(),
            'selesai' => (clone $baseQuery)->where('status', 'selesai')->count(),
            'tertunda' => (clone $baseQuery)->where('status', 'tertunda')->count(),
        ];

        $query = (clone $baseQuery)->with([
            'pemandu:id,name,email',
            'kenderaan:id,no_plat,jenama,model',
        ]);

        // Apply tab filter
        $query = match ($activeTab) {
            'aktif' => $query->where('status', 'dalam_perjalanan'),
            'selesai' => $query->where('status', 'selesai'),
            'tertunda' => $query->where('status', 'tertunda'),
            default => $query,
        };

        // Apply search & filters
        if ($search = $request->get('search')) {
            $query->search($search);
        }

        if ($pemanduId = $request->get('pemandu_id')) {
            $query->where('pemandu_id', $pemanduId);
        }

        if ($kenderaanId = $request->get('kenderaan_id')) {
            $query->where('kenderaan_id', $kenderaanId);
        }

        $query->byTarikh($request->get('tarikh_dari'), $request->get('tarikh_hingga'));

        $logs = $query
            ->orderByDesc('tarikh_perjalanan')
            ->orderByDesc('created_at')
            ->paginate(5)
            ->withQueryString();

        $tabUrls = $this->buildTabUrls($request, $user);

        return view('log-pemandu.index', [
            'logs' => $logs,
            'activeTab' => $activeTab,
            'tabCounts' => $tabCounts,
            'tabUrls' => $tabUrls,
            'canViewTab' => fn (string $tab) => $this->canViewTab($user, $tab),
        ]);
    }

    /**
     * Resolve and sanitise active tab value.
     */
    private function resolveActiveTab(?string $tab, ?User $user): string
    {
        $allowed = ['semua', 'aktif', 'selesai', 'tertunda'];
        $tab = in_array($tab, $allowed, true) ? $tab : 'semua';

        if (!$user) {
            return 'semua';
        }

        if (!$this->canViewTab($user, $tab)) {
            foreach ($allowed as $candidate) {
                if ($this->canViewTab($user, $candidate)) {
                    return $candidate;
                }
            }

            return 'semua';
        }

        return $tab;
    }

    /**
     * Apply organisation-based scoping for multi-tenancy.
     */
    private function applyOrganisationScope($query, ?User $user): void
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

    /**
     * Build tab URLs preserving current filters.
     */
    private function buildTabUrls(Request $request, ?User $user): array
    {
        $current = $request->query();

        return collect(['semua', 'aktif', 'selesai', 'tertunda'])
            ->filter(fn ($tab) => $this->canViewTab($user, $tab))
            ->mapWithKeys(function ($tab) use ($current) {
                $params = array_merge($current, ['tab' => $tab]);
                unset($params['page']);

                return [$tab => route('log-pemandu.index', $params)];
            })
            ->toArray();
    }

    private function canViewTab(?User $user, string $tab): bool
    {
        if (!$user) {
            return false;
        }

        return match ($tab) {
            'semua' => $user->adaKebenaran('log_pemandu_semua', 'lihat'),
            'aktif' => $user->adaKebenaran('log_pemandu_semua', 'lihat') || $user->adaKebenaran('log_pemandu_aktif', 'lihat'),
            'selesai' => $user->adaKebenaran('log_pemandu_semua', 'lihat') || $user->adaKebenaran('log_pemandu_selesai', 'lihat'),
            'tertunda' => $user->adaKebenaran('log_pemandu_semua', 'lihat') || $user->adaKebenaran('log_pemandu_tertunda', 'lihat'),
            default => false,
        };
    }

    public function show(LogPemandu $logPemandu)
    {
        $user = request()->user();
        $this->ensurePermission($user, 'lihat_butiran');
        $this->ensureLogAccessible($logPemandu, $user);

        $logPemandu->load(['pemandu', 'kenderaan', 'program', 'creator', 'updater']);

        return view('log-pemandu.show', [
            'log' => $logPemandu,
        ]);
    }

    public function edit(LogPemandu $logPemandu)
    {
        $user = request()->user();
        $this->ensurePermission($user, 'kemaskini_status', $logPemandu);
        $this->ensureLogAccessible($logPemandu, $user);

        $logPemandu->load(['pemandu', 'kenderaan', 'program', 'creator', 'updater']);

        return view('log-pemandu.edit', [
            'log' => $logPemandu,
        ]);
    }

    public function update(Request $request, LogPemandu $logPemandu)
    {
        $user = $request->user();
        $this->ensurePermission($user, 'kemaskini_status', $logPemandu);
        $this->ensureLogAccessible($logPemandu, $user);

        $data = $request->validate([
            'status' => 'required|in:dalam_perjalanan,selesai,tertunda',
            'masa_keluar' => 'required|date_format:H:i',
            'masa_masuk' => 'nullable|date_format:H:i',
            'destinasi' => 'required|string|max:255',
            'lokasi_checkin' => 'nullable|string|max:255',
            'lokasi_checkout' => 'nullable|string|max:255',
            'catatan' => 'nullable|string|max:1000',
            'odometer_keluar' => 'required|integer|min:0',
            'odometer_masuk' => 'nullable|integer|min:' . $logPemandu->odometer_keluar,
            'lokasi_checkin_lat' => 'nullable|numeric',
            'lokasi_checkin_long' => 'nullable|numeric',
            'lokasi_checkout_lat' => 'nullable|numeric',
            'lokasi_checkout_long' => 'nullable|numeric',
        ]);

        $logPemandu->fill($data);
        $logPemandu->dikemaskini_oleh = $user->id;
        $logPemandu->save();

        return redirect()->route('log-pemandu.index', ['tab' => $logPemandu->status === 'dalam_perjalanan' ? 'aktif' : ($logPemandu->status === 'selesai' ? 'selesai' : 'tertunda')])
            ->with('success', 'Log pemandu berjaya dikemaskini.');
    }

    public function destroy(LogPemandu $logPemandu)
    {
        $user = request()->user();
        $this->ensurePermission($user, 'padam', $logPemandu);
        $this->ensureLogAccessible($logPemandu, $user);

        $logPemandu->delete();

        return redirect()->route('log-pemandu.index')
            ->with('success', 'Log pemandu berjaya dipadam.');
    }

    private function ensurePermission(?User $user, string $permission, ?LogPemandu $log = null): void
    {
        if (!$user) {
            abort(403, 'Anda tidak mempunyai kebenaran untuk tindakan ini.');
        }

        $checks = match ($permission) {
            'lihat_butiran' => [
                ['module' => 'log_pemandu_semua', 'action' => 'lihat'],
                ['module' => $this->resolveStatusModule($log), 'action' => 'lihat'],
            ],
            'kemaskini_status' => [
                ['module' => 'log_pemandu_semua', 'action' => 'kemaskini'],
                ['module' => $this->resolveStatusModule($log), 'action' => 'kemaskini'],
            ],
            'padam' => [
                ['module' => 'log_pemandu_semua', 'action' => 'padam'],
                ['module' => $this->resolveStatusModule($log), 'action' => 'padam'],
            ],
            default => [
                ['module' => 'log_pemandu_semua', 'action' => $permission],
            ],
        };

        foreach ($checks as $check) {
            $module = $check['module'] ?? null;
            $action = $check['action'] ?? null;

            if ($module && $action && $user->adaKebenaran($module, $action)) {
                return;
            }
        }

        abort(403, 'Anda tidak mempunyai kebenaran untuk tindakan ini.');
    }

    private function resolveStatusModule(?LogPemandu $log): ?string
    {
        if (!$log) {
            return null;
        }

        return match ($log->status) {
            'dalam_perjalanan' => 'log_pemandu_aktif',
            'selesai' => 'log_pemandu_selesai',
            'tertunda' => 'log_pemandu_tertunda',
            default => null,
        };
    }

    private function ensureLogAccessible(LogPemandu $logPemandu, ?User $user): void
    {
        if (!$user || $user->jenis_organisasi === 'semua') {
            return;
        }

        $organisasiId = (string) $logPemandu->organisasi_id;

        if ($user->jenis_organisasi === 'stesen') {
            abort_if($organisasiId !== (string) $user->organisasi_id, 403, 'Log ini berada di luar akses anda.');
            return;
        }

        if ($user->jenis_organisasi === 'bahagian') {
            $stesenIds = collect($user->stesen_akses_ids ?? [$user->organisasi_id])
                ->map(fn ($id) => (string) $id)
                ->filter();

            if ($stesenIds->isEmpty()) {
                $stesenIds = collect([(string) $user->organisasi_id]);
            }

            abort_if(!$stesenIds->contains($organisasiId), 403, 'Log ini berada di luar akses anda.');
        }
    }
}


