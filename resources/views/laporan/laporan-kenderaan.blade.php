@php
    $vehicles = $vehicles ?? collect();
    if ($vehicles instanceof \Illuminate\Pagination\Paginator || $vehicles instanceof \Illuminate\Pagination\LengthAwarePaginator) {
        $vehiclesCollection = $vehicles->getCollection();
    } else {
        $vehiclesCollection = collect($vehicles);
    }

    $vehicleData = collect($vehicleData ?? []);
    $overallStats = array_merge([
        'total_kenderaan' => 0,
        'total_log' => 0,
        'total_pemandu' => 0,
        'total_program' => 0,
        'jumlah_jarak' => 0.0,
        'jumlah_kos' => 0.0,
    ], $overallStats ?? []);
@endphp

@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout title="Laporan Kenderaan">
    <x-ui.page-header
        title="Laporan Kenderaan"
        description="Ringkasan prestasi dan penggunaan kenderaan berdasarkan log perjalanan"
    >
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <x-ui.stat-card
                icon="directions_car"
                icon-color="text-blue-600"
                :value="number_format($overallStats['total_kenderaan'] ?? 0)"
                label="Jumlah Kenderaan"
            />
            <x-ui.stat-card
                icon="history"
                icon-color="text-green-600"
                :value="number_format($overallStats['total_log'] ?? 0)"
                label="Jumlah Log"
            />
            <x-ui.stat-card
                icon="group"
                icon-color="text-emerald-600"
                :value="number_format($overallStats['total_pemandu'] ?? 0)"
                label="Pemandu Terlibat"
            />
            <x-ui.stat-card
                icon="event"
                icon-color="text-indigo-600"
                :value="number_format($overallStats['total_program'] ?? 0)"
                label="Program Disertai"
            />
            <x-ui.stat-card
                icon="alt_route"
                icon-color="text-rose-600"
                :value="number_format($overallStats['jumlah_jarak'] ?? 0, 1)"
                suffix=" km"
                label="Jarak Direkod"
            />
            <x-ui.stat-card
                icon="payments"
                icon-color="text-red-500"
                :value="number_format($overallStats['jumlah_kos'] ?? 0, 2)"
                prefix="RM "
                label="Kos Bahan Api"
            />
        </div>

        <x-ui.search-filter
            :action="route('laporan.laporan-kenderaan')"
            search-placeholder="Cari no. plat, jenama, model atau lokasi"
            :search-value="request('search')"
            :filters="[
                [
                    'name' => 'status',
                    'type' => 'select',
                    'placeholder' => 'Semua Status',
                    'options' => [
                        'aktif' => 'Aktif',
                        'penyelenggaraan' => 'Penyelenggaraan',
                        'tidak_aktif' => 'Tidak Aktif',
                    ]
                ],
                [
                    'name' => 'jenis_bahan_api',
                    'type' => 'select',
                    'placeholder' => 'Semua Bahan Api',
                    'options' => [
                        'petrol' => 'Petrol',
                        'diesel' => 'Diesel',
                    ]
                ]
            ]"
            :reset-url="route('laporan.laporan-kenderaan')"
        />

        <!-- Desktop Table (Hidden on Mobile) -->
        <div class="data-table-container">
        <x-ui.data-table
            :headers="[
                ['label' => 'Kenderaan', 'align' => 'text-left'],
                ['label' => 'Lokasi', 'align' => 'text-left'],
                ['label' => 'Status', 'align' => 'text-left'],
                ['label' => 'Statistik Log', 'align' => 'text-left'],
                ['label' => 'Jarak & Kos', 'align' => 'text-left']
            ]"
            empty-message="Tiada kenderaan ditemui untuk penapis semasa."
        >
            @forelse($vehiclesCollection as $vehicle)
                @php
                    $stats = $vehicleData->get($vehicle->id);
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 max-w-xs">
                        <div class="text-sm font-medium text-gray-900 truncate" title="{{ $vehicle->no_plat }}" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            {{ $vehicle->no_plat }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1 truncate" title="{{ $vehicle->jenama }} {{ $vehicle->model }}" style="font-family: Poppins, sans-serif !important; font-size: 11px !important; max-width: 12rem;">
                            {{ $vehicle->jenama }} {{ $vehicle->model }}
                        </div>
                        <div class="text-xs text-gray-400 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                            Tahun {{ $vehicle->tahun ?? '-' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            {{ $vehicle->bahagian->nama_bahagian ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            {{ $vehicle->stesen->nama_stesen ?? 'Semua Stesen' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-ui.status-badge :status="$vehicle->status" />
                        <div class="text-xs text-gray-500 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                            {{ ucfirst($vehicle->jenis_bahan_api_label ?? '-') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-xs text-gray-500 space-y-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            <div>Jumlah Log: {{ number_format($stats['jumlah_log'] ?? 0) }}</div>
                            <div>Aktif / Selesai / Tertunda: {{ number_format($stats['jumlah_aktif'] ?? 0) }} / {{ number_format($stats['jumlah_selesai'] ?? 0) }} / {{ number_format($stats['jumlah_tertunda'] ?? 0) }}</div>
                            <div>Check-in / Check-out: {{ number_format($stats['jumlah_checkin'] ?? 0) }} / {{ number_format($stats['jumlah_checkout'] ?? 0) }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-xs text-gray-500 space-y-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            <div>Jarak: {{ number_format($stats['jumlah_jarak'] ?? 0, 1) }} km</div>
                            <div>Kos: RM {{ number_format($stats['jumlah_kos'] ?? 0, 2) }}</div>
                            <div>Program: {{ number_format($stats['jumlah_program'] ?? 0) }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <x-ui.action-buttons
                            :show-url="route('laporan.laporan-kenderaan.show', $vehicle)"
                            :show-view="true"
                            :show-edit="false"
                            :show-delete="false"
                            :custom-actions="[
                                [
                                    'url' => route('laporan.laporan-kenderaan.pdf', $vehicle),
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
                    <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Tiada kenderaan ditemui buat masa ini.</td>
                </tr>
            @endforelse
        </x-ui.data-table>
        </div>

        <!-- Mobile Card View -->
        <div class="mobile-table-card">
            @forelse($vehiclesCollection as $vehicle)
                @php
                    $stats = $vehicleData->get($vehicle->id);
                @endphp

                <div class="mobile-card">
                    <!-- Header: No. Plat + Status -->
                    <div class="mobile-card-header">
                        <div class="mobile-card-title">
                            {{ $vehicle->no_plat }}
                            <div class="mobile-card-value-secondary">{{ trim(($vehicle->jenama ?? '') . ' ' . ($vehicle->model ?? '')) }}</div>
                        </div>
                        <div class="mobile-card-badge">
                            <x-ui.status-badge :status="$vehicle->status" />
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="mobile-card-body">
                        <!-- Lokasi -->
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">location_on</span></span>
                            <span class="mobile-card-value">
                                {{ $vehicle->bahagian->nama_bahagian ?? '-' }}
                                <div class="mobile-card-value-secondary">{{ $vehicle->stesen->nama_stesen ?? 'Semua Stesen' }}</div>
                            </span>
                        </div>

                        <!-- Bahan Api -->
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">local_gas_station</span></span>
                            <span class="mobile-card-value">{{ ucfirst($vehicle->jenis_bahan_api_label ?? '-') }}</span>
                        </div>

                        <!-- Statistik Log -->
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">history</span></span>
                            <span class="mobile-card-value">
                                Jumlah Log: <strong>{{ number_format($stats['jumlah_log'] ?? 0) }}</strong>
                                <div class="mobile-card-value-secondary">Aktif / Selesai / Tertunda: {{ number_format($stats['jumlah_aktif'] ?? 0) }} / {{ number_format($stats['jumlah_selesai'] ?? 0) }} / {{ number_format($stats['jumlah_tertunda'] ?? 0) }}</div>
                                <div class="mobile-card-value-secondary">Check-in / Check-out: {{ number_format($stats['jumlah_checkin'] ?? 0) }} / {{ number_format($stats['jumlah_checkout'] ?? 0) }}</div>
                            </span>
                        </div>

                        <!-- Jarak & Kos -->
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">straighten</span></span>
                            <span class="mobile-card-value">Jarak: <strong>{{ number_format($stats['jumlah_jarak'] ?? 0, 1) }}</strong> km</span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">payments</span></span>
                            <span class="mobile-card-value">Kos: <strong>RM {{ number_format($stats['jumlah_kos'] ?? 0, 2) }}</strong></span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">event</span></span>
                            <span class="mobile-card-value">Program: {{ number_format($stats['jumlah_program'] ?? 0) }}</span>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="mobile-card-footer">
                        <a href="{{ route('laporan.laporan-kenderaan.show', $vehicle) }}" class="mobile-card-action mobile-action-view">
                            <span class="material-symbols-outlined mobile-card-action-icon">visibility</span>
                            <span class="mobile-card-action-label">Lihat</span>
                        </a>
                        <a href="{{ route('laporan.laporan-kenderaan.pdf', $vehicle) }}" class="mobile-card-action" style="color:#dc2626;">
                            <span class="material-symbols-outlined mobile-card-action-icon">picture_as_pdf</span>
                            <span class="mobile-card-action-label">PDF</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="mobile-empty-state">
                    <span class="material-symbols-outlined" style="font-size:48px; color:#9ca3af;">directions_car</span>
                    <p>Tiada kenderaan ditemui</p>
                </div>
            @endforelse
        </div>

        <x-ui.pagination :paginator="$vehicles" record-label="kenderaan" />
    </x-ui.page-header>
</x-dashboard-layout>
