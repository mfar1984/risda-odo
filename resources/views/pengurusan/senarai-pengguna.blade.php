<x-dashboard-layout title="Senarai Pengguna">
    <x-ui.page-header
        title="Senarai Pengguna"
        description="Pengurusan pengguna dan akaun dalam sistem"
    >
        <!-- Header with Add Button -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <!-- Remove duplicate text here -->
            </div>
            <a href="{{ route('pengurusan.tambah-pengguna') }}">
                <x-buttons.primary-button type="button">
                    <span class="material-symbols-outlined mr-2" style="font-size: 16px;">add_circle</span>
                    Pengguna
                </x-buttons.primary-button>
            </a>
        </div>

        <!-- Filter Section -->
        <x-ui.search-filter
            :action="route('pengurusan.senarai-pengguna')"
            search-placeholder="Masukkan nama atau email"
            :search-value="request('search')"
            :filters="[
                [
                    'name' => 'status',
                    'type' => 'select',
                    'placeholder' => 'Semua Status',
                    'options' => [
                        'aktif' => 'Aktif',
                        'tidak_aktif' => 'Tidak Aktif',
                        'gantung' => 'Digantung'
                    ]
                ]
            ]"
            :reset-url="route('pengurusan.senarai-pengguna')"
        />

        <!-- Table -->
        <x-ui.data-table
            :headers="[
                ['label' => 'Staf RISDA', 'align' => 'text-left'],
                ['label' => 'Email', 'align' => 'text-left'],
                ['label' => 'Peranan', 'align' => 'text-left'],
                ['label' => 'Status', 'align' => 'text-center']
            ]"
            empty-message="Tiada data pengguna dijumpai."
        >
            @forelse($penggunas ?? [] as $pengguna)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div>
                        <div class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            {{ $pengguna->nama_penuh ?? $pengguna->name }}
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                        {{ $pengguna->email }}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                        {{ $pengguna->kumpulan->nama_kumpulan ?? 'Semua Akses' }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <x-ui.status-badge :status="$pengguna->status" />
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                    <x-ui.action-buttons
                        :show-url="route('pengurusan.show-pengguna', $pengguna)"
                        :edit-url="route('pengurusan.edit-pengguna', $pengguna)"
                        :delete-url="route('pengurusan.delete-pengguna', $pengguna)"
                        :delete-confirm-message="'Adakah anda pasti untuk memadam ' . $pengguna->name . '?'"
                    />
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                    Tiada data pengguna dijumpai.
                </td>
            </tr>
            @endforelse
        </x-ui.data-table>

        <!-- Pagination -->
        <x-ui.pagination :paginator="$penggunas" record-label="pengguna" />
    </x-ui.page-header>
</x-dashboard-layout>
