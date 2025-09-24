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

        <!-- Table -->
        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Nama Kumpulan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Keterangan</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Status</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
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
                            @if($group->status === 'aktif')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                    Aktif
                                </span>
                            @elseif($group->status === 'tidak_aktif')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                    Tidak Aktif
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                    Gantung
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('pengurusan.show-kumpulan', $group) }}" class="text-blue-600 hover:text-blue-900">
                                    <span class="material-symbols-outlined" style="font-size: 18px;">visibility</span>
                                </a>
                                <a href="{{ route('pengurusan.edit-kumpulan', $group) }}" class="text-yellow-600 hover:text-yellow-900">
                                    <span class="material-symbols-outlined" style="font-size: 18px;">edit</span>
                                </a>
                                <form action="{{ route('pengurusan.delete-kumpulan', $group) }}" method="POST" class="inline" onsubmit="return confirm('Adakah anda pasti untuk memadam {{ $group->nama_kumpulan }}?')">
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
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                            Tiada data kumpulan pengguna dijumpai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.page-header>
</x-dashboard-layout>
