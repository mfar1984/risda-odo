@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout title="Senarai Kenderaan">
    <x-ui.page-header
        title="Senarai Kenderaan"
        description="Pengurusan maklumat kenderaan organisasi"
    >
        @php
            $currentUser = auth()->user();
        @endphp

        <!-- Header with Add Buttons -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <!-- Remove duplicate text here -->
            </div>
            <div class="flex gap-3">
                @if($currentUser && $currentUser->adaKebenaran('selenggara_kenderaan', 'lihat'))
                <a href="{{ route('pengurusan.senarai-selenggara') }}">
                    <x-buttons.primary-button type="button" class="bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:ring-green-500">
                        <span class="material-symbols-outlined mr-2" style="font-size: 16px;">build</span>
                        Selenggara
                    </x-buttons.primary-button>
                </a>
                @endif
                
                @if($currentUser && $currentUser->adaKebenaran('senarai_kenderaan', 'tambah'))
                <a href="{{ route('pengurusan.tambah-kenderaan') }}">
                    <x-buttons.primary-button type="button">
                        <span class="material-symbols-outlined mr-2" style="font-size: 16px;">add_circle</span>
                        Kenderaan
                    </x-buttons.primary-button>
                </a>
                @endif
            </div>
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
            :action="route('pengurusan.senarai-kenderaan')"
            search-placeholder="Masukkan no. plat, jenama atau model"
            :search-value="request('search')"
            :filters="[
                [
                    'name' => 'status',
                    'type' => 'select',
                    'placeholder' => 'Semua Status',
                    'options' => [
                        'aktif' => 'Aktif',
                        'tidak_aktif' => 'Tidak Aktif',
                        'penyelenggaraan' => 'Penyelenggaraan'
                    ]
                ],
                [
                    'name' => 'jenis_bahan_api',
                    'type' => 'select',
                    'placeholder' => 'Semua Bahan Api',
                    'options' => [
                        'petrol' => 'Petrol',
                        'diesel' => 'Diesel'
                    ]
                ]
            ]"
            :reset-url="route('pengurusan.senarai-kenderaan')"
        />

        <!-- Desktop Table (Hidden on Mobile) -->
        <div class="data-table-container">
        <x-ui.data-table
            :headers="[
                ['label' => 'No. Plat', 'align' => 'text-left'],
                ['label' => 'Jenama & Model', 'align' => 'text-left'],
                ['label' => 'Tahun', 'align' => 'text-left'],
                ['label' => 'Bahan Api', 'align' => 'text-left'],
                ['label' => 'Status', 'align' => 'text-center'],
                ['label' => 'Cukai Tamat', 'align' => 'text-left'],
                ['label' => 'Dicipta Oleh', 'align' => 'text-left']
            ]"
            empty-message="Tiada data kenderaan dijumpai."
        >
            @forelse($kenderaans as $kenderaan)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $kenderaan->no_plat }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $kenderaan->jenama }}</div>
                    <div class="text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">{{ $kenderaan->model }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $kenderaan->tahun }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $kenderaan->jenis_bahan_api_label }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <x-ui.status-badge :status="$kenderaan->status" />
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm {{ $kenderaan->is_cukai_expired ? 'text-red-600 font-medium' : 'text-gray-900' }}" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                        {{ $kenderaan->cukai_tamat_tempoh->format('d/m/Y') }}
                    </div>
                    @if($kenderaan->is_cukai_expired)
                        <div class="text-xs text-red-500" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">Tamat Tempoh</div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $kenderaan->pencipta->name ?? 'Unknown' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                    @php
                        $customActions = [];
                        if ($currentUser && $currentUser->adaKebenaran('selenggara_kenderaan', 'tambah')) {
                            $customActions[] = [
                                'url' => route('pengurusan.tambah-selenggara', ['kenderaan_id' => $kenderaan->id]),
                                'icon' => 'build',
                                'class' => 'text-green-600 hover:text-green-900',
                            ];
                        }
                    @endphp
                    <x-ui.action-buttons
                        :show-url="$currentUser && $currentUser->adaKebenaran('senarai_kenderaan', 'lihat') ? route('pengurusan.show-kenderaan', $kenderaan) : ''"
                        :edit-url="$currentUser && $currentUser->adaKebenaran('senarai_kenderaan', 'kemaskini') ? route('pengurusan.edit-kenderaan', $kenderaan) : ''"
                        :delete-onclick="$currentUser && $currentUser->adaKebenaran('senarai_kenderaan', 'padam') ? 'deleteKenderaanItem(' . $kenderaan->id . ')' : ''"
                        :show-view="$currentUser && $currentUser->adaKebenaran('senarai_kenderaan', 'lihat')"
                        :show-edit="$currentUser && $currentUser->adaKebenaran('senarai_kenderaan', 'kemaskini')"
                        :show-delete="$currentUser && $currentUser->adaKebenaran('senarai_kenderaan', 'padam')"
                        :custom-actions="$customActions"
                    />
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-6 py-4 text-center text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                    Tiada data kenderaan dijumpai.
                </td>
            </tr>
            @endforelse
        </x-ui.data-table>
        </div>

        <!-- Mobile Card View -->
        <div class="mobile-table-card">
            @forelse($kenderaans as $kenderaan)
                <div class="mobile-card">
                    <div class="mobile-card-header">
                        <div class="mobile-card-title">{{ $kenderaan->no_plat }}</div>
                        <div class="mobile-card-badge"><x-ui.status-badge :status="$kenderaan->status" /></div>
                    </div>
                    <div class="mobile-card-body">
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">directions_car</span></span>
                            <span class="mobile-card-value">{{ $kenderaan->jenama }}<div class="mobile-card-value-secondary">{{ $kenderaan->model }}</div></span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">calendar_month</span></span>
                            <span class="mobile-card-value">Tahun: {{ $kenderaan->tahun ?? '-' }}</span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">local_gas_station</span></span>
                            <span class="mobile-card-value">{{ $kenderaan->jenis_bahan_api_label }}</span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">receipt_long</span></span>
                            <span class="mobile-card-value">Cukai Tamat: {{ $kenderaan->cukai_tamat_tempoh->format('d/m/Y') }}</span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">person</span></span>
                            <span class="mobile-card-value">{{ $kenderaan->pencipta->name ?? 'Unknown' }}</span>
                        </div>
                    </div>
                    <div class="mobile-card-footer">
                        <a href="{{ route('pengurusan.show-kenderaan', $kenderaan) }}" class="mobile-card-action mobile-action-view">
                            <span class="material-symbols-outlined mobile-card-action-icon">visibility</span>
                            <span class="mobile-card-action-label">Lihat</span>
                        </a>
                        @if($currentUser && $currentUser->adaKebenaran('senarai_kenderaan', 'kemaskini'))
                        <a href="{{ route('pengurusan.edit-kenderaan', $kenderaan) }}" class="mobile-card-action mobile-action-edit">
                            <span class="material-symbols-outlined mobile-card-action-icon">edit</span>
                            <span class="mobile-card-action-label">Edit</span>
                        </a>
                        @endif
                        @if($currentUser && $currentUser->adaKebenaran('selenggara_kenderaan', 'tambah'))
                        <a href="{{ route('pengurusan.tambah-selenggara', ['kenderaan_id' => $kenderaan->id]) }}" class="mobile-card-action mobile-action-approve">
                            <span class="material-symbols-outlined mobile-card-action-icon">build</span>
                            <span class="mobile-card-action-label">Selenggara</span>
                        </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="mobile-empty-state">
                    <span class="material-symbols-outlined" style="font-size:48px; color:#9ca3af;">directions_car</span>
                    <p>Tiada kenderaan</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <x-ui.pagination :paginator="$kenderaans" record-label="kenderaan" />
    </x-ui.page-header>

    {{-- Centralized Delete Modal --}}
    <x-modals.delete-confirmation-modal />

    {{-- Centralized JavaScript --}}
    @vite('resources/js/delete-actions.js')
</x-dashboard-layout>
