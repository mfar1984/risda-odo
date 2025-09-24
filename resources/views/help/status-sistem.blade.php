<x-dashboard-layout title="Status Sistem">


    <!-- Status Sistem Container -->
    <x-ui.page-header 
        title="Status Sistem" 
        description="Semak status operasi dan kesihatan sistem"
    >
        <div class="dashboard-maintenance-content">
            <!-- Maintenance Icon -->
            <div class="maintenance-icon">
                <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <!-- Maintenance Text -->
            <div class="maintenance-text">
                <h3 class="maintenance-title">Dalam Pembangunan</h3>
                <p class="maintenance-subtitle">Status sistem sedang dalam proses pembangunan</p>
            </div>
        </div>
    </x-ui.page-header>
</x-dashboard-layout>
