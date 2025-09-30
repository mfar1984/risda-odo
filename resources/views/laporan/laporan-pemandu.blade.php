@php
    $drivers = $drivers ?? collect();
    if ($drivers instanceof \Illuminate\Pagination\Paginator || $drivers instanceof \Illuminate\Pagination\LengthAwarePaginator) {
        $driverCollection = $drivers->getCollection();
    } else {
        $driverCollection = collect($drivers);
    }

    $driverData = collect($driverData ?? []);
    $overallStats = array_merge([
        'total_pemandu' => 0,
        'total_log' => 0,
        'jumlah_jarak' => 0.0,
        'jumlah_kos' => 0.0,
        'purata_jarak_log' => 0.0,
        'purata_kos_log' => 0.0,
    ], $overallStats ?? []);
@endphp

<x-dashboard-layout title="Laporan Pemandu">
    <x-ui.page-header
        title="Laporan Pemandu"
        description="Ringkasan prestasi pemandu berdasarkan log perjalanan dan penggunaan kos"
    >
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <x-ui.stat-card icon="group" icon-color="text-blue-600" :value="number_format($overallStats['total_pemandu'])" label="Jumlah Pemandu" />
            <x-ui.stat-card icon="history" icon-color="text-green-600" :value="number_format($overallStats['total_log'])" label="Jumlah Log" />
            <x-ui.stat-card icon="alt_route" icon-color="text-emerald-600" :value="number_format($overallStats['jumlah_jarak'], 1)" suffix=" km" label="Jarak Direkod" />
            <x-ui.stat-card icon="payments" icon-color="text-indigo-600" :value="number_format($overallStats['jumlah_kos'], 2)" prefix="RM " label="Kos Direkod" />
            <x-ui.stat-card icon="equalizer" icon-color="text-rose-600" :value="number_format($overallStats['purata_jarak_log'], 2)" suffix=" km" label="Purata Jarak/Log" />
            <x-ui.stat-card icon="avg_pace" icon-color="text-yellow-600" :value="number_format($overallStats['purata_kos_log'], 2)" prefix="RM " label="Purata Kos/Log" />
        </div>

        <x-ui.search-filter
            :action="route('laporan.laporan-pemandu')"
            search-placeholder="Cari nama pemandu, email atau stesen"
            :search-value="request('search')"
            :filters="[
                [
                    'name' => 'status',
                    'type' => 'select',
                    'placeholder' => 'Semua Status',
                    'options' => [
                        'aktif' => 'Aktif',
                        'tidak_aktif' => 'Tidak Aktif',
                        'digantung' => 'Digantung',
                    ]
                ],
                [
                    'name' => 'status_log',
                    'type' => 'select',
                    'placeholder' => 'Semua Status Log',
                    'options' => [
                        'dalam_perjalanan' => 'Dalam Perjalanan',
                        'selesai' => 'Selesai',
                        'tertunda' => 'Tertunda',
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
            :reset-url="route('laporan.laporan-pemandu')"
        />

        <x-ui.data-table
            :headers="[
                ['label' => 'Pemandu', 'align' => 'text-left'],
                ['label' => 'Organisasi', 'align' => 'text-left'],
                ['label' => 'Statistik Log', 'align' => 'text-left'],
                ['label' => 'Jarak & Kos', 'align' => 'text-left'],
            ]"
            empty-message="Tiada pemandu ditemui untuk penapis semasa."
        >
            @forelse($driverCollection as $driver)
                @php
                    $stats = $driverData->get($driver->id);
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 max-w-xs">
                        <div class="text-sm font-medium text-gray-900 truncate" title="{{ $driver->name }}" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            {{ $driver->name }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1 truncate" title="{{ $driver->email }}" style="font-family: Poppins, sans-serif !important; font-size: 11px !important; max-width: 12rem;">
                            {{ $driver->email }}
                        </div>
                        <div class="text-xs text-gray-400 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                            Status Akaun: {{ ucfirst($driver->status ?? '-') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            {{ $driver->bahagian->nama_bahagian ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            {{ $driver->stesen->nama_stesen ?? 'Semua Stesen' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-xs text-gray-500 space-y-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            <div>Jumlah Log: {{ number_format($stats['jumlah_log'] ?? 0) }}</div>
                            <div>Aktif / Selesai / Tertunda: {{ number_format($stats['jumlah_aktif'] ?? 0) }} / {{ number_format($stats['jumlah_selesai'] ?? 0) }} / {{ number_format($stats['jumlah_tertunda'] ?? 0) }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-xs text-gray-500 space-y-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            <div>Jarak: {{ number_format($stats['jumlah_jarak'] ?? 0, 1) }} km</div>
                            <div>Kos: RM {{ number_format($stats['jumlah_kos'] ?? 0, 2) }}</div>
                            <div>Purata Kos/Log: RM {{ number_format($stats['purata_kos'] ?? 0, 2) }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <x-ui.action-buttons
                            :show-url="route('laporan.laporan-pemandu.show', $driver)"
                            :show-view="true"
                            :show-edit="false"
                            :show-delete="false"
                            :custom-actions="[
                                [
                                    'url' => route('laporan.laporan-pemandu.pdf', $driver),
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
                    <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Tiada pemandu ditemui buat masa ini.</td>
                </tr>
            @endforelse
        </x-ui.data-table>

        <x-ui.pagination :paginator="$drivers" record-label="pemandu" />
    </x-ui.page-header>
</x-dashboard-layout>
