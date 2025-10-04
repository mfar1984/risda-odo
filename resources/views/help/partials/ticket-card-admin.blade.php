{{-- TICKET CARD (Admin View) - Enhanced with escalation indicator --}}

<div class="bg-white p-4 rounded-sm border border-gray-200 @if($ticket->is_escalated) border-l-4 border-l-red-500 @endif hover:border-blue-300 hover:shadow-sm transition-all cursor-pointer">
    
    {{-- Header --}}
    <div class="flex justify-between items-start mb-2">
        <div class="flex items-center gap-2">
            @if($ticket->is_escalated)
                <span class="material-symbols-outlined text-red-600 text-[14px]">trending_up</span>
            @else
                <span class="material-symbols-outlined text-gray-600 text-[14px]">confirmation_number</span>
            @endif
            <span class="text-[11px] font-medium text-gray-900">{{ $ticket->ticket_number }}</span>
        </div>
        <div class="flex items-center gap-2">
            @if($ticket->priority === 'tinggi')
                <span class="inline-flex items-center h-5 px-2 text-[10px] font-medium rounded-sm bg-red-100 text-red-800 border border-red-200">
                    <span class="material-symbols-outlined text-[12px] mr-1">circle</span>
                    Tinggi
                </span>
            @elseif($ticket->priority === 'sederhana')
                <span class="inline-flex items-center h-5 px-2 text-[10px] font-medium rounded-sm bg-yellow-100 text-yellow-800 border border-yellow-200">
                    <span class="material-symbols-outlined text-[12px] mr-1">circle</span>
                    Sederhana
                </span>
            @else
                <span class="inline-flex items-center h-5 px-2 text-[10px] font-medium rounded-sm bg-green-100 text-green-800 border border-green-200">
                    <span class="material-symbols-outlined text-[12px] mr-1">circle</span>
                    Rendah
                </span>
            @endif
            
            @if($ticket->status === 'escalated')
                <span class="inline-flex items-center h-5 px-2 text-[10px] font-medium rounded-sm bg-red-100 text-red-800 border border-red-200">
                    Escalated
                </span>
            @elseif($ticket->status === 'dalam_proses')
                <span class="inline-flex items-center h-5 px-2 text-[10px] font-medium rounded-sm bg-yellow-100 text-yellow-800 border border-yellow-200">
                    Sedang Diproses
                </span>
            @else
                <span class="inline-flex items-center h-5 px-2 text-[10px] font-medium rounded-sm bg-blue-100 text-blue-800 border border-blue-200">
                    {{ ucfirst($ticket->status) }}
                </span>
            @endif
            
            @if($ticket->created_by_role === 'Staff')
                <span class="inline-flex items-center h-5 px-2 text-[10px] font-medium rounded-sm bg-purple-100 text-purple-800 border border-purple-200">
                    Staff Request
                </span>
            @endif
        </div>
    </div>

    {{-- Subject --}}
    <div class="text-[12px] font-semibold text-gray-900 mb-2">
        {{ $ticket->subject }}
    </div>

    {{-- Enhanced Meta Info for Admin --}}
    <div class="space-y-1 mb-3">
        <div class="flex flex-wrap items-center gap-4 text-[10px] text-gray-600">
            <div class="flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[14px]">person</span>
                <span>{{ $ticket->created_by }} ({{ $ticket->created_by_role }}, {{ $ticket->organization }})</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[14px]">schedule</span>
                <span>{{ $ticket->created_ago }}</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[14px]">chat_bubble</span>
                <span>{{ $ticket->message_count }} mesej</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[14px]">label</span>
                <span>{{ $ticket->category }}</span>
            </div>
        </div>
        
        @if($ticket->is_escalated)
            <div class="flex items-center gap-1.5 text-[10px] text-red-700">
                <span class="material-symbols-outlined text-[14px]">trending_up</span>
                <span>Escalated by: faizan@jara.my ({{ $ticket->created_ago }})</span>
            </div>
        @endif
    </div>

    {{-- Admin Actions --}}
    <div class="flex items-center gap-2">
        <button @click="viewTicketModal = true" class="h-7 px-3 text-[10px] font-medium rounded-sm bg-blue-600 text-white hover:bg-blue-700 transition-colors inline-flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[14px]">visibility</span>
            Lihat & {{ $ticket->is_escalated ? 'Resolve' : 'Respond' }}
        </button>
        <button @click="assignTicketModal = true" class="h-7 px-3 text-[10px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors inline-flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[14px]">person_add</span>
            Assign to...
        </button>
        <button class="h-7 px-3 text-[10px] rounded-sm border border-yellow-300 text-yellow-700 hover:bg-yellow-50 transition-colors inline-flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[14px]">flag</span>
            Mark Priority
        </button>
    </div>

</div>

