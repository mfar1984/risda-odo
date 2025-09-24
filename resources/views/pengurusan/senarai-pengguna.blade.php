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

        <!-- Table -->
        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Staf RISDA</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Peranan</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Status</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($penggunas ?? [] as $pengguna)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                    {{ $pengguna->nama_penuh ?? $pengguna->name }}
                                </div>
                                <div class="text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                                    {{ $pengguna->email }}
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
                            @if($pengguna->status === 'aktif')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                    Aktif
                                </span>
                            @elseif($pengguna->status === 'tidak_aktif')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                    Tidak Aktif
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                    Digantung
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('pengurusan.show-pengguna', $pengguna) }}" class="text-blue-600 hover:text-blue-900">
                                    <span class="material-symbols-outlined" style="font-size: 18px;">visibility</span>
                                </a>
                                <a href="{{ route('pengurusan.edit-pengguna', $pengguna) }}" class="text-yellow-600 hover:text-yellow-900">
                                    <span class="material-symbols-outlined" style="font-size: 18px;">edit</span>
                                </a>
                                <form action="{{ route('pengurusan.delete-pengguna', $pengguna) }}" method="POST" class="inline" onsubmit="return confirm('Adakah anda pasti untuk memadam {{ $pengguna->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            Tiada data pengguna dijumpai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.page-header>
</x-dashboard-layout>
