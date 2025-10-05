@php
    $programs = $programs ?? collect();
    if ($programs instanceof \Illuminate\Pagination\Paginator || $programs instanceof \Illuminate\Pagination\LengthAwarePaginator) {
        $programCollection = $programs->getCollection();
    } else {
        $programCollection = collect($programs);
    }

    $programData = collect($programData ?? []);
    $overallStats = array_merge([
        'total_program' => 0,
        'total_log' => 0,
        'total_maintenance' => 0,
        'jumlah_kos_minyak' => 0.0,
        'jumlah_kos_selenggara' => 0.0,
        'jumlah_kos' => 0.0,
        'jumlah_liter' => 0.0,
        'purata_kos_log' => 0.0,
        'purata_liter_log' => 0.0,
    ], $overallStats ?? []);
@endphp

@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout title="Laporan Kos">
    <x-ui.page-header
        title="Laporan Kos"
        description="Analisis kos bahan api, penyelenggaraan dan penggunaan liter mengikut program"
    >
        <!-- Overall Statistics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <x-ui.stat-card icon="event" icon-color="text-blue-600" :value="number_format($overallStats['total_program'])" label="Jumlah Program" />
            <x-ui.stat-card icon="history" icon-color="text-green-600" :value="number_format($overallStats['total_log'])" label="Jumlah Log" />
            <x-ui.stat-card icon="build" icon-color="text-purple-600" :value="number_format($overallStats['total_maintenance'])" label="Jumlah Selenggara" />
            <x-ui.stat-card icon="payments" icon-color="text-red-500" :value="number_format($overallStats['jumlah_kos'], 2)" prefix="RM " label="Jumlah Kos" />
            <x-ui.stat-card icon="local_gas_station" icon-color="text-emerald-600" :value="number_format($overallStats['jumlah_liter'], 2)" suffix=" L" label="Jumlah Liter" />
            <x-ui.stat-card icon="equalizer" icon-color="text-indigo-600" :value="number_format($overallStats['purata_kos_log'], 2)" prefix="RM " label="Purata Kos/Log" />
        </div>

        <!-- Cost Breakdown -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <span class="material-symbols-outlined text-blue-600 mr-3" style="font-size: 32px;">local_gas_station</span>
                    <div>
                        <p class="text-sm text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Kos Bahan Api</p>
                        <p class="text-2xl font-bold text-blue-600" style="font-family: Poppins, sans-serif !important;">RM {{ number_format($overallStats['jumlah_kos_minyak'], 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <div class="flex items-center">
                    <span class="material-symbols-outlined text-purple-600 mr-3" style="font-size: 32px;">build</span>
                    <div>
                        <p class="text-sm text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Kos Penyelenggaraan</p>
                        <p class="text-2xl font-bold text-purple-600" style="font-family: Poppins, sans-serif !important;">RM {{ number_format($overallStats['jumlah_kos_selenggara'], 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <x-ui.search-filter
            :action="route('laporan.laporan-kos')"
            search-placeholder="Cari program, lokasi atau pemandu"
            :search-value="request('search')"
            :filters="[
                [
                    'name' => 'status',
                    'type' => 'select',
                    'placeholder' => 'Status Program',
                    'options' => [
                        'draf' => 'Draf',
                        'lulus' => 'Lulus',
                        'aktif' => 'Aktif',
                        'tertunda' => 'Tertunda',
                        'selesai' => 'Selesai',
                    ]
                ],
                [
                    'name' => 'status_selenggara',
                    'type' => 'select',
                    'placeholder' => 'Status Selenggara',
                    'options' => [
                        'selesai' => 'Selesai',
                        'dalam_proses' => 'Dalam Proses',
                        'dijadualkan' => 'Dijadualkan',
                    ]
                ],
                [
                    'name' => 'tarikh_dari',
                    'type' => 'date',
                    'placeholder' => 'Tarikh Dari'
                ],
                [
                    'name' => 'tarikh_hingga',
                    'type' => 'date',
                    'placeholder' => 'Tarikh Hingga'
                ]
            ]"
            :reset-url="route('laporan.laporan-kos')"
        />

        <!-- Desktop Table (Hidden on Mobile) -->
        <div class="data-table-container">
        <x-ui.data-table
            :headers="[
                ['label' => 'Program', 'align' => 'text-left'],
                ['label' => 'Tempoh', 'align' => 'text-left'],
                ['label' => 'Kos & Liter', 'align' => 'text-left'],
                ['label' => 'Log & Statistik', 'align' => 'text-left'],
            ]"
            empty-message="Tiada program ditemui untuk penapis semasa."
        >
            @forelse($programCollection as $program)
                @php
                    $stats = $programData->get($program->id);
                    $jumlahKos = $stats['jumlah_kos'] ?? 0;
                    $jumlahLiter = $stats['jumlah_liter'] ?? 0;
                    $purataKos = $stats['purata_kos_log'] ?? 0;
                    $purataLiter = $stats['purata_liter_log'] ?? 0;
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 max-w-xs">
                        <div class="text-sm font-medium text-gray-900 truncate" title="{{ $program->nama_program }}" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            {{ $program->nama_program }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1 truncate" title="Pemohon: {{ $program->pemohon->nama_penuh ?? '-' }}" style="font-family: Poppins, sans-serif !important; font-size: 11px !important; max-width: 12rem;">
                            Pemohon: {{ $program->pemohon->nama_penuh ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-400 mt-1 truncate" title="Lokasi: {{ $program->lokasi_program ?? '-' }}" style="font-family: Poppins, sans-serif !important; font-size: 10px !important; max-width: 12rem;">
                            Lokasi: {{ $program->lokasi_program ?? '-' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            {{ optional($program->tarikh_mula)->format('d/m/Y H:i') ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            hingga {{ optional($program->tarikh_selesai)->format('d/m/Y H:i') ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-400 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                            Kenderaan: {{ $program->kenderaan->no_plat ?? '-' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-xs text-gray-500 space-y-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            <div>Jumlah Kos: RM {{ number_format($jumlahKos, 2) }}</div>
                            <div>Jumlah Liter: {{ number_format($jumlahLiter, 2) }} L</div>
                            <div>Purata Kos/Log: RM {{ number_format($purataKos, 2) }}</div>
                            <div>Purata Liter/Log: {{ number_format($purataLiter, 2) }} L</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-xs text-gray-500 space-y-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            <div>Jumlah Log: {{ number_format($stats['jumlah_log'] ?? 0) }}</div>
                            <div>Jarak Direkod: {{ number_format($stats['jumlah_jarak'] ?? 0, 1) }} km</div>
                            <div>Check-in / Check-out: {{ number_format($stats['jumlah_checkin'] ?? 0) }} / {{ number_format($stats['jumlah_checkout'] ?? 0) }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <x-ui.action-buttons
                            :show-url="route('laporan.laporan-kos.show', $program)"
                            :show-view="true"
                            :show-edit="false"
                            :show-delete="false"
                            :custom-actions="[
                                [
                                    'url' => route('laporan.laporan-kos.pdf', $program),
                                    'icon' => 'picture_as_pdf',
                                    'class' => 'text-red-600 hover:text-red-800',
                                    'title' => 'Eksport ke PDF'
                                ]
                            ]"
                        />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Tiada program ditemui buat masa ini.</td>
                </tr>
            @endforelse
        </x-ui.data-table>
        </div>

        <!-- Mobile Card View -->
        <div class="mobile-table-card">
            @forelse($programCollection as $program)
                @php
                    $stats = $programData->get($program->id);
                    $jumlahKos = $stats['jumlah_kos'] ?? 0;
                    $jumlahLiter = $stats['jumlah_liter'] ?? 0;
                    $purataKos = $stats['purata_kos_log'] ?? 0;
                    $purataLiter = $stats['purata_liter_log'] ?? 0;
                @endphp

                <div class="mobile-card">
                    <div class="mobile-card-header">
                        <div class="mobile-card-title">{{ $program->nama_program }}</div>
                        <div class="mobile-card-badge"><x-ui.status-badge :status="$program->status" /></div>
                    </div>

                    <div class="mobile-card-body">
                        <!-- Tempoh -->
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">today</span></span>
                            <span class="mobile-card-value">
                                {{ optional($program->tarikh_mula)->format('d/m/Y H:i') ?? '-' }}
                                <div class="mobile-card-value-secondary">hingga {{ optional($program->tarikh_selesai)->format('d/m/Y H:i') ?? '-' }}</div>
                            </span>
                        </div>
                        <!-- Pemohon -->
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">person</span></span>
                            <span class="mobile-card-value">{{ $program->pemohon->nama_penuh ?? '-' }}</span>
                        </div>
                        <!-- Kenderaan -->
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">directions_car</span></span>
                            <span class="mobile-card-value">{{ $program->kenderaan->no_plat ?? '-' }}<div class="mobile-card-value-secondary">{{ trim(($program->kenderaan->jenama ?? '') . ' ' . ($program->kenderaan->model ?? '')) ?: '-' }}</div></span>
                        </div>
                        <!-- Kos & Liter -->
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">payments</span></span>
                            <span class="mobile-card-value">Jumlah Kos: <strong>RM {{ number_format($jumlahKos, 2) }}</strong></span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">local_gas_station</span></span>
                            <span class="mobile-card-value">Jumlah Liter: <strong>{{ number_format($jumlahLiter, 2) }}</strong> L</span>
                        </div>
                        <!-- Log & Statistik -->
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">history</span></span>
                            <span class="mobile-card-value">
                                Log: {{ number_format($stats['jumlah_log'] ?? 0) }}
                                <div class="mobile-card-value-secondary">Check-in/Check-out: {{ number_format($stats['jumlah_checkin'] ?? 0) }} / {{ number_format($stats['jumlah_checkout'] ?? 0) }}</div>
                            </span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">equalizer</span></span>
                            <span class="mobile-card-value">Purata Kos/Log: RM {{ number_format($purataKos, 2) }} • Purata Liter/Log: {{ number_format($purataLiter, 2) }} L</span>
                        </div>
                    </div>

                    <div class="mobile-card-footer">
                        <a href="{{ route('laporan.laporan-kos.show', $program) }}" class="mobile-card-action mobile-action-view">
                            <span class="material-symbols-outlined mobile-card-action-icon">visibility</span>
                            <span class="mobile-card-action-label">Lihat</span>
                        </a>
                        <a href="{{ route('laporan.laporan-kos.pdf', $program) }}" class="mobile-card-action" style="color:#dc2626;">
                            <span class="material-symbols-outlined mobile-card-action-icon">picture_as_pdf</span>
                            <span class="mobile-card-action-label">PDF</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="mobile-empty-state">
                    <span class="material-symbols-outlined" style="font-size:48px; color:#9ca3af;">payments</span>
                    <p>Tiada program ditemui</p>
                </div>
            @endforelse
        </div>

        <x-ui.pagination :paginator="$programs" record-label="program" />
    </x-ui.page-header>
</x-dashboard-layout>
