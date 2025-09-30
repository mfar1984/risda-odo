<x-dashboard-layout title="Senarai Kenderaan">
    <x-ui.page-header
        title="Senarai Kenderaan"
        description="Pengurusan maklumat kenderaan organisasi"
    >
        @php
            $currentUser = auth()->user();
        @endphp

        <!-- Header with Add Button -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <!-- Remove duplicate text here -->
            </div>
            @if($currentUser && $currentUser->adaKebenaran('senarai_kenderaan', 'tambah'))
            <a href="{{ route('pengurusan.tambah-kenderaan') }}">
                <x-buttons.primary-button type="button">
                    <span class="material-symbols-outlined mr-2" style="font-size: 16px;">add_circle</span>
                    Kenderaan
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

        <!-- Table -->
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
                    <x-ui.action-buttons
                        :show-url="$currentUser && $currentUser->adaKebenaran('senarai_kenderaan', 'lihat') ? route('pengurusan.show-kenderaan', $kenderaan) : ''"
                        :edit-url="$currentUser && $currentUser->adaKebenaran('senarai_kenderaan', 'kemaskini') ? route('pengurusan.edit-kenderaan', $kenderaan) : ''"
                        :delete-url="$currentUser && $currentUser->adaKebenaran('senarai_kenderaan', 'padam') ? route('pengurusan.delete-kenderaan', $kenderaan) : ''"
                        :delete-confirm-message="'Adakah anda pasti untuk memadam ' . $kenderaan->no_plat . '?'"
                        :show-view="$currentUser && $currentUser->adaKebenaran('senarai_kenderaan', 'lihat')"
                        :show-edit="$currentUser && $currentUser->adaKebenaran('senarai_kenderaan', 'kemaskini')"
                        :show-delete="$currentUser && $currentUser->adaKebenaran('senarai_kenderaan', 'padam')"
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

        <!-- Pagination -->
        <x-ui.pagination :paginator="$kenderaans" record-label="kenderaan" />
    </x-ui.page-header>
</x-dashboard-layout>
