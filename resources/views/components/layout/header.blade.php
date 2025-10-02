@props(['title' => ''])

<header class="header">
    <!-- Top Bar -->
    <div class="topbar">
        <div class="topbar-container">
            <!-- Left side - Welcome message and date -->
            <div class="topbar-left">
                <span class="welcome-text">Welcome, {{ Auth::user()->name ?? 'Administrator' }}</span>
                <span class="topbar-separator">|</span>
                <span class="current-date" x-data="{
                    time: '{{ now()->format('l d F Y H:i:s') }}',
                    updateTime() {
                        const now = new Date();
                        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

                        const dayName = days[now.getDay()];
                        const day = now.getDate();
                        const month = months[now.getMonth()];
                        const year = now.getFullYear();
                        const hours = now.getHours().toString().padStart(2, '0');
                        const minutes = now.getMinutes().toString().padStart(2, '0');
                        const seconds = now.getSeconds().toString().padStart(2, '0');

                        this.time = `${dayName} ${day} ${month} ${year} ${hours}:${minutes}:${seconds}`;
                    }
                }"
                x-init="updateTime(); setInterval(() => updateTime(), 1000)"
                x-text="time">
                </span>
            </div>

            <!-- Right side - Notification and menu icons -->
            <div class="topbar-right">
                <!-- Notification Bell with Badge -->
                <div class="relative" x-data="{
                    open: false,
                    notifications: [],
                    unreadCount: 0,
                    loading: false,
                    async fetchNotifications() {
                        this.loading = true;
                        try {
                            const prevUnreadCount = this.unreadCount;
                            const response = await fetch('/notifications', {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                }
                            });
                            const data = await response.json();
                            if (data.success) {
                                this.notifications = data.data;
                                this.unreadCount = data.unread_count;
                                
                                // Play bell sound if new notification
                                if (this.unreadCount > prevUnreadCount) {
                                    this.playBellSound();
                                }
                            }
                        } catch (error) {
                            console.error('Failed to fetch notifications:', error);
                        }
                        this.loading = false;
                    },
                    playBellSound() {
                        // Create notification bell sound
                        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUKnl86xhGwU7k9n0yXgiBS16yO/ajj4IF14w4IuYIwU2jdLuxG8gAycF');
                        audio.volume = 0.5;
                        audio.play().catch(e => console.log('Sound play failed:', e));
                    },
                    async markAsRead(id) {
                        try {
                            await fetch(`/notifications/${id}/mark-as-read`, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                }
                            });
                            await this.fetchNotifications();
                        } catch (error) {
                            console.error('Failed to mark as read:', error);
                        }
                    },
                    async markAllAsRead() {
                        try {
                            await fetch('/notifications/mark-all-as-read', {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                }
                            });
                            await this.fetchNotifications();
                        } catch (error) {
                            console.error('Failed to mark all as read:', error);
                        }
                    },
                    init() {
                        this.fetchNotifications();
                        // Auto-refresh every 5 seconds for near real-time
                        setInterval(() => this.fetchNotifications(), 5000);
                    }
                }" @click.away="open = false">
                    <button @click="open = !open; if(open) fetchNotifications()" class="topbar-notification-btn">
                        <span class="material-symbols-outlined">notifications</span>
                        <!-- Notification Badge -->
                        <span x-show="unreadCount > 0" x-text="unreadCount" class="notification-badge"></span>
                    </button>

                    <!-- Notification Dropdown -->
                    <div x-show="open" x-transition class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                        <!-- Header -->
                        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-sm font-semibold text-gray-900">Notifikasi</h3>
                            <button @click="markAllAsRead()" x-show="unreadCount > 0" class="text-xs text-blue-600 hover:text-blue-800">
                                Tandakan Semua Dibaca
                            </button>
                        </div>

                        <!-- Notifications List -->
                        <div class="max-h-96 overflow-y-auto">
                            <template x-if="loading">
                                <div class="px-4 py-8 text-center text-gray-500">
                                    <span class="material-symbols-outlined animate-spin">refresh</span>
                                    <p class="mt-2 text-sm">Memuatkan...</p>
                                </div>
                            </template>

                            <template x-if="!loading && notifications.length === 0">
                                <div class="px-4 py-8 text-center text-gray-500">
                                    <span class="material-symbols-outlined text-4xl">notifications_off</span>
                                    <p class="mt-2 text-sm">Tiada notifikasi baharu</p>
                                </div>
                            </template>

                            <template x-for="notification in notifications" :key="notification.id">
                                <a :href="notification.action_url" 
                                   @click="markAsRead(notification.id)"
                                   class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 transition-colors"
                                   :class="{ 'bg-blue-50': !notification.read_at }">
                                    <div class="flex items-start">
                                        <span class="material-symbols-outlined text-blue-600 mr-3 mt-0.5" x-text="notification.type === 'claim_created' || notification.type === 'claim_resubmitted' ? 'receipt_long' : notification.type === 'journey_started' ? 'trip_origin' : notification.type === 'journey_ended' ? 'flag' : notification.type === 'program_auto_closed' ? 'check_circle' : notification.type === 'program_tertunda' ? 'warning' : 'info'"></span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900" x-text="notification.title"></p>
                                            <p class="text-sm text-gray-600 mt-1" x-text="notification.message"></p>
                                            <p class="text-xs text-gray-500 mt-1" x-text="new Date(notification.created_at).toLocaleString('ms-MY', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })"></p>
                                        </div>
                                        <span x-show="!notification.read_at" class="ml-2 h-2 w-2 bg-blue-600 rounded-full"></span>
                                    </div>
                                </a>
                            </template>
                        </div>

                        <!-- Footer -->
                        <div class="px-4 py-3 border-t border-gray-200 text-center">
                            <a href="#" class="text-sm text-blue-600 hover:text-blue-800">Lihat Semua Notifikasi</a>
                        </div>
                    </div>
                </div>

                <!-- Apps Grid Menu Icon -->
                <button class="topbar-grid-btn">
                    <span class="material-symbols-outlined">apps</span>
                </button>

                <!-- Help & Support -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="topbar-help-btn">
                        <span class="material-symbols-outlined">help</span>
                    </button>

                    <!-- Help dropdown menu -->
                    <div x-show="open" @click.away="open = false" x-transition class="topbar-help-dropdown">
                        <div class="help-dropdown-header">
                            <h3 class="help-dropdown-title">Bantuan & Sokongan</h3>
                            <p class="help-dropdown-subtitle">Dapatkan bantuan dan maklumat sistem</p>
                        </div>
                        <div class="help-dropdown-menu">
                            <a href="{{ route('help.panduan-pengguna') }}" class="help-dropdown-item">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                <span>Panduan Pengguna</span>
                            </a>
                            <a href="{{ route('help.faq') }}" class="help-dropdown-item">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Soalan Lazim (FAQ)</span>
                            </a>
                            <a href="{{ route('help.api-dokumentasi') }}" class="help-dropdown-item">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>API</span>
                            </a>
                            <a href="{{ route('help.hubungi-sokongan') }}" class="help-dropdown-item">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 109.75 9.75A9.75 9.75 0 0012 2.25z"></path>
                                </svg>
                                <span>Hubungi Sokongan</span>
                            </a>
                            <a href="{{ route('help.status-sistem') }}" class="help-dropdown-item">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Status Sistem</span>
                            </a>
                            <a href="{{ route('help.nota-keluaran') }}" class="help-dropdown-item">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Nota Keluaran</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="topbar-user-btn">
                        <div class="user-avatar">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <span class="user-email">{{ Auth::user()->email ?? 'admin@risda.gov.my' }}</span>
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Dropdown menu -->
                    <div x-show="open" @click.away="open = false" x-transition class="topbar-dropdown">
                        <a href="{{ route('profile.edit') }}" class="topbar-dropdown-item">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Profile
                        </a>
                        <a href="{{ route('settings.index') }}" class="topbar-dropdown-item">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Settings
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="topbar-dropdown-item w-full text-left">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Breadcrumb Bar -->
    <div class="breadcrumb-bar">
        <div class="breadcrumb-container">
            <!-- Mobile menu button -->
            <button @click="sidebarOpen = !sidebarOpen" class="mobile-menu-btn md:hidden">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Breadcrumb navigation -->
            <nav class="breadcrumb-nav">
                <x-ui.breadcrumb />
            </nav>
        </div>
    </div>

</header>
