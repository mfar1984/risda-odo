@php
    $tuntutan_collection = $tuntutan ?? collect();
    if ($tuntutan instanceof \Illuminate\Pagination\Paginator || $tuntutan instanceof \Illuminate\Pagination\LengthAwarePaginator) {
        $tuntutan_collection = $tuntutan->getCollection();
    } else {
        $tuntutan_collection = collect($tuntutan);
    }

    // Calculate statistics
    $total_pending = $tuntutan_collection->where('status', 'pending')->sum('jumlah');
    $total_diluluskan = $tuntutan_collection->where('status', 'diluluskan')->sum('jumlah');
    $total_ditolak = $tuntutan_collection->where('status', 'ditolak')->sum('jumlah');
    $total_digantung = $tuntutan_collection->where('status', 'digantung')->sum('jumlah');
    $count_pending = $tuntutan_collection->where('status', 'pending')->count();
    $count_diluluskan = $tuntutan_collection->where('status', 'diluluskan')->count();
    $jumlah_keseluruhan = $tuntutan_collection->sum('jumlah');
@endphp

@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout title="Laporan Tuntutan">
    <x-ui.page-header
        title="Laporan Tuntutan"
        description="Analisis tuntutan pemandu termasuk tol, parking, makanan, penginapan dan lain-lain"
    >
        {{-- Overall Statistics --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <x-ui.stat-card 
                icon="pending_actions" 
                icon-color="text-yellow-600" 
                :value="$count_pending" 
                label="Pending" 
            />
            <x-ui.stat-card 
                icon="check_circle" 
                icon-color="text-green-600" 
                :value="$count_diluluskan" 
                label="Diluluskan" 
            />
            <x-ui.stat-card 
                icon="payments" 
                icon-color="text-indigo-600" 
                :value="number_format($jumlah_keseluruhan, 2)" 
                prefix="RM " 
                label="Jumlah Keseluruhan" 
            />
            <x-ui.stat-card 
                icon="hourglass_empty" 
                icon-color="text-orange-600" 
                :value="number_format($total_pending, 2)" 
                prefix="RM " 
                label="Pending (RM)" 
            />
            <x-ui.stat-card 
                icon="paid" 
                icon-color="text-emerald-600" 
                :value="number_format($total_diluluskan, 2)" 
                prefix="RM " 
                label="Diluluskan (RM)" 
            />
            <x-ui.stat-card 
                icon="cancel" 
                icon-color="text-red-600" 
                :value="number_format($total_ditolak, 2)" 
                prefix="RM " 
                label="Ditolak (RM)" 
            />
        </div>

        <x-ui.search-filter
            :action="route('laporan.laporan-tuntutan')"
            search-placeholder="Cari nama program atau pemandu"
            :search-value="request('search')"
            :filters="[
                [
                    'name' => 'status',
                    'type' => 'select',
                    'placeholder' => 'Semua Status',
                    'options' => $status_list
                ],
                [
                    'name' => 'kategori',
                    'type' => 'select',
                    'placeholder' => 'Semua Kategori',
                    'options' => $kategori_list
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
            :reset-url="route('laporan.laporan-tuntutan')"
        />

        <!-- Desktop Table (Hidden on Mobile) -->
        <div class="data-table-container">
        <x-ui.data-table
            :headers="[
                ['label' => 'Tuntutan', 'align' => 'text-left'],
                ['label' => 'Pemandu & Program', 'align' => 'text-left'],
                ['label' => 'Jumlah & Status', 'align' => 'text-left'],
            ]"
            empty-message="Tiada tuntutan ditemui untuk penapis semasa."
        >
            @forelse($tuntutan_collection as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 max-w-xs">
                        <div class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            {{ $item->kategori_label }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            Tarikh: {{ $item->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div class="text-xs text-gray-400 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                            @if($item->keterangan)
                                {{ Str::limit($item->keterangan, 40) }}
                            @else
                                Tiada keterangan
                            @endif
                        </div>
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            {{ $item->logPemandu->pemandu->risdaStaf->nama_penuh ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                            {{ $item->logPemandu->pemandu->risdaStaf->no_pekerja ?? '' }}
                        </div>
                        <div class="text-xs text-gray-400 mt-1 truncate" title="{{ $item->logPemandu->program->nama_program ?? '-' }}" style="font-family: Poppins, sans-serif !important; font-size: 10px !important; max-width: 12rem;">
                            Program: {{ $item->logPemandu->program->nama_program ?? '-' }}
                        </div>
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            RM {{ number_format($item->jumlah, 2) }}
                        </div>
                        <div class="mt-2">
                            <x-ui.status-badge 
                                :status="$item->status" 
                                :label="$item->status_label" 
                                :color="$item->status_badge_color" 
                            />
                        </div>
                        @if($item->tarikh_diproses)
                            <div class="text-xs text-gray-400 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                {{ $item->tarikh_diproses->format('d/m/Y') }}
                            </div>
                        @endif
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <x-ui.action-buttons
                            :show-url="route('laporan.laporan-tuntutan.show', $item)"
                            :show-view="true"
                            :show-edit="false"
                            :show-delete="Auth::user()->adaKebenaran('laporan_tuntutan', 'padam')"
                            :delete-id="$item->id"
                            delete-route="laporan.laporan-tuntutan.destroy"
                            :delete-confirm="'Adakah anda pasti untuk memadam tuntutan ini?'"
                            :custom-actions="array_filter([
                                $item->canBeApproved() && Auth::user()->adaKebenaran('laporan_tuntutan', 'terima') ? [
                                    'onclick' => 'approveItem(' . $item->id . ')',
                                    'icon' => 'check_circle',
                                    'class' => 'text-green-600 hover:text-green-800',
                                    'title' => 'Lulus'
                                ] : null,
                                $item->canBeRejected() && Auth::user()->adaKebenaran('laporan_tuntutan', 'tolak') ? [
                                    'onclick' => 'openRejectModal(' . $item->id . ')',
                                    'icon' => 'cancel',
                                    'class' => 'text-orange-600 hover:text-orange-800',
                                    'title' => 'Tolak'
                                ] : null,
                                $item->canBeCancelled() && Auth::user()->adaKebenaran('laporan_tuntutan', 'gantung') ? [
                                    'onclick' => 'openCancelModal(' . $item->id . ')',
                                    'icon' => 'block',
                                    'class' => 'text-red-600 hover:text-red-800',
                                    'title' => 'Batal'
                                ] : null,
                                [
                                    'url' => route('laporan.laporan-tuntutan.export-pdf', array_filter([
                                        'status' => request('status'),
                                        'kategori' => request('kategori'),
                                        'tarikh_dari' => request('tarikh_dari'),
                                        'tarikh_hingga' => request('tarikh_hingga'),
                                        'search' => request('search'),
                                        'tuntutan_id' => $item->id
                                    ])),
                                    'icon' => 'picture_as_pdf',
                                    'class' => 'text-purple-600 hover:text-purple-800',
                                    'title' => 'Eksport ke PDF'
                                ]
                            ])"
                        />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                        Tiada tuntutan ditemui buat masa ini.
                    </td>
                </tr>
            @endforelse
        </x-ui.data-table>
        </div>

        <!-- Mobile Card View -->
        <div class="mobile-table-card">
            @forelse($tuntutan_collection as $item)
                <div class="mobile-card">
                    <div class="mobile-card-header">
                        <div class="mobile-card-title">{{ $item->kategori_label }}</div>
                        <div class="mobile-card-badge">
                            <x-ui.status-badge :status="$item->status" :label="$item->status_label" :color="$item->status_badge_color" />
                        </div>
                    </div>
                    <div class="mobile-card-body">
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">today</span></span>
                            <span class="mobile-card-value">{{ $item->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($item->keterangan)
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">description</span></span>
                            <span class="mobile-card-value">{{ Str::limit($item->keterangan, 80) }}</span>
                        </div>
                        @endif
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">person</span></span>
                            <span class="mobile-card-value">
                                {{ $item->logPemandu->pemandu->risdaStaf->nama_penuh ?? '-' }}
                                <div class="mobile-card-value-secondary">{{ $item->logPemandu->pemandu->risdaStaf->no_pekerja ?? '' }}</div>
                            </span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">assignment</span></span>
                            <span class="mobile-card-value">Program: {{ $item->logPemandu->program->nama_program ?? '-' }}</span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">payments</span></span>
                            <span class="mobile-card-value"><strong>RM {{ number_format($item->jumlah, 2) }}</strong></span>
                        </div>
                        @if($item->tarikh_diproses)
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">event_available</span></span>
                            <span class="mobile-card-value">{{ $item->tarikh_diproses->format('d/m/Y') }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="mobile-card-footer">
                        <a href="{{ route('laporan.laporan-tuntutan.show', $item) }}" class="mobile-card-action mobile-action-view">
                            <span class="material-symbols-outlined mobile-card-action-icon">visibility</span>
                            <span class="mobile-card-action-label">Lihat</span>
                        </a>
                        @if($item->canBeApproved() && Auth::user()->adaKebenaran('laporan_tuntutan', 'terima'))
                        <button onclick="approveItem({{ $item->id }})" class="mobile-card-action mobile-action-approve">
                            <span class="material-symbols-outlined mobile-card-action-icon">check_circle</span>
                            <span class="mobile-card-action-label">Lulus</span>
                        </button>
                        @endif
                        @if($item->canBeRejected() && Auth::user()->adaKebenaran('laporan_tuntutan', 'tolak'))
                        <button onclick="openRejectModal({{ $item->id }})" class="mobile-card-action" style="color:#f97316;">
                            <span class="material-symbols-outlined mobile-card-action-icon">cancel</span>
                            <span class="mobile-card-action-label">Tolak</span>
                        </button>
                        @endif
                        @if($item->canBeCancelled() && Auth::user()->adaKebenaran('laporan_tuntutan', 'gantung'))
                        <button onclick="openCancelModal({{ $item->id }})" class="mobile-card-action mobile-action-delete">
                            <span class="material-symbols-outlined mobile-card-action-icon">block</span>
                            <span class="mobile-card-action-label">Batal</span>
                        </button>
                        @endif
                        <a href="{{ route('laporan.laporan-tuntutan.export-pdf', array_filter([
                                        'status' => request('status'),
                                        'kategori' => request('kategori'),
                                        'tarikh_dari' => request('tarikh_dari'),
                                        'tarikh_hingga' => request('tarikh_hingga'),
                                        'search' => request('search'),
                                        'tuntutan_id' => $item->id
                                    ])) }}" class="mobile-card-action" style="color:#a855f7;">
                            <span class="material-symbols-outlined mobile-card-action-icon">picture_as_pdf</span>
                            <span class="mobile-card-action-label">PDF</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="mobile-empty-state">
                    <span class="material-symbols-outlined" style="font-size:48px; color:#9ca3af;">receipt_long</span>
                    <p>Tiada tuntutan ditemui</p>
                </div>
            @endforelse
        </div>

        <x-ui.pagination :paginator="$tuntutan" record-label="tuntutan" />
    </x-ui.page-header>

    {{-- Centralized Modals --}}
    <x-modals.approve-tuntutan-modal />
    <x-modals.reject-tuntutan-modal />
    <x-modals.cancel-tuntutan-modal />
    <x-modals.delete-confirmation-modal />

    {{-- Centralized JavaScript --}}
    @vite('resources/js/tuntutan-actions.js')
    @vite('resources/js/delete-actions.js')
</x-dashboard-layout>
