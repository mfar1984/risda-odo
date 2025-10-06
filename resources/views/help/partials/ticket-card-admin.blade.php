{{-- TICKET CARD (Admin View) - Enhanced with escalation indicator --}}

<div class="bg-white p-4 rounded-sm border border-gray-200 @if($ticket->status === 'escalated') border-l-4 border-l-red-500 @endif hover:border-blue-300 hover:shadow-sm transition-all">
    
    {{-- Header --}}
    <div class="flex justify-between items-start mb-2">
        <div class="flex items-center gap-2">
            @if($ticket->status === 'escalated')
                <span class="material-symbols-outlined text-red-600 text-[14px]">trending_up</span>
            @else
                <span class="material-symbols-outlined text-gray-600 text-[14px]">confirmation_number</span>
            @endif
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
            
            @if($ticket->source === 'android')
                <span class="inline-flex items-center h-5 px-2 text-[10px] font-medium rounded-sm bg-purple-100 text-purple-800 border border-purple-200" style="font-family: Poppins, sans-serif !important;">
                    <span class="material-symbols-outlined text-[12px] mr-1">phone_android</span>
                    Android
                </span>
            @endif
        </div>
    </div>

    {{-- Subject --}}
    <div class="text-[12px] font-semibold text-gray-900 mb-2" style="font-family: Poppins, sans-serif !important;">
        {{ $ticket->subject }}
    </div>

    {{-- Enhanced Meta Info for Admin --}}
    <div class="space-y-1 mb-3">
        <div class="flex flex-wrap items-center gap-4 text-[10px] text-gray-600" style="font-family: Poppins, sans-serif !important;">
            <div class="flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[14px]">person</span>
                <span>{{ $ticket->creator->name }} ({{ $ticket->organization_name }})</span>
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
        
        @if($ticket->assigned_to)
            <div class="flex items-center gap-1.5 text-[10px] text-blue-700" style="font-family: Poppins, sans-serif !important;">
                <span class="material-symbols-outlined text-[14px]">person_check</span>
                <span>Assigned to: {{ $ticket->assignedAdmin->name }}</span>
            </div>
        @endif
    </div>

    {{-- Admin Actions --}}
    <div class="support-card-actions flex items-center gap-2">
        <button onclick="viewTicket({{ $ticket->id }})" class="h-7 px-3 text-[10px] font-medium rounded-sm bg-blue-600 text-white hover:bg-blue-700 transition-colors inline-flex items-center gap-1.5" style="font-family: Poppins, sans-serif !important;">
            <span class="material-symbols-outlined text-[14px]">visibility</span>
            Lihat & Respond
        </button>
        
        <button onclick="deleteSupportTicket({{ $ticket->id }}, '{{ $ticket->ticket_number }}')" class="h-7 px-3 text-[10px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors inline-flex items-center gap-1.5" style="font-family: Poppins, sans-serif !important;">
            <span class="material-symbols-outlined text-[14px]">delete</span>
            Padam
        </button>
    </div>

</div>

