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
        'jumlah_jarak' => 0.0,
        'purata_jarak_log' => 0.0,
        'jumlah_kos' => 0.0,
        'jumlah_checkin' => 0,
        'jumlah_checkout' => 0,
    ], $overallStats ?? []);
@endphp

<x-dashboard-layout title="Laporan Kilometer">
    <x-ui.page-header
        title="Laporan Kilometer"
        description="Analisis jarak perjalanan mengikut program dan log pemandu"
    >
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <x-ui.stat-card icon="event" icon-color="text-blue-600" :value="number_format($overallStats['total_program'])" label="Jumlah Program" />
            <x-ui.stat-card icon="history" icon-color="text-green-600" :value="number_format($overallStats['total_log'])" label="Jumlah Log" />
            <x-ui.stat-card icon="alt_route" icon-color="text-emerald-600" :value="number_format($overallStats['jumlah_jarak'], 1)" suffix=" km" label="Jarak Direkod" />
            <x-ui.stat-card icon="speed" icon-color="text-indigo-600" :value="number_format($overallStats['purata_jarak_log'], 2)" suffix=" km" label="Purata Jarak/Log" />
            <x-ui.stat-card icon="swap_horiz" icon-color="text-rose-600" :value="number_format($overallStats['jumlah_checkin'])" label="Jumlah Check-in" />
            <x-ui.stat-card icon="assignment_turned_in" icon-color="text-red-500" :value="number_format($overallStats['jumlah_checkout'])" label="Jumlah Check-out" />
        </div>

        <x-ui.search-filter
            :action="route('laporan.laporan-kilometer')"
            search-placeholder="Cari program, lokasi atau pemohon"
            :search-value="request('search')"
            :filters="[
                [
                    'name' => 'status',
                    'type' => 'select',
                    'placeholder' => 'Semua Status',
                    'options' => [
                        'draf' => 'Draf',
                        'lulus' => 'Lulus',
                        'aktif' => 'Aktif',
                        'tertunda' => 'Tertunda',
                        'selesai' => 'Selesai',
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
            :reset-url="route('laporan.laporan-kilometer')"
        />

        <x-ui.data-table
            :headers="[
                ['label' => 'Program', 'align' => 'text-left'],
                ['label' => 'Tempoh', 'align' => 'text-left'],
                ['label' => 'Jarak', 'align' => 'text-left'],
                ['label' => 'Jurnal Log', 'align' => 'text-left'],
                ['label' => 'Kos Bahan Api', 'align' => 'text-left'],
                ['label' => 'Tindakan', 'align' => 'text-center'],
            ]"
            :actions="false"
            empty-message="Tiada program ditemui untuk penapis semasa."
        >
            @forelse($programCollection as $program)
                @php
                    $stats = $programData->get($program->id);
                    $jarakDirekod = $stats['jumlah_jarak'] ?? 0;
                    $anggara = $stats['jarak_anggaran'] ?? 0;
                    $perbezaan = $stats['perbezaan_jarak'] ?? ($jarakDirekod - $anggara);
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
                            <div>Direkod: {{ number_format($jarakDirekod, 1) }} km</div>
                            <div>Anggaran: {{ number_format($anggara, 1) }} km</div>
                            <div>Perbezaan: {{ number_format($perbezaan, 1) }} km</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-xs text-gray-500 space-y-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            <div>Jumlah Log: {{ number_format($stats['jumlah_log'] ?? 0) }}</div>
                            <div>Check-in / Check-out: {{ number_format($stats['jumlah_checkin'] ?? 0) }} / {{ number_format($stats['jumlah_checkout'] ?? 0) }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            RM {{ number_format($stats['jumlah_kos'] ?? 0, 2) }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <div class="flex justify-center space-x-2">
                            <a href="{{ route('laporan.laporan-kilometer.show', $program) }}" class="text-blue-600 hover:text-blue-900" title="Lihat Laporan">
                                <span class="material-symbols-outlined" style="font-size: 18px;">visibility</span>
                            </a>
                            <a href="{{ route('laporan.laporan-kilometer.pdf', $program) }}" class="text-red-600 hover:text-red-800" title="Eksport ke PDF">
                                <span class="material-symbols-outlined" style="font-size: 18px;">picture_as_pdf</span>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Tiada program ditemui buat masa ini.</td>
                </tr>
            @endforelse
        </x-ui.data-table>

        <x-ui.pagination :paginator="$programs" record-label="program" />
    </x-ui.page-header>
</x-dashboard-layout>
