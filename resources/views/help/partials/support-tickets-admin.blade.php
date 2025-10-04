{{-- ADMINISTRATOR VIEW --}}

<div x-data="{ 
    activeTab: 'escalated',
    viewTicketModal: false,
    replyTicketModal: false,
    createTicketModal: false,
    assignTicketModal: false,
    escalateTicketModal: false
}" class="mb-8">
    
    {{-- Tab Navigation (senarai-risda style with badges) --}}
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button 
                @click="activeTab = 'escalated'"
                :class="activeTab === 'escalated' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                :style="activeTab === 'escalated' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                <span class="material-symbols-outlined" style="font-size: 16px;">trending_up</span>
                Escalated
                <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1.5 text-[9px] font-semibold rounded-full bg-red-600 text-white">8</span>
            </button>
            <button 
                @click="activeTab = 'staff'"
                :class="activeTab === 'staff' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                :style="activeTab === 'staff' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                <span class="material-symbols-outlined" style="font-size: 16px;">mail</span>
                Tiket Staff
                <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1.5 text-[9px] font-semibold rounded-full bg-blue-600 text-white">3</span>
            </button>
            <button 
                @click="activeTab = 'driver'"
                :class="activeTab === 'driver' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                :style="activeTab === 'driver' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                <span class="material-symbols-outlined" style="font-size: 16px;">person</span>
                Dari Pemandu
            </button>
            <button 
                @click="activeTab = 'resolved'"
                :class="activeTab === 'resolved' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                :style="activeTab === 'resolved' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                <span class="material-symbols-outlined" style="font-size: 16px;">check_circle</span>
                Selesai
            </button>
        </nav>
    </div>

    {{-- Tab Content --}}
    <div class="mt-8">

        {{-- Dashboard Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-red-50 to-white p-4 rounded-sm border border-red-200">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-red-600 text-[18px]">trending_up</span>
                <span class="text-[10px] text-gray-600">Escalated</span>
            </div>
            <div class="text-[24px] font-bold text-gray-900">{{ $adminStats['escalated'] }}</div>
            <div class="text-[9px] text-gray-500 mt-1">tiket perlu perhatian</div>
        </div>
        <div class="bg-gradient-to-br from-blue-50 to-white p-4 rounded-sm border border-blue-200">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-blue-600 text-[18px]">mail</span>
                <span class="text-[10px] text-gray-600">Staff</span>
            </div>
            <div class="text-[24px] font-bold text-gray-900">{{ $adminStats['staff'] }}</div>
            <div class="text-[9px] text-gray-500 mt-1">tiket dari staff</div>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-white p-4 rounded-sm border border-purple-200">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-purple-600 text-[18px]">group</span>
                <span class="text-[10px] text-gray-600">Driver</span>
            </div>
            <div class="text-[24px] font-bold text-gray-900">{{ $adminStats['driver'] }}</div>
            <div class="text-[9px] text-gray-500 mt-1">tiket dari pemandu</div>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-white p-4 rounded-sm border border-green-200">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-green-600 text-[18px]">check_circle</span>
                <span class="text-[10px] text-gray-600">Hari Ini</span>
            </div>
            <div class="text-[24px] font-bold text-gray-900">{{ $adminStats['today_resolved'] }}</div>
            <div class="text-[9px] text-gray-500 mt-1">tiket diselesaikan</div>
        </div>
        </div>

        {{-- Alert Box --}}
        <div class="bg-red-50 border-l-4 border-red-500 p-3 mb-6 rounded-sm">
        <div class="flex items-start gap-3">
            <span class="material-symbols-outlined text-red-600 text-[18px]">warning</span>
            <div>
                <div class="text-[11px] font-semibold text-red-900 mb-1">⚠️ Perlu Perhatian Segera</div>
                <ul class="text-[10px] text-red-800 space-y-0.5">
                    <li>• 2 tiket escalated lebih 24 jam</li>
                    <li>• 1 critical staff request</li>
                </ul>
            </div>
        </div>
        </div>

        {{-- Advanced Filters --}}
        <div class="grid grid-cols-5 gap-3 mb-6">
        <div class="col-span-2 relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[16px]">search</span>
            <input type="text" placeholder="Cari tiket..." class="w-full h-8 pl-9 pr-3 text-[11px] rounded-sm border border-gray-200 focus:ring-0 focus:border-blue-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
        </div>
        <select class="h-8 px-3 text-[11px] rounded-sm border border-gray-200 focus:ring-0 focus:border-blue-500 bg-white flex items-center" style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 32px !important; padding-top: 0 !important; padding-bottom: 0 !important; display: flex; align-items: center;">
            <option value="">Organisasi</option>
            <option value="bahagian_utara">Bahagian Utara</option>
            <option value="bahagian_selatan">Bahagian Selatan</option>
            <option value="stesen_a">Stesen A</option>
        </select>
        <select class="h-8 px-3 text-[11px] rounded-sm border border-gray-200 focus:ring-0 focus:border-blue-500 bg-white flex items-center" style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 32px !important; padding-top: 0 !important; padding-bottom: 0 !important; display: flex; align-items: center;">
            <option value="">Status</option>
            <option value="baru">Baru</option>
            <option value="dalam_proses">Sedang Diproses</option>
            <option value="escalated">Escalated</option>
            <option value="selesai">Selesai</option>
        </select>
        <select class="h-8 px-3 text-[11px] rounded-sm border border-gray-200 focus:ring-0 focus:border-blue-500 bg-white flex items-center" style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 32px !important; padding-top: 0 !important; padding-bottom: 0 !important; display: flex; align-items: center;">
            <option value="">Keutamaan</option>
            <option value="rendah">Rendah</option>
            <option value="sederhana">Sederhana</option>
            <option value="tinggi">Tinggi</option>
        </select>
        </div>

        {{-- Ticket List --}}
        <div class="space-y-3">
        
        @forelse($allTickets as $ticket)
            @include('help.partials.ticket-card-admin', ['ticket' => $ticket])
        @empty
            <div class="text-center py-12">
                <span class="material-symbols-outlined text-gray-400 text-[48px]">inbox</span>
                <p class="text-[12px] text-gray-500 mt-2" style="font-family: Poppins, sans-serif !important;">Tiada tiket</p>
            </div>
        @endforelse

        </div>

        {{-- Pagination (exactly like log-pemandu) --}}
        <x-ui.pagination :paginator="$allTickets" record-label="tiket" />
    </div>

    {{-- Modals --}}
    @include('help.partials.ticket-view-modal')
    @include('help.partials.ticket-reply-modal')
    @include('help.partials.ticket-create-modal')
    @include('help.partials.ticket-assign-modal')
    @include('help.partials.ticket-escalate-modal')

</div>

