<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'RISDA Odometer') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

        <!-- DateRangePicker CDN -->
        <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50" 
          x-data="{ sidebarCollapsed: false }" 
          @sidebar-toggled.window="sidebarCollapsed = $event.detail.collapsed">
        <div class="min-h-screen flex flex-col">
            <!-- Sidebar -->
            <x-layout.sidebar />
            
            <!-- Mobile sidebar overlay - Event based -->
            <div x-data="{ showOverlay: false }"
                 x-show="showOverlay" 
                 @toggle-mobile-menu.window="showOverlay = !showOverlay"
                 @close-mobile-menu.window="showOverlay = false"
                 @click="$dispatch('close-mobile-menu')" 
                 class="sidebar-overlay md:hidden" 
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 style="pointer-events: auto;">
            </div>

            <!-- Main content -->
            <div class="main-content flex flex-col flex-1" 
                 x-bind:class="sidebarCollapsed ? 'main-content-collapsed' : 'main-content-expanded'"
                 style="transition: margin-left 0.3s ease !important;">
                <!-- Header -->
                <x-layout.header
                    :title="$title ?? ''"
                >
                    <x-slot name="breadcrumbs">
                        {{ $breadcrumbs ?? '' }}
                    </x-slot>
                </x-layout.header>

                <!-- Page Content -->
                <main class="p-6 flex-1">
                    {{ $slot }}
                </main>

                <!-- Footer -->
                <x-layout.footer />
            </div>
        </div>

        <!-- Toast Notifications -->
        <x-ui.toast-notification />

        <!-- Custom Scripts -->
        @stack('scripts')

        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </body>
</html>
