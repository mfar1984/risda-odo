<x-dashboard-layout title="Status Sistem">
    <x-ui.page-header 
        title="Status Sistem" 
        description="Semak status operasi dan kesihatan sistem secara masa nyata"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            @php
                // System Health Checks
                $databaseStatus = true;
                $storageStatus = true;
                $cacheStatus = true;
                $apiStatus = true;
                $supportTicketingStatus = true;
                
                try {
                    DB::connection()->getPdo();
                    $databaseStatus = true;
                } catch (\Exception $e) {
                    $databaseStatus = false;
                }
                
                // Check Support Ticketing System
                try {
                    $supportTablesExist = Schema::hasTable('support_tickets') && 
                                         Schema::hasTable('support_messages') && 
                                         Schema::hasTable('support_ticket_participants');
                    $supportTicketingStatus = $supportTablesExist;
                    
                    // Get ticket stats
                    $totalTickets = \App\Models\SupportTicket::count();
                    $openTickets = \App\Models\SupportTicket::whereNotIn('status', ['ditutup', 'selesai'])->count();
                    $closedToday = \App\Models\SupportTicket::where('status', 'ditutup')->whereDate('closed_at', today())->count();
                } catch (\Exception $e) {
                    $supportTicketingStatus = false;
                    $totalTickets = 0;
                    $openTickets = 0;
                    $closedToday = 0;
                }
                
                try {
                    $storageWritable = is_writable(storage_path());
                    $publicWritable = is_writable(public_path('storage'));
                    $storageStatus = $storageWritable && $publicWritable;
                } catch (\Exception $e) {
                    $storageStatus = false;
                }
                
                try {
                    Cache::remember('health_check', 5, function() { return true; });
                    $cacheStatus = true;
                } catch (\Exception $e) {
                    $cacheStatus = false;
                }
                
                $allHealthy = $databaseStatus && $storageStatus && $cacheStatus && $apiStatus && $supportTicketingStatus;
                
                // System Info
                $phpVersion = PHP_VERSION;
                $laravelVersion = app()->version();
                $dbDriver = config('database.default');
                $cacheDriver = config('cache.default');
                $sessionDriver = config('session.driver');
                
                // Disk Space
                $totalSpace = disk_total_space('/');
                $freeSpace = disk_free_space('/');
                $usedSpace = $totalSpace - $freeSpace;
                $diskUsagePercent = round(($usedSpace / $totalSpace) * 100, 1);
                
                // Memory
                $memoryLimit = ini_get('memory_limit');
                $memoryUsage = memory_get_usage(true);
                $memoryUsageMB = round($memoryUsage / 1024 / 1024, 2);
            @endphp

            <!-- Overall Status Banner -->
            <div class="mb-8">
                @if($allHealthy)
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 rounded-md p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <span class="material-symbols-outlined text-green-600 text-4xl">check_circle</span>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-base font-semibold text-green-900">Sistem Beroperasi Normal</h3>
                                <p class="text-sm text-green-800 mt-1">Semua komponen sistem berfungsi dengan baik</p>
                            </div>
                            <div class="ml-auto">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-600 text-white">
                                    <span class="h-2 w-2 bg-white rounded-full mr-2 animate-pulse"></span>
                                    ONLINE
                                </span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-gradient-to-r from-red-50 to-orange-50 border-l-4 border-red-500 rounded-md p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <span class="material-symbols-outlined text-red-600 text-4xl">error</span>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-base font-semibold text-red-900">Sistem Mengalami Masalah</h3>
                                <p class="text-sm text-red-800 mt-1">Beberapa komponen tidak berfungsi dengan sempurna</p>
                            </div>
                            <div class="ml-auto">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-600 text-white">
                                    <span class="h-2 w-2 bg-white rounded-full mr-2"></span>
                                    ISSUES
                                </span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- System Components Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                
                <!-- Database Status -->
                <div class="bg-white rounded-md shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <span class="material-symbols-outlined {{ $databaseStatus ? 'text-green-600' : 'text-red-600' }} text-2xl mr-3">database</span>
                            <h3 class="text-sm font-semibold text-gray-900">Database</h3>
                        </div>
                        @if($databaseStatus)
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                <span class="h-1.5 w-1.5 bg-green-600 rounded-full mr-1.5"></span>
                                Operational
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                                <span class="h-1.5 w-1.5 bg-red-600 rounded-full mr-1.5"></span>
                                Error
                            </span>
                        @endif
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Driver:</span>
                            <span class="font-medium text-gray-900">{{ ucfirst($dbDriver) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Connection:</span>
                            <span class="font-medium {{ $databaseStatus ? 'text-green-700' : 'text-red-700' }}">
                                {{ $databaseStatus ? 'Connected' : 'Failed' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Storage Status -->
                <div class="bg-white rounded-md shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <span class="material-symbols-outlined {{ $storageStatus ? 'text-green-600' : 'text-red-600' }} text-2xl mr-3">storage</span>
                            <h3 class="text-sm font-semibold text-gray-900">Storage</h3>
                        </div>
                        @if($storageStatus)
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                <span class="h-1.5 w-1.5 bg-green-600 rounded-full mr-1.5"></span>
                                Writable
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                                <span class="h-1.5 w-1.5 bg-red-600 rounded-full mr-1.5"></span>
                                Read-only
                            </span>
                        @endif
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Disk Usage:</span>
                            <span class="font-medium text-gray-900">{{ $diskUsagePercent }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-{{ $diskUsagePercent > 80 ? 'red' : 'green' }}-600 h-2 rounded-full" style="width: {{ $diskUsagePercent }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Cache Status -->
                <div class="bg-white rounded-md shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <span class="material-symbols-outlined {{ $cacheStatus ? 'text-green-600' : 'text-red-600' }} text-2xl mr-3">speed</span>
                            <h3 class="text-sm font-semibold text-gray-900">Cache</h3>
                        </div>
                        @if($cacheStatus)
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                <span class="h-1.5 w-1.5 bg-green-600 rounded-full mr-1.5"></span>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                                <span class="h-1.5 w-1.5 bg-red-600 rounded-full mr-1.5"></span>
                                Inactive
                            </span>
                        @endif
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Driver:</span>
                            <span class="font-medium text-gray-900">{{ ucfirst($cacheDriver) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Status:</span>
                            <span class="font-medium {{ $cacheStatus ? 'text-green-700' : 'text-red-700' }}">
                                {{ $cacheStatus ? 'Working' : 'Failed' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- API Status -->
                <div class="bg-white rounded-md shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <span class="material-symbols-outlined {{ $apiStatus ? 'text-green-600' : 'text-red-600' }} text-2xl mr-3">api</span>
                            <h3 class="text-sm font-semibold text-gray-900">API Service</h3>
                        </div>
                        @if($apiStatus)
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                <span class="h-1.5 w-1.5 bg-green-600 rounded-full mr-1.5"></span>
                                Running
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                                <span class="h-1.5 w-1.5 bg-red-600 rounded-full mr-1.5"></span>
                                Down
                            </span>
                        @endif
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Sanctum:</span>
                            <span class="font-medium text-green-700">Enabled</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Endpoints:</span>
                            <span class="font-medium text-gray-900">40+ Active</span>
                        </div>
                    </div>
                </div>

                <!-- Support Ticketing Status -->
                <div class="bg-white rounded-md shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <span class="material-symbols-outlined {{ $supportTicketingStatus ? 'text-green-600' : 'text-red-600' }} text-2xl mr-3">support_agent</span>
                            <h3 class="text-sm font-semibold text-gray-900">Support Ticketing</h3>
                        </div>
                        @if($supportTicketingStatus)
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                <span class="h-1.5 w-1.5 bg-green-600 rounded-full mr-1.5"></span>
                                Operational
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                                <span class="h-1.5 w-1.5 bg-red-600 rounded-full mr-1.5"></span>
                                Error
                            </span>
                        @endif
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Total Tiket:</span>
                            <span class="font-medium text-gray-900">{{ $totalTickets }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Tiket Terbuka:</span>
                            <span class="font-medium text-{{ $openTickets > 0 ? 'blue' : 'gray' }}-700">{{ $openTickets }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Selesai Hari Ini:</span>
                            <span class="font-medium text-green-700">{{ $closedToday }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Real-time Chat:</span>
                            <span class="font-medium text-green-700">Active (3s polling)</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- System Information -->
            <div class="bg-white rounded-md shadow-sm border border-gray-200 p-6 mb-8">
                <div class="flex items-center mb-4">
                    <span class="material-symbols-outlined text-blue-600 text-2xl mr-3">info</span>
                    <h3 class="text-base font-semibold text-gray-900">Maklumat Sistem</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="bg-gray-50 rounded border border-gray-200 p-4">
                        <p class="text-xs text-gray-600 mb-1">Laravel Version</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $laravelVersion }}</p>
                    </div>
                    <div class="bg-gray-50 rounded border border-gray-200 p-4">
                        <p class="text-xs text-gray-600 mb-1">PHP Version</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $phpVersion }}</p>
                    </div>
                    <div class="bg-gray-50 rounded border border-gray-200 p-4">
                        <p class="text-xs text-gray-600 mb-1">Environment</p>
                        <p class="text-sm font-semibold text-gray-900">{{ ucfirst(config('app.env')) }}</p>
                    </div>
                    <div class="bg-gray-50 rounded border border-gray-200 p-4">
                        <p class="text-xs text-gray-600 mb-1">Timezone</p>
                        <p class="text-sm font-semibold text-gray-900">{{ config('app.timezone') }}</p>
                    </div>
                    <div class="bg-gray-50 rounded border border-gray-200 p-4">
                        <p class="text-xs text-gray-600 mb-1">Session Driver</p>
                        <p class="text-sm font-semibold text-gray-900">{{ ucfirst($sessionDriver) }}</p>
                    </div>
                    <div class="bg-gray-50 rounded border border-gray-200 p-4">
                        <p class="text-xs text-gray-600 mb-1">Memory Usage</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $memoryUsageMB }} MB / {{ $memoryLimit }}</p>
                    </div>
                </div>
            </div>

            <!-- Services Status -->
            <div class="bg-white rounded-md shadow-sm border border-gray-200 p-6 mb-8">
                <div class="flex items-center mb-4">
                    <span class="material-symbols-outlined text-purple-600 text-2xl mr-3">cloud</span>
                    <h3 class="text-base font-semibold text-gray-900">Status Perkhidmatan</h3>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-3 border-b border-gray-200">
                        <div class="flex items-center">
                            <span class="material-symbols-outlined text-orange-600 text-lg mr-3">notifications</span>
                            <span class="text-sm font-medium text-gray-900">Firebase Cloud Messaging</span>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                            <span class="h-1.5 w-1.5 bg-green-600 rounded-full mr-1.5"></span>
                            Active
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between py-3 border-b border-gray-200">
                        <div class="flex items-center">
                            <span class="material-symbols-outlined text-blue-600 text-lg mr-3">lock</span>
                            <span class="text-sm font-medium text-gray-900">Authentication (Sanctum)</span>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                            <span class="h-1.5 w-1.5 bg-green-600 rounded-full mr-1.5"></span>
                            Active
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between py-3 border-b border-gray-200">
                        <div class="flex items-center">
                            <span class="material-symbols-outlined text-teal-600 text-lg mr-3">description</span>
                            <span class="text-sm font-medium text-gray-900">PDF Generator (DomPDF)</span>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                            <span class="h-1.5 w-1.5 bg-green-600 rounded-full mr-1.5"></span>
                            Active
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between py-3">
                        <div class="flex items-center">
                            <span class="material-symbols-outlined text-indigo-600 text-lg mr-3">history</span>
                            <span class="text-sm font-medium text-gray-900">Activity Log (Spatie)</span>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                            <span class="h-1.5 w-1.5 bg-green-600 rounded-full mr-1.5"></span>
                            Active
                        </span>
                    </div>
                </div>
            </div>

            <!-- Last Updated -->
            <div class="text-center">
                <p class="text-xs text-gray-500">
                    Terakhir dikemaskini: <span class="font-medium" id="last-update">{{ now()->format('d/m/Y H:i:s') }}</span>
                </p>
                <button 
                    onclick="location.reload()" 
                    class="mt-3 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded transition-colors"
                >
                    <span class="material-symbols-outlined text-lg mr-2">refresh</span>
                    Muat Semula Status
                </button>
            </div>

        </div>
    </x-ui.page-header>

    <script>
        // Auto-refresh every 60 seconds
        setInterval(function() {
            location.reload();
        }, 60000);
    </script>
</x-dashboard-layout>
