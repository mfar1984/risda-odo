{{-- STAFF VIEW (Bahagian/Stesen) --}}

<div id="supportTicketsRoot" x-data="{ 
    activeTab: 'all',
    viewTicketModal: false,
    createTicketModal: false,
    assignTicketModal: false,
    escalateTicketModal: false
}"
x-init="$watch('viewTicketModal', value => { if (!value && typeof stopMessagePolling === 'function') stopMessagePolling(); })"
@open-view-ticket-modal.window="viewTicketModal = true"
@open-assign-ticket-modal.window="assignTicketModal = true"
@close-assign-ticket-modal.window="assignTicketModal = false"
@open-escalate-ticket-modal.window="escalateTicketModal = true"
class="mb-8">
    
    {{-- Tab Navigation --}}
    <div class="border-b border-gray-200 support-tabs-container">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button 
                @click="activeTab = 'all'"
                :class="activeTab === 'all' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                :style="activeTab === 'all' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                <span class="material-symbols-outlined" style="font-size: 16px;">list</span>
                Semua
                <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1.5 text-[9px] font-semibold rounded-full bg-blue-600 text-white" style="font-family: Poppins, sans-serif !important;">{{ $tickets->count() }}</span>
            </button>
            <button 
                @click="activeTab = 'driver'"
                :class="activeTab === 'driver' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                :style="activeTab === 'driver' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                <span class="material-symbols-outlined" style="font-size: 16px;">phone_android</span>
                Dari Pemandu (Android)
                @php $driverTickets = $tickets->where('source', 'android')->count(); @endphp
                @if($driverTickets > 0)
                    <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1.5 text-[9px] font-semibold rounded-full bg-purple-600 text-white" style="font-family: Poppins, sans-serif !important;">{{ $driverTickets }}</span>
                @endif
            </button>
            <button 
                @click="activeTab = 'mine'"
                :class="activeTab === 'mine' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                :style="activeTab === 'mine' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                <span class="material-symbols-outlined" style="font-size: 16px;">person</span>
                Tiket Saya
                @php $myTickets = $tickets->where('created_by', auth()->id())->where('source','!=','android')->count(); @endphp
                @if($myTickets > 0)
                    <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1.5 text-[9px] font-semibold rounded-full bg-green-600 text-white" style="font-family: Poppins, sans-serif !important;">{{ $myTickets }}</span>
                @endif
            </button>
        </nav>
    </div>

    {{-- Tab Content --}}
    <div class="mt-8">

        {{-- Header with Button --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Tiket Sokongan</h3>
                <p class="text-sm text-gray-600 mt-1" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">Tiket sokongan untuk organisasi anda</p>
            </div>
            <button @click="createTicketModal = true" type="button" class="inline-flex items-center h-8 px-4 text-[11px] font-medium rounded-sm bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                <span class="material-symbols-outlined text-[16px] mr-1.5">add</span>
                Buat Tiket Baru
            </button>
        </div>

        {{-- Stats Summary --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-gray-50 p-4 rounded-sm border border-gray-200">
            <div class="flex items-center gap-2 mb-1">
                <span class="material-symbols-outlined text-green-600 text-[16px]">circle</span>
                <span class="text-[10px] text-gray-600" style="font-family: Poppins, sans-serif !important;">Baru</span>
            </div>
            <div class="text-[20px] font-bold text-gray-900" style="font-family: Poppins, sans-serif !important;">{{ $tickets->where('status', 'baru')->count() }}</div>
        </div>
        <div class="bg-gray-50 p-4 rounded-sm border border-gray-200">
            <div class="flex items-center gap-2 mb-1">
                <span class="material-symbols-outlined text-yellow-600 text-[16px]">circle</span>
                <span class="text-[10px] text-gray-600" style="font-family: Poppins, sans-serif !important;">Sedang Diproses</span>
            </div>
            <div class="text-[20px] font-bold text-gray-900" style="font-family: Poppins, sans-serif !important;">{{ $tickets->where('status', 'dalam_proses')->count() }}</div>
        </div>
        <div class="bg-gray-50 p-4 rounded-sm border border-gray-200">
            <div class="flex items-center gap-2 mb-1">
                <span class="material-symbols-outlined text-red-600 text-[16px]">circle</span>
                <span class="text-[10px] text-gray-600" style="font-family: Poppins, sans-serif !important;">Urgent</span>
            </div>
            <div class="text-[20px] font-bold text-gray-900" style="font-family: Poppins, sans-serif !important;">{{ $tickets->whereIn('priority', ['tinggi', 'kritikal'])->count() }}</div>
        </div>
        <div class="bg-gray-50 p-4 rounded-sm border border-gray-200">
            <div class="flex items-center gap-2 mb-1">
                <span class="material-symbols-outlined text-green-600 text-[16px]">check_circle</span>
                <span class="text-[10px] text-gray-600" style="font-family: Poppins, sans-serif !important;">Selesai</span>
            </div>
            <div class="text-[20px] font-bold text-gray-900" style="font-family: Poppins, sans-serif !important;">{{ $tickets->where('status', 'ditutup')->count() }}</div>
        </div>
    </div>

        {{-- Filter & Search --}}
        <div class="support-filters-grid grid grid-cols-5 gap-3 mb-6">
        <div class="col-span-2 support-filter-search relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[16px]">search</span>
            <input type="text" placeholder="Cari tiket..." class="w-full h-8 pl-9 pr-3 text-[11px] rounded-sm border border-gray-200 focus:ring-0 focus:border-blue-500" style="font-family: Poppins, sans-serif !important; font-size: 11px !important;">
        </div>
        <select class="support-filter-status h-8 px-3 text-[11px] rounded-sm border border-gray-200 focus:ring-0 focus:border-blue-500 bg-white flex items-center" style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 32px !important; padding-top: 0 !important; padding-bottom: 0 !important; display: flex; align-items: center;">
            <option value="">Status</option>
            <option value="baru">Baru</option>
            <option value="dalam_proses">Sedang Diproses</option>
            <option value="escalated">Escalated</option>
            <option value="selesai">Selesai</option>
        </select>
        <select class="support-filter-priority h-8 px-3 text-[11px] rounded-sm border border-gray-200 focus:ring-0 focus:border-blue-500 bg-white flex items-center" style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 32px !important; padding-top: 0 !important; padding-bottom: 0 !important; display: flex; align-items: center;">
            <option value="">Keutamaan</option>
            <option value="rendah">Rendah</option>
            <option value="sederhana">Sederhana</option>
            <option value="tinggi">Tinggi</option>
        </select>
        <select class="support-filter-category h-8 px-3 text-[11px] rounded-sm border border-gray-200 focus:ring-0 focus:border-blue-500 bg-white flex items-center" style="font-family: Poppins, sans-serif !important; font-size: 11px !important; line-height: 32px !important; padding-top: 0 !important; padding-bottom: 0 !important; display: flex; align-items: center;">
            <option value="">Kategori</option>
            <option value="teknikal">Teknikal</option>
            <option value="akaun">Akaun</option>
            <option value="perjalanan">Perjalanan</option>
            <option value="tuntutan">Tuntutan</option>
            <option value="lain">Lain-lain</option>
        </select>
        </div>

        {{-- Ticket List --}}
        <div class="space-y-3">
        
        {{-- Tab: Semua --}}
        <div x-show="activeTab === 'all'">
            @forelse($tickets as $ticket)
                @include('help.partials.ticket-card', ['ticket' => $ticket])
            @empty
                <div class="text-center py-12">
                    <span class="material-symbols-outlined text-gray-400 text-[48px]">inbox</span>
                    <p class="text-[12px] text-gray-500 mt-2" style="font-family: Poppins, sans-serif !important;">Tiada tiket</p>
                </div>
            @endforelse
        </div>

        {{-- Tab: Dari Pemandu (Android) --}}
        <div x-show="activeTab === 'driver'">
            @php $driverTicketsList = $tickets->where('source', 'android'); @endphp
            @forelse($driverTicketsList as $ticket)
                @include('help.partials.ticket-card', ['ticket' => $ticket])
            @empty
                <div class="text-center py-12">
                    <span class="material-symbols-outlined text-gray-400 text-[48px]">phone_android</span>
                    <p class="text-[12px] text-gray-500 mt-2" style="font-family: Poppins, sans-serif !important;">Tiada tiket dari pemandu (Android)</p>
                </div>
            @endforelse
        </div>

        {{-- Tab: Tiket Saya --}}
        <div x-show="activeTab === 'mine'">
            @php $myTicketsList = $tickets->where('created_by', auth()->id())->where('source','!=','android'); @endphp
            @forelse($myTicketsList as $ticket)
                @include('help.partials.ticket-card', ['ticket' => $ticket])
            @empty
                <div class="text-center py-12">
                    <span class="material-symbols-outlined text-gray-400 text-[48px]">person</span>
                    <p class="text-[12px] text-gray-500 mt-2" style="font-family: Poppins, sans-serif !important;">Anda belum mencipta tiket</p>
                </div>
            @endforelse
        </div>

        </div>
    </div>

    {{-- Modals --}}
    @include('help.partials.ticket-view-modal')
    @include('help.partials.ticket-escalate-modal')
    @include('help.partials.ticket-create-modal')
    @include('help.partials.ticket-assign-modal')
    @include('components.modals.delete-confirmation-modal')

</div>


