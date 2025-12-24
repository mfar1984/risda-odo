@php
    use Illuminate\Support\Facades\Storage;
    $resitUrl = $tuntutan->resit ? Storage::url($tuntutan->resit) : null;
    $program = $tuntutan->logPemandu->program;
@endphp

@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout title="Butiran Tuntutan">
    <x-ui.page-header
        title="Butiran Tuntutan: {{ $program->nama_program }}"
        description="Perincian lengkap tuntutan {{ $tuntutan->kategori_label }} untuk program ini"
    >
        <div class="space-y-8">
            {{-- Maklumat Program Card --}}
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

            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <x-ui.stat-card icon="receipt_long" icon-color="text-blue-600" :value="formatNombor($stats['jumlah_tuntutan'])" label="Jumlah Tuntutan" />
                <x-ui.stat-card icon="payments" icon-color="text-indigo-600" :value="formatWang($stats['jumlah_keseluruhan'])" label="Jumlah Keseluruhan" />
                <x-ui.stat-card icon="pending_actions" icon-color="text-yellow-600" :value="formatNombor($stats['pending'])" label="Pending" />
                <x-ui.stat-card icon="check_circle" icon-color="text-green-600" :value="formatNombor($stats['diluluskan'])" label="Diluluskan" />
                <x-ui.stat-card icon="cancel" icon-color="text-red-600" :value="formatNombor($stats['ditolak'])" label="Ditolak" />
                <x-ui.stat-card icon="account_balance_wallet" icon-color="text-emerald-600" :value="formatWang($stats['jumlah_diluluskan'])" label="Jumlah Diluluskan" />
            </div>

            {{-- Maklumat Tuntutan Card --}}
            <x-ui.card>
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Maklumat Tuntutan</h3>
                        <p class="text-xs text-gray-500 mt-1">Rincian asas tuntutan ini</p>
                    </div>
                    <x-ui.status-badge 
                        :status="$tuntutan->status" 
                        :label="$tuntutan->status_label"
                        :color="$tuntutan->status_badge_color" 
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div class="space-y-3">
                        <div>
                            <x-forms.input-label value="Pemandu" />
                            <x-forms.text-input class="mt-1 block w-full" 
                                value="{{ $tuntutan->logPemandu->pemandu->risdaStaf->nama_penuh ?? '-' }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="No. Pekerja" />
                            <x-forms.text-input class="mt-1 block w-full" 
                                value="{{ $tuntutan->logPemandu->pemandu->risdaStaf->no_pekerja ?? '-' }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Tarikh Perjalanan" />
                            <x-forms.text-input class="mt-1 block w-full" 
                                value="{{ $tuntutan->logPemandu->tarikh_perjalanan ? formatTarikh($tuntutan->logPemandu->tarikh_perjalanan) : '-' }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Kenderaan" />
                            <x-forms.text-input class="mt-1 block w-full" 
                                value="{{ $tuntutan->logPemandu->kenderaan ? $tuntutan->logPemandu->kenderaan->no_plat . ' - ' . trim(($tuntutan->logPemandu->kenderaan->jenama ?? '') . ' ' . ($tuntutan->logPemandu->kenderaan->model ?? '')) : '-' }}" 
                                readonly />
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <x-forms.input-label value="Kategori" />
                            <x-forms.text-input class="mt-1 block w-full" 
                                value="{{ $tuntutan->kategori_label }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Jumlah Tuntutan" />
                            <x-forms.text-input class="mt-1 block w-full font-semibold text-blue-700" 
                                value="{{ formatWang($tuntutan->jumlah) }}" readonly />
                        </div>
                        <div>
                            <x-forms.input-label value="Tarikh Dituntut" />
                            <x-forms.text-input class="mt-1 block w-full" 
                                value="{{ formatTarikhMasa($tuntutan->created_at) }}" readonly />
                        </div>
                        @if($tuntutan->keterangan)
                        <div>
                            <x-forms.input-label value="Keterangan" />
                            <textarea class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" 
                                      rows="1" readonly>{{ $tuntutan->keterangan }}</textarea>
                        </div>
                        @endif
                    </div>
                </div>
            </x-ui.card>

            {{-- Receipt Card --}}
            @if($resitUrl)
            <x-ui.card>
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-blue-600">receipt</span>
                    <h3 class="text-base font-semibold text-gray-900">Resit</h3>
                </div>

                <div class="flex justify-center">
                    <button 
                        onclick="openImageModal('{{ $resitUrl }}', 'Resit Tuntutan - {{ $tuntutan->kategori_label }}')"
                        class="relative group cursor-pointer">
                        <img src="{{ $resitUrl }}" 
                             alt="Resit Tuntutan" 
                             class="max-w-full rounded-lg shadow-md hover:opacity-90 transition-opacity"
                             style="width: 100%; height: auto; max-height: 220px; object-fit: contain;">
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black bg-opacity-30 rounded-lg">
                            <span class="material-symbols-outlined text-white text-5xl">zoom_in</span>
                        </div>
                    </button>
                </div>
            </x-ui.card>
            @endif

            {{-- Senarai Tuntutan --}}
            <x-ui.card>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600">list_alt</span>
                        <h3 class="text-base font-semibold text-gray-900">Senarai Tuntutan</h3>
                    </div>
                    <div class="text-xs text-gray-500">Jumlah rekod: {{ formatNombor($relatedClaims->count() + 1) }}</div>
                </div>

                <!-- Desktop Table -->
                <div class="data-table-container">
                <x-ui.data-table
                    :headers="[
                        ['label' => 'Tarikh', 'align' => 'text-left'],
                        ['label' => 'Pemandu', 'align' => 'text-left'],
                        ['label' => 'Kategori', 'align' => 'text-left'],
                        ['label' => 'Jumlah (RM)', 'align' => 'text-right'],
                        ['label' => 'Status', 'align' => 'text-left'],
                        ['label' => 'No. Resit', 'align' => 'text-left'],
                        ['label' => 'Resit', 'align' => 'text-center'],
                        ['label' => 'Tindakan', 'align' => 'text-center'],
                    ]"
                    :actions="false"
                    empty-message="Tiada tuntutan lain untuk program ini."
                >
                    {{-- Current Claim (highlighted) --}}
                    <tr class="bg-blue-50 border-l-4 border-blue-500">
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <div>{{ formatTarikh($tuntutan->created_at) }}</div>
                            <div class="text-xs text-gray-500">{{ formatMasa($tuntutan->created_at) }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div>{{ $tuntutan->logPemandu->pemandu->risdaStaf->nama_penuh ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $tuntutan->logPemandu->pemandu->risdaStaf->no_pekerja ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $tuntutan->kategori_label }}
                        </td>
                        <td class="px-6 py-4 text-sm text-right text-gray-900 font-semibold">
                            {{ formatWang($tuntutan->jumlah) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <x-ui.status-badge 
                                :status="$tuntutan->status" 
                                :label="$tuntutan->status_label"
                                :color="$tuntutan->status_badge_color" 
                            />
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $tuntutan->no_resit ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-900">
                            @php $resitUrl = $tuntutan->resit ? Storage::url($tuntutan->resit) : null; @endphp
                            @if($resitUrl)
                                <a href="{{ $resitUrl }}" target="_blank" class="inline-flex items-center justify-center p-2 hover:bg-blue-50 rounded-sm" title="Lihat Resit">
                                    <span class="material-symbols-outlined" style="font-size: 20px;">receipt_long</span>
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs font-medium text-blue-700">⬅ Tuntutan Semasa</span>
                        </td>
                    </tr>

                    {{-- Related Claims --}}
                    @forelse($relatedClaims as $claim)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div>{{ formatTarikh($claim->created_at) }}</div>
                                <div class="text-xs text-gray-500">{{ formatMasa($claim->created_at) }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div>{{ $claim->logPemandu->pemandu->risdaStaf->nama_penuh ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $claim->logPemandu->pemandu->risdaStaf->no_pekerja ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $claim->kategori_label }}
                            </td>
                            <td class="px-6 py-4 text-sm text-right text-gray-900">
                                {{ formatWang($claim->jumlah) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <x-ui.status-badge 
                                    :status="$claim->status" 
                                    :label="$claim->status_label"
                                    :color="$claim->status_badge_color" 
                                />
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $claim->no_resit ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-900">
                                @php $claimResitUrl = $claim->resit ? Storage::url($claim->resit) : null; @endphp
                                @if($claimResitUrl)
                                    <a href="{{ $claimResitUrl }}" target="_blank" class="inline-flex items-center justify-center p-2 hover:bg-blue-50 rounded-sm" title="Lihat Resit">
                                        <span class="material-symbols-outlined" style="font-size: 20px;">receipt_long</span>
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('laporan.laporan-tuntutan.show', $claim->id) }}" 
                                   class="inline-flex items-center justify-center p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors"
                                   title="Lihat Butiran">
                                    <span class="material-symbols-outlined" style="font-size: 20px;">visibility</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        @if($relatedClaims->count() === 0)
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">Tiada tuntutan lain untuk program ini.</td>
                        </tr>
                        @endif
                    @endforelse
                </x-ui.data-table>
                </div>

                <!-- Mobile Card View -->
                <div class="mobile-table-card">
                    <!-- Current Claim Highlight -->
                    <div class="mobile-card" style="border-left: 3px solid #2563eb;">
                        <div class="mobile-card-header">
                            <div class="mobile-card-title">{{ formatTarikh($tuntutan->created_at) }}</div>
                            <div class="mobile-card-badge">
                                <x-ui.status-badge 
                                    :status="$tuntutan->status" 
                                    :label="$tuntutan->status_label"
                                    :color="$tuntutan->status_badge_color" 
                                />
                            </div>
                        </div>
                        <div class="mobile-card-body">
                            <div class="mobile-card-row">
                                <span class="mobile-card-label"><span class="material-symbols-outlined">person</span></span>
                                <span class="mobile-card-value">{{ $tuntutan->logPemandu->pemandu->risdaStaf->nama_penuh ?? '-' }}<div class="mobile-card-value-secondary">{{ $tuntutan->logPemandu->pemandu->risdaStaf->no_pekerja ?? '-' }}</div></span>
                            </div>
                            <div class="mobile-card-row">
                                <span class="mobile-card-label"><span class="material-symbols-outlined">category</span></span>
                                <span class="mobile-card-value">{{ $tuntutan->kategori_label }}</span>
                            </div>
                            <div class="mobile-card-row">
                                <span class="mobile-card-label"><span class="material-symbols-outlined">payments</span></span>
                                <span class="mobile-card-value"><strong>{{ formatWang($tuntutan->jumlah) }}</strong></span>
                            </div>
                        </div>
                        <div class="mobile-card-footer">
                            <span class="mobile-card-action" style="color:#2563eb;">
                                <span class="material-symbols-outlined mobile-card-action-icon">info</span>
                                <span class="mobile-card-action-label">Tuntutan Semasa</span>
                            </span>
                        </div>
                    </div>

                    @forelse($relatedClaims as $claim)
                        <div class="mobile-card">
                            <div class="mobile-card-header">
                                <div class="mobile-card-title">{{ formatTarikh($claim->created_at) }}</div>
                                <div class="mobile-card-badge">
                                    <x-ui.status-badge 
                                        :status="$claim->status" 
                                        :label="$claim->status_label"
                                        :color="$claim->status_badge_color" 
                                    />
                                </div>
                            </div>
                            <div class="mobile-card-body">
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label"><span class="material-symbols-outlined">person</span></span>
                                    <span class="mobile-card-value">{{ $claim->logPemandu->pemandu->risdaStaf->nama_penuh ?? '-' }}<div class="mobile-card-value-secondary">{{ $claim->logPemandu->pemandu->risdaStaf->no_pekerja ?? '-' }}</div></span>
                                </div>
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label"><span class="material-symbols-outlined">category</span></span>
                                    <span class="mobile-card-value">{{ $claim->kategori_label }}</span>
                                </div>
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label"><span class="material-symbols-outlined">payments</span></span>
                                    <span class="mobile-card-value"><strong>{{ formatWang($claim->jumlah) }}</strong></span>
                                </div>
                            </div>
                            <div class="mobile-card-footer">
                                <a href="{{ route('laporan.laporan-tuntutan.show', $claim->id) }}" class="mobile-card-action mobile-action-view">
                                    <span class="material-symbols-outlined mobile-card-action-icon">visibility</span>
                                    <span class="mobile-card-action-label">Lihat</span>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="mobile-empty-state">
                            <span class="material-symbols-outlined" style="font-size:48px; color:#9ca3af;">list_alt</span>
                            <p>Tiada tuntutan lain</p>
                        </div>
                    @endforelse
                </div>
            </x-ui.card>

            {{-- Processing Info Card (if processed) --}}
            @if($tuntutan->tarikh_diproses)
            <x-ui.card>
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-blue-600">verified</span>
                    <h3 class="text-base font-semibold text-gray-900">Maklumat Pemprosesan</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm mb-6">
                    <div>
                        <x-forms.input-label value="Diproses Oleh" />
                        <x-forms.text-input class="mt-1 block w-full" 
                            value="{{ $tuntutan->diprosesOleh->risdaStaf->nama_penuh ?? $tuntutan->diprosesOleh->name ?? '-' }}" readonly />
                    </div>
                    <div>
                        <x-forms.input-label value="Tarikh Diproses" />
                        <x-forms.text-input class="mt-1 block w-full" 
                            value="{{ formatTarikhMasa($tuntutan->tarikh_diproses) }}" readonly />
                    </div>
                </div>

                @if($tuntutan->status === 'ditolak' && $tuntutan->alasan_tolak)
                <div class="pt-6 border-t border-gray-200">
                    <x-forms.input-label value="Alasan Penolakan" />
                    <textarea class="mt-1 block w-full border-red-300 bg-red-50 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 text-red-900 text-sm" 
                              rows="4" readonly>{{ $tuntutan->alasan_tolak }}</textarea>
                    <p class="mt-2 text-xs text-orange-600 font-medium">
                        <span class="material-symbols-outlined text-xs align-middle">info</span>
                        Pemandu boleh edit & hantar semula
                    </p>
                </div>
                @endif

                @if($tuntutan->status === 'digantung' && $tuntutan->alasan_gantung)
                <div class="pt-6 border-t border-gray-200">
                    <x-forms.input-label value="Alasan Pembatalan" />
                    <textarea class="mt-1 block w-full border-gray-300 bg-gray-50 rounded-md shadow-sm focus:border-gray-500 focus:ring-gray-500 text-gray-900 text-sm" 
                              rows="4" readonly>{{ $tuntutan->alasan_gantung }}</textarea>
                    <p class="mt-2 text-xs text-red-600 font-medium">
                        <span class="material-symbols-outlined text-xs align-middle">block</span>
                        Tuntutan tidak boleh diedit
                    </p>
                </div>
                @endif
            </x-ui.card>
            @endif

            {{-- Action Buttons --}}
            <div class="flex flex-wrap justify-end gap-2 tuntutan-action-buttons">
                {{-- Approve Button --}}
                @if($tuntutan->canBeApproved() && Auth::user()->adaKebenaran('laporan_tuntutan', 'terima'))
                <button onclick="approveItem({{ $tuntutan->id }})" 
                        class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-sm hover:bg-green-700 transition-colors"
                        title="Lulus Tuntutan">
                    <span class="material-symbols-outlined text-base mr-2">check_circle</span>
                    Lulus
                </button>
                @endif

                {{-- Reject Button --}}
                @if($tuntutan->canBeRejected() && Auth::user()->adaKebenaran('laporan_tuntutan', 'tolak'))
                <button onclick="openRejectModal({{ $tuntutan->id }})" 
                        class="inline-flex items-center justify-center px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-sm hover:bg-orange-700 transition-colors"
                        title="Tolak Tuntutan">
                    <span class="material-symbols-outlined text-base mr-2">cancel</span>
                    Tolak
                </button>
                @endif

                {{-- Cancel Button --}}
                @if($tuntutan->canBeCancelled() && Auth::user()->adaKebenaran('laporan_tuntutan', 'gantung'))
                <button onclick="openCancelModal({{ $tuntutan->id }})" 
                        class="inline-flex items-center justify-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-sm hover:bg-red-700 transition-colors"
                        title="Batal Tuntutan">
                    <span class="material-symbols-outlined text-base mr-2">block</span>
                    Batal
                </button>
                @endif

                {{-- Delete Button --}}
                @if(Auth::user()->adaKebenaran('laporan_tuntutan', 'padam'))
                <button onclick="deleteTuntutanItem({{ $tuntutan->id }})" 
                        class="inline-flex items-center justify-center px-4 py-2 bg-gray-700 text-white text-sm font-medium rounded-sm hover:bg-gray-800 transition-colors"
                        title="Padam Tuntutan">
                    <span class="material-symbols-outlined text-base mr-2">delete</span>
                    Padam
                </button>
                @endif
            </div>
        </div>
    </x-ui.page-header>

    {{-- Centralized Modals --}}
    <x-modals.approve-tuntutan-modal />
    <x-modals.reject-tuntutan-modal />
    <x-modals.cancel-tuntutan-modal />
    <x-modals.delete-confirmation-modal />

    {{-- Image Modal for Receipt --}}
    <div id="imageModal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.9);">
        <span onclick="closeImageModal()" style="position: absolute; top: 20px; right: 35px; color: #f1f1f1; font-size: 40px; font-weight: bold; cursor: pointer;">&times;</span>
        <img id="modalImage" style="margin: auto; display: block; max-width: 90%; max-height: 90%; margin-top: 50px; cursor: zoom-in;" onclick="toggleZoom(this)">
        <div id="modalCaption" style="margin: auto; display: block; width: 80%; max-width: 700px; text-align: center; color: #ccc; padding: 10px 0; font-size: 16px;"></div>
    </div>

    {{-- Centralized JavaScript --}}
    @vite('resources/js/tuntutan-actions.js')
    @vite('resources/js/delete-actions.js')

    {{-- Image Modal Functions --}}
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
        
        // Close image modal on ESC key
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
