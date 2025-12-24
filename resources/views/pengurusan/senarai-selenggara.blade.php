@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout title="Senarai Penyelenggaraan">
    <x-ui.page-header
        title="Senarai Penyelenggaraan"
        description="Pengurusan rekod penyelenggaraan kenderaan"
    >
        @php
            $currentUser = auth()->user();
        @endphp

        <!-- Header with Add Button -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <!-- Remove duplicate text here -->
            </div>
            @if($currentUser && $currentUser->adaKebenaran('selenggara_kenderaan', 'tambah'))
            <a href="{{ route('pengurusan.tambah-selenggara') }}">
                <x-buttons.primary-button type="button">
                    <span class="material-symbols-outlined mr-2" style="font-size: 16px;">add_circle</span>
                    Penyelenggaraan
                </x-buttons.primary-button>
            </a>
            @endif
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <x-ui.success-alert class="mb-6">
                {{ session('success') }}
            </x-ui.success-alert>
        @endif

        @if(session('error'))
            <x-ui.error-alert class="mb-6">
                {{ session('error') }}
            </x-ui.error-alert>
        @endif

        <!-- Filter Section -->
        <x-ui.search-filter
            :action="route('pengurusan.senarai-selenggara')"
            search-placeholder="Cari no. plat, jenama atau model kenderaan"
            :search-value="request('search')"
            :filters="[
                [
                    'name' => 'status',
                    'type' => 'select',
                    'placeholder' => 'Semua Status',
                    'options' => [
                        'selesai' => 'Selesai',
                        'dalam_proses' => 'Dalam Proses',
                        'dijadualkan' => 'Dijadualkan'
                    ]
                ],
                [
                    'name' => 'kategori_kos_id',
                    'type' => 'select',
                    'placeholder' => 'Semua Kategori',
                    'options' => $kategoriList->pluck('nama_kategori', 'id')->toArray()
                ]
            ]"
            :reset-url="route('pengurusan.senarai-selenggara')"
        />

        <!-- Desktop Table (Hidden on Mobile) -->
        <div class="data-table-container">
        <x-ui.data-table
            :headers="[
                ['label' => 'Kenderaan', 'align' => 'text-left'],
                ['label' => 'Kategori Kos', 'align' => 'text-left'],
                ['label' => 'Tarikh Mula', 'align' => 'text-left'],
                ['label' => 'Tarikh Selesai', 'align' => 'text-left'],
                ['label' => 'Jumlah Kos (RM)', 'align' => 'text-right'],
                ['label' => 'Status', 'align' => 'text-center']
            ]"
            empty-message="Tiada data penyelenggaraan dijumpai."
        >
            @forelse($selenggaraRecords as $selenggara)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                        {{ $selenggara->kenderaan->no_plat }}
                    </div>
                    <div class="text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                        {{ $selenggara->kenderaan->nama_penuh }}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                        {{ $selenggara->kategoriKos->nama_kategori }}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                        {{ formatTarikh($selenggara->tarikh_mula) }}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                        {{ formatTarikh($selenggara->tarikh_selesai) }}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right">
                    <div class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                        {{ formatNombor($selenggara->jumlah_kos, 2) }}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <x-ui.status-badge :status="$selenggara->status" />
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                    <x-ui.action-buttons
                        :show-url="$currentUser && $currentUser->adaKebenaran('selenggara_kenderaan', 'lihat') ? route('pengurusan.show-selenggara', $selenggara) : ''"
                        :edit-url="$currentUser && $currentUser->adaKebenaran('selenggara_kenderaan', 'kemaskini') ? route('pengurusan.edit-selenggara', $selenggara) : ''"
                        :delete-onclick="$currentUser && $currentUser->adaKebenaran('selenggara_kenderaan', 'padam') ? 'deleteSelenggaraItem(' . $selenggara->id . ')' : ''"
                        :show-view="$currentUser && $currentUser->adaKebenaran('selenggara_kenderaan', 'lihat')"
                        :show-edit="$currentUser && $currentUser->adaKebenaran('selenggara_kenderaan', 'kemaskini')"
                        :show-delete="$currentUser && $currentUser->adaKebenaran('selenggara_kenderaan', 'padam')"
                    />
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                    Tiada data penyelenggaraan dijumpai.
                </td>
            </tr>
            @endforelse
        </x-ui.data-table>
        </div>

        <!-- Mobile Card View -->
        <div class="mobile-table-card">
            @forelse($selenggaraRecords as $selenggara)
                <div class="mobile-card">
                    <div class="mobile-card-header">
                        <div class="mobile-card-title">{{ $selenggara->kenderaan->no_plat }}</div>
                        <div class="mobile-card-badge"><x-ui.status-badge :status="$selenggara->status" /></div>
                    </div>
                    <div class="mobile-card-body">
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">directions_car</span></span>
                            <span class="mobile-card-value">{{ $selenggara->kenderaan->nama_penuh }}</span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">category</span></span>
                            <span class="mobile-card-value">{{ $selenggara->kategoriKos->nama_kategori }}</span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">calendar_month</span></span>
                            <span class="mobile-card-value">Mula: {{ formatTarikh($selenggara->tarikh_mula) }}</span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">event_available</span></span>
                            <span class="mobile-card-value">Tamat: {{ formatTarikh($selenggara->tarikh_selesai) }}</span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">attach_money</span></span>
                            <span class="mobile-card-value">{{ formatWang($selenggara->jumlah_kos) }}</span>
                        </div>
                    </div>
                    <div class="mobile-card-footer">
                        @if($currentUser && $currentUser->adaKebenaran('selenggara_kenderaan', 'lihat'))
                        <a href="{{ route('pengurusan.show-selenggara', $selenggara) }}" class="mobile-card-action mobile-action-view">
                            <span class="material-symbols-outlined mobile-card-action-icon">visibility</span>
                            <span class="mobile-card-action-label">Lihat</span>
                        </a>
                        @endif
                        @if($currentUser && $currentUser->adaKebenaran('selenggara_kenderaan', 'kemaskini'))
                        <a href="{{ route('pengurusan.edit-selenggara', $selenggara) }}" class="mobile-card-action mobile-action-edit">
                            <span class="material-symbols-outlined mobile-card-action-icon">edit</span>
                            <span class="mobile-card-action-label">Edit</span>
                        </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="mobile-empty-state">
                    <span class="material-symbols-outlined" style="font-size:48px; color:#9ca3af;">build</span>
                    <p>Tiada rekod penyelenggaraan</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <x-ui.pagination :paginator="$selenggaraRecords" record-label="rekod penyelenggaraan" />
    </x-ui.page-header>

    {{-- Centralized Delete Modal --}}
    <x-modals.delete-confirmation-modal />

    {{-- Centralized JavaScript --}}
    @vite('resources/js/delete-actions.js')
</x-dashboard-layout>
