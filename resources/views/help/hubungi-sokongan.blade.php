<x-dashboard-layout title="Hubungi Sokongan">
    <x-ui.page-header 
        title="Hubungi Sokongan" 
        description="Sistem tiket sokongan untuk pengurusan isu dan pertanyaan"
    >
        @if($user->jenis_organisasi === 'semua')
            {{-- ADMINISTRATOR VIEW --}}
            @include('help.partials.support-tickets-admin')
        @else
            {{-- STAFF VIEW --}}
            @include('help.partials.support-tickets-staff')
        @endif
    </x-ui.page-header>

    {{-- Include Support Actions JS --}}
    @vite('resources/js/support-actions.js')
    @vite('resources/js/delete-actions.js')
</x-dashboard-layout>
