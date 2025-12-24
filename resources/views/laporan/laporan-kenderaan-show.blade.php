@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout title="Laporan Kenderaan">
    <x-ui.page-header
        title="Laporan Kenderaan: {{ $kenderaan->no_plat }}"
        description="Analisis terperinci penggunaan dan prestasi kenderaan"
    >
        <div class="space-y-8">
            <x-ui.card>
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Maklumat Kenderaan</h3>
                        <p class="text-xs text-gray-500 mt-1">Profil kenderaan dan status semasa</p>
                    </div>
                    <x-ui.status-badge :status="$kenderaan->status" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div class="space-y-3">
                        <div>
                            <x-forms.input-label value="No. Plat" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $kenderaan->no_plat }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Jenama" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $kenderaan->jenama ?? '-' }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Model" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $kenderaan->model ?? '-' }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Jenis Bahan Api" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ ucfirst($kenderaan->jenis_bahan_api_label ?? '-') }}" readonly />
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <x-forms.input-label value="Bahagian" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $kenderaan->bahagian->nama_bahagian ?? '-' }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Stesen" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $kenderaan->stesen->nama_stesen ?? 'Semua Stesen' }}" readonly />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-forms.input-label value="Tahun" />
                                <x-forms.text-input class="mt-1 block w-full" value="{{ $kenderaan->tahun ?? '-' }}" readonly />
                            </div>
                            <div>
                                <x-forms.input-label value="Cukai Tamat" />
                                <x-forms.text-input class="mt-1 block w-full" value="{{ $kenderaan->cukai_tamat_tempoh ? formatTarikh($kenderaan->cukai_tamat_tempoh) : '-' }}" readonly />
                            </div>
                        </div>
                        <div>
                            <x-forms.input-label value="Tarikh Pendaftaran" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $kenderaan->tarikh_pendaftaran ? formatTarikh($kenderaan->tarikh_pendaftaran) : '-' }}" readonly />
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <x-ui.stat-card icon="history" icon-color="text-blue-600" :value="formatNombor($stats['jumlah_log'])" label="Jumlah Log" />
                <x-ui.stat-card icon="play_arrow" icon-color="text-emerald-600" :value="formatNombor($stats['jumlah_aktif'])" label="Log Aktif" />
                <x-ui.stat-card icon="task_alt" icon-color="text-green-600" :value="formatNombor($stats['jumlah_selesai'])" label="Log Selesai" />
                <x-ui.stat-card icon="pending_actions" icon-color="text-amber-600" :value="formatNombor($stats['jumlah_tertunda'])" label="Log Tertunda" />
                <x-ui.stat-card icon="alt_route" icon-color="text-rose-600" :value="formatNombor($stats['jarak'], 1)" suffix=" km" label="Jarak Direkod" />
                <x-ui.stat-card icon="payments" icon-color="text-red-500" :value="formatWang($stats['kos'])" label="Jumlah Kos" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <span class="material-symbols-outlined text-blue-600 mr-3" style="font-size: 32px;">local_gas_station</span>
                        <div>
                            <p class="text-xs text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Kos Bahan Api</p>
                            <p class="text-xl font-bold text-blue-600" style="font-family: Poppins, sans-serif !important;">{{ formatWang($stats['kos_minyak'] ?? 0) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <span class="material-symbols-outlined text-purple-600 mr-3" style="font-size: 32px;">build</span>
                        <div>
                            <p class="text-xs text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Kos Selenggara</p>
                            <p class="text-xl font-bold text-purple-600" style="font-family: Poppins, sans-serif !important;">{{ formatWang($stats['kos_selenggara'] ?? 0) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <span class="material-symbols-outlined text-green-600 mr-3" style="font-size: 32px;">build</span>
                        <div>
                            <p class="text-xs text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Jumlah Selenggara</p>
                            <p class="text-xl font-bold text-green-600" style="font-family: Poppins, sans-serif !important;">{{ formatNombor($stats['jumlah_selenggara'] ?? 0) }} rekod</p>
                        </div>
                    </div>
                </div>
            </div>

            <x-ui.card>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600">receipt_long</span>
                        <h3 class="text-base font-semibold text-gray-900">Senarai Log Kenderaan</h3>
                    </div>
                    <div class="text-xs text-gray-500">Jumlah rekod: {{ formatNombor($logs->count()) }}</div>
                </div>

                <!-- Desktop Table -->
                <div class="data-table-container">
                <x-ui.data-table
                    :headers="[
                        ['label' => 'Tarikh & Masa', 'align' => 'text-left'],
                        ['label' => 'Pemandu', 'align' => 'text-left'],
                        ['label' => 'Program', 'align' => 'text-left'],
                        ['label' => 'Check-in / Check-out', 'align' => 'text-left'],
                        ['label' => 'Jarak (km)', 'align' => 'text-left'],
                        ['label' => 'Kos (RM)', 'align' => 'text-left'],
                        ['label' => 'Status', 'align' => 'text-left'],
                    ]"
                    :actions="false"
                    empty-message="Tiada log direkod untuk kenderaan ini."
                >
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div>{{ $log->tarikh_perjalanan ? formatTarikh($log->tarikh_perjalanan) : '-' }}</div>
                                <div class="text-xs text-gray-500">Keluar: {{ $log->masa_keluar ? formatMasa(\Carbon\Carbon::parse($log->masa_keluar)) : '-' }}</div>
                                <div class="text-xs text-gray-500">Masuk: {{ $log->masa_masuk ? formatMasa(\Carbon\Carbon::parse($log->masa_masuk)) : '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div>{{ $log->pemandu->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500">Catatan: {{ $log->destinasi ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div>{{ $log->program->nama_program ?? '-' }}</div>
                                <div class="text-xs text-gray-500">Status: {{ ucfirst($log->program->status ?? '-') }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div>Check-in: {{ $log->masa_keluar_label ?? '-' }}</div>
                                <div>Check-out: {{ $log->masa_masuk_label ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div>{{ $log->jarak ? formatNombor($log->jarak, 1) : '0.0' }}</div>
                                <div class="text-xs text-gray-500">Odometer: {{ $log->odometer_keluar_label ?? '-' }} → {{ $log->odometer_masuk_label ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $log->kos_minyak ? formatWang($log->kos_minyak) : '-' }}
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
                                <div class="mobile-card-title">
                                    {{ $log->tarikh_perjalanan ? formatTarikh($log->tarikh_perjalanan) : '-' }}
                                    <div class="mobile-card-value-secondary">Keluar: {{ $log->masa_keluar ? formatMasa(\Carbon\Carbon::parse($log->masa_keluar)) : '-' }}@if($log->masa_masuk) • Masuk: {{ formatMasa(\Carbon\Carbon::parse($log->masa_masuk)) }} @endif</div>
                                </div>
                                <div class="mobile-card-badge">
                                    <x-ui.status-badge :status="$log->status" />
                                </div>
                            </div>

                            <div class="mobile-card-body">
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label"><span class="material-symbols-outlined">person</span></span>
                                    <span class="mobile-card-value">{{ $log->pemandu->name ?? '-' }}</span>
                                </div>
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label"><span class="material-symbols-outlined">event</span></span>
                                    <span class="mobile-card-value">
                                        {{ $log->program->nama_program ?? '-' }}
                                        <div class="mobile-card-value-secondary">Status: {{ ucfirst($log->program->status ?? '-') }}</div>
                                    </span>
                                </div>
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label"><span class="material-symbols-outlined">straighten</span></span>
                                    <span class="mobile-card-value">Jarak: {{ $log->jarak ? formatNombor($log->jarak, 1) : '0.0' }} km</span>
                                </div>
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label"><span class="material-symbols-outlined">payments</span></span>
                                    <span class="mobile-card-value">Kos: {{ $log->kos_minyak ? formatWang($log->kos_minyak) : '-' }}</span>
                                </div>
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label"><span class="material-symbols-outlined">speed</span></span>
                                    <span class="mobile-card-value-secondary">Odometer: {{ $log->odometer_keluar_label ?? '-' }} → {{ $log->odometer_masuk_label ?? '-' }}</span>
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

            <x-ui.card>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-purple-600">build</span>
                        <h3 class="text-base font-semibold text-gray-900">Rekod Penyelenggaraan</h3>
                    </div>
                    <div class="text-xs text-gray-500">Jumlah rekod: {{ formatNombor($maintenance->count() ?? 0) }}</div>
                </div>

                <!-- Desktop Table -->
                <div class="data-table-container">
                <x-ui.data-table
                    :headers="[
                        ['label' => 'Tarikh', 'align' => 'text-left'],
                        ['label' => 'Kategori Kos', 'align' => 'text-left'],
                        ['label' => 'Keterangan', 'align' => 'text-left'],
                        ['label' => 'Kos (RM)', 'align' => 'text-right'],
                        ['label' => 'Status', 'align' => 'text-center'],
                    ]"
                    :actions="false"
                    empty-message="Tiada rekod penyelenggaraan untuk kenderaan ini."
                >
                    @forelse($maintenance as $selenggara)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div>{{ formatTarikh($selenggara->tarikh_mula) }}</div>
                                <div class="text-xs text-gray-500">hingga {{ formatTarikh($selenggara->tarikh_selesai) }}</div>
                                <div class="text-xs text-gray-400">{{ $selenggara->jumlah_hari }} hari</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div>{{ $selenggara->kategoriKos->nama_kategori ?? '-' }}</div>
                                @if($selenggara->tukar_minyak)
                                    <div class="text-xs text-blue-600">🔧 Tukar Minyak ({{ formatNombor($selenggara->jangka_hayat_km) }} km)</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="max-w-xs truncate" title="{{ $selenggara->keterangan }}">
                                    {{ $selenggara->keterangan ?? '-' }}
                                </div>
                                @if($selenggara->pelaksana)
                                    <div class="text-xs text-gray-500">Oleh: {{ $selenggara->pelaksana->name }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-right font-medium text-gray-900">
                                {{ formatWang($selenggara->jumlah_kos) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <x-ui.status-badge :status="$selenggara->status" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">Tiada rekod penyelenggaraan buat masa ini.</td>
                        </tr>
                    @endforelse
                </x-ui.data-table>
                </div>

                <!-- Mobile Card View -->
                <div class="mobile-table-card">
                    @forelse($maintenance as $selenggara)
                        <div class="mobile-card">
                            <div class="mobile-card-header">
                                <div class="mobile-card-title">
                                    {{ $selenggara->tarikh_mula->format('d/m/Y') }} → {{ $selenggara->tarikh_selesai->format('d/m/Y') }}
                                    <div class="mobile-card-value-secondary">{{ $selenggara->jumlah_hari }} hari</div>
                                </div>
                                <div class="mobile-card-badge">
                                    <x-ui.status-badge :status="$selenggara->status" />
                                </div>
                            </div>

                            <div class="mobile-card-body">
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label"><span class="material-symbols-outlined">category</span></span>
                                    <span class="mobile-card-value">
                                        {{ $selenggara->kategoriKos->nama_kategori ?? '-' }}
                                        @if($selenggara->tukar_minyak)
                                            <div class="mobile-card-value-secondary">Tukar Minyak ({{ number_format($selenggara->jangka_hayat_km) }} km)</div>
                                        @endif
                                    </span>
                                </div>
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label"><span class="material-symbols-outlined">description</span></span>
                                    <span class="mobile-card-value">{{ $selenggara->keterangan ?? '-' }}</span>
                                </div>
                                @if($selenggara->pelaksana)
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label"><span class="material-symbols-outlined">person</span></span>
                                    <span class="mobile-card-value-secondary">Oleh: {{ $selenggara->pelaksana->name }}</span>
                                </div>
                                @endif
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label"><span class="material-symbols-outlined">payments</span></span>
                                    <span class="mobile-card-value"><strong>RM {{ number_format($selenggara->jumlah_kos, 2) }}</strong></span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="mobile-empty-state">
                            <span class="material-symbols-outlined" style="font-size:48px; color:#9ca3af;">build</span>
                            <p>Tiada rekod penyelenggaraan</p>
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
                                <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-500">Tiada pemandu terlibat.</td>
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
                                        <span class="mobile-card-value" style="font-weight: bold;">{{ number_format($row['jarak'], 1) }} km</span>
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
                        <span class="material-symbols-outlined text-blue-600">event</span>
                        <h3 class="text-base font-semibold text-gray-900">Program Disertai</h3>
                    </div>
                    <!-- Desktop Table -->
                    <div class="data-table-container">
                    <x-ui.data-table
                        :headers="[
                            ['label' => 'Program', 'align' => 'text-left'],
                            ['label' => 'Tarikh', 'align' => 'text-left'],
                            ['label' => 'Jumlah Log', 'align' => 'text-center'],
                            ['label' => 'Jarak (km)', 'align' => 'text-right']
                        ]"
                        :actions="false"
                        empty-message="Tiada program direkod."
                    >
                        @forelse($programSummary as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $row['nama_program'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $row['tarikh_mula'] }} - {{ $row['tarikh_selesai'] }}</td>
                                <td class="px-6 py-4 text-sm text-center text-gray-900">{{ number_format($row['jumlah_log']) }}</td>
                                <td class="px-6 py-4 text-sm text-right text-gray-900">{{ number_format($row['jarak'], 1) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">Tiada program direkod.</td>
                            </tr>
                        @endforelse
                    </x-ui.data-table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="mobile-table-card">
                        @forelse($programSummary as $row)
                            <div class="mobile-card">
                                <div class="mobile-card-body">
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><span class="material-symbols-outlined">assignment</span></span>
                                        <span class="mobile-card-value">{{ $row['nama_program'] }}</span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><span class="material-symbols-outlined">today</span></span>
                                        <span class="mobile-card-value">{{ $row['tarikh_mula'] }} - {{ $row['tarikh_selesai'] }}</span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><span class="material-symbols-outlined">history</span></span>
                                        <span class="mobile-card-value">{{ number_format($row['jumlah_log']) }} log</span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><span class="material-symbols-outlined">straighten</span></span>
                                        <span class="mobile-card-value" style="font-weight: bold;">{{ number_format($row['jarak'], 1) }} km</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="mobile-empty-state">
                                <span class="material-symbols-outlined" style="font-size:48px; color:#9ca3af;">event</span>
                                <p>Tiada program direkod</p>
                            </div>
                        @endforelse
                    </div>
                </x-ui.card>
            </div>
        </div>
    </x-ui.page-header>
</x-dashboard-layout>

