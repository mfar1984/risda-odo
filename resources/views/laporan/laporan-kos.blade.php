<x-dashboard-layout title="Laporan Kos">
    <x-slot name="breadcrumbs">
        <!-- Home icon -->
        <a href="{{ route('dashboard') }}" class="breadcrumb-home">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
        </a>
        <span class="breadcrumb-separator">></span>
        <a href="#" class="breadcrumb-link">Laporan</a>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-current">Laporan Kos</span>
    </x-slot>

    <!-- Laporan Kos Container -->
    <x-ui.page-header 
        title="Laporan Kos" 
        description="Laporan kos operasi dan penyelenggaraan kenderaan"
    >
        <div class="dashboard-maintenance-content">
            <!-- Maintenance Icon -->
            <div class="maintenance-icon">
                <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <!-- Maintenance Text -->
            <div class="maintenance-text">
                <h3 class="maintenance-title">Dalam Pembangunan</h3>
                <p class="maintenance-subtitle">Laporan kos sedang dalam proses pembangunan</p>
            </div>
        </div>
    </x-ui.page-header>
</x-dashboard-layout>
