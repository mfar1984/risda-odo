@props(['collapsed' => false])

<div class="sidebar sidebar-expanded" 
     x-data="{ 
         collapsed: false,
         mobileOpen: false
     }"
     :class="{ 
         'sidebar-collapsed': collapsed, 
         'sidebar-expanded': !collapsed,
         'sidebar-mobile-open': mobileOpen 
     }"
     @toggle-mobile-menu.window="mobileOpen = !mobileOpen"
     @close-mobile-menu.window="mobileOpen = false"
     @toggle-sidebar.window="collapsed = !collapsed">
    <!-- Sidebar Header -->
    <div class="sidebar-header" style="position: relative; z-index: 5000;">
        <div class="flex items-center justify-between" style="position: relative; z-index: 5000;">
            <div class="flex items-center">
                <x-application-logo class="h-8 w-8" />
                <span x-show="!collapsed" x-transition:enter="transition ease-out duration-100" x-transition:leave="transition ease-in duration-100" class="ml-3 text-lg font-semibold text-gray-800">RISDA ODO</span>
            </div>
            
            <!-- Desktop Toggle Button ONLY (Hidden on Mobile) -->
            <button @click="collapsed = !collapsed; $dispatch('sidebar-toggled', { collapsed: collapsed })" 
                    class="sidebar-toggle hidden md:flex" 
                    type="button"
                    style="position: relative; z-index: 5000 !important; pointer-events: auto !important;">
                <!-- Widgets icon when expanded (default state) -->
                <span x-show="!collapsed" 
                      x-transition:enter="transition ease-out duration-150"
                      x-transition:enter-start="opacity-0 scale-75"
                      x-transition:enter-end="opacity-100 scale-100"
                      x-transition:leave="transition ease-in duration-100"
                      x-transition:leave-start="opacity-100 scale-100"
                      x-transition:leave-end="opacity-0 scale-75"
                      class="material-icons-outlined" 
                      style="font-size: 20px; position: relative; z-index: 5001;">widgets</span>
                <!-- Arrow Circle Right when collapsed (click to expand) -->
                <span x-show="collapsed" 
                      x-transition:enter="transition ease-out duration-150"
                      x-transition:enter-start="opacity-0 scale-75"
                      x-transition:enter-end="opacity-100 scale-100"
                      x-transition:leave="transition ease-in duration-100"
                      x-transition:leave-start="opacity-100 scale-100"
                      x-transition:leave-end="opacity-0 scale-75"
                      class="material-symbols-outlined" 
                      style="font-size: 24px !important; position: relative !important; z-index: 4000 !important; color: #374151 !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; visibility: visible !important; opacity: 1 !important; pointer-events: auto !important; font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24 !important;">arrow_circle_right</span>
            </button>
        </div>
    </div>

    <!-- Sidebar Navigation -->
    <nav class="sidebar-nav mt-7">
        <div class="space-y-2">
            <!-- Papan Pemuka -->
            <a href="{{ route('dashboard') }}" class="sidebar-nav-item {{ request()->routeIs('dashboard') ? 'sidebar-nav-item-active' : 'sidebar-nav-item-inactive' }}">
                <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                </svg>
                <span x-show="!collapsed">Papan Pemuka</span>
            </a>

            @php
                $currentUser = auth()->user();
            @endphp

            <!-- Program -->
            @if($currentUser && $currentUser->adaKebenaran('program', 'lihat'))
            <a href="{{ route('program.index') }}" class="sidebar-nav-item {{ request()->routeIs('program.*') ? 'sidebar-nav-item-active' : 'sidebar-nav-item-inactive' }}">
                <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <span x-show="!collapsed">Program</span>
            </a>
            @endif

            <!-- Log Pemandu -->
            @if($currentUser && $currentUser->adaKebenaran('log_pemandu', 'lihat'))
            <a href="{{ route('log-pemandu.index') }}" class="sidebar-nav-item {{ request()->routeIs('log-pemandu.*') ? 'sidebar-nav-item-active' : 'sidebar-nav-item-inactive' }}">
                <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span x-show="!collapsed">Log Pemandu</span>
            </a>
            @endif

            <!-- Laporan -->
            <div x-data="{ 
                    open: {{ request()->routeIs('laporan.*') ? 'true' : 'false' }}, 
                    hovered: false,
                    init() {
                        this.$watch('collapsed', value => {
                            if(value) {
                                this.hovered = false;
                                this.open = false;
                            }
                        })
                    }
                 }" 
                 class="relative"
                 @mouseenter="hovered = collapsed" 
                 @mouseleave="hovered = false">
                <button @click="if(!collapsed) open = !open" class="sidebar-nav-item {{ request()->routeIs('laporan.*') ? 'sidebar-nav-item-parent-active' : 'sidebar-nav-item-inactive' }} w-full text-left">
                    <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span x-show="!collapsed" class="flex-1">Laporan</span>
                    <!-- Arrow Right (closed) / Arrow Up (open) -->
                    <svg x-show="!collapsed && !open" class="ml-auto h-4 w-4 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <svg x-show="!collapsed && open" class="ml-auto h-4 w-4 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                </button>
                
                <!-- Submenu: Expanded inline OR Collapsed floating -->
                <div x-show="(collapsed && hovered) || (!collapsed && open)" 
                     x-transition
                     :class="collapsed ? 'submenu-dropdown' : 'submenu-container space-y-1'">
                    @if($currentUser && $currentUser->adaKebenaran('laporan_senarai_program', 'lihat'))
                    <a href="{{ route('laporan.senarai-program') }}" class="submenu-item sidebar-nav-item {{ request()->routeIs('laporan.senarai-program') ? 'sidebar-nav-item-active' : 'sidebar-nav-item-inactive' }} text-sm">Senarai Program</a>
                    @endif

                    @if($currentUser && $currentUser->adaKebenaran('laporan_kenderaan', 'lihat'))
                    <a href="{{ route('laporan.laporan-kenderaan') }}" class="submenu-item sidebar-nav-item {{ request()->routeIs('laporan.laporan-kenderaan') ? 'sidebar-nav-item-active' : 'sidebar-nav-item-inactive' }} text-sm">Laporan Kenderaan</a>
                    @endif

                    @if($currentUser && $currentUser->adaKebenaran('laporan_kilometer', 'lihat'))
                    <a href="{{ route('laporan.laporan-kilometer') }}" class="submenu-item sidebar-nav-item {{ request()->routeIs('laporan.laporan-kilometer') ? 'sidebar-nav-item-active' : 'sidebar-nav-item-inactive' }} text-sm">Laporan Kilometer</a>
                    @endif

                    @if($currentUser && $currentUser->adaKebenaran('laporan_kos', 'lihat'))
                    <a href="{{ route('laporan.laporan-kos') }}" class="submenu-item sidebar-nav-item {{ request()->routeIs('laporan.laporan-kos') ? 'sidebar-nav-item-active' : 'sidebar-nav-item-inactive' }} text-sm">Laporan Kos</a>
                    @endif

                    @if($currentUser && $currentUser->adaKebenaran('laporan_pemandu', 'lihat'))
                    <a href="{{ route('laporan.laporan-pemandu') }}" class="submenu-item sidebar-nav-item {{ request()->routeIs('laporan.laporan-pemandu') ? 'sidebar-nav-item-active' : 'sidebar-nav-item-inactive' }} text-sm">Laporan Pemandu</a>
                    @endif

                    @if($currentUser && $currentUser->adaKebenaran('laporan_tuntutan', 'lihat'))
                    <a href="{{ route('laporan.laporan-tuntutan') }}" class="submenu-item sidebar-nav-item {{ request()->routeIs('laporan.laporan-tuntutan*') ? 'sidebar-nav-item-active' : 'sidebar-nav-item-inactive' }} text-sm">Laporan Tuntutan</a>
                    @endif
                </div>
            </div>

            <!-- Separator Line -->
            <div class="sidebar-separator">
                <hr class="sidebar-separator-line">
            </div>

            <!-- Pengurusan -->
            <div x-data="{ 
                    open: {{ request()->routeIs('pengurusan.*') ? 'true' : 'false' }}, 
                    hovered: false,
                    init() {
                        this.$watch('collapsed', value => {
                            if(value) {
                                this.hovered = false;
                                this.open = false;
                            }
                        })
                    }
                 }" 
                 class="relative"
                 @mouseenter="hovered = collapsed" 
                 @mouseleave="hovered = false">
                <button @click="if(!collapsed) open = !open" class="sidebar-nav-item {{ request()->routeIs('pengurusan.*') ? 'sidebar-nav-item-parent-active' : 'sidebar-nav-item-inactive' }} w-full text-left">
                    <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span x-show="!collapsed" class="flex-1">Pengurusan</span>
                    <!-- Arrow Right (closed) / Arrow Up (open) -->
                    <svg x-show="!collapsed && !open" class="ml-auto h-4 w-4 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <svg x-show="!collapsed && open" class="ml-auto h-4 w-4 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                </button>
                
                <!-- Submenu: Expanded inline OR Collapsed floating -->
                <div x-show="(collapsed && hovered) || (!collapsed && open)" 
                     x-transition
                     :class="collapsed ? 'submenu-dropdown' : 'submenu-container space-y-1'">
                    @php
                        $currentUser = auth()->user();
                        $isAdministrator = $currentUser && $currentUser->jenis_organisasi === 'semua';
                    @endphp

                    @if($currentUser && $currentUser->adaKebenaran('tetapan_umum', 'lihat'))
                    <a href="{{ route('pengurusan.tetapan-umum') }}" class="submenu-item sidebar-nav-item {{ request()->routeIs('pengurusan.tetapan-umum') ? 'sidebar-nav-item-active' : 'sidebar-nav-item-inactive' }} text-sm">Tetapan Umum</a>
                    @endif

                    @if($isAdministrator)
                    <a href="{{ route('pengurusan.senarai-risda') }}" class="submenu-item sidebar-nav-item {{ request()->routeIs('pengurusan.senarai-risda') ? 'sidebar-nav-item-active' : 'sidebar-nav-item-inactive' }} text-sm">Senarai RISDA</a>
                    @endif

                    @if($currentUser && $currentUser->adaKebenaran('senarai_kumpulan', 'lihat'))
                    <a href="{{ route('pengurusan.senarai-kumpulan') }}" class="submenu-item sidebar-nav-item {{ request()->routeIs('pengurusan.senarai-kumpulan') ? 'sidebar-nav-item-active' : 'sidebar-nav-item-inactive' }} text-sm">Senarai Kumpulan</a>
                    @endif

                    @if($currentUser && $currentUser->adaKebenaran('senarai_pengguna', 'lihat'))
                    <a href="{{ route('pengurusan.senarai-pengguna') }}" class="submenu-item sidebar-nav-item {{ request()->routeIs('pengurusan.senarai-pengguna') ? 'sidebar-nav-item-active' : 'sidebar-nav-item-inactive' }} text-sm">Senarai Pengguna</a>
                    @endif

                    @if($currentUser && $currentUser->adaKebenaran('senarai_kenderaan', 'lihat'))
                    <a href="{{ route('pengurusan.senarai-kenderaan') }}" class="submenu-item sidebar-nav-item {{ request()->routeIs('pengurusan.senarai-kenderaan') ? 'sidebar-nav-item-active' : 'sidebar-nav-item-inactive' }} text-sm">Senarai Kenderaan</a>
                    @endif

                    @if($currentUser && $currentUser->adaKebenaran('integrasi', 'lihat'))
                    <a href="{{ route('pengurusan.integrasi') }}" class="submenu-item sidebar-nav-item {{ request()->routeIs('pengurusan.integrasi') ? 'sidebar-nav-item-active' : 'sidebar-nav-item-inactive' }} text-sm">Integrasi</a>
                    @endif

                    @if($currentUser && $currentUser->adaKebenaran('aktiviti_log', 'lihat'))
                    <a href="{{ route('pengurusan.aktiviti-log') }}" class="submenu-item sidebar-nav-item {{ request()->routeIs('pengurusan.aktiviti-log') ? 'sidebar-nav-item-active' : 'sidebar-nav-item-inactive' }} text-sm">Aktiviti Log</a>
                    @endif

                    @if($currentUser && $currentUser->adaKebenaran('aktiviti_log_keselamatan', 'lihat'))
                    <a href="{{ route('pengurusan.aktiviti-log-keselamatan') }}" class="submenu-item sidebar-nav-item {{ request()->routeIs('pengurusan.aktiviti-log-keselamatan') ? 'sidebar-nav-item-active' : 'sidebar-nav-item-inactive' }} text-sm">Aktiviti Log Keselamatan</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>
</div>
