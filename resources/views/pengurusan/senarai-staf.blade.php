@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout title="Senarai RISDA Staf">
    <x-ui.page-header
        title="Senarai RISDA Staf"
        description="Pengurusan senarai staf RISDA dalam organisasi anda"
    >
        <!-- Header with Add Button -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Senarai RISDA Staf</h3>
                <p class="text-sm text-gray-600 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Pengurusan RISDA Staf dalam organisasi anda</p>
            </div>
            @if(auth()->user()->adaKebenaran('senarai_risda_staf', 'tambah'))
            <a href="{{ route('pengurusan.tambah-staf') }}">
                <x-buttons.primary-button type="button">
                    <span class="material-symbols-outlined mr-2" style="font-size: 16px;">add_circle</span>
                    Tambah Staf
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
            :action="route('pengurusan.senarai-staf')"
            search-placeholder="Masukkan no. pekerja, nama, email atau jawatan"
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
            :reset-url="route('pengurusan.senarai-staf')"
        />

        <!-- Desktop Table -->
        <div class="data-table-container">
        <x-ui.data-table
            :headers="[
                ['label' => 'No. Pekerja', 'align' => 'text-left'],
                ['label' => 'Nama Penuh', 'align' => 'text-left'],
                ['label' => 'Bahagian', 'align' => 'text-left'],
                ['label' => 'Jawatan', 'align' => 'text-left'],
                ['label' => 'Status', 'align' => 'text-left']
            ]"
            empty-message="Tiada data RISDA Staf dijumpai."
        >
            @forelse($stafs ?? [] as $staf)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $staf->no_pekerja }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $staf->nama_penuh }}</div>
                    <div class="text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">{{ $staf->email }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $staf->bahagian->nama_bahagian ?? 'N/A' }}</div>
                    <div class="text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">{{ $staf->stesen->nama_stesen ?? 'Semua Stesen' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $staf->jawatan }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <x-ui.status-badge :status="$staf->status" />
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                    @php
                        $canEdit = auth()->user()->adaKebenaran('senarai_risda_staf', 'kemaskini');
                        $canDelete = auth()->user()->adaKebenaran('senarai_risda_staf', 'padam');
                    @endphp
                    <x-ui.action-buttons
                        :show-url="route('pengurusan.show-staf', $staf)"
                        :edit-url="$canEdit ? route('pengurusan.edit-staf', $staf) : null"
                        :delete-onclick="$canDelete ? 'deleteStafItem(' . $staf->id . ')' : null"
                    />
                </td>
            </tr>
            @empty
            @endforelse
        </x-ui.data-table>
        </div>

        <!-- Mobile Card View -->
        <div class="mobile-table-card">
            @forelse($stafs ?? [] as $staf)
                <div class="mobile-card">
                    <div class="mobile-card-header">
                        <div class="mobile-card-title">{{ $staf->nama_penuh }}</div>
                        <div class="mobile-card-badge"><x-ui.status-badge :status="$staf->status" /></div>
                    </div>
                    <div class="mobile-card-body">
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">badge</span></span>
                            <span class="mobile-card-value">{{ $staf->no_pekerja }}</span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">apartment</span></span>
                            <span class="mobile-card-value">{{ $staf->bahagian->nama_bahagian ?? 'N/A' }}<div class="mobile-card-value-secondary">{{ $staf->stesen->nama_stesen ?? 'Semua Stesen' }}</div></span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">work</span></span>
                            <span class="mobile-card-value">{{ $staf->jawatan }}</span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">mail</span></span>
                            <span class="mobile-card-value">{{ $staf->email }}</span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">call</span></span>
                            <span class="mobile-card-value">{{ $staf->no_telefon }}</span>
                        </div>
                    </div>
                    <div class="mobile-card-footer">
                        <a href="{{ route('pengurusan.show-staf', $staf) }}" class="mobile-card-action mobile-action-view">
                            <span class="material-symbols-outlined mobile-card-action-icon">visibility</span>
                            <span class="mobile-card-action-label">Lihat</span>
                        </a>
                        @if(auth()->user()->adaKebenaran('senarai_risda_staf', 'kemaskini'))
                        <a href="{{ route('pengurusan.edit-staf', $staf) }}" class="mobile-card-action mobile-action-edit">
                            <span class="material-symbols-outlined mobile-card-action-icon">edit</span>
                            <span class="mobile-card-action-label">Edit</span>
                        </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="mobile-empty-state">
                    <span class="material-symbols-outlined" style="font-size:48px; color:#9ca3af;">people</span>
                    <p>Tiada RISDA Staf</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if(isset($stafs) && $stafs instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <x-ui.pagination :paginator="$stafs" record-label="staf" />
        @endif

    </x-ui.page-header>

    {{-- Centralized Delete Modal --}}
    <x-modals.delete-confirmation-modal />

    {{-- Delete JavaScript --}}
    <script>
        function deleteStafItem(id) {
            if (typeof window.showDeleteModal === 'function') {
                window.showDeleteModal(
                    'Padam RISDA Staf',
                    'Adakah anda pasti untuk memadam staf ini? Tindakan ini tidak boleh dibatalkan.',
                    '{{ url("pengurusan/senarai-risda/staf") }}/' + id,
                    'DELETE'
                );
            }
        }
    </script>
</x-dashboard-layout>
