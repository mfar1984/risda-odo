<x-dashboard-layout title="Laporan Pemandu">
    <x-ui.page-header
        title="Laporan Pemandu: {{ $driver->name }}"
        description="Perincian log perjalanan, jarak dan kos untuk pemandu ini"
    >
        <div class="space-y-8">
            <x-ui.card>
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Maklumat Pemandu</h3>
                        <p class="text-xs text-gray-500 mt-1">Profil asas pemandu dan lokasi organisasi</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div class="space-y-3">
                        <div>
                            <x-forms.input-label value="Nama Pemandu" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $driver->name }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Email" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $driver->email }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Status Akaun" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ ucfirst($driver->status ?? '-') }}" readonly />
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <x-forms.input-label value="Bahagian" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $driver->bahagian->nama_bahagian ?? '-' }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Stesen" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $driver->stesen->nama_stesen ?? 'Semua Stesen' }}" readonly />
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <x-ui.stat-card icon="history" icon-color="text-blue-600" :value="number_format($stats['jumlah_log'])" label="Jumlah Log" />
                <x-ui.stat-card icon="alt_route" icon-color="text-green-600" :value="number_format($stats['jumlah_jarak'], 1)" suffix=" km" label="Jarak Direkod" />
                <x-ui.stat-card icon="payments" icon-color="text-emerald-600" :value="number_format($stats['jumlah_kos'], 2)" prefix="RM " label="Kos Direkod" />
                <x-ui.stat-card icon="water_drop" icon-color="text-indigo-600" :value="number_format($stats['jumlah_liter'], 2)" suffix=" L" label="Liter Digunakan" />
                <x-ui.stat-card icon="equalizer" icon-color="text-rose-600" :value="number_format($stats['purata_jarak'], 2)" suffix=" km" label="Purata Jarak/Log" />
                <x-ui.stat-card icon="savings" icon-color="text-yellow-600" :value="number_format($stats['purata_kos'], 2)" prefix="RM " label="Purata Kos/Log" />
            </div>

            <x-ui.card>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600">receipt_long</span>
                        <h3 class="text-base font-semibold text-gray-900">Senarai Log Pemandu</h3>
                    </div>
                    <div class="text-xs text-gray-500">Jumlah rekod: {{ number_format($logs->count()) }}</div>
                </div>

                <x-ui.data-table
                    :headers="[
                        ['label' => 'Tarikh', 'align' => 'text-left'],
                        ['label' => 'Program', 'align' => 'text-left'],
                        ['label' => 'Kenderaan', 'align' => 'text-left'],
                        ['label' => 'Jarak (km)', 'align' => 'text-left'],
                        ['label' => 'Kos (RM)', 'align' => 'text-left'],
                        ['label' => 'Status', 'align' => 'text-left'],
                    ]"
                    :actions="false"
                    empty-message="Tiada log direkod untuk pemandu ini."
                >
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div>{{ $log->tarikh_perjalanan?->format('d/m/Y') ?? '-' }}</div>
                                <div class="text-xs text-gray-500">Check-in: {{ $log->masa_keluar_label ?? '-' }}</div>
                                <div class="text-xs text-gray-500">Check-out: {{ $log->masa_masuk_label ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div>{{ $log->program->nama_program ?? '-' }}</div>
                                <div class="text-xs text-gray-500">Lokasi: {{ $log->program->lokasi_program ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div>{{ $log->kenderaan->no_plat ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ trim(($log->kenderaan->jenama ?? '') . ' ' . ($log->kenderaan->model ?? '')) ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $log->jarak ? number_format($log->jarak, 1) : '0.0' }} km
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                RM {{ $log->kos_minyak ? number_format($log->kos_minyak, 2) : '0.00' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <x-ui.status-badge :status="$log->status" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">Tiada log direkod buat masa ini.</td>
                        </tr>
                    @endforelse
                </x-ui.data-table>
            </x-ui.card>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-ui.card>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="material-symbols-outlined text-blue-600">event</span>
                        <h3 class="text-base font-semibold text-gray-900">Program Terlibat</h3>
                    </div>
                    <x-ui.data-table
                        :headers="[
                            ['label' => 'Program', 'align' => 'text-left'],
                            ['label' => 'Status', 'align' => 'text-left'],
                            ['label' => 'Log', 'align' => 'text-center'],
                            ['label' => 'Jarak (km)', 'align' => 'text-right'],
                            ['label' => 'Kos (RM)', 'align' => 'text-right'],
                        ]"
                        :actions="false"
                        empty-message="Tiada program terlibat."
                    >
                        @forelse($programSummary as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $row['nama_program'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ ucfirst($row['status']) }}</td>
                                <td class="px-6 py-4 text-sm text-center text-gray-900">{{ number_format($row['jumlah_log']) }}</td>
                                <td class="px-6 py-4 text-sm text-right text-gray-900">{{ number_format($row['jumlah_jarak'], 1) }}</td>
                                <td class="px-6 py-4 text-sm text-right text-gray-900">RM {{ number_format($row['jumlah_kos'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">Tiada program direkod.</td>
                            </tr>
                        @endforelse
                    </x-ui.data-table>
                </x-ui.card>

                <x-ui.card>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="material-symbols-outlined text-blue-600">directions_car</span>
                        <h3 class="text-base font-semibold text-gray-900">Kenderaan Digunakan</h3>
                    </div>
                    <x-ui.data-table
                        :headers="[
                            ['label' => 'Kenderaan', 'align' => 'text-left'],
                            ['label' => 'Log', 'align' => 'text-center'],
                            ['label' => 'Jarak (km)', 'align' => 'text-right'],
                            ['label' => 'Kos (RM)', 'align' => 'text-right'],
                        ]"
                        :actions="false"
                        empty-message="Tiada kenderaan direkod."
                    >
                        @forelse($kenderaanSummary as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $row['no_plat'] }}<br><span class="text-xs text-gray-500">{{ $row['nama'] }}</span></td>
                                <td class="px-6 py-4 text-sm text-center text-gray-900">{{ number_format($row['jumlah_log']) }}</td>
                                <td class="px-6 py-4 text-sm text-right text-gray-900">{{ number_format($row['jumlah_jarak'], 1) }}</td>
                                <td class="px-6 py-4 text-sm text-right text-gray-900">RM {{ number_format($row['jumlah_kos'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">Tiada kenderaan direkod.</td>
                            </tr>
                        @endforelse
                    </x-ui.data-table>
                </x-ui.card>
            </div>
        </div>
    </x-ui.page-header>
</x-dashboard-layout>
