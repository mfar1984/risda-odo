@push('styles')
    @vite('resources/css/mobile.css')
@endpush

<x-dashboard-layout title="Aktiviti Log">
    <div x-data="{ 
        showModal: false, 
        selectedActivity: null,
        openModal(activity) {
            this.selectedActivity = activity;
            this.showModal = true;
        }
    }">
    <x-ui.page-header
        title="Aktiviti Log"
        description="Rekod aktiviti sistem dan pengguna"
    >
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

        {{-- Tab Navigation (Admin sees 2 tabs, others see 1) --}}
        @if($isAdmin ?? false)
        <div class="mb-6" x-data="{ activeTab: '{{ request('tab', 'aktiviti-log') }}' }">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button @click="activeTab = 'aktiviti-log'"
                            :class="activeTab === 'aktiviti-log' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'aktiviti-log' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">history</span>
                        Aktiviti Log
                    </button>
                    <button @click="activeTab = 'audit-trail'"
                            :class="activeTab === 'audit-trail' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'audit-trail' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">policy</span>
                        Audit Trail
                    </button>
                </nav>
            </div>

            {{-- TAB 1: Aktiviti Log --}}
            <div x-show="activeTab === 'aktiviti-log'" x-transition class="mt-6">
        @endif

        <!-- Filter Section -->
        <x-ui.search-filter
            :action="route('pengurusan.aktiviti-log')"
            search-placeholder="Cari aktiviti, model atau penerangan"
            :search-value="request('search')"
            :filters="[
                [
                    'name' => 'event',
                    'type' => 'select',
                    'placeholder' => 'Semua Event',
                    'options' => [
                        'created' => 'Cipta',
                        'updated' => 'Kemaskini',
                        'deleted' => 'Padam'
                    ]
                ]
            ]"
            :reset-url="route('pengurusan.aktiviti-log')"
        />

        <!-- Desktop Table (Hidden on Mobile) -->
        <div class="data-table-container">
        <x-ui.data-table
            :headers="[
                ['label' => 'Pengguna', 'align' => 'text-left'],
                ['label' => 'Aktiviti', 'align' => 'text-left'],
                ['label' => 'Model', 'align' => 'text-left'],
                ['label' => 'Masa', 'align' => 'text-left'],
                ['label' => 'IP Address', 'align' => 'text-left']
            ]"
            empty-message="Tiada rekod aktiviti dijumpai."
        >
            @forelse($activities as $activity)
            <tr class="hover:bg-gray-50" x-data='{
                activityData: {
                    id: @json($activity->id),
                    causer_name: @json($activity->causer ? $activity->causer->name : 'Sistem'),
                    causer_email: @json($activity->causer ? $activity->causer->email : 'N/A'),
                    description: @json($activity->description),
                    event: @json($activity->event ?? 'N/A'),
                    subject_type: @json(class_basename($activity->subject_type ?? 'N/A')),
                    subject_id: @json($activity->subject_id ?? 'N/A'),
                    log_name: @json($activity->log_name ?? 'default'),
                    ip: @json($activity->properties['ip'] ?? 'N/A'),
                    user_agent: @json($activity->properties['user_agent'] ?? 'N/A'),
                    created_at: @json($activity->created_at->format('d/m/Y H:i:s')),
                    properties: @json($activity->properties ?? [])
                }
            }'>
                <!-- Pengguna -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                        {{ $activity->causer ? $activity->causer->name : 'Sistem' }}
                    </div>
                    @if($activity->causer && $activity->causer->email)
                    <div class="text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                        {{ $activity->causer->email }}
                    </div>
                    @endif
                </td>

                <!-- Aktiviti -->
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                        {{ $activity->description }}
                    </div>
                    @if($activity->event)
                    <div class="text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                        @if($activity->event === 'created')
                            <span class="text-green-600">Cipta</span>
                        @elseif($activity->event === 'updated')
                            <span class="text-blue-600">Kemaskini</span>
                        @elseif($activity->event === 'deleted')
                            <span class="text-red-600">Padam</span>
                        @else
                            {{ ucfirst($activity->event) }}
                        @endif
                    </div>
                    @endif
                </td>

                <!-- Model -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                        {{ class_basename($activity->subject_type ?? 'N/A') }}
                    </div>
                    @if($activity->subject_id)
                    <div class="text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                        ID: {{ $activity->subject_id }}
                    </div>
                    @endif
                </td>

                <!-- Masa -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
                        {{ $activity->created_at->format('d/m/Y') }}
                    </div>
                    <div class="text-sm text-gray-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
                        {{ $activity->created_at->format('H:i:s') }}
                    </div>
                </td>

                <!-- IP Address -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 font-mono" style="font-family: 'Courier New', monospace !important; font-size: 11px !important;">
                        {{ $activity->properties['ip'] ?? 'N/A' }}
                    </div>
                </td>

                <!-- Tindakan -->
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <button 
                        type="button"
                        @click="openModal(activityData)"
                        class="inline-flex items-center p-1 text-blue-600 hover:text-blue-800 transition-colors"
                        title="Lihat Butiran"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </td>
            </tr>
            @empty
            @endforelse
        </x-ui.data-table>
        </div>

        <!-- Mobile Card View -->
        <div class="mobile-table-card">
            @forelse($activities as $activity)
                <div class="mobile-card">
                    <div class="mobile-card-header">
                        <div class="mobile-card-title">{{ $activity->description }}</div>
                        @if($activity->event)
                        <div class="mobile-card-badge">
                            <span class="inline-flex items-center h-5 px-2 text-[10px] font-medium rounded-sm"
                                  style="font-family: Poppins, sans-serif !important;"
                                  class="bg-gray-100 text-gray-800 border border-gray-200">
                                {{ ucfirst($activity->event) }}
                            </span>
            </div>
        @endif
                    </div>
                    <div class="mobile-card-body">
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">person</span></span>
                            <span class="mobile-card-value">{{ $activity->causer->name ?? 'Sistem' }}<div class="mobile-card-value-secondary">{{ $activity->causer->email ?? 'N/A' }}</div></span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">category</span></span>
                            <span class="mobile-card-value">{{ class_basename($activity->subject_type ?? 'N/A') }}<div class="mobile-card-value-secondary">ID: {{ $activity->subject_id ?? 'N/A' }}</div></span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">today</span></span>
                            <span class="mobile-card-value">{{ $activity->created_at->format('d/m/Y') }}<div class="mobile-card-value-secondary">{{ $activity->created_at->format('H:i:s') }}</div></span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label"><span class="material-symbols-outlined">wifi</span></span>
                            <span class="mobile-card-value">{{ $activity->properties['ip'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="mobile-card-footer">
                        <button type="button" @click="openModal({
                            id: @js((string) $activity->id),
                            causer_name: @js($activity->causer->name ?? 'Sistem'),
                            causer_email: @js($activity->causer->email ?? 'N/A'),
                            description: @js($activity->description),
                            event: @js($activity->event ?? 'N/A'),
                            subject_type: @js(class_basename($activity->subject_type ?? 'N/A')),
                            subject_id: @js($activity->subject_id ?? 'N/A'),
                            log_name: @js($activity->log_name ?? 'default'),
                            ip: @js($activity->properties['ip'] ?? 'N/A'),
                            user_agent: @js($activity->properties['user_agent'] ?? 'N/A'),
                            created_at: @js($activity->created_at->format('d/m/Y H:i:s')), 
                            properties: @js($activity->properties ?? [])
                        })" class="mobile-card-action mobile-action-view">
                            <span class="material-symbols-outlined mobile-card-action-icon">visibility</span>
                            <span class="mobile-card-action-label">Butiran</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="mobile-empty-state">
                    <span class="material-symbols-outlined" style="font-size:48px; color:#9ca3af;">article</span>
                    <p>Tiada rekod aktiviti</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination (exactly like log-pemandu) -->
        <div class="mt-6">
            <x-ui.pagination :paginator="$activities" record-label="aktiviti" />
        </div>

        {{-- Close TAB 1 and add TAB 2 for Admin --}}
        @if($isAdmin ?? false)
            </div>

            {{-- TAB 2: Audit Trail (Admin Only) --}}
            <div x-show="activeTab === 'audit-trail'" x-transition class="mt-6">
                @include('pengurusan.partials.audit-trail-tab')
            </div>
        </div>
        @endif

    </x-ui.page-header>

    <!-- Modal Popup (Beautiful Design) -->
    <div x-show="showModal" 
         x-cloak
         @keydown.escape.window="showModal = false"
         class="fixed inset-0 overflow-y-auto"
         style="z-index: 9999 !important;">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
             @click="showModal = false"></div>
        
        <!-- Modal Container -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-sm shadow-xl w-full max-w-3xl max-h-[85vh] my-8 flex flex-col"
                 @click.away="showModal = false">
                
                <!-- Header (Gradient Blue) -->
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-white text-[20px]">article</span>
                        <div>
                            <h3 class="text-white font-semibold" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">
                    Butiran Aktiviti Log
                </h3>
                            <p class="text-indigo-100" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
                                Maklumat lengkap rekod aktiviti sistem
                            </p>
                        </div>
                    </div>
                    <button @click="showModal = false" class="text-white hover:text-gray-200">
                        <span class="material-symbols-outlined text-[24px]">close</span>
                </button>
            </div>

                <!-- Body (Scrollable) -->
                <div class="p-6 overflow-y-auto flex-1" style="max-height: calc(85vh - 140px);">
                <template x-if="selectedActivity">
                    <div>
                        
                        <!-- Activity Info Card -->
                        <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 mb-6 rounded-sm">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-indigo-600 text-[20px]">info</span>
                                <div class="flex-1">
                                    <div class="text-[12px] font-semibold text-indigo-900 mb-1" style="font-family: Poppins, sans-serif !important;">
                                        <span x-text="selectedActivity.description"></span>
                            </div>
                                    <div class="text-[10px] text-indigo-700" style="font-family: Poppins, sans-serif !important;">
                                        ID: <span x-text="selectedActivity.id" class="font-mono"></span> 
                                        <span class="mx-2">•</span> 
                                        <span x-text="selectedActivity.created_at"></span>
                            </div>
                            </div>
                            <template x-if="selectedActivity.event && selectedActivity.event !== 'N/A'">
                                    <span class="inline-flex items-center h-5 px-2 text-[10px] font-medium rounded-sm"
                                          :class="{
                                              'bg-green-100 text-green-800 border border-green-200': selectedActivity.event === 'created',
                                              'bg-blue-100 text-blue-800 border border-blue-200': selectedActivity.event === 'updated',
                                              'bg-red-100 text-red-800 border border-red-200': selectedActivity.event === 'deleted',
                                              'bg-yellow-100 text-yellow-800 border border-yellow-200': selectedActivity.event === 'approved',
                                              'bg-orange-100 text-orange-800 border border-orange-200': selectedActivity.event === 'rejected',
                                              'bg-purple-100 text-purple-800 border border-purple-200': selectedActivity.event === 'exported',
                                              'bg-gray-100 text-gray-800 border border-gray-200': !['created','updated','deleted','approved','rejected','exported'].includes(selectedActivity.event)
                                          }"
                                          style="font-family: Poppins, sans-serif !important;"
                                          x-text="selectedActivity.event === 'created' ? 'Cipta' : 
                                                  selectedActivity.event === 'updated' ? 'Kemaskini' : 
                                                  selectedActivity.event === 'deleted' ? 'Padam' : 
                                                  selectedActivity.event === 'approved' ? 'Lulus' :
                                                  selectedActivity.event === 'rejected' ? 'Tolak' :
                                                  selectedActivity.event === 'exported' ? 'Eksport' :
                                                  selectedActivity.event">
                                    </span>
                            </template>
                            </div>
                            </div>

                        <!-- Details Grid -->
                        <div class="space-y-6">

                            <!-- User Info Section -->
                            <div>
                                <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-200">
                                    <span class="material-symbols-outlined text-gray-600 text-[16px]">person</span>
                                    <h4 class="text-[11px] font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important;">
                                        Maklumat Pengguna
                                    </h4>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Nama Pengguna</div>
                                        <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.causer_name"></div>
                                    </div>
                                    <div>
                                        <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Email</div>
                                        <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.causer_email"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Context Info Section (Adaptive based on log_name) -->
                            <template x-if="selectedActivity.properties && Object.keys(selectedActivity.properties).length > 2">
                            <div>
                                <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-200">
                                    <span class="material-symbols-outlined text-gray-600 text-[16px]" 
                                          x-text="selectedActivity.log_name === 'program' ? 'event' : 
                                                  selectedActivity.log_name === 'kenderaan' ? 'directions_car' :
                                                  selectedActivity.log_name === 'tuntutan' ? 'receipt_long' :
                                                  selectedActivity.log_name === 'kumpulan' ? 'groups' :
                                                  selectedActivity.log_name === 'pengguna' ? 'account_circle' :
                                                  selectedActivity.log_name === 'integrasi' ? 'sync_alt' :
                                                  selectedActivity.log_name === 'risda' ? 'apartment' :
                                                  selectedActivity.log_name === 'log_pemandu' ? 'local_taxi' :
                                                  selectedActivity.log_name === 'tetapan' ? 'settings' :
                                                  selectedActivity.log_name === 'authentication' ? 'lock' :
                                                  'info'"></span>
                                    <h4 class="text-[11px] font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important;">
                                        Maklumat <span x-text="selectedActivity.log_name === 'program' ? 'Program' : 
                                                            selectedActivity.log_name === 'kenderaan' ? 'Kenderaan' :
                                                            selectedActivity.log_name === 'tuntutan' ? 'Tuntutan' :
                                                            selectedActivity.log_name === 'kumpulan' ? 'Kumpulan' :
                                                            selectedActivity.log_name === 'pengguna' ? 'Pengguna' :
                                                            selectedActivity.log_name === 'integrasi' ? 'Integrasi' :
                                                            selectedActivity.log_name === 'risda' ? 'RISDA' :
                                                            selectedActivity.log_name === 'log_pemandu' ? 'Log Pemandu' :
                                                            selectedActivity.log_name === 'tetapan' ? 'Tetapan' :
                                                            selectedActivity.log_name === 'authentication' ? 'Akses' :
                                                            'Tambahan'"></span>
                                    </h4>
                            </div>

                                <!-- Adaptive Content based on log_name -->
                                <div class="space-y-3">
                                    <!-- Program specific fields -->
                                    <template x-if="selectedActivity.log_name === 'program'">
                                        <div class="grid grid-cols-2 gap-4">
                                            <template x-if="selectedActivity.properties.program_name">
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Nama Program</div>
                                                    <div class="text-[11px] font-semibold text-indigo-700" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.program_name"></div>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.properties.pemohon_nama">
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Pemohon</div>
                                                    <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.pemohon_nama"></div>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.properties.pemandu_nama">
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Pemandu</div>
                                                    <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.pemandu_nama"></div>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.properties.kenderaan_plat">
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Kenderaan</div>
                                                    <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.kenderaan_plat"></div>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.properties.lokasi">
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Lokasi</div>
                                                    <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.lokasi"></div>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.properties.tarikh_mula">
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Tarikh Mula</div>
                                                    <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.tarikh_mula"></div>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.properties.status">
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Status</div>
                                                    <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.status"></div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Tuntutan specific fields -->
                                    <template x-if="selectedActivity.log_name === 'tuntutan'">
                                        <div class="grid grid-cols-2 gap-4">
                                            <template x-if="selectedActivity.properties.program_name">
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Program</div>
                                                    <div class="text-[11px] font-semibold text-indigo-700" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.program_name"></div>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.properties.category">
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Kategori</div>
                                                    <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.category"></div>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.properties.amount">
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Jumlah</div>
                                                    <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="'RM ' + Number(selectedActivity.properties.amount).toFixed(2)"></div>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.properties.driver_name">
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Pemandu</div>
                                                    <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.driver_name"></div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Log Pemandu specific fields -->
                                    <template x-if="selectedActivity.log_name === 'log_pemandu'">
                                        <div class="grid grid-cols-2 gap-4">
                                            <template x-if="selectedActivity.properties.program_name">
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Program</div>
                                                    <div class="text-[11px] font-semibold text-indigo-700" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.program_name"></div>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.properties.driver_name">
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Pemandu</div>
                                                    <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.driver_name"></div>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.properties.vehicle_plate">
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Kenderaan</div>
                                                    <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.vehicle_plate"></div>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.properties.tarikh_perjalanan">
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Tarikh</div>
                                                    <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.tarikh_perjalanan"></div>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.properties.masa_keluar">
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Masa Keluar</div>
                                                    <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.masa_keluar"></div>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.properties.masa_masuk">
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Masa Masuk</div>
                                                    <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.masa_masuk"></div>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.properties.odometer_keluar">
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Odometer Keluar</div>
                                                    <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.odometer_keluar"></div>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.properties.odometer_masuk">
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Odometer Masuk</div>
                                                    <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.odometer_masuk"></div>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.properties.jarak">
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Jarak</div>
                                                    <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.jarak + ' km'"></div>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.properties.liter_minyak">
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Isi Minyak</div>
                                                    <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.liter_minyak + ' L'"></div>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.properties.kos_minyak">
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Kos Minyak</div>
                                                    <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="'RM ' + Number(selectedActivity.properties.kos_minyak).toFixed(2)"></div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Kumpulan specific fields -->
                                    <template x-if="selectedActivity.log_name === 'kumpulan'">
                                        <div class="space-y-4">
                                            <div class="grid grid-cols-2 gap-4">
                                                <template x-if="selectedActivity.properties.group_name">
                                                    <div>
                                                        <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Nama Kumpulan</div>
                                                        <div class="text-[11px] font-semibold text-indigo-700" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.group_name"></div>
                                                    </div>
                                                </template>
                                                <template x-if="selectedActivity.properties.new_status">
                                                    <div>
                                                        <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Status</div>
                                                        <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.new_status"></div>
                                                    </div>
                                                </template>
                                            </div>

                                            <!-- On created: list of granted permissions -->
                                            <template x-if="selectedActivity.event === 'created' && selectedActivity.properties.permissions_granted_detailed && selectedActivity.properties.permissions_granted_detailed.length">
                                                <div class="bg-green-50 border border-green-200 rounded-sm p-3">
                                                    <div class="text-[10px] font-semibold text-green-900 mb-2" style="font-family: Poppins, sans-serif !important;">Kebenaran Ditetapkan</div>
                                                    <ul class="list-disc ml-5 space-y-1">
                                                        <template x-for="perm in selectedActivity.properties.permissions_granted_detailed" :key="perm.module + '-' + perm.action">
                                                            <li class="text-[11px] text-gray-900" style="font-family: Poppins, sans-serif !important;">
                                                                <span class="font-medium" x-text="perm.module_label"></span>
                                                                <span class="mx-1">•</span>
                                                                <span x-text="perm.action_label"></span>
                                                                <span class="ml-2 inline-flex items-center px-1.5 rounded-sm text-[10px] bg-green-100 text-green-800 border border-green-200">Grant</span>
                                                            </li>
                                                        </template>
                                                    </ul>
                                                </div>
                                            </template>

                                            <!-- On updated: list of permission changes -->
                                            <template x-if="selectedActivity.event === 'updated' && selectedActivity.properties.permission_changes_detailed && selectedActivity.properties.permission_changes_detailed.length">
                                                <div class="bg-blue-50 border border-blue-200 rounded-sm p-3">
                                                    <div class="text-[10px] font-semibold text-blue-900 mb-2" style="font-family: Poppins, sans-serif !important;">Perubahan Kebenaran</div>
                                                    <ul class="list-disc ml-5 space-y-1">
                                                        <template x-for="chg in selectedActivity.properties.permission_changes_detailed" :key="chg.module + '-' + chg.action">
                                                            <li class="text-[11px] text-gray-900" style="font-family: Poppins, sans-serif !important;">
                                                                <span class="font-medium" x-text="chg.module_label"></span>
                                                                <span class="mx-1">•</span>
                                                                <span x-text="chg.action_label"></span>
                                                                <span class="ml-2 inline-flex items-center px-1.5 rounded-sm text-[10px]" :class="chg.change === 'grant' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200'" x-text="chg.change === 'grant' ? 'Grant' : 'Revoke'"></span>
                                                            </li>
                                                        </template>
                                                    </ul>
                                                    <div class="mt-2 text-[10px] text-blue-900" style="font-family: Poppins, sans-serif !important;" x-show="selectedActivity.properties.granted_before !== undefined">
                                                        Jumlah kebenaran: <span class="font-semibold" x-text="selectedActivity.properties.granted_before"></span> → <span class="font-semibold" x-text="selectedActivity.properties.granted_after"></span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Pengguna specific fields -->
                                    <template x-if="selectedActivity.log_name === 'pengguna'">
                                        <div class="space-y-4">
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Nama</div>
                                                    <div class="text-[11px] font-semibold text-indigo-700" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.user_name"></div>
                                                </div>
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Email</div>
                                                    <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.user_email"></div>
                                                </div>
                                                <template x-if="selectedActivity.properties.group_name || selectedActivity.properties.group_name_after">
                                                    <div>
                                                        <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Kumpulan</div>
                                                        <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;">
                                                            <template x-if="selectedActivity.event === 'created'">
                                                                <span x-text="selectedActivity.properties.group_name || '-' "></span>
                                                            </template>
                                                            <template x-if="selectedActivity.event === 'updated'">
                                                                <span x-text="selectedActivity.properties.group_name_before || '-' "></span>
                                                                <span class="mx-1 text-blue-600">→</span>
                                                                <span class="font-semibold" x-text="selectedActivity.properties.group_name_after || '-' "></span>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </template>
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Status</div>
                                                    <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.status || selectedActivity.properties.changes?.status?.new"></div>
                                                </div>
                                            </div>

                                            <!-- Perubahan Medan -->
                                            <template x-if="selectedActivity.event === 'updated' && selectedActivity.properties.total_fields_changed">
                                                <div class="bg-blue-50 border border-blue-200 rounded-sm p-3">
                                                    <div class="text-[10px] font-semibold text-blue-900 mb-2" style="font-family: Poppins, sans-serif !important;">Perubahan Maklumat Pengguna</div>
                                                    <div class="text-[11px] text-gray-900" style="font-family: Poppins, sans-serif !important;">
                                                        <template x-if="selectedActivity.properties.changes?.name">
                                                            <div>Nama: <span class="text-gray-600" x-text="selectedActivity.properties.changes.name.old"></span> <span class="mx-1 text-blue-600">→</span> <span class="font-semibold" x-text="selectedActivity.properties.changes.name.new"></span></div>
                                                        </template>
                                                        <template x-if="selectedActivity.properties.changes?.email">
                                                            <div>Email: <span class="text-gray-600" x-text="selectedActivity.properties.changes.email.old"></span> <span class="mx-1 text-blue-600">→</span> <span class="font-semibold" x-text="selectedActivity.properties.changes.email.new"></span></div>
                                                        </template>
                                                        <template x-if="selectedActivity.properties.changes?.status">
                                                            <div>Status: <span class="text-gray-600" x-text="selectedActivity.properties.changes.status.old"></span> <span class="mx-1 text-blue-600">→</span> <span class="font-semibold" x-text="selectedActivity.properties.changes.status.new"></span></div>
                                                        </template>
                                                        <template x-if="selectedActivity.properties.changes?.jenis_organisasi">
                                                            <div>Jenis Organisasi: <span class="text-gray-600" x-text="selectedActivity.properties.changes.jenis_organisasi.old"></span> <span class="mx-1 text-blue-600">→</span> <span class="font-semibold" x-text="selectedActivity.properties.changes.jenis_organisasi.new"></span></div>
                                                        </template>
                                                        <template x-if="selectedActivity.properties.changes?.organisasi_id">
                                                            <div>Organisasi ID: <span class="text-gray-600" x-text="selectedActivity.properties.changes.organisasi_id.old"></span> <span class="mx-1 text-blue-600">→</span> <span class="font-semibold" x-text="selectedActivity.properties.changes.organisasi_id.new"></span></div>
                                                        </template>
                                                        <template x-if="selectedActivity.properties.changes?.stesen_akses_ids">
                                                            <div>Stesen Akses: <span class="text-gray-600" x-text="JSON.stringify(selectedActivity.properties.changes.stesen_akses_ids.old || [])"></span> <span class="mx-1 text-blue-600">→</span> <span class="font-semibold" x-text="JSON.stringify(selectedActivity.properties.changes.stesen_akses_ids.new || [])"></span></div>
                                                        </template>
                                                        <template x-if="selectedActivity.properties.password_changed">
                                                            <div class="text-red-700">Kata Laluan: Ditukar</div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Kenderaan specific fields -->
                                    <template x-if="selectedActivity.log_name === 'kenderaan'">
                                        <div class="space-y-4">
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">No. Plat</div>
                                                    <div class="text-[11px] font-semibold text-indigo-700" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.no_plat"></div>
                                                </div>
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Jenama/Model</div>
                                                    <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="(selectedActivity.properties.jenama || '') + ' ' + (selectedActivity.properties.model || '')"></div>
                                                </div>
                                                <template x-if="selectedActivity.properties.cukai_tamat_tempoh">
                                                    <div>
                                                        <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Cukai Tamat Tempoh</div>
                                                        <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.cukai_tamat_tempoh"></div>
                                                    </div>
                                                </template>
                                                <template x-if="selectedActivity.properties.tarikh_pendaftaran">
                                                    <div>
                                                        <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Tarikh Pendaftaran</div>
                                                        <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.tarikh_pendaftaran"></div>
                                                    </div>
                                                </template>
                                            </div>

                                            <!-- Perubahan Kenderaan -->
                                            <template x-if="selectedActivity.event === 'updated' && selectedActivity.properties.changes">
                                                <div class="bg-blue-50 border border-blue-200 rounded-sm p-3">
                                                    <div class="text-[10px] font-semibold text-blue-900 mb-2" style="font-family: Poppins, sans-serif !important;">Perubahan Kenderaan</div>
                                                    <div class="text-[11px] text-gray-900" style="font-family: Poppins, sans-serif !important;">
                                                        <template x-if="selectedActivity.properties.changes.no_plat">
                                                            <div>No. Plat: <span class="text-gray-600" x-text="selectedActivity.properties.changes.no_plat.old"></span> <span class="mx-1 text-blue-600">→</span> <span class="font-semibold" x-text="selectedActivity.properties.changes.no_plat.new"></span></div>
                                                        </template>
                                                        <template x-if="selectedActivity.properties.changes.status">
                                                            <div>Status: <span class="text-gray-600" x-text="selectedActivity.properties.changes.status.old"></span> <span class="mx-1 text-blue-600">→</span> <span class="font-semibold" x-text="selectedActivity.properties.changes.status.new"></span></div>
                                                        </template>
                                                        <template x-if="selectedActivity.properties.changes.cukai_tamat_tempoh">
                                                            <div>Cukai Tamat Tempoh: <span class="text-gray-600" x-text="selectedActivity.properties.changes.cukai_tamat_tempoh.old"></span> <span class="mx-1 text-blue-600">→</span> <span class="font-semibold" x-text="selectedActivity.properties.changes.cukai_tamat_tempoh.new"></span></div>
                                                        </template>
                                                        <template x-if="selectedActivity.properties.new_documents && selectedActivity.properties.new_documents.length">
                                                            <div>Dokumen Baharu: <span class="font-medium" x-text="selectedActivity.properties.new_documents.join(', ')"></span></div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Integrasi specific fields -->
                                    <template x-if="selectedActivity.log_name === 'integrasi'">
                                        <div class="space-y-4">
                                            <!-- API Token generated -->
                                            <template x-if="selectedActivity.event === 'generated_token'">
                                                <div class="bg-purple-50 border border-purple-200 rounded-sm p-3">
                                                    <div class="text-[10px] font-semibold text-purple-900 mb-2" style="font-family: Poppins, sans-serif !important;">API Token Dijana</div>
                                                    <div class="text-[11px] text-gray-900" style="font-family: Poppins, sans-serif !important;">
                                                        <div>Token Lama: <span class="font-mono" x-text="selectedActivity.properties.old_token_masked || '-' "></span></div>
                                                        <div>Token Baharu: <span class="font-mono" x-text="selectedActivity.properties.new_token_masked"></span></div>
                                                    </div>
                                                </div>
                                            </template>

                                            <!-- CORS updated -->
                                            <template x-if="selectedActivity.event === 'updated_cors'">
                                                <div class="bg-indigo-50 border border-indigo-200 rounded-sm p-3">
                                                    <div class="text-[10px] font-semibold text-indigo-900 mb-2" style="font-family: Poppins, sans-serif !important;">CORS Dikemaskini</div>
                                                    <div class="text-[11px] text-gray-900 space-y-1" style="font-family: Poppins, sans-serif !important;">
                                                        <div>Allow All: <span class="text-gray-600" x-text="selectedActivity.properties.old_allow_all"></span> <span class="mx-1 text-blue-600">→</span> <span class="font-semibold" x-text="selectedActivity.properties.new_allow_all"></span></div>
                                                        <div>Origins Lama: <span class="text-gray-600" x-text="JSON.stringify(selectedActivity.properties.old_origins || [])"></span></div>
                                                        <div>Origins Baharu: <span class="font-semibold" x-text="JSON.stringify(selectedActivity.properties.new_origins || [])"></span></div>
                                                    </div>
                                                </div>
                                            </template>

                                            <!-- Cuaca updated -->
                                            <template x-if="selectedActivity.event === 'updated_weather'">
                                                <div class="bg-sky-50 border border-sky-200 rounded-sm p-3">
                                                    <div class="text-[10px] font-semibold text-sky-900 mb-2" style="font-family: Poppins, sans-serif !important;">Konfigurasi Cuaca</div>
                                                    <div class="text-[11px] text-gray-900 space-y-1" style="font-family: Poppins, sans-serif !important;">
                                                        <template x-if="selectedActivity.properties.changes?.location">
                                                            <div>Lokasi: <span class="text-gray-600" x-text="selectedActivity.properties.changes.location.old"></span> <span class="mx-1 text-blue-600">→</span> <span class="font-semibold" x-text="selectedActivity.properties.changes.location.new"></span></div>
                                                        </template>
                                                        <template x-if="selectedActivity.properties.changes?.units">
                                                            <div>Unit: <span class="text-gray-600" x-text="selectedActivity.properties.changes.units.old"></span> <span class="mx-1 text-blue-600">→</span> <span class="font-semibold" x-text="selectedActivity.properties.changes.units.new"></span></div>
                                                        </template>
                                                        <template x-if="selectedActivity.properties.api_key_changed">
                                                            <div class="text-amber-700">API Key: Ditukar</div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>

                                            <!-- Email updated -->
                                            <template x-if="selectedActivity.event === 'updated_email'">
                                                <div class="bg-rose-50 border border-rose-200 rounded-sm p-3">
                                                    <div class="text-[10px] font-semibold text-rose-900 mb-2" style="font-family: Poppins, sans-serif !important;">Konfigurasi Email</div>
                                                    <div class="text-[11px] text-gray-900 space-y-1" style="font-family: Poppins, sans-serif !important;">
                                                        <template x-if="selectedActivity.properties.changes?.smtp_host">
                                                            <div>SMTP Host: <span class="text-gray-600" x-text="selectedActivity.properties.changes.smtp_host.old"></span> <span class="mx-1 text-blue-600">→</span> <span class="font-semibold" x-text="selectedActivity.properties.changes.smtp_host.new"></span></div>
                                                        </template>
                                                        <template x-if="selectedActivity.properties.changes?.smtp_port">
                                                            <div>SMTP Port: <span class="text-gray-600" x-text="selectedActivity.properties.changes.smtp_port.old"></span> <span class="mx-1 text-blue-600">→</span> <span class="font-semibold" x-text="selectedActivity.properties.changes.smtp_port.new"></span></div>
                                                        </template>
                                                        <template x-if="selectedActivity.properties.changes?.smtp_from_address">
                                                            <div>From Address: <span class="text-gray-600" x-text="selectedActivity.properties.changes.smtp_from_address.old"></span> <span class="mx-1 text-blue-600">→</span> <span class="font-semibold" x-text="selectedActivity.properties.changes.smtp_from_address.new"></span></div>
                                                        </template>
                                                        <template x-if="selectedActivity.properties.password_changed">
                                                            <div class="text-red-700">Kata Laluan SMTP: Ditukar</div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- RISDA (Bahagian/Stesen/Staf) specific fields -->
                                    <template x-if="selectedActivity.log_name === 'risda'">
                                        <div class="space-y-4">
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Entiti</div>
                                                    <div class="text-[11px] font-semibold text-indigo-700" style="font-family: Poppins, sans-serif !important;" x-text="(selectedActivity.properties.entity || '').toUpperCase()"></div>
                                                </div>
                                                <div>
                                                    <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Nama</div>
                                                    <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.nama_bahagian || selectedActivity.properties.nama_stesen || selectedActivity.properties.nama_penuh || '-' "></div>
                                                </div>
                                            </div>

                                            <template x-if="selectedActivity.event === 'updated' && selectedActivity.properties.changes && Object.keys(selectedActivity.properties.changes).length">
                                                <div class="bg-blue-50 border border-blue-200 rounded-sm p-3">
                                                    <div class="text-[10px] font-semibold text-blue-900 mb-2" style="font-family: Poppins, sans-serif !important;">Perubahan Butiran</div>
                                                    <div class="text-[11px] text-gray-900 space-y-1" style="font-family: Poppins, sans-serif !important;">
                                                        <template x-for="(chg, key) in selectedActivity.properties.changes" :key="key">
                                                            <div>
                                                                <span class="capitalize" x-text="key.replace(/_/g,' ')"></span>:
                                                                <span class="text-gray-600" x-text="String(chg.old)"></span>
                                                                <span class="mx-1 text-blue-600">→</span>
                                                                <span class="font-semibold" x-text="String(chg.new)"></span>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Tetapan Umum specific fields -->
                                    <template x-if="selectedActivity.log_name === 'tetapan'">
                                        <div class="space-y-3">
                                <div>
                                                <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Nama Sistem</div>
                                                <div class="text-[11px] font-semibold text-indigo-700" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.system_name"></div>
                                            </div>
                                            <template x-if="selectedActivity.properties.total_fields_changed !== undefined">
                                                <div class="bg-yellow-50 border border-yellow-200 rounded-sm p-3">
                                                    <div class="text-[10px] font-semibold text-yellow-900 mb-1" style="font-family: Poppins, sans-serif !important;">Medan Dikemaskini</div>
                                                    <div class="text-[11px] text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.total_fields_changed + ' medan ditukar'"></div>
                                                </div>
                                            </template>
                                </div>
                            </template>
                            
                                    <!-- Support Ticket specific fields -->
                                    <template x-if="selectedActivity.log_name === 'support'">
                                        <div class="space-y-4">
                                            <div class="bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 rounded-sm p-3">
                                                <div class="text-[10px] font-semibold text-purple-900 mb-3" style="font-family: Poppins, sans-serif !important;">
                                                    🎫 Maklumat Tiket Sokongan
                                                </div>
                                                <div class="grid grid-cols-2 gap-4">
                                                    <template x-if="selectedActivity.properties.ticket_number">
                                                        <div>
                                                            <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">No. Tiket</div>
                                                            <div class="text-[12px] font-bold text-purple-700 font-mono" style="font-family: 'Courier New', monospace !important;" x-text="selectedActivity.properties.ticket_number"></div>
                                                        </div>
                                                    </template>
                                                    <template x-if="selectedActivity.properties.subject">
                                                        <div>
                                                            <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Subjek</div>
                                                            <div class="text-[11px] font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.subject"></div>
                                                        </div>
                                                    </template>
                                                    <template x-if="selectedActivity.properties.category">
                                                        <div>
                                                            <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Kategori</div>
                                                            <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.category"></div>
                                                        </div>
                                                    </template>
                                                    <template x-if="selectedActivity.properties.priority">
                                                        <div>
                                                            <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Keutamaan</div>
                                                            <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.priority.toUpperCase()"></div>
                                                        </div>
                                                    </template>
                                                    <template x-if="selectedActivity.properties.organization">
                                <div>
                                                            <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Organisasi</div>
                                                            <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.organization"></div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>

                                            <!-- Show assignee info for 'assigned' event -->
                                            <template x-if="selectedActivity.event === 'assigned'">
                                                <div class="bg-blue-50 border border-blue-200 rounded-sm p-3">
                                                    <div class="text-[10px] font-semibold text-blue-900 mb-2" style="font-family: Poppins, sans-serif !important;">
                                                        👤 Tugasan
                                                    </div>
                                                    <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;">
                                                        <template x-if="selectedActivity.properties.old_assignee && selectedActivity.properties.old_assignee !== 'Tiada'">
                                                            <span>
                                                                <span class="text-gray-600" x-text="selectedActivity.properties.old_assignee"></span>
                                                                <span class="mx-2 text-blue-600">→</span>
                                                            </span>
                                                        </template>
                                                        <span class="text-blue-700 font-semibold" x-text="selectedActivity.properties.new_assignee"></span>
                                                    </div>
                                                </div>
                                            </template>

                                            <!-- Show reply info for 'replied' event -->
                                            <template x-if="selectedActivity.event === 'replied' && selectedActivity.properties.reply_role">
                                                <div class="bg-green-50 border border-green-200 rounded-sm p-3">
                                                    <div class="text-[10px] font-semibold text-green-900 mb-2" style="font-family: Poppins, sans-serif !important;">
                                                        💬 Balasan Oleh
                                                    </div>
                                                    <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.reply_role === 'admin' ? 'Administrator' : 'Pengguna'"></div>
                                                </div>
                                            </template>

                                            <!-- Show resolution note for 'closed' event -->
                                            <template x-if="selectedActivity.event === 'closed' && selectedActivity.properties.resolution_note">
                                                <div class="bg-gray-50 border border-gray-300 rounded-sm p-3">
                                                    <div class="text-[10px] font-semibold text-gray-900 mb-2" style="font-family: Poppins, sans-serif !important;">
                                                        📝 Nota Penyelesaian
                                                    </div>
                                                    <div class="text-[11px] text-gray-700 leading-relaxed" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.properties.resolution_note"></div>
                                                </div>
                                            </template>

                                            <!-- Show escalation info for 'escalated' event -->
                                            <template x-if="selectedActivity.event === 'escalated'">
                                                <div class="bg-red-50 border border-red-200 rounded-sm p-3">
                                                    <div class="text-[10px] font-semibold text-red-900 mb-2" style="font-family: Poppins, sans-serif !important;">
                                                        ⚠️ Escalation
                                                    </div>
                                                    <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;">
                                                        <div class="flex items-center gap-2 mb-1">
                                                            <span class="text-gray-600">Keutamaan:</span>
                                                            <span class="text-gray-700" x-text="selectedActivity.properties.old_priority"></span>
                                                            <span class="text-red-600">→</span>
                                                            <span class="text-red-700 font-bold" x-text="selectedActivity.properties.new_priority"></span>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-gray-600">Status:</span>
                                                            <span class="text-gray-700" x-text="selectedActivity.properties.old_status"></span>
                                                            <span class="text-red-600">→</span>
                                                            <span class="text-red-700 font-bold" x-text="selectedActivity.properties.new_status"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Show status change for update/approve/reject events -->
                                    <template x-if="(selectedActivity.event === 'updated' || selectedActivity.event === 'approved' || selectedActivity.event === 'rejected') && selectedActivity.properties.old_status && selectedActivity.properties.new_status">
                                        <div class="bg-blue-50 border border-blue-200 rounded-sm p-3">
                                            <div class="text-[10px] font-semibold text-blue-900 mb-2" style="font-family: Poppins, sans-serif !important;">
                                                📝 Perubahan Status
                                            </div>
                                            <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;">
                                                <span class="text-gray-600" x-text="selectedActivity.properties.old_status"></span>
                                                <span class="mx-2 text-blue-600">→</span>
                                                <span class="text-blue-700 font-semibold" x-text="selectedActivity.properties.new_status"></span>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Show approval code -->
                                    <template x-if="selectedActivity.event === 'approved' && selectedActivity.properties.approval_code">
                                        <div class="bg-green-50 border border-green-200 rounded-sm p-3">
                                            <div class="text-[10px] font-semibold text-green-900 mb-2" style="font-family: Poppins, sans-serif !important;">
                                                ✅ Kod Kelulusan
                                            </div>
                                            <div class="text-[14px] font-bold text-green-700 font-mono tracking-widest" style="font-family: 'Courier New', monospace !important;" x-text="selectedActivity.properties.approval_code"></div>
                                        </div>
                                    </template>

                                    <!-- Show rejection code -->
                                    <template x-if="selectedActivity.event === 'rejected' && selectedActivity.properties.reject_code">
                                        <div class="bg-orange-50 border border-orange-200 rounded-sm p-3">
                                            <div class="text-[10px] font-semibold text-orange-900 mb-2" style="font-family: Poppins, sans-serif !important;">
                                                ❌ Kod Penolakan
                                            </div>
                                            <div class="text-[14px] font-bold text-orange-700 font-mono tracking-widest" style="font-family: 'Courier New', monospace !important;" x-text="selectedActivity.properties.reject_code"></div>
                                        </div>
                                    </template>

                                    <!-- Show delete code -->
                                    <template x-if="selectedActivity.event === 'deleted' && selectedActivity.properties.delete_code">
                                        <div class="bg-gray-100 border border-gray-300 rounded-sm p-3">
                                            <div class="text-[10px] font-semibold text-gray-900 mb-2" style="font-family: Poppins, sans-serif !important;">
                                                🗑️ Kod Pemadaman
                                            </div>
                                            <div class="text-[14px] font-bold text-gray-700 font-mono tracking-widest" style="font-family: 'Courier New', monospace !important;" x-text="selectedActivity.properties.delete_code"></div>
                                        </div>
                                    </template>
                                </div>
                                </div>
                            </template>

                            <!-- Technical Info Section -->
                            <div>
                                <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-200">
                                    <span class="material-symbols-outlined text-gray-600 text-[16px]">computer</span>
                                    <h4 class="text-[11px] font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important;">
                                        Maklumat Teknikal
                                    </h4>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">IP Address</div>
                                        <div class="text-[11px] font-medium text-gray-900 font-mono inline-flex items-center gap-2 bg-gray-50 px-3 py-1 rounded-sm" style="font-family: 'Courier New', monospace !important;">
                                            <span class="material-symbols-outlined text-[14px] text-gray-500">wifi</span>
                                            <span x-text="selectedActivity.ip"></span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">User Agent</div>
                                        <div class="text-[10px] text-gray-700 bg-gray-50 p-3 rounded-sm leading-relaxed" style="font-family: Poppins, sans-serif !important; word-break: break-all;" x-text="selectedActivity.user_agent"></div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        </div>
                    </template>
                </div>

                <!-- Footer -->
                <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex justify-end">
                    <button @click="showModal = false" 
                            type="button"
                            class="h-8 px-4 text-[11px] font-medium rounded-sm bg-indigo-600 text-white hover:bg-indigo-700 transition-colors inline-flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">check</span>
                        Tutup
                    </button>
                </div>

        </div>
    </div>
    </div>
</x-dashboard-layout>
