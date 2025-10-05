@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout title="Laporan Program">
    <x-ui.page-header
        title="Laporan Program: {{ $program->nama_program }}"
        description="Maklumat terperinci program beserta log, pemandu dan kenderaan"
    >
        <div class="space-y-8">
            <!-- Maklumat Program Asas -->
            <x-ui.card>
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Maklumat Program</h3>
                        <p class="text-xs text-gray-500 mt-1">Rincian ringkas status dan jadual program</p>
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
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $program->jarak_anggaran ? number_format($program->jarak_anggaran, 1) . ' km' : '-' }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Pemohon" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $program->pemohon->nama_penuh ?? '-' }}" readonly />
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <x-forms.input-label value="Tarikh Mula" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $program->tarikh_mula ? $program->tarikh_mula->format('d/m/Y H:i') : '-' }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Tarikh Selesai" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $program->tarikh_selesai ? $program->tarikh_selesai->format('d/m/Y H:i') : '-' }}" readonly />
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

                @if($program->penerangan)
                    <div class="mt-6">
                        <x-forms.input-label value="Penerangan" />
                        <textarea class="mt-1 block w-full form-textarea" rows="3" readonly>{{ $program->penerangan }}</textarea>
                    </div>
                @endif
            </x-ui.card>

            <!-- Statistik Program -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-4">
                <x-ui.stat-card icon="history" icon-color="text-blue-600" :value="number_format($stats['jumlah_log'])" label="Jumlah Log" />
                <x-ui.stat-card icon="group" icon-color="text-emerald-600" :value="number_format($stats['jumlah_pemandu'])" label="Pemandu Terlibat" />
                <x-ui.stat-card icon="directions_car" icon-color="text-yellow-600" :value="number_format($stats['jumlah_kenderaan'])" label="Kenderaan Digunakan" />
                <x-ui.stat-card icon="swap_horiz" icon-color="text-purple-600" :value="number_format($stats['jumlah_checkin'])" label="Jumlah Check-in" />
                <x-ui.stat-card icon="assignment_turned_in" icon-color="text-indigo-600" :value="number_format($stats['jumlah_checkout'])" label="Jumlah Check-out" />
                <x-ui.stat-card icon="alt_route" icon-color="text-rose-600" :value="number_format($stats['jarak_km'], 1)" suffix=" km" label="Jarak Direkod" />
                <x-ui.stat-card icon="payments" icon-color="text-red-500" :value="number_format($stats['kos'], 2)" prefix="RM " label="Kos Bahan Api" />
            </div>

            <!-- Ringkasan Status -->
            <x-ui.card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-900">Ringkasan Status Log</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <div class="p-4 rounded-lg border border-blue-100 bg-blue-50">
                        <div class="text-xs font-semibold text-blue-600 uppercase">Dalam Perjalanan</div>
                        <div class="text-2xl font-bold text-blue-700 mt-1">{{ number_format($statusSummary['dalam_perjalanan']) }}</div>
                    </div>
                    <div class="p-4 rounded-lg border border-green-100 bg-green-50">
                        <div class="text-xs font-semibold text-green-600 uppercase">Selesai</div>
                        <div class="text-2xl font-bold text-green-700 mt-1">{{ number_format($statusSummary['selesai']) }}</div>
                    </div>
                    <div class="p-4 rounded-lg border border-amber-100 bg-amber-50">
                        <div class="text-xs font-semibold text-amber-600 uppercase">Tertunda</div>
                        <div class="text-2xl font-bold text-amber-700 mt-1">{{ number_format($statusSummary['tertunda']) }}</div>
                    </div>
                </div>
            </x-ui.card>

            <!-- Jadual Log Pemandu -->
            <x-ui.card>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600">receipt_long</span>
                        <h3 class="text-base font-semibold text-gray-900">Senarai Log Pemandu</h3>
                    </div>
                    <div class="text-xs text-gray-500">Jumlah rekod: {{ number_format($logs->count()) }}</div>
                </div>

                <!-- Desktop Table -->
                <div class="data-table-container">
                    <x-ui.data-table
                    :headers="[
                        ['label' => 'Tarikh & Masa', 'align' => 'text-left'],
                        ['label' => 'Pemandu', 'align' => 'text-left'],
                        ['label' => 'Kenderaan', 'align' => 'text-left'],
                        ['label' => 'Check-in / Check-out', 'align' => 'text-left'],
                        ['label' => 'Odometer', 'align' => 'text-left'],
                        ['label' => 'Status', 'align' => 'text-left'],
                        ['label' => 'Jarak (km)', 'align' => 'text-right']
                    ]"
                    :actions="false"
                    empty-message="Tiada log direkod untuk program ini."
                >
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div>{{ $log->tarikh_perjalanan ? $log->tarikh_perjalanan->format('d/m/Y') : '-' }}</div>
                                <div class="text-xs text-gray-500">Dicipta: {{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div>{{ $log->pemandu->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500">Catatan: {{ $log->destinasi ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div>{{ $log->kenderaan->no_plat ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ trim(($log->kenderaan->jenama ?? '') . ' ' . ($log->kenderaan->model ?? '')) }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div>Check-in: {{ $log->masa_keluar_label ?? '-' }}</div>
                                <div>Check-out: {{ $log->masa_masuk_label ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div>Keluar: {{ $log->odometer_keluar ? number_format($log->odometer_keluar) . ' km' : '-' }}</div>
                                <div>Masuk: {{ $log->odometer_masuk ? number_format($log->odometer_masuk) . ' km' : '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <x-ui.status-badge :status="$log->status" />
                            </td>
                            <td class="px-6 py-4 text-sm text-right text-gray-900">
                                {{ $log->jarak ? number_format($log->jarak, 1) : '0.0' }}
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
                            <!-- Card Header -->
                            <div class="mobile-card-header">
                                <div class="mobile-card-title">{{ $log->tarikh_perjalanan ? $log->tarikh_perjalanan->format('d/m/Y') : '-' }}</div>
                                <div class="mobile-card-badge">
                                    <x-ui.status-badge :status="$log->status" />
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="mobile-card-body">
                                <!-- Pemandu -->
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">person</span>
                                    </span>
                                    <span class="mobile-card-value">{{ $log->pemandu->name ?? '-' }}</span>
                                </div>

                                <!-- Destinasi -->
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">location_on</span>
                                    </span>
                                    <span class="mobile-card-value-secondary">{{ $log->destinasi ?? '-' }}</span>
                                </div>

                                <!-- Kenderaan -->
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">directions_car</span>
                                    </span>
                                    <span class="mobile-card-value">
                                        {{ $log->kenderaan->no_plat ?? '-' }}
                                        <span class="mobile-card-value-secondary">{{ trim(($log->kenderaan->jenama ?? '') . ' ' . ($log->kenderaan->model ?? '')) }}</span>
                                    </span>
                                </div>

                                <!-- Check-in/out -->
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">swap_horiz</span>
                                    </span>
                                    <span class="mobile-card-value">
                                        In: {{ $log->masa_keluar_label ?? '-' }}
                                        <span class="mobile-card-value-secondary">Out: {{ $log->masa_masuk_label ?? '-' }}</span>
                                    </span>
                                </div>

                                <!-- Odometer -->
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">speed</span>
                                    </span>
                                    <span class="mobile-card-value">
                                        {{ $log->odometer_keluar ? number_format($log->odometer_keluar) : '-' }} → {{ $log->odometer_masuk ? number_format($log->odometer_masuk) : '-' }} km
                                    </span>
                                </div>

                                <!-- Jarak -->
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">straighten</span>
                                    </span>
                                    <span class="mobile-card-value" style="font-weight: bold;">{{ $log->jarak ? number_format($log->jarak, 1) : '0.0' }} km</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="mobile-empty-state">
                            <span class="material-symbols-outlined" style="font-size: 48px; color: #9ca3af;">inbox</span>
                            <p>Tiada log direkod</p>
                        </div>
                    @endforelse
                </div>
            </x-ui.card>

            <!-- Ringkasan Pemandu & Kenderaan -->
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
                            ['label' => 'Jarak (km)', 'align' => 'text-right']
                        ]"
                        :actions="false"
                        empty-message="Tiada pemandu direkod."
                    >
                        @forelse($pemanduSummary as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $row['nama'] }}</td>
                                <td class="px-6 py-4 text-sm text-center text-gray-900">{{ number_format($row['jumlah_log']) }}</td>
                                <td class="px-6 py-4 text-sm text-right text-gray-900">{{ number_format($row['jarak'], 1) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-500">Tiada pemandu terlibat untuk program ini.</td>
                            </tr>
                        @endforelse
                    </x-ui.data-table>
                    </div>

                    <!-- Mobile Card View - Pemandu -->
                    <div class="mobile-table-card">
                        @forelse($pemanduSummary as $row)
                            <div class="mobile-card">
                                <div class="mobile-card-body">
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">person</span>
                                        </span>
                                        <span class="mobile-card-value">{{ $row['nama'] }}</span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">history</span>
                                        </span>
                                        <span class="mobile-card-value">{{ number_format($row['jumlah_log']) }} log</span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">straighten</span>
                                        </span>
                                        <span class="mobile-card-value" style="font-weight: bold;">{{ number_format($row['jarak'], 1) }} km</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="mobile-empty-state">
                                <span class="material-symbols-outlined" style="font-size: 48px; color: #9ca3af;">person_off</span>
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
                            ['label' => 'No. Plat', 'align' => 'text-left'],
                            ['label' => 'Keterangan', 'align' => 'text-left'],
                            ['label' => 'Jumlah Log', 'align' => 'text-center'],
                            ['label' => 'Jarak (km)', 'align' => 'text-right']
                        ]"
                        :actions="false"
                        empty-message="Tiada kenderaan direkod."
                    >
                        @forelse($kenderaanSummary as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $row['no_plat'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ trim(($row['jenis'] ?? '') . ' ' . ($row['model'] ?? '')) }}</td>
                                <td class="px-6 py-4 text-sm text-center text-gray-900">{{ number_format($row['jumlah_log']) }}</td>
                                <td class="px-6 py-4 text-sm text-right text-gray-900">{{ number_format($row['jarak'], 1) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">Tiada kenderaan direkod untuk program ini.</td>
                            </tr>
                        @endforelse
                    </x-ui.data-table>
                    </div>

                    <!-- Mobile Card View - Kenderaan -->
                    <div class="mobile-table-card">
                        @forelse($kenderaanSummary as $row)
                            <div class="mobile-card">
                                <div class="mobile-card-body">
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">directions_car</span>
                                        </span>
                                        <span class="mobile-card-value">{{ $row['no_plat'] }}</span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">info</span>
                                        </span>
                                        <span class="mobile-card-value-secondary">{{ trim(($row['jenis'] ?? '') . ' ' . ($row['model'] ?? '')) }}</span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">history</span>
                                        </span>
                                        <span class="mobile-card-value">{{ number_format($row['jumlah_log']) }} log</span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">straighten</span>
                                        </span>
                                        <span class="mobile-card-value" style="font-weight: bold;">{{ number_format($row['jarak'], 1) }} km</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="mobile-empty-state">
                                <span class="material-symbols-outlined" style="font-size: 48px; color: #9ca3af;">no_crash</span>
                                <p>Tiada kenderaan direkod</p>
                            </div>
                        @endforelse
                    </div>
                </x-ui.card>
            </div>

            <div class="flex justify-between">
                <a href="{{ route('laporan.senarai-program') }}">
                    <x-buttons.secondary-button type="button">
                        <span class="material-symbols-outlined mr-2" style="font-size: 16px;">arrow_back</span>
                        Kembali ke Senarai
                    </x-buttons.secondary-button>
                </a>
            </div>
        </div>
    </x-ui.page-header>
</x-dashboard-layout>
