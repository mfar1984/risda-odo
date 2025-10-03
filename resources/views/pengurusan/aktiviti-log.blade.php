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

        <!-- Pagination -->
        @if($activities->hasPages())
            <div class="mt-6">
                {{ $activities->links() }}
            </div>
        @endif

        <!-- Record Counter -->
        <div class="mt-4 text-sm text-gray-600" style="font-family: Poppins, sans-serif !important; font-size: 12px !important;">
            Menunjukkan {{ $activities->firstItem() ?? 0 }} hingga {{ $activities->lastItem() ?? 0 }} daripada {{ $activities->total() }} rekod
        </div>
    </x-ui.page-header>

    <!-- Modal Popup -->
    <div x-show="showModal" 
         x-cloak
         class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center" 
         style="font-family: Poppins, sans-serif; z-index: 9999;">
        
        <!-- Modal Container -->
        <div @click.away="showModal = false" class="relative mx-auto p-5 border w-[700px] shadow-lg rounded-md bg-white">
            <!-- Header -->
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900" style="font-size: 13px;">
                    Butiran Aktiviti Log
                </h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="space-y-3">
                <template x-if="selectedActivity">
                    <div>
                        <!-- Compact 2-column layout -->
                        <div class="grid grid-cols-2 gap-x-6 gap-y-2 text-xs">
                            <!-- ID -->
                            <div>
                                <span class="text-gray-500" style="font-size: 10px;">ID:</span>
                                <span class="font-medium text-gray-900 ml-2" style="font-size: 11px;" x-text="selectedActivity.id"></span>
                            </div>
                            
                            <!-- Log Name -->
                            <div>
                                <span class="text-gray-500" style="font-size: 10px;">Kategori:</span>
                                <span class="font-medium text-gray-900 ml-2" style="font-size: 11px;" x-text="selectedActivity.log_name"></span>
                            </div>

                            <!-- Description -->
                            <div class="col-span-2">
                                <span class="text-gray-500" style="font-size: 10px;">Penerangan:</span>
                                <span class="font-medium text-gray-900 ml-2" style="font-size: 11px;" x-text="selectedActivity.description"></span>
                            </div>

                            <!-- Event (only show if not N/A) -->
                            <template x-if="selectedActivity.event && selectedActivity.event !== 'N/A'">
                                <div>
                                    <span class="text-gray-500" style="font-size: 10px;">Event:</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ml-2"
                                          :class="{
                                              'bg-green-100 text-green-800': selectedActivity.event === 'created',
                                              'bg-blue-100 text-blue-800': selectedActivity.event === 'updated',
                                              'bg-red-100 text-red-800': selectedActivity.event === 'deleted',
                                              'bg-gray-100 text-gray-800': true
                                          }"
                                          style="font-size: 10px;"
                                          x-text="selectedActivity.event === 'created' ? 'Cipta' : 
                                                  selectedActivity.event === 'updated' ? 'Kemaskini' : 
                                                  selectedActivity.event === 'deleted' ? 'Padam' : 
                                                  selectedActivity.event">
                                    </span>
                                </div>
                            </template>

                            <!-- Created At -->
                            <div>
                                <span class="text-gray-500" style="font-size: 10px;">Masa:</span>
                                <span class="font-medium text-gray-900 ml-2" style="font-size: 11px;" x-text="selectedActivity.created_at"></span>
                            </div>

                            <!-- Divider -->
                            <div class="col-span-2 border-t border-gray-200 my-2"></div>

                            <!-- User Name -->
                            <div>
                                <span class="text-gray-500" style="font-size: 10px;">Pengguna:</span>
                                <span class="font-medium text-gray-900 ml-2" style="font-size: 11px;" x-text="selectedActivity.causer_name"></span>
                            </div>
                            
                            <!-- Email -->
                            <div>
                                <span class="text-gray-500" style="font-size: 10px;">Email:</span>
                                <span class="font-medium text-gray-900 ml-2" style="font-size: 11px;" x-text="selectedActivity.causer_email"></span>
                            </div>

                            <!-- Divider -->
                            <div class="col-span-2 border-t border-gray-200 my-2"></div>

                            <!-- Model Type (only show if not N/A) -->
                            <template x-if="selectedActivity.subject_type && selectedActivity.subject_type !== 'N/A'">
                                <div>
                                    <span class="text-gray-500" style="font-size: 10px;">Jenis Model:</span>
                                    <span class="font-medium text-gray-900 ml-2" style="font-size: 11px;" x-text="selectedActivity.subject_type"></span>
                                </div>
                            </template>
                            
                            <!-- Model ID (only show if not N/A) -->
                            <template x-if="selectedActivity.subject_id && selectedActivity.subject_id !== 'N/A'">
                                <div>
                                    <span class="text-gray-500" style="font-size: 10px;">ID Model:</span>
                                    <span class="font-medium text-gray-900 ml-2" style="font-size: 11px;" x-text="selectedActivity.subject_id"></span>
                                </div>
                            </template>

                            <!-- Divider -->
                            <div class="col-span-2 border-t border-gray-200 my-2"></div>

                            <!-- IP -->
                            <div>
                                <span class="text-gray-500" style="font-size: 10px;">IP Address:</span>
                                <span class="font-medium text-gray-900 font-mono ml-2" style="font-size: 10px;" x-text="selectedActivity.ip"></span>
                            </div>
                            
                            <!-- User Agent -->
                            <div>
                                <span class="text-gray-500" style="font-size: 10px;">User Agent:</span>
                                <span class="font-medium text-gray-900 ml-2 block mt-1 text-wrap" style="font-size: 9px; line-height: 1.3;" x-text="selectedActivity.user_agent"></span>
                            </div>
                        </div>
                        </div>
                    </template>
                </div>

            </div>
        </div>
    </div>
    </div>
</x-dashboard-layout>
