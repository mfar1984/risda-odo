@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout title="Senarai RISDA">
    <x-ui.page-header
        title="Senarai RISDA"
        description="Pengurusan senarai RISDA Bahagian dan Stesen"
    >

        <!-- Tab Navigation -->
        <div class="mb-8" x-data="{
            activeTab: '{{ request('tab', 'bahagian') }}',
            init() {
                // Ensure tab state is properly set on page load
                const urlParams = new URLSearchParams(window.location.search);
                const tabParam = urlParams.get('tab');
                if (tabParam) {
                    this.activeTab = tabParam;
                }
            }
        }">
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
                        :action="route('pengurusan.senarai-risda')"
                        search-placeholder="Masukkan nama bahagian, alamat atau telefon"
                        search-name="search_bahagian"
                        :search-value="request('search_bahagian')"
                        :filters="[
                            [
                                'name' => 'status_bahagian',
                                'type' => 'select',
                                'placeholder' => 'Semua Status',
                                'options' => [
                                    'aktif' => 'Aktif',
                                    'tidak_aktif' => 'Tidak Aktif',
                                    'dalam_pembinaan' => 'Dalam Pembinaan'
                                ]
                            ]
                        ]"
                        :reset-url="route('pengurusan.senarai-risda', ['tab' => 'bahagian'])"
                    />

                    <!-- Desktop Table -->
                    <div class="data-table-container">
                    <x-ui.data-table
                        :headers="[
                            ['label' => 'Nama', 'align' => 'text-left'],
                            ['label' => 'Alamat', 'align' => 'text-left'],
                            ['label' => 'No. Tel', 'align' => 'text-left']
                        ]"
                        empty-message="Tiada data RISDA Bahagian dijumpai."
                    >
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
                                <x-ui.action-buttons
                                    :show-url="route('pengurusan.show-bahagian', $bahagian)"
                                    :edit-url="route('pengurusan.edit-bahagian', $bahagian)"
                                    :delete-onclick="'deleteBahagianItem(' . $bahagian->id . ')'"
                                />
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                Tiada data RISDA Bahagian dijumpai.
                            </td>
                        </tr>
                        @endforelse
                    </x-ui.data-table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="mobile-table-card">
                        @forelse($bahagians ?? [] as $bahagian)
                            <div class="mobile-card">
                                <div class="mobile-card-header">
                                    <div class="mobile-card-title">{{ $bahagian->nama_bahagian }}</div>
                                    <div class="mobile-card-badge"><x-ui.status-badge :status="$bahagian->status_dropdown" /></div>
                                </div>
                                <div class="mobile-card-body">
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><span class="material-symbols-outlined">home_pin</span></span>
                                        <span class="mobile-card-value">{{ $bahagian->alamat_1 }}{{ $bahagian->alamat_2 ? ', ' . $bahagian->alamat_2 : '' }}</span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><span class="material-symbols-outlined">location_city</span></span>
                                        <span class="mobile-card-value">{{ $bahagian->poskod }} {{ $bahagian->bandar }}, {{ $bahagian->negeri }}</span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><span class="material-symbols-outlined">call</span></span>
                                        <span class="mobile-card-value">{{ $bahagian->no_telefon }}</span>
                                    </div>
                                </div>
                                <div class="mobile-card-footer">
                                    <a href="{{ route('pengurusan.show-bahagian', $bahagian) }}" class="mobile-card-action mobile-action-view">
                                        <span class="material-symbols-outlined mobile-card-action-icon">visibility</span>
                                        <span class="mobile-card-action-label">Lihat</span>
                                    </a>
                                    <a href="{{ route('pengurusan.edit-bahagian', $bahagian) }}" class="mobile-card-action mobile-action-edit">
                                        <span class="material-symbols-outlined mobile-card-action-icon">edit</span>
                                        <span class="mobile-card-action-label">Edit</span>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="mobile-empty-state">
                                <span class="material-symbols-outlined" style="font-size:48px; color:#9ca3af;">business</span>
                                <p>Tiada RISDA Bahagian</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <x-ui.pagination :paginator="$bahagians" record-label="bahagian" />
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
                        :action="route('pengurusan.senarai-risda')"
                        search-placeholder="Masukkan nama stesen, bahagian atau alamat"
                        search-name="search_stesen"
                        :search-value="request('search_stesen')"
                        :filters="[
                            [
                                'name' => 'status_stesen',
                                'type' => 'select',
                                'placeholder' => 'Semua Status',
                                'options' => [
                                    'aktif' => 'Aktif',
                                    'tidak_aktif' => 'Tidak Aktif',
                                    'dalam_pembinaan' => 'Dalam Pembinaan'
                                ]
                            ],
                            [
                                'name' => 'bahagian_stesen',
                                'type' => 'select',
                                'placeholder' => 'Semua Bahagian',
                                'options' => collect(\App\Models\RisdaBahagian::where('status_dropdown', 'aktif')->orderBy('nama_bahagian')->get())->pluck('nama_bahagian', 'id')->toArray()
                            ]
                        ]"
                        :reset-url="route('pengurusan.senarai-risda', ['tab' => 'stesen'])"
                    />

                    <!-- Desktop Table -->
                    <div class="data-table-container">
                    <x-ui.data-table
                        :headers="[
                            ['label' => 'Nama', 'align' => 'text-left'],
                            ['label' => 'Bahagian', 'align' => 'text-left'],
                            ['label' => 'Alamat', 'align' => 'text-left'],
                            ['label' => 'No. Tel', 'align' => 'text-left']
                        ]"
                        empty-message="Tiada data RISDA Stesen dijumpai."
                    >
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
                                <x-ui.action-buttons
                                    :show-url="route('pengurusan.show-stesen', $stesen)"
                                    :edit-url="route('pengurusan.edit-stesen', $stesen)"
                                    :delete-onclick="'deleteStesenItem(' . $stesen->id . ')'"
                                />
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                Tiada data RISDA Stesen dijumpai.
                            </td>
                        </tr>
                        @endforelse
                    </x-ui.data-table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="mobile-table-card">
                        @forelse($stesens ?? [] as $stesen)
                            <div class="mobile-card">
                                <div class="mobile-card-header">
                                    <div class="mobile-card-title">{{ $stesen->nama_stesen }}</div>
                                    <div class="mobile-card-badge"><x-ui.status-badge :status="$stesen->status_dropdown" /></div>
                                </div>
                                <div class="mobile-card-body">
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><span class="material-symbols-outlined">business</span></span>
                                        <span class="mobile-card-value">{{ $stesen->risdaBahagian->nama_bahagian ?? 'N/A' }}</span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><span class="material-symbols-outlined">home_pin</span></span>
                                        <span class="mobile-card-value">{{ $stesen->alamat_1 }}@if($stesen->alamat_2)<div class="mobile-card-value-secondary">{{ $stesen->alamat_2 }}</div>@endif<div class="mobile-card-value-secondary">{{ $stesen->poskod }} {{ $stesen->bandar }}, {{ $stesen->negeri }}</div></span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><span class="material-symbols-outlined">call</span></span>
                                        <span class="mobile-card-value">{{ $stesen->no_telefon }}</span>
                                    </div>
                                    @if($stesen->email)
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><span class="material-symbols-outlined">mail</span></span>
                                        <span class="mobile-card-value">{{ $stesen->email }}</span>
                                    </div>
                                    @endif
                                </div>
                                <div class="mobile-card-footer">
                                    <a href="{{ route('pengurusan.show-stesen', $stesen) }}" class="mobile-card-action mobile-action-view">
                                        <span class="material-symbols-outlined mobile-card-action-icon">visibility</span>
                                        <span class="mobile-card-action-label">Lihat</span>
                                    </a>
                                    <a href="{{ route('pengurusan.edit-stesen', $stesen) }}" class="mobile-card-action mobile-action-edit">
                                        <span class="material-symbols-outlined mobile-card-action-icon">edit</span>
                                        <span class="mobile-card-action-label">Edit</span>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="mobile-empty-state">
                                <span class="material-symbols-outlined" style="font-size:48px; color:#9ca3af;">location_on</span>
                                <p>Tiada RISDA Stesen</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <x-ui.pagination :paginator="$stesens" record-label="stesen" />
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
                        :action="route('pengurusan.senarai-risda')"
                        search-placeholder="Masukkan no. pekerja, nama, email atau jawatan"
                        search-name="search_staf"
                        :search-value="request('search_staf')"
                        :filters="[
                            [
                                'name' => 'status_staf',
                                'type' => 'select',
                                'placeholder' => 'Semua Status',
                                'options' => [
                                    'aktif' => 'Aktif',
                                    'tidak_aktif' => 'Tidak Aktif',
                                    'gantung' => 'Gantung'
                                ]
                            ],
                            [
                                'name' => 'bahagian_staf',
                                'type' => 'select',
                                'placeholder' => 'Semua Bahagian',
                                'options' => collect(\App\Models\RisdaBahagian::where('status_dropdown', 'aktif')->orderBy('nama_bahagian')->get())->pluck('nama_bahagian', 'id')->toArray()
                            ]
                        ]"
                        :reset-url="route('pengurusan.senarai-risda', ['tab' => 'staf'])"
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
                                <x-ui.action-buttons
                                    :show-url="route('pengurusan.show-staf', $staf)"
                                    :edit-url="route('pengurusan.edit-staf', $staf)"
                                    :delete-onclick="'deleteStafItem(' . $staf->id . ')'"
                                />
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                                Tiada data RISDA Staf dijumpai.
                            </td>
                        </tr>
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
                                    <a href="{{ route('pengurusan.edit-staf', $staf) }}" class="mobile-card-action mobile-action-edit">
                                        <span class="material-symbols-outlined mobile-card-action-icon">edit</span>
                                        <span class="mobile-card-action-label">Edit</span>
                                    </a>
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
                    <x-ui.pagination :paginator="$stafs" record-label="staf" />
                </div>
            </div>
        </div>
    </x-ui.page-header>

    {{-- Centralized Delete Modal --}}
    <x-modals.delete-confirmation-modal />

    {{-- Centralized JavaScript --}}
    @vite('resources/js/delete-actions.js')
</x-dashboard-layout>
