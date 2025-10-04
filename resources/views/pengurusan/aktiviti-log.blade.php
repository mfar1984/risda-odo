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

        <!-- Table -->
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

        <!-- Pagination (exactly like log-pemandu) -->
        <div class="mt-6">
            <x-ui.pagination :paginator="$activities" record-label="aktiviti" />
        </div>
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
                                    <div>
                                        <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Kategori Log</div>
                                        <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.log_name"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Model Info Section (conditional) -->
                            <template x-if="(selectedActivity.subject_type && selectedActivity.subject_type !== 'N/A') || (selectedActivity.subject_id && selectedActivity.subject_id !== 'N/A')">
                            <div>
                                <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-200">
                                    <span class="material-symbols-outlined text-gray-600 text-[16px]">category</span>
                                    <h4 class="text-[11px] font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important;">
                                        Maklumat Model
                                    </h4>
                                </div>
                                <div class="grid grid-cols-2 gap-4">

                                    <template x-if="selectedActivity.subject_type && selectedActivity.subject_type !== 'N/A'">
                                        <div>
                                            <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">Jenis Model</div>
                                            <div class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;" x-text="selectedActivity.subject_type"></div>
                                        </div>
                                    </template>
                                    <template x-if="selectedActivity.subject_id && selectedActivity.subject_id !== 'N/A'">
                                        <div>
                                            <div class="text-[10px] text-gray-500 mb-1" style="font-family: Poppins, sans-serif !important;">ID Model</div>
                                            <div class="text-[11px] font-medium text-gray-900 font-mono" style="font-family: 'Courier New', monospace !important;" x-text="selectedActivity.subject_id"></div>
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
