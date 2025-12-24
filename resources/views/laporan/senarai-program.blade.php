@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout title="Laporan Senarai Program">
    <x-ui.page-header
        title="Laporan Senarai Program"
        description="Ringkasan program beserta log dan statistik perjalanan"
    >
        <!-- Statistik Ringkas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <x-ui.stat-card
                icon="event"
                icon-color="text-blue-600"
                :value="formatNombor($overallStats['total_program'])"
                label="Jumlah Program"
            />

            <x-ui.stat-card
                icon="group"
                icon-color="text-blue-600"
                :value="formatNombor($overallStats['total_pemandu'])"
                label="Jumlah Pemandu"
            />

            <x-ui.stat-card
                icon="directions_car"
                icon-color="text-yellow-500"
                :value="formatNombor($overallStats['total_kenderaan'])"
                label="Jumlah Kenderaan"
            />

            <x-ui.stat-card
                icon="history"
                icon-color="text-green-600"
                :value="formatNombor($overallStats['total_log'])"
                label="Jumlah Log"
            />

            <x-ui.stat-card
                icon="swap_horiz"
                icon-color="text-emerald-600"
                :value="formatNombor($overallStats['total_checkin'])"
                label="Jumlah Check-in"
            />

            <x-ui.stat-card
                icon="assignment_turned_in"
                icon-color="text-rose-600"
                :value="formatNombor($overallStats['total_checkout'])"
                label="Jumlah Check-out"
            />
        </div>

        <x-ui.search-filter
            :action="route('laporan.senarai-program')"
            search-placeholder="Cari program, lokasi atau pemandu"
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
                        'selesai' => 'Selesai'
                    ]
                ]
            ]"
            :reset-url="route('laporan.senarai-program')"
        />

        <!-- Desktop Table View -->
        <div class="data-table-container">
            <x-ui.data-table
                :headers="[
                    ['label' => 'Nama Program', 'align' => 'text-left'],
                    ['label' => 'Status', 'align' => 'text-left'],
                    ['label' => 'Tarikh', 'align' => 'text-left'],
                    ['label' => 'Lokasi', 'align' => 'text-left'],
                    ['label' => 'Log & Statistik', 'align' => 'text-left']
                ]"
                empty-message="Tiada program ditemui untuk penapis semasa."
            >
                @forelse($programs as $program)
                    @php
                        $stats = $programData->firstWhere('id', $program->id);
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 max-w-xs">
                            <div class="text-sm font-medium text-gray-900 truncate" title="{{ $program->nama_program }}" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                {{ $program->nama_program }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1 truncate" title="Pemohon: {{ $program->pemohon->nama_penuh ?? '-' }}" style="font-family: Poppins, sans-serif !important; font-size: 11px !important; max-width: 12rem;">
                                Pemohon: {{ $program->pemohon->nama_penuh ?? '-' }}
                            </div>
                            <div class="text-xs text-gray-400 mt-1 truncate" style="font-family: Poppins, sans-serif !important; font-size: 10px !important; max-width: 12rem;">
                                Pemandu: {{ $stats['jumlah_pemandu'] ?? 0 }} • Kenderaan: {{ $stats['jumlah_kenderaan'] ?? 0 }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-ui.status-badge :status="$program->status" />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                {{ $program->tarikh_mula ? formatTarikhMasa($program->tarikh_mula) : '-' }}
                            </div>
                            <div class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                hingga {{ $program->tarikh_selesai ? formatTarikhMasa($program->tarikh_selesai) : '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 max-w-xs">
                            <div class="text-sm text-gray-900 truncate" title="{{ $program->lokasi_program ?? '-' }}" style="font-family: Poppins, sans-serif !important; font-size: 12px !important; max-width: 12rem;">
                                {{ $program->lokasi_program ?? '-' }}
                            </div>
                            @if($program->jarak_anggaran)
                                <div class="text-xs text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                    Anggaran: {{ formatNombor($program->jarak_anggaran, 1) }} km
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-xs text-gray-500 space-y-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                <div>Log Aktif: {{ formatNombor($stats['jumlah_aktif'] ?? 0) }}</div>
                                <div>Log Selesai: {{ formatNombor($stats['jumlah_selesai'] ?? 0) }}</div>
                                <div>Log Tertunda: {{ formatNombor($stats['jumlah_tertunda'] ?? 0) }}</div>
                                <div>Check-in / Check-out: {{ formatNombor($stats['jumlah_checkin'] ?? 0) }} / {{ formatNombor($stats['jumlah_checkout'] ?? 0) }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <x-ui.action-buttons
                                :show-url="route('laporan.senarai-program.show', $program)"
                                :show-view="true"
                                :show-edit="false"
                                :show-delete="false"
                                :custom-actions="[
                                    [
                                        'url' => route('laporan.senarai-program.pdf', $program),
                                        'icon' => 'picture_as_pdf',
                                        'class' => 'text-red-600 hover:text-red-800'
                                    ]
                                ]"
                            />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">Tiada data program buat masa ini.</td>
                    </tr>
                @endforelse
            </x-ui.data-table>
        </div>

        <!-- Mobile Card View -->
        <div class="mobile-table-card">
            @forelse($programs as $program)
                @php
                    $stats = $programData->firstWhere('id', $program->id);
                @endphp
                <div class="mobile-card">
                    <!-- Card Header -->
                    <div class="mobile-card-header">
                        <div class="mobile-card-title">{{ $program->nama_program }}</div>
                        <div class="mobile-card-badge">
                            <x-ui.status-badge :status="$program->status" />
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="mobile-card-body">
                        <!-- Pemohon -->
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">
                                <span class="material-symbols-outlined" style="font-size: 16px;">person</span>
                            </span>
                            <span class="mobile-card-value">{{ $program->pemohon->nama_penuh ?? '-' }}</span>
                        </div>

                        <!-- Tarikh -->
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">
                                <span class="material-symbols-outlined" style="font-size: 16px;">calendar_today</span>
                            </span>
                            <span class="mobile-card-value">
                                {{ $program->tarikh_mula ? formatTarikhMasa($program->tarikh_mula) : '-' }}
                                <span class="mobile-card-value-secondary">hingga {{ $program->tarikh_selesai ? formatTarikhMasa($program->tarikh_selesai) : '-' }}</span>
                            </span>
                        </div>

                        <!-- Lokasi -->
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">
                                <span class="material-symbols-outlined" style="font-size: 16px;">location_on</span>
                            </span>
                            <span class="mobile-card-value">
                                {{ $program->lokasi_program ?? '-' }}
                                @if($program->jarak_anggaran)
                                    <span class="mobile-card-value-secondary">({{ formatNombor($program->jarak_anggaran, 1) }} km)</span>
                                @endif
                            </span>
                        </div>

                        <!-- Resources -->
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">
                                <span class="material-symbols-outlined" style="font-size: 16px;">group</span>
                            </span>
                            <span class="mobile-card-value">
                                {{ formatNombor($stats['jumlah_pemandu'] ?? 0) }} Pemandu • {{ formatNombor($stats['jumlah_kenderaan'] ?? 0) }} Kenderaan
                            </span>
                        </div>

                        <!-- Statistik Log -->
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">
                                <span class="material-symbols-outlined" style="font-size: 16px;">analytics</span>
                            </span>
                            <span class="mobile-card-value">
                                <div style="display: flex; flex-direction: column; gap: 2px;">
                                    <span>Aktif: {{ formatNombor($stats['jumlah_aktif'] ?? 0) }} • Selesai: {{ formatNombor($stats['jumlah_selesai'] ?? 0) }}</span>
                                    <span class="mobile-card-value-secondary">Tertunda: {{ formatNombor($stats['jumlah_tertunda'] ?? 0) }}</span>
                                    <span class="mobile-card-value-secondary">Check-in: {{ formatNombor($stats['jumlah_checkin'] ?? 0) }} • Check-out: {{ formatNombor($stats['jumlah_checkout'] ?? 0) }}</span>
                                </div>
                            </span>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="mobile-card-footer">
                        <a href="{{ route('laporan.senarai-program.show', $program) }}" class="mobile-card-action mobile-action-view">
                            <span class="material-symbols-outlined" style="font-size: 18px;">visibility</span>
                            Lihat
                        </a>
                        <a href="{{ route('laporan.senarai-program.pdf', $program) }}" class="mobile-card-action" style="color: #dc2626;">
                            <span class="material-symbols-outlined" style="font-size: 18px;">picture_as_pdf</span>
                            PDF
                        </a>
                    </div>
                </div>
            @empty
                <div class="mobile-empty-state">
                    <span class="material-symbols-outlined" style="font-size: 48px; color: #9ca3af;">inbox</span>
                    <p>Tiada program ditemui</p>
                </div>
            @endforelse
        </div>

        <x-ui.pagination :paginator="$programs" record-label="program" />
    </x-ui.page-header>
</x-dashboard-layout>
