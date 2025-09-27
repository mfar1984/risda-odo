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

        <!-- Table -->
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
                        :delete-url="route('pengurusan.delete-kumpulan', $group)"
                        :delete-confirm-message="'Adakah anda pasti untuk memadam ' . $group->nama_kumpulan . '?'"
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

        <!-- Pagination -->
        <x-ui.pagination :paginator="$kumpulans" record-label="rekod" />
    </x-ui.page-header>
</x-dashboard-layout>
