@php
    use Illuminate\Support\Facades\Storage;
@endphp

@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout title="Laporan Kos Program">
    <x-ui.page-header
        title="Laporan Kos: {{ $program->nama_program }}"
        description="Perincian kos bahan api dan penggunaan liter untuk program ini"
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
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $program->jarak_anggaran ? formatNombor($program->jarak_anggaran, 1) . ' km' : '-' }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Pemohon" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $program->pemohon->nama_penuh ?? '-' }}" readonly />
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <x-forms.input-label value="Tarikh Mula" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $program->tarikh_mula ? formatTarikhMasa($program->tarikh_mula) : '-' }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Tarikh Selesai" />
                            <x-forms.text-input class="mt-1 block w-full" value="{{ $program->tarikh_selesai ? formatTarikhMasa($program->tarikh_selesai) : '-' }}" readonly />
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
                <x-ui.stat-card icon="payments" icon-color="text-red-500" :value="formatWang($stats['jumlah_kos'])" label="Jumlah Kos" />
                <x-ui.stat-card icon="local_gas_station" icon-color="text-emerald-600" :value="formatNombor($stats['jumlah_liter'], 2)" suffix=" L" label="Jumlah Liter" />
                <x-ui.stat-card icon="equalizer" icon-color="text-indigo-600" :value="formatWang($stats['purata_kos_log'])" label="Purata Kos/Log" />
                <x-ui.stat-card icon="water_drop" icon-color="text-rose-600" :value="formatNombor($stats['purata_liter_log'], 2)" suffix=" L" label="Purata Liter/Log" />
                <x-ui.stat-card icon="alt_route" icon-color="text-yellow-600" :value="formatNombor($stats['jumlah_jarak'], 1)" suffix=" km" label="Jarak Direkod" />
            </div>

            <x-ui.card>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600">receipt_long</span>
                        <h3 class="text-base font-semibold text-gray-900">Senarai Log Kos</h3>
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
                        ['label' => 'Kos (RM)', 'align' => 'text-left'],
                        ['label' => 'Liter (L)', 'align' => 'text-left'],
                        ['label' => 'Catatan', 'align' => 'text-left'],
                        ['label' => 'Status', 'align' => 'text-left'],
                        ['label' => 'No. Resit', 'align' => 'text-left'],
                        ['label' => 'Resit', 'align' => 'text-center'],
                    ]"
                    :actions="false"
                    empty-message="Tiada log kos direkod untuk program ini."
                >
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div>{{ $log->tarikh_perjalanan ? formatTarikh($log->tarikh_perjalanan) : '-' }}</div>
                                <div class="text-xs text-gray-500">Check-in: {{ $log->masa_keluar ? formatMasa(\Carbon\Carbon::parse($log->masa_keluar)) : '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div>{{ $log->pemandu->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $log->destinasi ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div>{{ $log->kenderaan->no_plat ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ trim(($log->kenderaan->jenama ?? '') . ' ' . ($log->kenderaan->model ?? '')) ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $log->kos_minyak ? formatWang($log->kos_minyak) : '0.00' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $log->liter_minyak ? formatNombor($log->liter_minyak, 2) : '0.00' }} L
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $log->catatan ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <x-ui.status-badge :status="$log->status" />
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $log->no_resit ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($log->resit_minyak)
                                    @php
                                        $resitUrl = Storage::url($log->resit_minyak);
                                    @endphp
                                    <button 
                                        onclick="openImageModal('{{ $resitUrl }}', 'Resit Minyak - {{ $log->pemandu->name ?? "Pemandu" }}')"
                                        class="inline-flex items-center justify-center p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors"
                                        title="Lihat Resit">
                                        <span class="material-symbols-outlined" style="font-size: 20px;">receipt</span>
                                    </button>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500">Tiada log direkod buat masa ini.</td>
                        </tr>
                    @endforelse
                </x-ui.data-table>
                </div>

                <!-- Mobile Card View -->
                <div class="mobile-table-card">
                    @forelse($logs as $log)
                        <div class="mobile-card">
                            <div class="mobile-card-header">
                                <div class="mobile-card-title">{{ $log->tarikh_perjalanan ? formatTarikh($log->tarikh_perjalanan) : '-' }}</div>
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
                                    <span class="mobile-card-label"><span class="material-symbols-outlined">payments</span></span>
                                    <span class="mobile-card-value">{{ $log->kos_minyak ? formatWang($log->kos_minyak) : '0.00' }}</span>
                                </div>
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label"><span class="material-symbols-outlined">water_drop</span></span>
                                    <span class="mobile-card-value">{{ $log->liter_minyak ? formatNombor($log->liter_minyak, 2) : '0.00' }} L</span>
                                </div>
                                @if($log->catatan)
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label"><span class="material-symbols-outlined">description</span></span>
                                    <span class="mobile-card-value">{{ $log->catatan }}</span>
                                </div>
                                @endif
                            </div>
                            @if($log->resit_minyak)
                            <div class="mobile-card-footer">
                                @php $resitUrl = Storage::url($log->resit_minyak); @endphp
                                <a href="{{ $resitUrl }}" target="_blank" class="mobile-card-action" style="color:#2563eb;">
                                    <span class="material-symbols-outlined mobile-card-action-icon">receipt</span>
                                    <span class="mobile-card-action-label">Resit</span>
                                </a>
                            </div>
                            @endif
                        </div>
                    @empty
                        <div class="mobile-empty-state">
                            <span class="material-symbols-outlined" style="font-size:48px; color:#9ca3af;">payments</span>
                            <p>Tiada log direkod</p>
                        </div>
                    @endforelse
                </div>
            </x-ui.card>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-ui.card>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="material-symbols-outlined text-blue-600">group</span>
                        <h3 class="text-base font-semibold text-gray-900">Pemandu dan Kos</h3>
                    </div>
                    <!-- Desktop Table -->
                    <div class="data-table-container">
                    <x-ui.data-table
                        :headers="[
                            ['label' => 'Pemandu', 'align' => 'text-left'],
                            ['label' => 'Jumlah Log', 'align' => 'text-center'],
                            ['label' => 'Kos (RM)', 'align' => 'text-right'],
                            ['label' => 'Liter (L)', 'align' => 'text-right'],
                        ]"
                        :actions="false"
                        empty-message="Tiada pemandu direkod."
                    >
                        @forelse($pemanduSummary as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $row['nama'] }}</td>
                                <td class="px-6 py-4 text-sm text-center text-gray-900">{{ formatNombor($row['jumlah_log']) }}</td>
                                <td class="px-6 py-4 text-sm text-right text-gray-900">{{ formatWang($row['jumlah_kos']) }}</td>
                                <td class="px-6 py-4 text-sm text-right text-gray-900">{{ formatNombor($row['jumlah_liter'], 2) }} L</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">Tiada pemandu direkod.</td>
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
                                        <span class="mobile-card-label"><span class="material-symbols-outlined">equalizer</span></span>
                                        <span class="mobile-card-value">RM {{ number_format($row['jumlah_kos'], 2) }} • {{ number_format($row['jumlah_liter'], 2) }} L</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="mobile-empty-state">
                                <span class="material-symbols-outlined" style="font-size:48px; color:#9ca3af;">group</span>
                                <p>Tiada pemandu direkod</p>
                            </div>
                        @endforelse
                    </div>
                </x-ui.card>

                <x-ui.card>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="material-symbols-outlined text-blue-600">directions_car</span>
                        <h3 class="text-base font-semibold text-gray-900">Kenderaan dan Kos</h3>
                    </div>
                    <!-- Desktop Table -->
                    <div class="data-table-container">
                    <x-ui.data-table
                        :headers="[
                            ['label' => 'Kenderaan', 'align' => 'text-left'],
                            ['label' => 'Jumlah Log', 'align' => 'text-center'],
                            ['label' => 'Kos (RM)', 'align' => 'text-right'],
                            ['label' => 'Liter (L)', 'align' => 'text-right'],
                        ]"
                        :actions="false"
                        empty-message="Tiada kenderaan direkod."
                    >
                        @forelse($kenderaanSummary as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $row['no_plat'] }}<br><span class="text-xs text-gray-500">{{ $row['nama'] }}</span></td>
                                <td class="px-6 py-4 text-sm text-center text-gray-900">{{ formatNombor($row['jumlah_log']) }}</td>
                                <td class="px-6 py-4 text-sm text-right text-gray-900">{{ formatWang($row['jumlah_kos']) }}</td>
                                <td class="px-6 py-4 text-sm text-right text-gray-900">{{ formatNombor($row['jumlah_liter'], 2) }} L</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">Tiada kenderaan direkod.</td>
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
                                        <span class="mobile-card-label"><span class="material-symbols-outlined">equalizer</span></span>
                                        <span class="mobile-card-value">RM {{ number_format($row['jumlah_kos'], 2) }} • {{ number_format($row['jumlah_liter'], 2) }} L</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="mobile-empty-state">
                                <span class="material-symbols-outlined" style="font-size:48px; color:#9ca3af;">directions_car</span>
                                <p>Tiada kenderaan direkod</p>
                            </div>
                        @endforelse
                    </div>
                </x-ui.card>
            </div>
        </div>
    </x-ui.page-header>

    {{-- Image Modal for Receipt --}}
    <div id="imageModal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.9);">
        <span onclick="closeImageModal()" style="position: absolute; top: 20px; right: 35px; color: #f1f1f1; font-size: 40px; font-weight: bold; cursor: pointer;">&times;</span>
        <img id="modalImage" style="margin: auto; display: block; max-width: 90%; max-height: 90%; margin-top: 50px; cursor: zoom-in;" onclick="toggleZoom(this)">
        <div id="modalCaption" style="margin: auto; display: block; width: 80%; max-width: 700px; text-align: center; color: #ccc; padding: 10px 0; font-size: 16px;"></div>
    </div>

    <script>
        let isZoomed = false;
        
        function openImageModal(imageSrc, caption) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            const modalCaption = document.getElementById('modalCaption');
            
            modal.style.display = 'block';
            modalImg.src = imageSrc;
            modalCaption.innerHTML = caption;
            isZoomed = false;
            modalImg.style.cursor = 'zoom-in';
            modalImg.style.maxWidth = '90%';
            modalImg.style.maxHeight = '90%';
            modalImg.style.width = 'auto';
        }
        
        function closeImageModal() {
            document.getElementById('imageModal').style.display = 'none';
            isZoomed = false;
        }
        
        function toggleZoom(img) {
            if (!isZoomed) {
                img.style.maxWidth = '100%';
                img.style.maxHeight = 'none';
                img.style.width = '100%';
                img.style.cursor = 'zoom-out';
                isZoomed = true;
            } else {
                img.style.maxWidth = '90%';
                img.style.maxHeight = '90%';
                img.style.width = 'auto';
                img.style.cursor = 'zoom-in';
                isZoomed = false;
            }
        }
        
        // Close modal on ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeImageModal();
            }
        });
        
        // Close modal on click outside image
        document.getElementById('imageModal').addEventListener('click', function(event) {
            if (event.target.id === 'imageModal') {
                closeImageModal();
            }
        });
    </script>
</x-dashboard-layout>
