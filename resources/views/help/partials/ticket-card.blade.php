{{-- TICKET CARD (Staff View) --}}

<div class="bg-white p-4 rounded-sm border border-gray-200 hover:border-blue-300 hover:shadow-sm transition-all">
    
    {{-- Header --}}
    <div class="flex justify-between items-start mb-2">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-gray-600 text-[14px]">confirmation_number</span>
            <span class="text-[11px] font-medium text-gray-900" style="font-family: Poppins, sans-serif !important;">{{ $ticket->ticket_number }}</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center h-5 px-2 text-[10px] font-medium rounded-sm {{ $ticket->priority_color }}" style="font-family: Poppins, sans-serif !important;">
                <span class="material-symbols-outlined text-[12px] mr-1">circle</span>
                {{ $ticket->priority_label }}
            </span>
            
            <span class="inline-flex items-center h-5 px-2 text-[10px] font-medium rounded-sm {{ $ticket->status_color }}" style="font-family: Poppins, sans-serif !important;">
                {{ $ticket->status_label }}
            </span>
        </div>
    </div>

    {{-- Subject --}}
    <div class="text-[12px] font-semibold text-gray-900 mb-2" style="font-family: Poppins, sans-serif !important;">
        {{ $ticket->subject }}
    </div>

    {{-- Meta Info --}}
    <div class="flex flex-wrap items-center gap-4 text-[10px] text-gray-600 mb-3" style="font-family: Poppins, sans-serif !important;">
        <div class="flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[14px]">person</span>
            <span>{{ $ticket->creator->name }}</span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[14px]">schedule</span>
            <span>{{ $ticket->created_at->diffForHumans() }}</span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[14px]">chat_bubble</span>
            <span>{{ $ticket->messages->count() }} mesej</span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[14px]">label</span>
            <span>{{ $ticket->category }}</span>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-2">
        <button onclick="viewTicket({{ $ticket->id }})" class="h-7 px-3 text-[10px] font-medium rounded-sm bg-blue-600 text-white hover:bg-blue-700 transition-colors inline-flex items-center gap-1.5" style="font-family: Poppins, sans-serif !important;">
            <span class="material-symbols-outlined text-[14px]">visibility</span>
            Lihat
        </button>
        <button onclick="window.currentTicketId = {{ $ticket->id }}; document.querySelector('[x-data]').__x.$data.replyTicketModal = true" class="h-7 px-3 text-[10px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors inline-flex items-center gap-1.5" style="font-family: Poppins, sans-serif !important;">
            <span class="material-symbols-outlined text-[14px]">reply</span>
            Balas
        </button>
    </div>

</div>

