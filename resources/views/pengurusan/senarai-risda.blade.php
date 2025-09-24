<x-dashboard-layout title="Senarai RISDA">
    <x-ui.page-header
        title="Senarai RISDA"
        description="Pengurusan senarai RISDA Bahagian dan Stesen"
    >

        <!-- Tab Navigation -->
        <div class="mb-8" x-data="{ activeTab: 'bahagian' }">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button @click="activeTab = 'bahagian'"
                            :class="activeTab === 'bahagian' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'bahagian' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">business</span>
                        RISDA Bahagian
                    </button>
                    <button @click="activeTab = 'stesen'"
                            :class="activeTab === 'stesen' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'stesen' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">location_on</span>
                        RISDA Stesen
                    </button>
                    <button @click="activeTab = 'staf'"
                            :class="activeTab === 'staf' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'staf' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">people</span>
                        RISDA Staf
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="mt-8">
                <!-- RISDA Bahagian Tab -->
                <div x-show="activeTab === 'bahagian'" x-transition>
                    <!-- Header with Add Button -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Senarai RISDA Bahagian</h3>
                            <p class="text-sm text-gray-600 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Pengurusan RISDA Bahagian dalam sistem</p>
                        </div>
                        <a href="{{ route('pengurusan.tambah-bahagian') }}">
                            <x-buttons.primary-button type="button">
                                <span class="material-symbols-outlined mr-2" style="font-size: 16px;">add_circle</span>
                                Bahagian
                            </x-buttons.primary-button>
                        </a>
                    </div>

                    <!-- Table -->
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Nama</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Alamat</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">No. Tel</th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($bahagians ?? [] as $bahagian)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $bahagian->nama_bahagian }}</div>
                                            <div class="text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">{{ ucfirst(str_replace('_', ' ', $bahagian->status_dropdown)) }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $bahagian->alamat_1 }}{{ $bahagian->alamat_2 ? ', ' . $bahagian->alamat_2 : '' }}</div>
                                            <div class="text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">{{ $bahagian->poskod }} {{ $bahagian->bandar }}, {{ $bahagian->negeri }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $bahagian->no_telefon }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex justify-center space-x-2">
                                                <a href="{{ route('pengurusan.show-bahagian', $bahagian) }}" class="text-blue-600 hover:text-blue-900" title="Lihat">
                                                    <span class="material-symbols-outlined" style="font-size: 18px;">visibility</span>
                                                </a>
                                                <a href="{{ route('pengurusan.edit-bahagian', $bahagian) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                    <span class="material-symbols-outlined" style="font-size: 18px;">edit</span>
                                                </a>
                                                <form action="{{ route('pengurusan.delete-bahagian', $bahagian) }}" method="POST" class="inline" onsubmit="return confirm('Adakah anda pasti untuk memadam {{ $bahagian->nama_bahagian }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                                        <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                            Tiada data RISDA Bahagian dijumpai.
                                        </td>
                                    </tr>
                                    @endforelse
                                    <!-- Sample Data Row 2 -->
                                </tbody>
                            </table>
                        </div>
                </div>

                <!-- RISDA Stesen Tab -->
                <div x-show="activeTab === 'stesen'" x-transition>
                    <!-- Header with Add Button -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Senarai RISDA Stesen</h3>
                            <p class="text-sm text-gray-600 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Pengurusan RISDA Stesen dalam sistem</p>
                        </div>
                        <a href="{{ route('pengurusan.tambah-stesen') }}">
                            <x-buttons.primary-button type="button">
                                <span class="material-symbols-outlined mr-2" style="font-size: 16px;">add_circle</span>
                                Stesen
                            </x-buttons.primary-button>
                        </a>
                    </div>

                    <!-- Table -->
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Nama</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Bahagian</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Alamat</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">No. Tel</th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($stesens ?? [] as $stesen)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $stesen->nama_stesen }}</div>
                                            <div class="text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">{{ ucfirst(str_replace('_', ' ', $stesen->status_dropdown)) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $stesen->risdaBahagian->nama_bahagian ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $stesen->alamat_1 }}</div>
                                            @if($stesen->alamat_2)
                                                <div class="text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">{{ $stesen->alamat_2 }}</div>
                                            @endif
                                            <div class="text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">{{ $stesen->poskod }} {{ $stesen->bandar }}, {{ $stesen->negeri }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">{{ $stesen->no_telefon }}</div>
                                            @if($stesen->email)
                                                <div class="text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">{{ $stesen->email }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <div class="flex justify-center space-x-2">
                                                <a href="{{ route('pengurusan.show-stesen', $stesen) }}" class="text-blue-600 hover:text-blue-900">
                                                    <span class="material-symbols-outlined" style="font-size: 18px;">visibility</span>
                                                </a>
                                                <a href="{{ route('pengurusan.edit-stesen', $stesen) }}" class="text-yellow-600 hover:text-yellow-900">
                                                    <span class="material-symbols-outlined" style="font-size: 18px;">edit</span>
                                                </a>
                                                <form action="{{ route('pengurusan.delete-stesen', $stesen) }}" method="POST" class="inline" onsubmit="return confirm('Adakah anda pasti untuk memadam {{ $stesen->nama_stesen }}?')">
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
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                            Tiada data RISDA Stesen dijumpai.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                </div>

                <!-- RISDA Staf Tab -->
                <div x-show="activeTab === 'staf'" x-transition>
                    <!-- Header with Add Button -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Senarai RISDA Staf</h3>
                            <p class="text-sm text-gray-600 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Pengurusan RISDA Staf dalam sistem</p>
                        </div>
                        <a href="{{ route('pengurusan.tambah-staf') }}">
                            <x-buttons.primary-button type="button">
                                <span class="material-symbols-outlined mr-2" style="font-size: 16px;">add_circle</span>
                                Staf
                            </x-buttons.primary-button>
                        </a>
                    </div>

                    <!-- Table -->
                    <div class="bg-white shadow overflow-hidden sm:rounded-md">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">No. Pekerja</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Nama Penuh</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Bahagian</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Jawatan</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Status</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($stafs ?? [] as $staf)
                                <tr>
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
                                        @if($staf->status === 'aktif')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Aktif
                                            </span>
                                        @elseif($staf->status === 'tidak_aktif')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Tidak Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Gantung
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('pengurusan.show-staf', $staf) }}" class="text-blue-600 hover:text-blue-900">
                                                <span class="material-symbols-outlined" style="font-size: 18px;">visibility</span>
                                            </a>
                                            <a href="{{ route('pengurusan.edit-staf', $staf) }}" class="text-yellow-600 hover:text-yellow-900">
                                                <span class="material-symbols-outlined" style="font-size: 18px;">edit</span>
                                            </a>
                                            <form action="{{ route('pengurusan.delete-staf', $staf) }}" method="POST" class="inline" onsubmit="return confirm('Adakah anda pasti untuk memadam {{ $staf->nama_penuh }}?')">
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
                                        Tiada data RISDA Staf dijumpai.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </x-ui.page-header>
</x-dashboard-layout>
