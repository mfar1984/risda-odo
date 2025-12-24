@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout title="Laporan Kilometer Program">
    <x-ui.page-header
        title="Laporan Kilometer: {{ $program->nama_program }}"
        description="Perincian jarak perjalanan, log dan kos untuk program ini"
    >
        <div class="space-y-8">
            <x-ui.card>
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Maklumat Program</h3>
                        <p class="text-xs text-gray-500 mt-1">Rincian asas program dan tugasan</p>
                    </div>
                    <x-ui.status-badge :status="$program->status" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div class="space-y-3">
                        <div>
                            <x-forms.input-label value="Nama Program" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $program->nama_program }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Lokasi" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $program->lokasi_program ?? '-' }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Anggaran KM" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $stats['jarak_anggaran'] ? formatNombor($stats['jarak_anggaran'], 1) . ' km' : '-' }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Pemohon" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $program->pemohon->nama_penuh ?? '-' }}" readonly />
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <x-forms.input-label value="Tarikh Mula" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ formatTarikhMasa($program->tarikh_mula) }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Tarikh Selesai" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ formatTarikhMasa($program->tarikh_selesai) }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Pemandu Tugasan" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $program->pemandu->nama_penuh ?? '-' }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Kenderaan" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $program->kenderaan ? $program->kenderaan->no_plat . ' - ' . trim(($program->kenderaan->jenama ?? '') . ' ' . ($program->kenderaan->model ?? '')) : '-' }}" readonly />
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <x-ui.stat-card icon="history" icon-color="text-blue-600" :value="formatNombor($stats['jumlah_log'])" label="Jumlah Log" />
                <x-ui.stat-card icon="alt_route" icon-color="text-green-600" :value="formatNombor($stats['jumlah_jarak'], 1)" suffix=" km" label="Jarak Direkod" />
                <x-ui.stat-card icon="straighten" icon-color="text-emerald-600" :value="formatNombor($stats['jarak_anggaran'], 1)" suffix=" km" label="Jarak Anggaran" />
                <x-ui.stat-card icon="difference" icon-color="text-indigo-600" :value="formatNombor(($stats['jumlah_jarak'] ?? 0) - ($stats['jarak_anggaran'] ?? 0), 1)" suffix=" km" label="Perbezaan" />
                <x-ui.stat-card icon="swap_horiz" icon-color="text-rose-600" :value="formatNombor($stats['jumlah_checkin'])" label="Jumlah Check-in" />
                <x-ui.stat-card icon="assignment_turned_in" icon-color="text-red-500" :value="formatNombor($stats['jumlah_checkout'])" label="Jumlah Check-out" />
            </div>

            <x-ui.card>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600">receipt_long</span>
                        <h3 class="text-base font-semibold text-gray-900">Senarai Log Kilometer</h3>
                    </div>
                    <div class="text-xs text-gray-500">Jumlah rekod: {{ formatNombor($logs->count()) }}</div>
                </div>

                <!-- Desktop Table -->
                <div class="data-table-container">
                <x-ui.data-table
                    :headers="[
                        ['label' => 'Tarikh', 'align' => 'text-left'],
                        ['label' => 'Pemandu', 'align' => 'text-left'],
                        ['label' => 'Kenderaan', 'align' => 'text-left'],
                        ['label' => 'Jarak', 'align' => 'text-left'],
                        ['label' => 'Check-in / Check-out', 'align' => 'text-left'],
                        ['label' => 'Kos (RM)', 'align' => 'text-left'],
                        ['label' => 'Status', 'align' => 'text-left'],
                    ]"
                    :actions="false"
                    empty-message="Tiada log direkod untuk program ini."
                >
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div>{{ formatTarikh($log->tarikh_perjalanan) }}</div>
                                <div class="text-xs text-gray-500">Dicipta: {{ formatTarikhMasa($log->created_at) }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div>{{ $log->pemandu->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500">Catatan: {{ $log->destinasi ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div>{{ $log->kenderaan->no_plat ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ trim(($log->kenderaan->jenama ?? '') . ' ' . ($log->kenderaan->model ?? '')) ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div>Jarak: {{ $log->jarak ? formatNombor($log->jarak, 1) . ' km' : '-' }}</div>
                                <div class="text-xs text-gray-500">Odometer: {{ $log->odometer_keluar_label ?? '-' }} → {{ $log->odometer_masuk_label ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div>Check-in: {{ $log->masa_keluar_label ?? '-' }}</div>
                                <div>Check-out: {{ $log->masa_masuk_label ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ formatWang($log->kos_minyak ?? 0) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <x-ui.status-badge :status="$log->status" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">Tiada log direkod buat masa ini.</td>
                        </tr>
                    @endforelse
                </x-ui.data-table>
                </div>

                <!-- Mobile Card View -->
                <div class="mobile-table-card">
                    @forelse($logs as $log)
                        <div class="mobile-card">
                            <div class="mobile-card-header">
                                <div class="mobile-card-title">{{ formatTarikh($log->tarikh_perjalanan) }}</div>
                                <div class="mobile-card-badge"><x-ui.status-badge :status="$log->status" /></div>
                            </div>
                            <div class="mobile-card-body">
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label"><span class="material-symbols-outlined">person</span></span>
                                    <span class="mobile-card-value">{{ $log->pemandu->name ?? '-' }}</span>
                                </div>
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label"><span class="material-symbols-outlined">directions_car</span></span>
                                    <span class="mobile-card-value">{{ $log->kenderaan->no_plat ?? '-' }}<div class="mobile-card-value-secondary">{{ trim(($log->kenderaan->jenama ?? '') . ' ' . ($log->kenderaan->model ?? '')) ?: '-' }}</div></span>
                                </div>
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label"><span class="material-symbols-outlined">straighten</span></span>
                                    <span class="mobile-card-value">{{ $log->jarak ? formatNombor($log->jarak, 1) . ' km' : '-' }}</span>
                                </div>
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label"><span class="material-symbols-outlined">swap_horiz</span></span>
                                    <span class="mobile-card-value">Check-in: {{ $log->masa_keluar_label ?? '-' }}<div class="mobile-card-value-secondary">Check-out: {{ $log->masa_masuk_label ?? '-' }}</div></span>
                                </div>
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label"><span class="material-symbols-outlined">payments</span></span>
                                    <span class="mobile-card-value">{{ formatWang($log->kos_minyak ?? 0) }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="mobile-empty-state">
                            <span class="material-symbols-outlined" style="font-size:48px; color:#9ca3af;">receipt_long</span>
                            <p>Tiada log direkod</p>
                        </div>
                    @endforelse
                </div>
            </x-ui.card>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-ui.card>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="material-symbols-outlined text-blue-600">group</span>
                        <h3 class="text-base font-semibold text-gray-900">Pemandu Terlibat</h3>
                    </div>
                    <!-- Desktop Table -->
                    <div class="data-table-container">
                    <x-ui.data-table
                        :headers="[
                            ['label' => 'Nama', 'align' => 'text-left'],
                            ['label' => 'Jumlah Log', 'align' => 'text-center'],
                            ['label' => 'Jarak (km)', 'align' => 'text-right'],
                        ]"
                        :actions="false"
                        empty-message="Tiada pemandu direkod."
                    >
                        @forelse($pemanduSummary as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $row['nama'] }}</td>
                                <td class="px-6 py-4 text-sm text-center text-gray-900">{{ number_format($row['jumlah_log']) }}</td>
                                <td class="px-6 py-4 text-sm text-right text-gray-900">{{ number_format($row['jumlah_jarak'], 1) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-500">Tiada pemandu direkod.</td>
                            </tr>
                        @endforelse
                    </x-ui.data-table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="mobile-table-card">
                        @forelse($pemanduSummary as $row)
                            <div class="mobile-card">
                                <div class="mobile-card-body">
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><span class="material-symbols-outlined">person</span></span>
                                        <span class="mobile-card-value">{{ $row['nama'] }}</span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><span class="material-symbols-outlined">history</span></span>
                                        <span class="mobile-card-value">{{ number_format($row['jumlah_log']) }} log</span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><span class="material-symbols-outlined">straighten</span></span>
                                        <span class="mobile-card-value" style="font-weight: bold;">{{ number_format($row['jumlah_jarak'], 1) }} km</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="mobile-empty-state">
                                <span class="material-symbols-outlined" style="font-size:48px; color:#9ca3af;">person_off</span>
                                <p>Tiada pemandu terlibat</p>
                            </div>
                        @endforelse
                    </div>
                </x-ui.card>

                <x-ui.card>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="material-symbols-outlined text-blue-600">directions_car</span>
                        <h3 class="text-base font-semibold text-gray-900">Kenderaan Digunakan</h3>
                    </div>
                    <!-- Desktop Table -->
                    <div class="data-table-container">
                    <x-ui.data-table
                        :headers="[
                            ['label' => 'Kenderaan', 'align' => 'text-left'],
                            ['label' => 'Jumlah Log', 'align' => 'text-center'],
                            ['label' => 'Jarak (km)', 'align' => 'text-right'],
                        ]"
                        :actions="false"
                        empty-message="Tiada kenderaan terlibat."
                    >
                        @forelse($kenderaanSummary as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $row['no_plat'] }}<br><span class="text-xs text-gray-500">{{ $row['nama'] }}</span></td>
                                <td class="px-6 py-4 text-sm text-center text-gray-900">{{ number_format($row['jumlah_log']) }}</td>
                                <td class="px-6 py-4 text-sm text-right text-gray-900">{{ number_format($row['jumlah_jarak'], 1) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-500">Tiada kenderaan terlibat.</td>
                            </tr>
                        @endforelse
                    </x-ui.data-table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="mobile-table-card">
                        @forelse($kenderaanSummary as $row)
                            <div class="mobile-card">
                                <div class="mobile-card-body">
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><span class="material-symbols-outlined">directions_car</span></span>
                                        <span class="mobile-card-value">{{ $row['no_plat'] }}<div class="mobile-card-value-secondary">{{ $row['nama'] }}</div></span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><span class="material-symbols-outlined">history</span></span>
                                        <span class="mobile-card-value">{{ number_format($row['jumlah_log']) }} log</span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><span class="material-symbols-outlined">straighten</span></span>
                                        <span class="mobile-card-value" style="font-weight: bold;">{{ number_format($row['jumlah_jarak'], 1) }} km</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="mobile-empty-state">
                                <span class="material-symbols-outlined" style="font-size:48px; color:#9ca3af;">directions_car</span>
                                <p>Tiada kenderaan terlibat</p>
                            </div>
                        @endforelse
                    </div>
                </x-ui.card>
            </div>
        </div>
    </x-ui.page-header>
</x-dashboard-layout>

