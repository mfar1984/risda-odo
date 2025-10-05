@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout title="Senarai Kumpulan">
    <x-ui.page-header
        title="Senarai Kumpulan"
        description="Pengurusan kumpulan pengguna dan kebenaran akses"
    >
        <!-- Header with Add Button -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <!-- Remove duplicate text here -->
            </div>
            <a href="{{ route('pengurusan.tambah-kumpulan') }}">
                <x-buttons.primary-button type="button">
                    <span class="material-symbols-outlined mr-2" style="font-size: 16px;">add_circle</span>
                    Kumpulan
                </x-buttons.primary-button>
            </a>
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
            :action="route('pengurusan.senarai-kumpulan')"
            search-placeholder="Masukkan nama kumpulan atau keterangan"
            :search-value="request('search')"
            :filters="[
                [
                    'name' => 'status',
                    'type' => 'select',
                    'placeholder' => 'Semua Status',
                    'options' => [
                        'aktif' => 'Aktif',
                        'tidak_aktif' => 'Tidak Aktif',
                        'gantung' => 'Gantung'
                    ]
                ]
            ]"
            :reset-url="route('pengurusan.senarai-kumpulan')"
        />

        <!-- Desktop Table (Hidden on Mobile) -->
        <div class="data-table-container">
        <x-ui.data-table
            :headers="[
                ['label' => 'Nama Kumpulan', 'align' => 'text-left'],
                ['label' => 'Keterangan', 'align' => 'text-left'],
                ['label' => 'Status', 'align' => 'text-center']
            ]"
            empty-message="Tiada data kumpulan pengguna dijumpai."
        >
            @forelse($kumpulans ?? [] as $group)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $group->nama_kumpulan }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                        {{ $group->keterangan ?? '-' }}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <x-ui.status-badge :status="$group->status" />
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                    <x-ui.action-buttons
                        :show-url="route('pengurusan.show-kumpulan', $group)"
                        :edit-url="route('pengurusan.edit-kumpulan', $group)"
                        :delete-onclick="'deleteKumpulanItem(' . $group->id . ')'"
                    />
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-4 text-center text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                    Tiada data kumpulan pengguna dijumpai.
                </td>
            </tr>
            @endforelse
        </x-ui.data-table>
        </div>

        <!-- Mobile Card View -->
        <div class="mobile-table-card">
            @forelse($kumpulans ?? [] as $group)
                <div class="mobile-card">
                    <div class="mobile-card-header">
                        <div class="mobile-card-title">{{ $group->nama_kumpulan }}</div>
                        <div class="mobile-card-badge"><x-ui.status-badge :status="$group->status" /></div>
                    </div>
                    <div class="mobile-card-body">
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">description</span></span>
                            <span class="mobile-card-value">{{ $group->keterangan ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="mobile-card-footer">
                        <a href="{{ route('pengurusan.show-kumpulan', $group) }}" class="mobile-card-action mobile-action-view">
                            <span class="material-symbols-outlined mobile-card-action-icon">visibility</span>
                            <span class="mobile-card-action-label">Lihat</span>
                        </a>
                        <a href="{{ route('pengurusan.edit-kumpulan', $group) }}" class="mobile-card-action mobile-action-edit">
                            <span class="material-symbols-outlined mobile-card-action-icon">edit</span>
                            <span class="mobile-card-action-label">Edit</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="mobile-empty-state">
                    <span class="material-symbols-outlined" style="font-size:48px; color:#9ca3af;">shield_person</span>
                    <p>Tiada kumpulan</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <x-ui.pagination :paginator="$kumpulans" record-label="kumpulan" />
    </x-ui.page-header>

    {{-- Centralized Delete Modal --}}
    <x-modals.delete-confirmation-modal />

    {{-- Centralized JavaScript --}}
    @vite('resources/js/delete-actions.js')
</x-dashboard-layout>
