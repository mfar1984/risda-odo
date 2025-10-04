<x-dashboard-layout title="API Documentation">
    <x-ui.page-header
        title="Dokumentasi API"
        description="Panduan lengkap untuk integrasi API sistem RISDA Odometer"
    >
        {{-- Hero Section --}}
        <div class="bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 rounded-sm p-8 shadow-lg mb-6">
            <div class="flex items-start justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-500/20 border border-blue-400/30 rounded-sm mb-3">
                        <span class="material-symbols-outlined text-blue-400" style="font-size: 18px;">code</span>
                        <span style="font-family: Poppins, sans-serif; font-size: 11px; font-weight: 600; color: #93c5fd;">Version 1.0</span>
                    </div>
                    <h1 style="font-family: Poppins, sans-serif; font-size: 28px; font-weight: 700; color: white; margin-bottom: 8px;">
                        RISDA Odometer API
                    </h1>
                    <p style="font-family: Poppins, sans-serif; font-size: 13px; color: #cbd5e1; max-width: 700px; line-height: 1.6;">
                        RESTful API untuk integrasi sistem pengurusan kenderaan, log pemandu, tuntutan, dan laporan. 
                        Dilengkapi dengan keselamatan berlapis dan multi-tenancy untuk isolasi data.
                    </p>
                </div>
                <div class="text-right">
                    <div style="font-family: Poppins, sans-serif; font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">Base URL</div>
                    <div style="font-family: 'Courier New', monospace; font-size: 13px; color: #60a5fa; font-weight: 600; margin-top: 4px;">
                        /api/v1
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-sm border shadow-sm p-5 hover:shadow-md transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-sm bg-blue-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-blue-600" style="font-size: 24px;">security</span>
                    </div>
                    <div>
                        <h3 style="font-family: Poppins, sans-serif; font-size: 14px; font-weight: 600; color: #1e293b;">3-Layer Security</h3>
                        <p style="font-family: Poppins, sans-serif; font-size: 10px; color: #64748b;">API Key + Token + Multi-tenancy</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-sm border shadow-sm p-5 hover:shadow-md transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-sm bg-green-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-green-600" style="font-size: 24px;">check_circle</span>
                    </div>
                    <div>
                        <h3 style="font-family: Poppins, sans-serif; font-size: 14px; font-weight: 600; color: #1e293b;">RESTful Design</h3>
                        <p style="font-family: Poppins, sans-serif; font-size: 10px; color: #64748b;">Standard HTTP & JSON</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-sm border shadow-sm p-5 hover:shadow-md transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-sm bg-purple-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-purple-600" style="font-size: 24px;">speed</span>
                    </div>
                    <div>
                        <h3 style="font-family: Poppins, sans-serif; font-size: 14px; font-weight: 600; color: #1e293b;">Fast & Reliable</h3>
                        <p style="font-family: Poppins, sans-serif; font-size: 10px; color: #64748b;">Optimized performance</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search Bar --}}
        <div class="bg-white rounded-sm border border-gray-200 shadow-sm p-4 mb-6">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" style="font-size: 20px;">search</span>
                <input 
                    type="text" 
                    id="endpoint-search"
                    placeholder="Cari endpoint... (cth: health, login, program)"
                    class="w-full pl-10 pr-4 h-[42px] border border-gray-300 rounded-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    style="font-family: Poppins, sans-serif; font-size: 12px;"
                    onkeyup="filterEndpoints(this.value)"
                >
            </div>
        </div>

        {{-- Main Content: Sidebar + Content Area --}}
        <div class="grid grid-cols-12 gap-6" style="height: calc(100vh - 250px);">
            {{-- Left Sidebar Menu (Scrollable with Grouping) --}}
            <div class="col-span-12 lg:col-span-3 overflow-hidden">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 sticky top-6 overflow-y-auto overflow-x-hidden" style="max-height: calc(100vh - 200px); width: 100%;" x-data="{ 
                    authOpen: true, 
                    getOpen: true, 
                    postOpen: true, 
                    putOpen: true, 
                    deleteOpen: true, 
                    comingSoonOpen: false 
                }">
                    {{-- Authentication Section --}}
                    <div class="border-b border-gray-200">
                        <button @click="authOpen = !authOpen" class="w-full p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Authentication</h4>
                            <span class="material-symbols-outlined text-gray-400 transition-transform" :class="authOpen ? 'rotate-180' : ''" style="font-size: 20px;">expand_more</span>
                        </button>
                        <div x-show="authOpen" x-collapse class="px-4 pb-4">
                            <nav class="space-y-1">
                                <button onclick="showSection('overview')" id="menu-overview" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors">
                                    <span class="material-symbols-outlined text-base align-middle mr-2">info</span>
                                    Overview
                                </button>
                                <button onclick="showSection('security')" id="menu-security" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors">
                                    <span class="material-symbols-outlined text-base align-middle mr-2">security</span>
                                    Security Layers
                                </button>
                            </nav>
                        </div>
                    </div>

                    {{-- GET Methods --}}
                    <div class="border-b border-gray-200">
                        <button @click="getOpen = !getOpen" class="w-full p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 bg-green-600 text-white text-xs font-semibold rounded">GET</span>
                                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">GET Methods</h4>
                            </div>
                            <span class="material-symbols-outlined text-gray-400 transition-transform" :class="getOpen ? 'rotate-180' : ''" style="font-size: 20px;">expand_more</span>
                        </button>
                        <div x-show="getOpen" x-collapse class="px-4 pb-4">
                            <nav class="space-y-1">
                                <button onclick="showSection('health')" id="menu-health" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-base mr-2 text-green-600 flex-shrink-0">check_circle</span>
                                    <span class="truncate">/health</span>
                                </button>
                                <button onclick="showSection('user')" id="menu-user" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-base mr-2 text-green-600 flex-shrink-0">person</span>
                                    <span class="truncate">/auth/user</span>
                                </button>
                                <button onclick="showSection('programs')" id="menu-programs" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-base mr-2 text-green-600 flex-shrink-0">event</span>
                                    <span class="truncate">/programs</span>
                                </button>
                                <button onclick="showSection('program-detail')" id="menu-program-detail" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-base mr-2 text-green-600 flex-shrink-0">info</span>
                                    <span class="truncate">/programs/{id}</span>
                                </button>
                                <button onclick="showSection('log-active')" id="menu-log-active" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-base mr-2 text-green-600 flex-shrink-0">directions_car</span>
                                    <span class="truncate">/log-pemandu/active</span>
                                </button>
                                <button onclick="showSection('log-list')" id="menu-log-list" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-base mr-2 text-green-600 flex-shrink-0">list</span>
                                    <span class="truncate">/log-pemandu</span>
                                </button>
                                <button onclick="showSection('tuntutan-list')" id="menu-tuntutan-list" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-base mr-2 text-green-600 flex-shrink-0">receipt</span>
                                    <span class="truncate">/tuntutan</span>
                                </button>
                                <button onclick="showSection('tuntutan-detail')" id="menu-tuntutan-detail" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-base mr-2 text-green-600 flex-shrink-0">receipt_long</span>
                                    <span class="truncate">/tuntutan/{id}</span>
                                </button>
                                <button onclick="showSection('report-vehicle')" id="menu-report-vehicle" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-base mr-2 text-green-600 flex-shrink-0">directions_car</span>
                                    <span class="truncate">/reports/vehicle</span>
                                </button>
                                <button onclick="showSection('report-cost')" id="menu-report-cost" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-base mr-2 text-green-600 flex-shrink-0">attach_money</span>
                                    <span class="truncate">/reports/cost</span>
                                </button>
                                <button onclick="showSection('report-driver')" id="menu-report-driver" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-base mr-2 text-green-600 flex-shrink-0">badge</span>
                                    <span class="truncate">/reports/driver</span>
                                </button>
                                <button onclick="showSection('dashboard-stats')" id="menu-dashboard-stats" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-base mr-2 text-green-600 flex-shrink-0">dashboard</span>
                                    <span class="truncate">/dashboard/statistics</span>
                                </button>
                                <button onclick="showSection('app-info')" id="menu-app-info" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-base mr-2 text-green-600 flex-shrink-0">info</span>
                                    <span class="truncate">/app-info</span>
                                </button>
                                <button onclick="showSection('privacy-policy')" id="menu-privacy-policy" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-base mr-2 text-green-600 flex-shrink-0">privacy_tip</span>
                                    <span class="truncate">/privacy-policy</span>
                                </button>
                                <button onclick="showSection('chart-overview')" id="menu-chart-overview" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-base mr-2 text-green-600 flex-shrink-0">show_chart</span>
                                    <span class="truncate">/chart/overview</span>
                                </button>
                                <button onclick="showSection('chart-do-activity')" id="menu-chart-do-activity" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-base mr-2 text-green-600 flex-shrink-0">analytics</span>
                                    <span class="truncate">/chart/do-activity</span>
                                </button>
                            </nav>
                        </div>
                    </div>

                    {{-- POST Methods --}}
                    <div class="border-b border-gray-200">
                        <button @click="postOpen = !postOpen" class="w-full p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 bg-blue-600 text-white text-xs font-semibold rounded">POST</span>
                                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">POST Methods</h4>
                            </div>
                            <span class="material-symbols-outlined text-gray-400 transition-transform" :class="postOpen ? 'rotate-180' : ''" style="font-size: 20px;">expand_more</span>
                        </button>
                        <div x-show="postOpen" x-collapse class="px-4 pb-4">
                            <nav class="space-y-1">
                                <button onclick="showSection('login')" id="menu-login" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-base mr-2 text-blue-600 flex-shrink-0">login</span>
                                    <span class="truncate">/auth/login</span>
                                </button>
                                <button onclick="showSection('logout')" id="menu-logout" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-base mr-2 text-blue-600 flex-shrink-0">logout</span>
                                    <span class="truncate">/auth/logout</span>
                                </button>
                                <button onclick="showSection('logout-all')" id="menu-logout-all" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-base mr-2 text-blue-600 flex-shrink-0">logout</span>
                                    <span class="truncate">/auth/logout-all</span>
                                </button>
                                <button onclick="showSection('upload-profile')" id="menu-upload-profile" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-base mr-2 text-blue-600 flex-shrink-0">upload</span>
                                    <span class="truncate">/user/profile-picture</span>
                                </button>
                                <button onclick="showSection('start-journey')" id="menu-start-journey" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-base mr-2 text-blue-600 flex-shrink-0">play_arrow</span>
                                    <span class="truncate">/log-pemandu/start</span>
                                </button>
                                <button onclick="showSection('create-tuntutan')" id="menu-create-tuntutan" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-base mr-2 text-blue-600 flex-shrink-0">add_circle</span>
                                    <span class="truncate">/tuntutan</span>
                                </button>
                            </nav>
                        </div>
                    </div>

                    {{-- PUT Methods --}}
                    <div class="border-b border-gray-200">
                        <button @click="putOpen = !putOpen" class="w-full p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 bg-yellow-600 text-white text-xs font-semibold rounded">PUT</span>
                                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">PUT Methods</h4>
                            </div>
                            <span class="material-symbols-outlined text-gray-400 transition-transform" :class="putOpen ? 'rotate-180' : ''" style="font-size: 20px;">expand_more</span>
                        </button>
                        <div x-show="putOpen" x-collapse class="px-4 pb-4">
                            <nav class="space-y-1">
                                <button onclick="showSection('change-password')" id="menu-change-password" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-base mr-2 text-yellow-600 flex-shrink-0">lock_reset</span>
                                    <span class="truncate">/user/change-password</span>
                                </button>
                                <button onclick="showSection('end-journey')" id="menu-end-journey" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-base mr-2 text-yellow-600 flex-shrink-0">stop</span>
                                    <span class="truncate">/log-pemandu/{id}/end</span>
                                </button>
                                <button onclick="showSection('update-tuntutan')" id="menu-update-tuntutan" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-base mr-2 text-yellow-600 flex-shrink-0">edit</span>
                                    <span class="truncate">/tuntutan/{id}</span>
                                </button>
                            </nav>
                        </div>
                    </div>

                    {{-- DELETE Methods --}}
                    <div class="border-b border-gray-200">
                        <button @click="deleteOpen = !deleteOpen" class="w-full p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 bg-red-600 text-white text-xs font-semibold rounded">DELETE</span>
                                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">DELETE Methods</h4>
                            </div>
                            <span class="material-symbols-outlined text-gray-400 transition-transform" :class="deleteOpen ? 'rotate-180' : ''" style="font-size: 20px;">expand_more</span>
                        </button>
                        <div x-show="deleteOpen" x-collapse class="px-4 pb-4">
                            <nav class="space-y-1">
                                <button onclick="showSection('delete-profile')" id="menu-delete-profile" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-base mr-2 text-red-600 flex-shrink-0">delete</span>
                                    <span class="truncate">/user/profile-picture</span>
                                </button>
                            </nav>
                        </div>
                    </div>

                    {{-- Coming Soon --}}
                    <div>
                        <button @click="comingSoonOpen = !comingSoonOpen" class="w-full p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Coming Soon</h4>
                            <span class="material-symbols-outlined text-gray-400 transition-transform" :class="comingSoonOpen ? 'rotate-180' : ''" style="font-size: 20px;">expand_more</span>
                        </button>
                        <div x-show="comingSoonOpen" x-collapse class="px-4 pb-4">
                            <button onclick="showSection('coming-soon')" id="menu-coming-soon" class="menu-item w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50 transition-colors">
                                <span class="material-symbols-outlined text-base align-middle mr-2 text-purple-600">rocket_launch</span>
                                Coming Soon
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Content Area (Scrollable) --}}
            <div class="col-span-12 lg:col-span-9 overflow-y-auto" style="max-height: calc(100vh - 200px);">
                {{-- Overview Content --}}
                <div id="content-overview" class="content-section bg-white rounded-sm border shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                        <div class="w-10 h-10 rounded-sm bg-blue-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-blue-600" style="font-size: 24px;">info</span>
                        </div>
                        <div>
                            <h3 style="font-family: Poppins, sans-serif; font-size: 14px; font-weight: 700; color: #1e293b;">
                        Authentication Overview
                    </h3>
                            <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                Sistem keselamatan berlapis untuk API RISDA Odometer
                            </p>
                        </div>
                    </div>

                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #475569; line-height: 1.7; margin-bottom: 20px;">
                                RISDA Odometer API menggunakan sistem keselamatan berlapis untuk memastikan hanya aplikasi dan pengguna yang sah dapat mengakses data.
                            </p>

                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-sm">
                        <div class="flex gap-3">
                            <span class="material-symbols-outlined text-yellow-600" style="font-size: 20px;">warning</span>
                            <div>
                                <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #92400e; line-height: 1.6;">
                                    <strong style="font-weight: 600;">Penting:</strong> Setiap request memerlukan header 
                                    <code class="bg-yellow-200 px-2 py-0.5 rounded-sm text-yellow-900" style="font-family: 'Courier New', monospace; font-size: 10px;">Accept: application/json</code> 
                                    untuk memastikan response dalam format JSON.
                                </p>
                                    </div>
                                </div>
                            </div>

                    <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                        Required Headers untuk Semua Requests:
                    </h4>

                    <div class="bg-slate-900 rounded-sm p-4 font-mono">
                        <div class="space-y-2">
                                <div class="flex gap-2">
                                <span style="color: #60a5fa;">Content-Type:</span>
                                <span style="color: #34d399;">application/json</span>
                                </div>
                                <div class="flex gap-2">
                                <span style="color: #60a5fa;">Accept:</span>
                                <span style="color: #34d399;">application/json</span>
                                </div>
                                <div class="flex gap-2">
                                <span style="color: #60a5fa;">X-API-Key:</span>
                                <span style="color: #a78bfa;">YOUR_GLOBAL_API_KEY</span>
                                </div>
                                <div class="flex gap-2">
                                <span style="color: #60a5fa;">Authorization:</span>
                                <span style="color: #34d399;">Bearer YOUR_USER_TOKEN</span>
                                <span style="font-family: Poppins, sans-serif; font-size: 10px; color: #94a3b8;">(selepas login)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Security Content --}}
                    <div id="content-security" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                            <div class="w-10 h-10 rounded-sm bg-blue-100 flex items-center justify-center">
                                <span class="material-symbols-outlined text-blue-600" style="font-size: 24px;">security</span>
                            </div>
                            <div>
                                <h3 style="font-family: Poppins, sans-serif; font-size: 18px; font-weight: 700; color: #1e293b;">
                            3-Layer Security System
                        </h3>
                                <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                    Triple-layer authentication untuk keselamatan maksimum
                                </p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="border-l-4 border-blue-500 bg-blue-50 p-4 rounded-sm">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-sm bg-blue-600 text-white flex items-center justify-center flex-shrink-0">
                                        <span style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 700;">1</span>
                                    </div>
                                    <div class="flex-1">
                                        <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e3a8a; margin-bottom: 6px;">
                                            Global API Key
                                        </h4>
                                        <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #1e40af; line-height: 1.6;">
                                            Header: <code class="bg-blue-200 px-2 py-0.5 rounded-sm text-blue-900" style="font-family: 'Courier New', monospace; font-size: 10px;">X-API-Key</code><br>
                                    Verify bahawa request datang dari aplikasi mobile RISDA yang sah. API Key ini global dan sama untuk semua users.
                                </p>
                            </div>
                                </div>
                            </div>

                            <div class="border-l-4 border-green-500 bg-green-50 p-4 rounded-sm">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-sm bg-green-600 text-white flex items-center justify-center flex-shrink-0">
                                        <span style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 700;">2</span>
                                    </div>
                                    <div class="flex-1">
                                        <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #065f46; margin-bottom: 6px;">
                                            User Authentication
                                        </h4>
                                        <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #047857; line-height: 1.6;">
                                            Endpoint: <code class="bg-green-200 px-2 py-0.5 rounded-sm text-green-900" style="font-family: 'Courier New', monospace; font-size: 10px;">POST /auth/login</code><br>
                                    User login dengan email & password menggunakan custom Argon2id + Email Salt untuk maximum security.
                                </p>
                            </div>
                                </div>
                            </div>

                            <div class="border-l-4 border-purple-500 bg-purple-50 p-4 rounded-sm">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-sm bg-purple-600 text-white flex items-center justify-center flex-shrink-0">
                                        <span style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 700;">3</span>
                                    </div>
                                    <div class="flex-1">
                                        <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #581c87; margin-bottom: 6px;">
                                            Laravel Sanctum Token
                                        </h4>
                                        <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #6b21a8; line-height: 1.6;">
                                            Header: <code class="bg-purple-200 px-2 py-0.5 rounded-sm text-purple-900" style="font-family: 'Courier New', monospace; font-size: 10px;">Authorization: Bearer TOKEN</code><br>
                                    Setiap request selepas login memerlukan Bearer token yang unique untuk setiap device/session.
                                </p>
                            </div>
                                </div>
                            </div>

                            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-200 rounded-sm p-4">
                                <div class="flex items-start gap-3">
                                    <span class="material-symbols-outlined text-indigo-600" style="font-size: 24px;">shield</span>
                                    <div>
                                        <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #312e81; margin-bottom: 6px;">
                                            Multi-Tenancy Data Isolation
                                        </h4>
                                        <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #4338ca; line-height: 1.6;">
                                            Setiap user mempunyai <code class="bg-indigo-200 px-2 py-0.5 rounded-sm text-indigo-900" style="font-family: 'Courier New', monospace; font-size: 10px;">jenis_organisasi</code> dan <code class="bg-indigo-200 px-2 py-0.5 rounded-sm text-indigo-900" style="font-family: 'Courier New', monospace; font-size: 10px;">organisasi_id</code>. API akan automatically filter data berdasarkan organizational scope user tersebut.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Health Check Content --}}
                    <div id="content-health" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-sm bg-green-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-green-600" style="font-size: 24px;">monitor_heart</span>
                        </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-green-600 text-white text-[10px] font-semibold rounded-sm" style="font-family: Poppins, sans-serif;">GET</span>
                                        <code class="text-[14px] font-mono text-gray-900">/health</code>
                                    </div>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                        Health check endpoint (public, no authentication required)
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-green-50 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                PUBLIC
                            </span>
                        </div>

                        {{-- Description --}}
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded-sm">
                            <div class="flex gap-3">
                                <span class="material-symbols-outlined text-blue-600" style="font-size: 20px;">info</span>
                                <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #1e40af; line-height: 1.6;">
                                    Endpoint ini tidak memerlukan authentication. Digunakan untuk monitoring dan health check sistem.
                                </p>
                            </div>
                        </div>

                        {{-- Request Example --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Contoh Request:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4 relative group">
                                <pre class="text-sm overflow-x-auto"><code style="color: #cbd5e1; font-family: 'Courier New', monospace; font-size: 11px;">curl -X GET {{ url('/api/health') }}</code></pre>
                            </div>
                        </div>

                        {{-- Response --}}
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b;">
                                    Response:
                                </h4>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                    200 OK
                                </span>
                            </div>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"success"</span>: <span style="color: #34d399;">true</span>,
  <span style="color: #60a5fa;">"message"</span>: <span style="color: #fbbf24;">"RISDA Odometer API is running"</span>,
  <span style="color: #60a5fa;">"version"</span>: <span style="color: #fbbf24;">"1.0.0"</span>,
  <span style="color: #60a5fa;">"timestamp"</span>: <span style="color: #fbbf24;">"2025-10-01T10:34:16+08:00"</span>
}</code></pre>
                            </div>
                        </div>
                    </div>

                    {{-- Login Content --}}
                    <div id="content-login" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-sm bg-green-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-green-600" style="font-size: 24px;">login</span>
                        </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-blue-600 text-white text-[10px] font-semibold rounded-sm" style="font-family: Poppins, sans-serif;">POST</span>
                                        <code class="text-[14px] font-mono text-gray-900">/auth/login</code>
                                    </div>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                        Login dan dapatkan Bearer token untuk authentication
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-green-50 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                PUBLIC
                            </span>
                        </div>

                        {{-- Request Headers --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Headers:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">Content-Type:</span> <span style="color: #34d399;">application/json</span>
<span style="color: #60a5fa;">Accept:</span> <span style="color: #34d399;">application/json</span>
<span style="color: #60a5fa;">X-API-Key:</span> <span style="color: #a78bfa;">YOUR_GLOBAL_API_KEY</span></code></pre>
                            </div>
                        </div>

                        {{-- Request Body --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Body:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"email"</span>: <span style="color: #fbbf24;">"user@jara.my"</span>,
  <span style="color: #60a5fa;">"password"</span>: <span style="color: #fbbf24;">"password"</span>
}</code></pre>
                            </div>
                        </div>

                        {{-- Response --}}
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-3">
                                <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b;">
                                    Response:
                                </h4>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                    200 OK
                                </span>
                            </div>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"success"</span>: <span style="color: #34d399;">true</span>,
  <span style="color: #60a5fa;">"message"</span>: <span style="color: #fbbf24;">"Login berjaya"</span>,
  <span style="color: #60a5fa;">"data"</span>: {
    <span style="color: #60a5fa;">"token"</span>: <span style="color: #fbbf24;">"23|xY9KpQm2vNrLwE4sHcT8fA5jB6gDz3nU1oP7iV0eR"</span>,
    <span style="color: #60a5fa;">"token_type"</span>: <span style="color: #fbbf24;">"Bearer"</span>,
    <span style="color: #60a5fa;">"user"</span>: {
      <span style="color: #60a5fa;">"id"</span>: <span style="color: #fb923c;">10</span>,
      <span style="color: #60a5fa;">"name"</span>: <span style="color: #fbbf24;">"Adam Bin Abdullah"</span>,
      <span style="color: #60a5fa;">"email"</span>: <span style="color: #fbbf24;">"user@jara.my"</span>,
      <span style="color: #60a5fa;">"jenis_organisasi"</span>: <span style="color: #fbbf24;">"stesen"</span>,
      <span style="color: #60a5fa;">"organisasi_id"</span>: <span style="color: #fb923c;">3</span>,
      <span style="color: #60a5fa;">"stesen"</span>: {
        <span style="color: #60a5fa;">"nama"</span>: <span style="color: #fbbf24;">"Pejabat RISDA Stesen KL"</span>,
        <span style="color: #60a5fa;">"kod"</span>: <span style="color: #fbbf24;">"STESEN-003"</span>
      },
      <span style="color: #60a5fa;">"staf"</span>: {
        <span style="color: #60a5fa;">"no_pekerja"</span>: <span style="color: #fbbf24;">"RS2025-1001"</span>,
        <span style="color: #60a5fa;">"nama_penuh"</span>: <span style="color: #fbbf24;">"Adam Bin Abdullah"</span>,
        <span style="color: #60a5fa;">"jawatan"</span>: <span style="color: #fbbf24;">"Pemandu Kenderaan"</span>
      }
    }
  }
}</code></pre>
                            </div>
                        </div>

                        {{-- Error Responses --}}
                        <div>
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Error Responses:
                            </h4>
                        <div class="space-y-2">
                                <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-sm">
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #991b1b;">
                                        <span style="font-weight: 600;">401 Unauthorized</span> - API Key tidak sah
                                    </p>
                            </div>
                                <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-sm">
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #991b1b;">
                                        <span style="font-weight: 600;">422 Validation Error</span> - Email atau password salah
                                    </p>
                            </div>
                                <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-sm">
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #991b1b;">
                                        <span style="font-weight: 600;">403 Forbidden</span> - Akaun tidak aktif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Get User Content --}}
                    <div id="content-user" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-sm bg-blue-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-blue-600" style="font-size: 24px;">account_circle</span>
                        </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-green-600 text-white text-[10px] font-semibold rounded-sm" style="font-family: Poppins, sans-serif;">GET</span>
                                        <code class="text-[14px] font-mono text-gray-900">/auth/user</code>
                                    </div>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                        Dapatkan maklumat user yang sedang login (authenticated)
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-red-50 text-red-700 text-[10px] font-semibold rounded-sm border border-red-200" style="font-family: Poppins, sans-serif;">
                                PROTECTED
                            </span>
                        </div>

                        {{-- Description --}}
                        <div class="bg-purple-50 border-l-4 border-purple-400 p-4 mb-6 rounded-sm">
                            <div class="flex gap-3">
                                <span class="material-symbols-outlined text-purple-600" style="font-size: 20px;">lock</span>
                                <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #6b21a8; line-height: 1.6;">
                                    Endpoint ini memerlukan Bearer token. Digunakan untuk mendapatkan profile user yang sedang login beserta data organisasi.
                                </p>
                            </div>
                        </div>

                        {{-- Request Headers --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Headers:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">Accept:</span> <span style="color: #34d399;">application/json</span>
<span style="color: #60a5fa;">X-API-Key:</span> <span style="color: #a78bfa;">YOUR_GLOBAL_API_KEY</span>
<span style="color: #60a5fa;">Authorization:</span> <span style="color: #34d399;">Bearer YOUR_TOKEN</span></code></pre>
                            </div>
                        </div>

                        {{-- Response --}}
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b;">
                                    Response:
                                </h4>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                    200 OK
                                </span>
                            </div>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"success"</span>: <span style="color: #34d399;">true</span>,
  <span style="color: #60a5fa;">"data"</span>: {
    <span style="color: #60a5fa;">"id"</span>: <span style="color: #fb923c;">10</span>,
    <span style="color: #60a5fa;">"name"</span>: <span style="color: #fbbf24;">"Adam Bin Abdullah"</span>,
    <span style="color: #60a5fa;">"email"</span>: <span style="color: #fbbf24;">"user@jara.my"</span>,
    <span style="color: #60a5fa;">"profile_picture_url"</span>: <span style="color: #fbbf24;">"http://localhost:8000/storage/profile_pictures/profile_10_1234567890.jpg"</span>,
    <span style="color: #60a5fa;">"no_telefon"</span>: <span style="color: #fbbf24;">"012-3456789"</span>,
    <span style="color: #60a5fa;">"jenis_organisasi"</span>: <span style="color: #fbbf24;">"stesen"</span>,
    <span style="color: #60a5fa;">"organisasi_id"</span>: <span style="color: #fb923c;">3</span>,
    <span style="color: #60a5fa;">"kumpulan_id"</span>: <span style="color: #fb923c;">2</span>,
    <span style="color: #60a5fa;">"status"</span>: <span style="color: #fbbf24;">"aktif"</span>,
    <span style="color: #60a5fa;">"bahagian"</span>: <span style="color: #94a3b8;">null</span>,
    <span style="color: #60a5fa;">"stesen"</span>: {
      <span style="color: #60a5fa;">"id"</span>: <span style="color: #fb923c;">3</span>,
      <span style="color: #60a5fa;">"nama"</span>: <span style="color: #fbbf24;">"Pejabat RISDA Stesen Kuala Lumpur"</span>,
      <span style="color: #60a5fa;">"kod"</span>: <span style="color: #fbbf24;">"STESEN-003"</span>
    },
    <span style="color: #60a5fa;">"kumpulan"</span>: {
      <span style="color: #60a5fa;">"id"</span>: <span style="color: #fb923c;">2</span>,
      <span style="color: #60a5fa;">"nama"</span>: <span style="color: #fbbf24;">"Pemandu"</span>,
      <span style="color: #60a5fa;">"kebenaran_matrix"</span>: { <span style="color: #94a3b8;">...</span> }
    },
    <span style="color: #60a5fa;">"staf"</span>: {
      <span style="color: #60a5fa;">"id"</span>: <span style="color: #fb923c;">15</span>,
      <span style="color: #60a5fa;">"no_pekerja"</span>: <span style="color: #fbbf24;">"RS2025-1001"</span>,
      <span style="color: #60a5fa;">"nama_penuh"</span>: <span style="color: #fbbf24;">"Adam Bin Abdullah"</span>,
      <span style="color: #60a5fa;">"no_kad_pengenalan"</span>: <span style="color: #fbbf24;">"850123-10-5678"</span>,
      <span style="color: #60a5fa;">"jawatan"</span>: <span style="color: #fbbf24;">"Pemandu Kenderaan"</span>,
      <span style="color: #60a5fa;">"no_telefon"</span>: <span style="color: #fbbf24;">"012-3456789"</span>,
      <span style="color: #60a5fa;">"alamat"</span>: <span style="color: #fbbf24;">"No. 123, Jalan Merdeka, Taman Sejahtera, 50000 Kuala Lumpur"</span>
    }
  }
}</code></pre>
                            </div>
                        </div>
                    </div>

                    {{-- Logout Content --}}
                    <div id="content-logout" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-sm bg-red-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-red-600" style="font-size: 24px;">logout</span>
                        </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-red-600 text-white text-[10px] font-semibold rounded-sm" style="font-family: Poppins, sans-serif;">POST</span>
                                        <code class="text-[14px] font-mono text-gray-900">/auth/logout</code>
                                    </div>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                        Logout dan delete current token (device ini sahaja)
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-red-50 text-red-700 text-[10px] font-semibold rounded-sm border border-red-200" style="font-family: Poppins, sans-serif;">
                                PROTECTED
                            </span>
                        </div>

                        {{-- Request Headers --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Headers:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">Accept:</span> <span style="color: #34d399;">application/json</span>
<span style="color: #60a5fa;">X-API-Key:</span> <span style="color: #a78bfa;">YOUR_GLOBAL_API_KEY</span>
<span style="color: #60a5fa;">Authorization:</span> <span style="color: #34d399;">Bearer YOUR_TOKEN</span></code></pre>
                            </div>
                        </div>

                        {{-- Response --}}
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b;">
                                    Response:
                                </h4>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                    200 OK
                                </span>
                            </div>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"success"</span>: <span style="color: #34d399;">true</span>,
  <span style="color: #60a5fa;">"message"</span>: <span style="color: #fbbf24;">"Logout berjaya"</span>
}</code></pre>
                            </div>
                        </div>
                    </div>

                    {{-- Logout All Content --}}
                    <div id="content-logout-all" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-sm bg-red-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-red-600" style="font-size: 24px;">devices_off</span>
                        </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-red-600 text-white text-[10px] font-semibold rounded-sm" style="font-family: Poppins, sans-serif;">POST</span>
                                        <code class="text-[14px] font-mono text-gray-900">/auth/logout-all</code>
                                    </div>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                        Logout dari semua devices (delete all tokens)
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-red-50 text-red-700 text-[10px] font-semibold rounded-sm border border-red-200" style="font-family: Poppins, sans-serif;">
                                PROTECTED
                            </span>
                        </div>

                        {{-- Request Headers --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Headers:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">Accept:</span> <span style="color: #34d399;">application/json</span>
<span style="color: #60a5fa;">X-API-Key:</span> <span style="color: #a78bfa;">YOUR_GLOBAL_API_KEY</span>
<span style="color: #60a5fa;">Authorization:</span> <span style="color: #34d399;">Bearer YOUR_TOKEN</span></code></pre>
                            </div>
                        </div>

                        {{-- Response --}}
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-3">
                                <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b;">
                                    Response:
                                </h4>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                    200 OK
                                </span>
                            </div>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"success"</span>: <span style="color: #34d399;">true</span>,
  <span style="color: #60a5fa;">"message"</span>: <span style="color: #fbbf24;">"Logout dari semua devices berjaya"</span>
}</code></pre>
                            </div>
                        </div>

                        {{-- Warning --}}
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-sm">
                            <div class="flex gap-3">
                                <span class="material-symbols-outlined text-red-600" style="font-size: 20px;">warning</span>
                                <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #991b1b; line-height: 1.6;">
                                    <strong style="font-weight: 600;">Security Action:</strong> Deletes ALL tokens for this user. User will be logged out from all devices including the current one.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Change Password Content --}}
                    <div id="content-change-password" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-sm bg-amber-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-amber-600" style="font-size: 24px;">lock_reset</span>
                        </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-yellow-600 text-white text-[10px] font-semibold rounded-sm" style="font-family: Poppins, sans-serif;">PUT</span>
                                        <code class="text-[14px] font-mono text-gray-900">/user/change-password</code>
                                    </div>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                        Tukar kata laluan user yang sedang login
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-red-50 text-red-700 text-[10px] font-semibold rounded-sm border border-red-200" style="font-family: Poppins, sans-serif;">
                                PROTECTED
                            </span>
                        </div>

                        {{-- Request Headers --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Headers:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">Accept:</span> <span style="color: #34d399;">application/json</span>
<span style="color: #60a5fa;">X-API-Key:</span> <span style="color: #a78bfa;">YOUR_GLOBAL_API_KEY</span>
<span style="color: #60a5fa;">Authorization:</span> <span style="color: #34d399;">Bearer YOUR_TOKEN</span>
<span style="color: #60a5fa;">Content-Type:</span> <span style="color: #34d399;">application/json</span></code></pre>
                            </div>
                        </div>

                        {{-- Request Body --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Body:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"current_password"</span>: <span style="color: #fbbf24;">"oldpassword123"</span>,
  <span style="color: #60a5fa;">"new_password"</span>: <span style="color: #fbbf24;">"newpassword456"</span>,
  <span style="color: #60a5fa;">"new_password_confirmation"</span>: <span style="color: #fbbf24;">"newpassword456"</span>
}</code></pre>
                            </div>
                        </div>

                        {{-- Response --}}
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-3">
                                <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b;">
                                    Response:
                                </h4>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                    200 OK
                                </span>
                            </div>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"success"</span>: <span style="color: #34d399;">true</span>,
  <span style="color: #60a5fa;">"message"</span>: <span style="color: #fbbf24;">"Kata laluan berjaya dikemaskini"</span>
}</code></pre>
                            </div>
                        </div>

                        {{-- Error Responses --}}
                        <div>
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Error Responses:
                            </h4>
                        <div class="space-y-2">
                                <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-sm">
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #991b1b;">
                                        <span style="font-weight: 600;">422 Validation Error</span> - Kata laluan semasa tidak sah
                                    </p>
                            </div>
                                <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-sm">
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #991b1b;">
                                        <span style="font-weight: 600;">401 Unauthorized</span> - Token tidak sah
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Upload Profile Picture Content --}}
                    <div id="content-upload-profile" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-sm bg-blue-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-blue-600" style="font-size: 24px;">photo_camera</span>
                        </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-blue-600 text-white text-[10px] font-semibold rounded-sm" style="font-family: Poppins, sans-serif;">POST</span>
                                        <code class="text-[14px] font-mono text-gray-900">/user/profile-picture</code>
                                    </div>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                        Upload atau update gambar profil user
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-red-50 text-red-700 text-[10px] font-semibold rounded-sm border border-red-200" style="font-family: Poppins, sans-serif;">
                                PROTECTED
                            </span>
                        </div>

                        {{-- Request Headers --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Headers:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">Accept:</span> <span style="color: #34d399;">application/json</span>
<span style="color: #60a5fa;">X-API-Key:</span> <span style="color: #a78bfa;">YOUR_GLOBAL_API_KEY</span>
<span style="color: #60a5fa;">Authorization:</span> <span style="color: #34d399;">Bearer YOUR_TOKEN</span>
<span style="color: #60a5fa;">Content-Type:</span> <span style="color: #34d399;">multipart/form-data</span></code></pre>
                            </div>
                        </div>

                        {{-- Request Body --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Body (FormData):
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">profile_picture:</span> <span style="color: #94a3b8;">(binary file)</span>
<span style="color: #94a3b8;">// File types: jpeg, jpg, png</span>
<span style="color: #94a3b8;">// Max size: 2MB</span></code></pre>
                            </div>
                        </div>

                        {{-- Response --}}
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-3">
                                <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b;">
                                    Response:
                                </h4>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                    200 OK
                                </span>
                            </div>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"success"</span>: <span style="color: #34d399;">true</span>,
  <span style="color: #60a5fa;">"message"</span>: <span style="color: #fbbf24;">"Gambar profil berjaya dikemaskini"</span>,
  <span style="color: #60a5fa;">"data"</span>: {
    <span style="color: #60a5fa;">"profile_picture"</span>: <span style="color: #fbbf24;">"profile_pictures/profile_10_1234567890.jpg"</span>,
    <span style="color: #60a5fa;">"profile_picture_url"</span>: <span style="color: #fbbf24;">"http://localhost:8000/storage/..."</span>
  }
}</code></pre>
                            </div>
                        </div>

                        {{-- Error Responses --}}
                        <div>
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Error Responses:
                            </h4>
                        <div class="space-y-2">
                                <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-sm">
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #991b1b;">
                                        <span style="font-weight: 600;">422 Validation Error</span> - File type tidak sah atau saiz melebihi 2MB
                                    </p>
                            </div>
                                <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-sm">
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #991b1b;">
                                        <span style="font-weight: 600;">401 Unauthorized</span> - Token tidak sah
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Delete Profile Picture Content --}}
                    <div id="content-delete-profile" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-sm bg-red-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-red-600" style="font-size: 24px;">delete</span>
                        </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-red-600 text-white text-[10px] font-semibold rounded-sm" style="font-family: Poppins, sans-serif;">DELETE</span>
                                        <code class="text-[14px] font-mono text-gray-900">/user/profile-picture</code>
                                    </div>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                        Padam gambar profil user
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-red-50 text-red-700 text-[10px] font-semibold rounded-sm border border-red-200" style="font-family: Poppins, sans-serif;">
                                PROTECTED
                            </span>
                        </div>

                        {{-- Request Headers --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Headers:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">Accept:</span> <span style="color: #34d399;">application/json</span>
<span style="color: #60a5fa;">X-API-Key:</span> <span style="color: #a78bfa;">YOUR_GLOBAL_API_KEY</span>
<span style="color: #60a5fa;">Authorization:</span> <span style="color: #34d399;">Bearer YOUR_TOKEN</span></code></pre>
                            </div>
                        </div>

                        {{-- Response --}}
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-3">
                                <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b;">
                                    Response:
                                </h4>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                    200 OK
                                </span>
                            </div>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"success"</span>: <span style="color: #34d399;">true</span>,
  <span style="color: #60a5fa;">"message"</span>: <span style="color: #fbbf24;">"Gambar profil berjaya dipadam"</span>
}</code></pre>
                            </div>
                        </div>

                        {{-- Error Responses --}}
                        <div>
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Error Responses:
                            </h4>
                        <div class="space-y-2">
                                <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-sm">
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #991b1b;">
                                        <span style="font-weight: 600;">404 Not Found</span> - Tiada gambar profil untuk dipadam
                                    </p>
                            </div>
                                <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-sm">
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #991b1b;">
                                        <span style="font-weight: 600;">401 Unauthorized</span> - Token tidak sah
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Get Programs Content --}}
                    <div id="content-programs" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-sm bg-purple-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-purple-600" style="font-size: 24px;">event_note</span>
                        </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-green-600 text-white text-[10px] font-semibold rounded-sm" style="font-family: Poppins, sans-serif;">GET</span>
                                        <code class="text-[14px] font-mono text-gray-900">/programs</code>
                                    </div>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                        Dapatkan senarai program yang ditugaskan kepada pemandu
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-red-50 text-red-700 text-[10px] font-semibold rounded-sm border border-red-200" style="font-family: Poppins, sans-serif;">
                                PROTECTED
                            </span>
                        </div>

                        {{-- Request Headers --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Headers:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">Accept:</span> <span style="color: #34d399;">application/json</span>
<span style="color: #60a5fa;">X-API-Key:</span> <span style="color: #a78bfa;">YOUR_GLOBAL_API_KEY</span>
<span style="color: #60a5fa;">Authorization:</span> <span style="color: #34d399;">Bearer YOUR_TOKEN</span></code></pre>
                            </div>
                        </div>

                        {{-- Query Parameters --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Query Parameters:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #94a3b8;">// Status filter (optional)</span>
<span style="color: #60a5fa;">status:</span> <span style="color: #fbbf24;">current</span> | <span style="color: #fbbf24;">ongoing</span> | <span style="color: #fbbf24;">past</span>

<span style="color: #94a3b8;">// Examples:</span>
<span style="color: #34d399;">GET</span> /api/programs                   <span style="color: #94a3b8;">// All programs</span>
<span style="color: #34d399;">GET</span> /api/programs?status=current    <span style="color: #94a3b8;">// Programs hari ini</span>
<span style="color: #34d399;">GET</span> /api/programs?status=ongoing    <span style="color: #94a3b8;">// Programs aktif</span>
<span style="color: #34d399;">GET</span> /api/programs?status=past       <span style="color: #94a3b8;">// Programs selesai</span></code></pre>
                            </div>
                        </div>

                        {{-- Response --}}
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-3">
                                <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b;">
                                    Response:
                                </h4>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                    200 OK
                                </span>
                            </div>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"success"</span>: <span style="color: #34d399;">true</span>,
  <span style="color: #60a5fa;">"data"</span>: [
    {
      <span style="color: #60a5fa;">"id"</span>: <span style="color: #fb923c;">8</span>,
      <span style="color: #60a5fa;">"nama_program"</span>: <span style="color: #fbbf24;">"Program Pembangunan Komuniti Kampung Sungai Rusa"</span>,
      <span style="color: #60a5fa;">"status"</span>: <span style="color: #fbbf24;">"aktif"</span>,
      <span style="color: #60a5fa;">"status_label"</span>: <span style="color: #fbbf24;">"Aktif"</span>,
      <span style="color: #60a5fa;">"tarikh_mula"</span>: <span style="color: #fbbf24;">"2025-10-01 08:00:00"</span>,
      <span style="color: #60a5fa;">"tarikh_mula_formatted"</span>: <span style="color: #fbbf24;">"01/10/2025"</span>,
      <span style="color: #60a5fa;">"tarikh_selesai"</span>: <span style="color: #fbbf24;">"2025-10-01 17:00:00"</span>,
      <span style="color: #60a5fa;">"tarikh_selesai_formatted"</span>: <span style="color: #fbbf24;">"01/10/2025"</span>,
      <span style="color: #60a5fa;">"lokasi_program"</span>: <span style="color: #fbbf24;">"Kampung Sungai Rusa, Sibu"</span>,
      <span style="color: #60a5fa;">"lokasi_lat"</span>: <span style="color: #fbbf24;">"2.3225"</span>,
      <span style="color: #60a5fa;">"lokasi_long"</span>: <span style="color: #fbbf24;">"111.8248"</span>,
      <span style="color: #60a5fa;">"jarak_anggaran"</span>: <span style="color: #fb923c;">45.50</span>,
      <span style="color: #60a5fa;">"penerangan"</span>: <span style="color: #fbbf24;">"Program pembangunan komuniti untuk petani getah"</span>,
      <span style="color: #60a5fa;">"permohonan_dari"</span>: {
        <span style="color: #60a5fa;">"id"</span>: <span style="color: #fb923c;">5</span>,
        <span style="color: #60a5fa;">"no_pekerja"</span>: <span style="color: #fbbf24;">"RS2020-0305"</span>,
        <span style="color: #60a5fa;">"nama_penuh"</span>: <span style="color: #fbbf24;">"Ahmad Bin Yusof"</span>,
        <span style="color: #60a5fa;">"no_telefon"</span>: <span style="color: #fbbf24;">"019-7654321"</span>
      },
      <span style="color: #60a5fa;">"pemandu"</span>: {
        <span style="color: #60a5fa;">"id"</span>: <span style="color: #fb923c;">15</span>,
        <span style="color: #60a5fa;">"no_pekerja"</span>: <span style="color: #fbbf24;">"RS2025-1001"</span>,
        <span style="color: #60a5fa;">"nama_penuh"</span>: <span style="color: #fbbf24;">"Adam Bin Abdullah"</span>,
        <span style="color: #60a5fa;">"no_telefon"</span>: <span style="color: #fbbf24;">"012-3456789"</span>
      },
      <span style="color: #60a5fa;">"kenderaan"</span>: {
        <span style="color: #60a5fa;">"id"</span>: <span style="color: #fb923c;">6</span>,
        <span style="color: #60a5fa;">"no_plat"</span>: <span style="color: #fbbf24;">"QKS 1234 K"</span>,
        <span style="color: #60a5fa;">"jenama"</span>: <span style="color: #fbbf24;">"Toyota"</span>,
        <span style="color: #60a5fa;">"model"</span>: <span style="color: #fbbf24;">"Hilux"</span>,
        <span style="color: #60a5fa;">"status"</span>: <span style="color: #fbbf24;">"tersedia"</span>,
        <span style="color: #60a5fa;">"latest_odometer"</span>: <span style="color: #fb923c;">10900</span>
      },
      <span style="color: #60a5fa;">"logs"</span>: {
        <span style="color: #60a5fa;">"total"</span>: <span style="color: #fb923c;">3</span>,
        <span style="color: #60a5fa;">"active"</span>: <span style="color: #fb923c;">1</span>,
        <span style="color: #60a5fa;">"completed"</span>: <span style="color: #fb923c;">2</span>
      },
      <span style="color: #60a5fa;">"created_at"</span>: <span style="color: #fbbf24;">"2025-09-28 10:00:00"</span>,
      <span style="color: #60a5fa;">"updated_at"</span>: <span style="color: #fbbf24;">"2025-09-30 14:30:00"</span>
    }
  ],
  <span style="color: #60a5fa;">"meta"</span>: {
    <span style="color: #60a5fa;">"total"</span>: <span style="color: #fb923c;">1</span>,
    <span style="color: #60a5fa;">"filter"</span>: <span style="color: #fbbf24;">"current"</span>
  }
}</code></pre>
                            </div>
                        </div>

                        {{-- Multi-Tenancy Info --}}
                        <div class="bg-indigo-50 border-l-4 border-indigo-400 p-4 rounded-sm">
                            <div class="flex gap-3">
                                <span class="material-symbols-outlined text-indigo-600" style="font-size: 20px;">shield</span>
                                <div>
                                    <h5 style="font-family: Poppins, sans-serif; font-size: 12px; font-weight: 600; color: #312e81; margin-bottom: 4px;">
                                        Multi-Tenancy Data Isolation
                                    </h5>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #4338ca; line-height: 1.6;">
                                        API ini automatically filter programs berdasarkan <code class="bg-indigo-200 px-2 py-0.5 rounded-sm text-indigo-900" style="font-family: 'Courier New', monospace; font-size: 10px;">pemandu_id</code> (staf_id dari user yang login) dan organizational scope user tersebut.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Get Program Detail Content --}}
                    <div id="content-program-detail" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-sm bg-purple-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-purple-600" style="font-size: 24px;">description</span>
                        </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-green-600 text-white text-[10px] font-semibold rounded-sm" style="font-family: Poppins, sans-serif;">GET</span>
                                        <code class="text-[14px] font-mono text-gray-900">/programs/<span style="color: #a78bfa;">{id}</span></code>
                                    </div>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                        Dapatkan detail program berdasarkan ID
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-red-50 text-red-700 text-[10px] font-semibold rounded-sm border border-red-200" style="font-family: Poppins, sans-serif;">
                                PROTECTED
                            </span>
                        </div>

                        {{-- Request Headers --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Headers:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">Accept:</span> <span style="color: #34d399;">application/json</span>
<span style="color: #60a5fa;">X-API-Key:</span> <span style="color: #a78bfa;">YOUR_GLOBAL_API_KEY</span>
<span style="color: #60a5fa;">Authorization:</span> <span style="color: #34d399;">Bearer YOUR_TOKEN</span></code></pre>
                            </div>
                        </div>

                        {{-- URL Parameters --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                URL Parameters:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">id</span> <span style="color: #94a3b8;">(required):</span> <span style="color: #fbbf24;">Program ID</span>

<span style="color: #94a3b8;">// Example:</span>
<span style="color: #34d399;">GET</span> /api/programs/<span style="color: #fb923c;">8</span></code></pre>
                            </div>
                        </div>

                        {{-- Response --}}
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-3">
                                <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b;">
                                    Response:
                                </h4>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                    200 OK
                                </span>
                            </div>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"success"</span>: <span style="color: #34d399;">true</span>,
  <span style="color: #60a5fa;">"data"</span>: {
    <span style="color: #60a5fa;">"id"</span>: <span style="color: #fb923c;">8</span>,
    <span style="color: #60a5fa;">"nama_program"</span>: <span style="color: #fbbf24;">"Program Pembangunan Komuniti..."</span>,
    <span style="color: #60a5fa;">"status"</span>: <span style="color: #fbbf24;">"aktif"</span>,
    <span style="color: #60a5fa;">"tarikh_mula"</span>: <span style="color: #fbbf24;">"2025-10-01 08:00:00"</span>,
    <span style="color: #60a5fa;">"tarikh_selesai"</span>: <span style="color: #fbbf24;">"2025-10-01 17:00:00"</span>,
    <span style="color: #60a5fa;">"lokasi_program"</span>: <span style="color: #fbbf24;">"Kampung Sungai Rusa, Sibu"</span>,
    <span style="color: #60a5fa;">"lokasi_lat"</span>: <span style="color: #fbbf24;">"2.3225"</span>,
    <span style="color: #60a5fa;">"lokasi_long"</span>: <span style="color: #fbbf24;">"111.8248"</span>,
    <span style="color: #60a5fa;">"permohonan_dari"</span>: { <span style="color: #94a3b8;">...</span> },
    <span style="color: #60a5fa;">"pemandu"</span>: { <span style="color: #94a3b8;">...</span> },
    <span style="color: #60a5fa;">"kenderaan"</span>: {
      <span style="color: #60a5fa;">"no_plat"</span>: <span style="color: #fbbf24;">"QKS 1234 K"</span>,
      <span style="color: #60a5fa;">"jenama"</span>: <span style="color: #fbbf24;">"Toyota"</span>,
      <span style="color: #60a5fa;">"model"</span>: <span style="color: #fbbf24;">"Hilux"</span>,
      <span style="color: #60a5fa;">"latest_odometer"</span>: <span style="color: #fb923c;">10900</span>
    }
  }
}</code></pre>
                            </div>
                        </div>

                        {{-- Error Responses --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Error Responses:
                            </h4>
                            <div class="space-y-2">
                                <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-sm">
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #991b1b;">
                                        <span style="font-weight: 600;">404 Not Found</span> - Program tidak dijumpai atau tidak diberikan akses
                                    </p>
                            </div>
                                <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-sm">
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #991b1b;">
                                        <span style="font-weight: 600;">401 Unauthorized</span> - Token tidak sah
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Date Fields Info --}}
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded-sm">
                            <div class="flex gap-3">
                                <span class="material-symbols-outlined text-blue-600" style="font-size: 20px;">calendar_month</span>
                                <div class="flex-1">
                                    <h5 style="font-family: Poppins, sans-serif; font-size: 12px; font-weight: 600; color: #1e40af; margin-bottom: 8px;">
                                        Date Fields Explanation
                                    </h5>
                                    <ul style="font-family: Poppins, sans-serif; font-size: 11px; color: #1e40af; line-height: 1.8; list-style: disc; margin-left: 16px;">
                                        <li><strong>tarikh_kelulusan:</strong> Tarikh program diluluskan oleh admin</li>
                                        <li><strong>tarikh_mula_aktif:</strong> Bila driver mula journey pertama</li>
                                        <li><strong>tarikh_sebenar_selesai:</strong> Bila program sebenarnya selesai</li>
                            </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Vehicle Odometer Info --}}
                        <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-sm">
                            <div class="flex gap-3">
                                <span class="material-symbols-outlined text-green-600" style="font-size: 20px;">speed</span>
                                <div class="flex-1">
                                    <h5 style="font-family: Poppins, sans-serif; font-size: 12px; font-weight: 600; color: #065f46; margin-bottom: 8px;">
                                        Vehicle Latest Odometer
                                    </h5>
                                    <ul style="font-family: Poppins, sans-serif; font-size: 11px; color: #047857; line-height: 1.8; list-style: disc; margin-left: 16px;">
                                        <li><strong>latest_odometer:</strong> Bacaan odometer terkini dari perjalanan selesai</li>
                                <li>Guna untuk display "Current Vehicle Odometer" di Start Journey screen</li>
                            </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Get Active Journey Content --}}
                    <div id="content-log-active" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-sm bg-orange-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-orange-600" style="font-size: 24px;">location_searching</span>
                        </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-green-600 text-white text-[10px] font-semibold rounded-sm" style="font-family: Poppins, sans-serif;">GET</span>
                                        <code class="text-[14px] font-mono text-gray-900">/log-pemandu/active</code>
                                    </div>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                        Semak sama ada driver mempunyai perjalanan aktif atau tidak
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-red-50 text-red-700 text-[10px] font-semibold rounded-sm border border-red-200" style="font-family: Poppins, sans-serif;">
                                PROTECTED
                            </span>
                        </div>

                        {{-- Request Headers --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Headers:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">X-API-Key:</span> <span style="color: #a78bfa;">YOUR_GLOBAL_API_KEY</span>
<span style="color: #60a5fa;">Authorization:</span> <span style="color: #34d399;">Bearer YOUR_USER_TOKEN</span>
<span style="color: #60a5fa;">Accept:</span> <span style="color: #34d399;">application/json</span></code></pre>
                            </div>
                        </div>

                        {{-- Response: Ada Journey Aktif --}}
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-3">
                                <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b;">
                                    Response (Ada Journey Aktif):
                                </h4>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                    200 OK
                                </span>
                            </div>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"success"</span>: <span style="color: #34d399;">true</span>,
  <span style="color: #60a5fa;">"data"</span>: {
    <span style="color: #60a5fa;">"id"</span>: <span style="color: #fb923c;">9</span>,
    <span style="color: #60a5fa;">"program_id"</span>: <span style="color: #fb923c;">9</span>,
    <span style="color: #60a5fa;">"program"</span>: {
      <span style="color: #60a5fa;">"id"</span>: <span style="color: #fb923c;">9</span>,
      <span style="color: #60a5fa;">"nama"</span>: <span style="color: #fbbf24;">"Program Jelajah Sarawak"</span>,
      <span style="color: #60a5fa;">"lokasi"</span>: <span style="color: #fbbf24;">"Dewan Suarah Sibu"</span>
    },
    <span style="color: #60a5fa;">"kenderaan"</span>: {
      <span style="color: #60a5fa;">"id"</span>: <span style="color: #fb923c;">6</span>,
      <span style="color: #60a5fa;">"no_plat"</span>: <span style="color: #fbbf24;">"QSR43"</span>,
      <span style="color: #60a5fa;">"jenama"</span>: <span style="color: #fbbf24;">"Toyota"</span>,
      <span style="color: #60a5fa;">"model"</span>: <span style="color: #fbbf24;">"Alphard"</span>
    },
    <span style="color: #60a5fa;">"status"</span>: <span style="color: #fbbf24;">"dalam_perjalanan"</span>,
    <span style="color: #60a5fa;">"masa_keluar"</span>: <span style="color: #fbbf24;">"2025-10-01T07:22:21.000000Z"</span>,
    <span style="color: #60a5fa;">"odometer_keluar"</span>: <span style="color: #fb923c;">12345</span>
  }
}</code></pre>
                            </div>
                        </div>

                        {{-- Response: Tiada Journey --}}
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b;">
                                    Response (Tiada Journey Aktif):
                                </h4>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                    200 OK
                                </span>
                            </div>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"success"</span>: <span style="color: #34d399;">true</span>,
  <span style="color: #60a5fa;">"data"</span>: <span style="color: #94a3b8;">null</span>,
  <span style="color: #60a5fa;">"message"</span>: <span style="color: #fbbf24;">"Tiada perjalanan aktif"</span>
}</code></pre>
                            </div>
                        </div>
                    </div>

                    {{-- Get All Logs Content --}}
                    <div id="content-log-list" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-sm bg-teal-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-teal-600" style="font-size: 24px;">history</span>
                        </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-green-600 text-white text-[10px] font-semibold rounded-sm" style="font-family: Poppins, sans-serif;">GET</span>
                                        <code class="text-[14px] font-mono text-gray-900">/log-pemandu</code>
                                    </div>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                        Dapatkan senarai semua log perjalanan driver
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-red-50 text-red-700 text-[10px] font-semibold rounded-sm border border-red-200" style="font-family: Poppins, sans-serif;">
                                PROTECTED
                            </span>
                        </div>

                        {{-- Query Parameters --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Query Parameters (Optional):
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">status:</span> <span style="color: #fbbf24;">aktif</span> | <span style="color: #fbbf24;">selesai</span>

<span style="color: #94a3b8;">// Examples:</span>
<span style="color: #34d399;">GET</span> /api/log-pemandu               <span style="color: #94a3b8;">// All logs</span>
<span style="color: #34d399;">GET</span> /api/log-pemandu?status=aktif   <span style="color: #94a3b8;">// Active only</span>
<span style="color: #34d399;">GET</span> /api/log-pemandu?status=selesai <span style="color: #94a3b8;">// Completed only</span></code></pre>
                            </div>
                        </div>

                        {{-- Response --}}
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b;">
                                    Response:
                                </h4>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                    200 OK
                                </span>
                            </div>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"success"</span>: <span style="color: #34d399;">true</span>,
  <span style="color: #60a5fa;">"data"</span>: [
    {
      <span style="color: #60a5fa;">"id"</span>: <span style="color: #fb923c;">9</span>,
      <span style="color: #60a5fa;">"program"</span>: {
        <span style="color: #60a5fa;">"nama"</span>: <span style="color: #fbbf24;">"Program Jelajah Sarawak"</span>
      },
      <span style="color: #60a5fa;">"kenderaan"</span>: {
        <span style="color: #60a5fa;">"no_plat"</span>: <span style="color: #fbbf24;">"QSR43"</span>,
        <span style="color: #60a5fa;">"jenama"</span>: <span style="color: #fbbf24;">"Toyota"</span>
      },
      <span style="color: #60a5fa;">"status"</span>: <span style="color: #fbbf24;">"selesai"</span>,
      <span style="color: #60a5fa;">"masa_keluar"</span>: <span style="color: #fbbf24;">"2025-10-01T07:22:21.000000Z"</span>,
      <span style="color: #60a5fa;">"masa_masuk"</span>: <span style="color: #fbbf24;">"2025-10-01T07:30:00.000000Z"</span>,
      <span style="color: #60a5fa;">"jarak"</span>: <span style="color: #fb923c;">100</span>
    }
  ],
  <span style="color: #60a5fa;">"meta"</span>: {
    <span style="color: #60a5fa;">"total"</span>: <span style="color: #fb923c;">1</span>,
    <span style="color: #60a5fa;">"filter"</span>: <span style="color: #fbbf24;">"all"</span>
  }
}</code></pre>
                            </div>
                        </div>
                    </div>

                    {{-- Start Journey Content --}}
                    <div id="content-start-journey" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-sm bg-orange-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-orange-600" style="font-size: 24px;">directions_car</span>
                        </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-blue-600 text-white text-[10px] font-semibold rounded-sm" style="font-family: Poppins, sans-serif;">POST</span>
                                        <code class="text-[14px] font-mono text-gray-900">/log-pemandu/start</code>
                                    </div>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                        Mulakan perjalanan baru (Start Journey / Check-Out)
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-red-50 text-red-700 text-[10px] font-semibold rounded-sm border border-red-200" style="font-family: Poppins, sans-serif;">
                                PROTECTED
                            </span>
                        </div>

                        {{-- Warning --}}
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-sm mb-6">
                            <div class="flex gap-3">
                                <span class="material-symbols-outlined text-yellow-600" style="font-size: 20px;">warning</span>
                                <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #92400e; line-height: 1.6;">
                                    <strong style="font-weight: 600;">Perhatian:</strong> Driver hanya boleh mempunyai SATU perjalanan aktif. Jika ada journey aktif, request akan ditolak.
                                </p>
                            </div>
                        </div>

                        {{-- Request Body --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Body:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"program_id"</span>: <span style="color: #fb923c;">9</span>,
  <span style="color: #60a5fa;">"kenderaan_id"</span>: <span style="color: #fb923c;">6</span>,
  <span style="color: #60a5fa;">"odometer_keluar"</span>: <span style="color: #fb923c;">12345</span>,
  <span style="color: #60a5fa;">"lokasi_keluar_lat"</span>: <span style="color: #fb923c;">2.310332</span>,
  <span style="color: #60a5fa;">"lokasi_keluar_long"</span>: <span style="color: #fb923c;">111.831561</span>,
  <span style="color: #60a5fa;">"catatan"</span>: <span style="color: #fbbf24;">"Start journey from office"</span>
}</code></pre>
                            </div>
                        </div>

                        {{-- Response --}}
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-3">
                                <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b;">
                                    Response:
                                </h4>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                    200 OK
                                </span>
                            </div>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"success"</span>: <span style="color: #34d399;">true</span>,
  <span style="color: #60a5fa;">"message"</span>: <span style="color: #fbbf24;">"Perjalanan dimulakan"</span>,
  <span style="color: #60a5fa;">"data"</span>: {
    <span style="color: #60a5fa;">"id"</span>: <span style="color: #fb923c;">9</span>,
    <span style="color: #60a5fa;">"program_id"</span>: <span style="color: #fb923c;">9</span>,
    <span style="color: #60a5fa;">"status"</span>: <span style="color: #fbbf24;">"dalam_perjalanan"</span>,
    <span style="color: #60a5fa;">"odometer_keluar"</span>: <span style="color: #fb923c;">12345</span>,
    <span style="color: #60a5fa;">"masa_keluar"</span>: <span style="color: #fbbf24;">"2025-10-01T07:22:21.000000Z"</span>
  }
}</code></pre>
                            </div>
                        </div>

                        {{-- Error Response --}}
                        <div>
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Error Response:
                            </h4>
                            <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-sm">
                                <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #991b1b;">
                                    <span style="font-weight: 600;">400 Bad Request</span> - Anda masih mempunyai perjalanan aktif
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- End Journey Content --}}
                    <div id="content-end-journey" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-sm bg-teal-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-teal-600" style="font-size: 24px;">where_to_vote</span>
                        </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-yellow-600 text-white text-[10px] font-semibold rounded-sm" style="font-family: Poppins, sans-serif;">PUT</span>
                                        <code class="text-[14px] font-mono text-gray-900">/log-pemandu/{id}/end</code>
                                    </div>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                        Tamatkan perjalanan aktif (End Journey / Check-In)
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-red-50 text-red-700 text-[10px] font-semibold rounded-sm border border-red-200" style="font-family: Poppins, sans-serif;">
                                PROTECTED
                            </span>
                        </div>

                        {{-- URL Parameters --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                URL Parameters:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">{id}</span> - <span style="color: #94a3b8;">ID log perjalanan yang aktif</span></code></pre>
                            </div>
                        </div>

                        {{-- Request Body --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Body:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"odometer_masuk"</span>: <span style="color: #fb923c;">12445</span>,
  <span style="color: #60a5fa;">"lokasi_checkin_lat"</span>: <span style="color: #fb923c;">2.310332</span>,
  <span style="color: #60a5fa;">"lokasi_checkin_long"</span>: <span style="color: #fb923c;">111.831561</span>,
  <span style="color: #60a5fa;">"catatan"</span>: <span style="color: #fbbf24;">"Journey completed"</span>,
  <span style="color: #60a5fa;">"liter_minyak"</span>: <span style="color: #fb923c;">45.5</span>,
  <span style="color: #60a5fa;">"kos_minyak"</span>: <span style="color: #fb923c;">120.50</span>,
  <span style="color: #60a5fa;">"stesen_minyak"</span>: <span style="color: #fbbf24;">"Petronas Sibu"</span>
}</code></pre>
                            </div>
                        </div>

                        {{-- Response --}}
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-3">
                                <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b;">
                                    Response:
                                </h4>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                    200 OK
                                </span>
                            </div>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"success"</span>: <span style="color: #34d399;">true</span>,
  <span style="color: #60a5fa;">"message"</span>: <span style="color: #fbbf24;">"Perjalanan berjaya ditamatkan"</span>,
  <span style="color: #60a5fa;">"data"</span>: {
    <span style="color: #60a5fa;">"id"</span>: <span style="color: #fb923c;">9</span>,
    <span style="color: #60a5fa;">"status"</span>: <span style="color: #fbbf24;">"selesai"</span>,
    <span style="color: #60a5fa;">"odometer_keluar"</span>: <span style="color: #fb923c;">12345</span>,
    <span style="color: #60a5fa;">"odometer_masuk"</span>: <span style="color: #fb923c;">12445</span>,
    <span style="color: #60a5fa;">"jarak"</span>: <span style="color: #fb923c;">100</span>,
    <span style="color: #60a5fa;">"kos_minyak"</span>: <span style="color: #fbbf24;">"120.50"</span>
  }
}</code></pre>
                            </div>
                        </div>

                        {{-- Tips --}}
                        <div class="bg-teal-50 border-l-4 border-teal-400 p-4 rounded-sm">
                            <div class="flex gap-3">
                                <span class="material-symbols-outlined text-teal-600" style="font-size: 20px;">calculate</span>
                                <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #134e4a; line-height: 1.6;">
                                    <strong style="font-weight: 600;">Auto Calculate:</strong> Field <code class="bg-teal-100 px-1 rounded text-[10px]">jarak</code> akan dikira automatik: odometer_masuk - odometer_keluar.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Get Active Journey Content --}}
                    <div id="content-log-active" class="content-section hidden">
                        <div class="mb-4">
                            <span class="px-2 py-1 bg-green-600 text-white text-xs font-semibold rounded mr-2">GET</span>
                            <code class="text-lg font-mono text-gray-900">/log-pemandu/active</code>
                        </div>
                        <p class="text-gray-600 mb-6">Dapatkan perjalanan aktif untuk pemandu (jika ada)</p>

                        <h4 class="font-semibold text-gray-900 mb-3">Request Headers:</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>Accept: application/json
X-API-Key: YOUR_GLOBAL_API_KEY
Authorization: Bearer YOUR_SANCTUM_TOKEN</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Success Response (With Active Journey):</h4>
                        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg mb-4 overflow-x-auto">
<pre class="text-sm"><code>{
  "success": true,
  "data": {
    "id": 9,
    "program_id": 8,
    "pemandu_id": 15,
    "kenderaan_id": 6,
    "destinasi": "Kampung Sungai Rusa, Sibu",
    "odometer_keluar": 12345,
    "odometer_masuk": null,
    "jarak": 0,
    "masa_keluar": "2025-10-01T07:22:21.000000Z",
    "masa_masuk": null,
    "status": "dalam_perjalanan",
    "program": {
      "id": 8,
      "nama_program": "Program Peningkatan Kualiti Getah",
      "lokasi_program": "Kampung Sungai Rusa, Sibu",
      "jarak_anggaran": 45.50,
      "permohonan_dari": {
        "id": 5,
        "nama_penuh": "Ahmad Bin Yusof"
      }
    },
    "kenderaan": {
      "id": 6,
      "no_plat": "QKS 1234 K",
      "jenama": "Toyota",
      "model": "Hilux"
    }
  },
  "message": "Perjalanan aktif dijumpai"
}</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Success Response (No Active Journey):</h4>
                        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg mb-4 overflow-x-auto">
<pre class="text-sm"><code>{
  "success": true,
  "data": null,
  "message": "Tiada perjalanan aktif"
}</code></pre>
                        </div>

                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                            <p class="text-sm text-blue-800"><strong>💡 Tips:</strong> Gunakan endpoint ini untuk check sama ada pemandu ada active journey sebelum allow Start Journey baru.</p>
                        </div>
                    </div>

                    {{-- Get All Logs Content --}}
                    <div id="content-log-list" class="content-section hidden">
                        <div class="mb-4">
                            <span class="px-2 py-1 bg-green-600 text-white text-xs font-semibold rounded mr-2">GET</span>
                            <code class="text-lg font-mono text-gray-900">/log-pemandu</code>
                        </div>
                        <p class="text-gray-600 mb-6">Dapatkan senarai semua log perjalanan untuk pemandu</p>

                        <h4 class="font-semibold text-gray-900 mb-3">Request Headers:</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>Accept: application/json
X-API-Key: YOUR_GLOBAL_API_KEY
Authorization: Bearer YOUR_SANCTUM_TOKEN</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Query Parameters (Optional):</h4>
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <ul class="space-y-2 text-sm">
                                <li><code>status</code> - Filter by status: <code>selesai</code>, <code>aktif</code></li>
                            </ul>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Example Request:</h4>
                        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg mb-4 overflow-x-auto">
<pre class="text-sm"><code>GET /api/log-pemandu?status=selesai</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Success Response:</h4>
                        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg mb-4 overflow-x-auto">
<pre class="text-sm"><code>{
  "success": true,
  "data": [
    {
      "id": 15,
      "program_id": 8,
      "pemandu_id": 15,
      "kenderaan_id": 6,
      "destinasi": "Kampung Sungai Rusa, Sibu",
      "odometer_keluar": 12345,
      "odometer_masuk": 12445,
      "jarak": 100,
      "tarikh_perjalanan": "2025-10-01",
      "masa_keluar": "2025-10-01T07:22:21.000000Z",
      "masa_masuk": "2025-10-01T15:30:00.000000Z",
      "liter_minyak": "45.50",
      "kos_minyak": "120.50",
      "stesen_minyak": "Petronas Sibu",
      "catatan": "Journey completed successfully",
      "status": "selesai",
      "tarikh_perjalanan": "2025-10-01",
      "program": {
        "id": 8,
        "nama_program": "Program Peningkatan Kualiti Getah",
        "lokasi_program": "Kampung Sungai Rusa, Sibu",
        "permohonan_dari": {
          "id": 5,
          "nama_penuh": "Ahmad Bin Yusof"
        }
      },
      "kenderaan": {
        "id": 6,
        "no_plat": "QKS 1234 K",
        "jenama": "Toyota",
        "model": "Hilux"
      },
      "created_at": "2025-10-01T07:22:21.000000Z",
      "updated_at": "2025-10-01T15:30:00.000000Z"
    }
  ],
  "meta": {
    "total": 1,
    "filter": "selesai"
  }
}</code></pre>
                        </div>

                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                            <p class="text-sm text-blue-800"><strong>💡 Tips:</strong> Gunakan filter <code>status=selesai</code> untuk display completed journeys dalam Logs Screen.</p>
                        </div>
                    </div>

                    {{-- GET Tuntutan List Content --}}
                    <div id="content-tuntutan-list" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-sm bg-amber-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-amber-600" style="font-size: 24px;">receipt_long</span>
                        </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-green-600 text-white text-[10px] font-semibold rounded-sm" style="font-family: Poppins, sans-serif;">GET</span>
                                        <code class="text-[14px] font-mono text-gray-900">/tuntutan</code>
                                    </div>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                        Dapatkan senarai semua tuntutan untuk pemandu yang log masuk
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-red-50 text-red-700 text-[10px] font-semibold rounded-sm border border-red-200" style="font-family: Poppins, sans-serif;">
                                PROTECTED
                            </span>
                        </div>

                        {{-- Request Headers --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Headers:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">Accept:</span> <span style="color: #34d399;">application/json</span>
<span style="color: #60a5fa;">X-API-Key:</span> <span style="color: #a78bfa;">YOUR_GLOBAL_API_KEY</span>
<span style="color: #60a5fa;">Authorization:</span> <span style="color: #34d399;">Bearer YOUR_SANCTUM_TOKEN</span></code></pre>
                            </div>
                        </div>

                        {{-- Query Parameters --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Query Parameters (Optional):
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">status:</span> <span style="color: #fbbf24;">pending</span> | <span style="color: #fbbf24;">diluluskan</span> | <span style="color: #fbbf24;">ditolak</span> | <span style="color: #fbbf24;">digantung</span>

<span style="color: #94a3b8;">// Examples:</span>
<span style="color: #34d399;">GET</span> /api/tuntutan                 <span style="color: #94a3b8;">// All claims</span>
<span style="color: #34d399;">GET</span> /api/tuntutan?status=pending  <span style="color: #94a3b8;">// Pending only</span></code></pre>
                            </div>
                        </div>

                        {{-- Response --}}
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-3">
                                <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b;">
                                    Response:
                                </h4>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                    200 OK
                                </span>
                            </div>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"success"</span>: <span style="color: #34d399;">true</span>,
  <span style="color: #60a5fa;">"data"</span>: [
    {
      <span style="color: #60a5fa;">"id"</span>: <span style="color: #fb923c;">1</span>,
      <span style="color: #60a5fa;">"log_pemandu_id"</span>: <span style="color: #fb923c;">19</span>,
      <span style="color: #60a5fa;">"kategori"</span>: <span style="color: #fbbf24;">"fuel"</span>,
      <span style="color: #60a5fa;">"kategori_label"</span>: <span style="color: #fbbf24;">"Fuel"</span>,
      <span style="color: #60a5fa;">"jumlah"</span>: <span style="color: #fb923c;">30.00</span>,
      <span style="color: #60a5fa;">"keterangan"</span>: <span style="color: #fbbf24;">"Minyak untuk perjalanan ke Sibu"</span>,
      <span style="color: #60a5fa;">"resit"</span>: <span style="color: #fbbf24;">"http://localhost:8000/storage/claim_receipts/abc123.jpg"</span>,
      <span style="color: #60a5fa;">"status"</span>: <span style="color: #fbbf24;">"pending"</span>,
      <span style="color: #60a5fa;">"status_label"</span>: <span style="color: #fbbf24;">"Pending"</span>,
      <span style="color: #60a5fa;">"status_badge_color"</span>: <span style="color: #fbbf24;">"yellow"</span>,
      <span style="color: #60a5fa;">"can_edit"</span>: <span style="color: #ef4444;">false</span>,
      <span style="color: #60a5fa;">"program"</span>: {
        <span style="color: #60a5fa;">"id"</span>: <span style="color: #fb923c;">9</span>,
        <span style="color: #60a5fa;">"nama_program"</span>: <span style="color: #fbbf24;">"Program Jelajah Sarawak"</span>,
        <span style="color: #60a5fa;">"lokasi_program"</span>: <span style="color: #fbbf24;">"Sibu, Sarawak"</span>
      },
      <span style="color: #60a5fa;">"kenderaan"</span>: {
        <span style="color: #60a5fa;">"no_plat"</span>: <span style="color: #fbbf24;">"QSR43"</span>,
        <span style="color: #60a5fa;">"jenama"</span>: <span style="color: #fbbf24;">"Toyota"</span>,
        <span style="color: #60a5fa;">"model"</span>: <span style="color: #fbbf24;">"Hilux"</span>
      }
    }
  ]
}</code></pre>
                            </div>
                        </div>

                        {{-- Tips --}}
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-sm">
                            <div class="flex gap-3">
                                <span class="material-symbols-outlined text-blue-600" style="font-size: 20px;">lightbulb</span>
                                <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #1e40af; line-height: 1.6;">
                                    <strong style="font-weight: 600;">Tips:</strong> Gunakan query parameter <code class="bg-blue-200 px-2 py-0.5 rounded-sm text-blue-900" style="font-family: 'Courier New', monospace; font-size: 10px;">status=ditolak</code> untuk mendapatkan tuntutan yang boleh diedit semula.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- GET Tuntutan Detail Content --}}
                    <div id="content-tuntutan-detail" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-sm bg-amber-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-amber-600" style="font-size: 24px;">description</span>
                        </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-green-600 text-white text-[10px] font-semibold rounded-sm" style="font-family: Poppins, sans-serif;">GET</span>
                                        <code class="text-[14px] font-mono text-gray-900">/tuntutan/<span style="color: #a78bfa;">{id}</span></code>
                                    </div>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                        Dapatkan maklumat terperinci untuk satu tuntutan
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-red-50 text-red-700 text-[10px] font-semibold rounded-sm border border-red-200" style="font-family: Poppins, sans-serif;">
                                PROTECTED
                            </span>
                        </div>

                        {{-- Request Headers --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Headers:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">Accept:</span> <span style="color: #34d399;">application/json</span>
<span style="color: #60a5fa;">X-API-Key:</span> <span style="color: #a78bfa;">YOUR_GLOBAL_API_KEY</span>
<span style="color: #60a5fa;">Authorization:</span> <span style="color: #34d399;">Bearer YOUR_SANCTUM_TOKEN</span></code></pre>
                            </div>
                        </div>

                        {{-- URL Parameters --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                URL Parameters:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">id</span> <span style="color: #94a3b8;">(required):</span> <span style="color: #fbbf24;">Tuntutan ID</span>

<span style="color: #94a3b8;">// Example:</span>
<span style="color: #34d399;">GET</span> /api/tuntutan/<span style="color: #fb923c;">1</span></code></pre>
                            </div>
                        </div>

                        {{-- Response --}}
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-3">
                                <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b;">
                                    Response:
                                </h4>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                    200 OK
                                </span>
                            </div>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"success"</span>: <span style="color: #34d399;">true</span>,
  <span style="color: #60a5fa;">"data"</span>: {
    <span style="color: #60a5fa;">"id"</span>: <span style="color: #fb923c;">1</span>,
    <span style="color: #60a5fa;">"log_pemandu_id"</span>: <span style="color: #fb923c;">19</span>,
    <span style="color: #60a5fa;">"kategori"</span>: <span style="color: #fbbf24;">"fuel"</span>,
    <span style="color: #60a5fa;">"kategori_label"</span>: <span style="color: #fbbf24;">"Fuel"</span>,
    <span style="color: #60a5fa;">"jumlah"</span>: <span style="color: #fb923c;">30.00</span>,
    <span style="color: #60a5fa;">"keterangan"</span>: <span style="color: #fbbf24;">"Minyak untuk perjalanan ke Sibu"</span>,
    <span style="color: #60a5fa;">"resit"</span>: <span style="color: #fbbf24;">"http://localhost:8000/storage/..."</span>,
    <span style="color: #60a5fa;">"status"</span>: <span style="color: #fbbf24;">"pending"</span>,
    <span style="color: #60a5fa;">"status_label"</span>: <span style="color: #fbbf24;">"Pending"</span>,
    <span style="color: #60a5fa;">"can_edit"</span>: <span style="color: #ef4444;">false</span>,
    <span style="color: #60a5fa;">"program"</span>: {
      <span style="color: #60a5fa;">"nama_program"</span>: <span style="color: #fbbf24;">"Program Jelajah Sarawak"</span>,
      <span style="color: #60a5fa;">"lokasi_program"</span>: <span style="color: #fbbf24;">"Sibu, Sarawak"</span>
    },
    <span style="color: #60a5fa;">"kenderaan"</span>: {
      <span style="color: #60a5fa;">"no_plat"</span>: <span style="color: #fbbf24;">"QSR43"</span>,
      <span style="color: #60a5fa;">"jenama"</span>: <span style="color: #fbbf24;">"Toyota"</span>,
      <span style="color: #60a5fa;">"model"</span>: <span style="color: #fbbf24;">"Hilux"</span>
    }
  }
}</code></pre>
                            </div>
                        </div>

                        {{-- Error Responses --}}
                        <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-sm">
                            <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #991b1b;">
                                <span style="font-weight: 600;">404 Not Found</span> - Tuntutan tidak dijumpai atau anda tidak mempunyai akses
                            </p>
                        </div>
                    </div>

                    {{-- POST Create Tuntutan Content --}}
                    <div id="content-create-tuntutan" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-sm bg-amber-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-amber-600" style="font-size: 24px;">receipt_long</span>
                        </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-blue-600 text-white text-[10px] font-semibold rounded-sm" style="font-family: Poppins, sans-serif;">POST</span>
                                        <code class="text-[14px] font-mono text-gray-900">/tuntutan</code>
                                    </div>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                        Buat tuntutan baru untuk perjalanan yang telah selesai
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-red-50 text-red-700 text-[10px] font-semibold rounded-sm border border-red-200" style="font-family: Poppins, sans-serif;">
                                PROTECTED
                            </span>
                        </div>

                        {{-- Request Headers --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Headers:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">Content-Type:</span> <span style="color: #34d399;">multipart/form-data</span>
<span style="color: #60a5fa;">Accept:</span> <span style="color: #34d399;">application/json</span>
<span style="color: #60a5fa;">X-API-Key:</span> <span style="color: #a78bfa;">YOUR_GLOBAL_API_KEY</span>
<span style="color: #60a5fa;">Authorization:</span> <span style="color: #34d399;">Bearer YOUR_SANCTUM_TOKEN</span></code></pre>
                            </div>
                        </div>

                        {{-- Request Body --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Body (Form Data):
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">log_pemandu_id:</span> <span style="color: #fb923c;">19</span> <span style="color: #94a3b8;">(required, integer)</span>
<span style="color: #60a5fa;">kategori:</span> <span style="color: #fbbf24;">"fuel"</span> <span style="color: #94a3b8;">(required: tol|parking|f&b|fuel|car_maintenance|others)</span>
<span style="color: #60a5fa;">jumlah:</span> <span style="color: #fb923c;">30.00</span> <span style="color: #94a3b8;">(required, numeric)</span>
<span style="color: #60a5fa;">keterangan:</span> <span style="color: #fbbf24;">"Minyak untuk perjalanan"</span> <span style="color: #94a3b8;">(optional)</span>
<span style="color: #60a5fa;">resit:</span> <span style="color: #94a3b8;">[FILE] (optional, jpg|jpeg|png|pdf, max 5MB)</span></code></pre>
                            </div>
                        </div>

                        {{-- Response --}}
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-3">
                                <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b;">
                                    Response:
                                </h4>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                    200 OK
                                </span>
                            </div>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"success"</span>: <span style="color: #34d399;">true</span>,
  <span style="color: #60a5fa;">"message"</span>: <span style="color: #fbbf24;">"Tuntutan berjaya dibuat"</span>,
  <span style="color: #60a5fa;">"data"</span>: {
    <span style="color: #60a5fa;">"id"</span>: <span style="color: #fb923c;">1</span>,
    <span style="color: #60a5fa;">"log_pemandu_id"</span>: <span style="color: #fb923c;">19</span>,
    <span style="color: #60a5fa;">"kategori"</span>: <span style="color: #fbbf24;">"fuel"</span>,
    <span style="color: #60a5fa;">"jumlah"</span>: <span style="color: #fb923c;">30.00</span>,
    <span style="color: #60a5fa;">"status"</span>: <span style="color: #fbbf24;">"pending"</span>,
    <span style="color: #60a5fa;">"resit"</span>: <span style="color: #fbbf24;">"http://localhost:8000/storage/..."</span>
  }
}</code></pre>
                            </div>
                        </div>

                        {{-- Error Responses --}}
                        <div>
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Error Responses:
                            </h4>
                        <div class="space-y-2">
                                <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-sm">
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #991b1b;">
                                        <span style="font-weight: 600;">422 Validation Error</span> - Data tidak sah atau log_pemandu_id tidak wujud
                                    </p>
                            </div>
                                <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-sm">
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #991b1b;">
                                        <span style="font-weight: 600;">403 Forbidden</span> - Anda tidak mempunyai akses ke log perjalanan ini
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- PUT Update Tuntutan Content --}}
                    <div id="content-update-tuntutan" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-sm bg-amber-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-amber-600" style="font-size: 24px;">edit_note</span>
                        </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-yellow-600 text-white text-[10px] font-semibold rounded-sm" style="font-family: Poppins, sans-serif;">PUT</span>
                                        <code class="text-[14px] font-mono text-gray-900">/tuntutan/<span style="color: #a78bfa;">{id}</span></code>
                                    </div>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                        Kemaskini tuntutan yang ditolak (status: ditolak sahaja)
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-red-50 text-red-700 text-[10px] font-semibold rounded-sm border border-red-200" style="font-family: Poppins, sans-serif;">
                                PROTECTED
                            </span>
                        </div>

                        {{-- URL Parameters --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                URL Parameters:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">{id}</span> - <span style="color: #94a3b8;">ID tuntutan yang ingin dikemaskini</span></code></pre>
                            </div>
                        </div>

                        {{-- Request Headers --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Headers:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">Content-Type:</span> <span style="color: #34d399;">multipart/form-data</span>
<span style="color: #60a5fa;">Accept:</span> <span style="color: #34d399;">application/json</span>
<span style="color: #60a5fa;">X-API-Key:</span> <span style="color: #a78bfa;">YOUR_GLOBAL_API_KEY</span>
<span style="color: #60a5fa;">Authorization:</span> <span style="color: #34d399;">Bearer YOUR_SANCTUM_TOKEN</span></code></pre>
                            </div>
                        </div>

                        {{-- Request Body --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Body (Form Data):
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">kategori:</span> <span style="color: #fbbf24;">"fuel"</span> <span style="color: #94a3b8;">(required: tol|parking|f&b|accommodation|fuel|car_maintenance|others)</span>
<span style="color: #60a5fa;">jumlah:</span> <span style="color: #fb923c;">30.00</span> <span style="color: #94a3b8;">(required, numeric)</span>
<span style="color: #60a5fa;">keterangan:</span> <span style="color: #fbbf24;">"Minyak untuk perjalanan ke Sibu - Updated"</span> <span style="color: #94a3b8;">(optional)</span>
<span style="color: #60a5fa;">resit:</span> <span style="color: #94a3b8;">[FILE] (optional, jpg|jpeg|png|pdf, max 5MB)</span></code></pre>
                            </div>
                        </div>

                        {{-- Response --}}
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-3">
                                <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b;">
                                    Response:
                                </h4>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                    200 OK
                                </span>
                            </div>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"success"</span>: <span style="color: #34d399;">true</span>,
  <span style="color: #60a5fa;">"message"</span>: <span style="color: #fbbf24;">"Tuntutan berjaya dikemaskini dan status ditukar ke pending"</span>,
  <span style="color: #60a5fa;">"data"</span>: {
    <span style="color: #60a5fa;">"id"</span>: <span style="color: #fb923c;">1</span>,
    <span style="color: #60a5fa;">"log_pemandu_id"</span>: <span style="color: #fb923c;">19</span>,
    <span style="color: #60a5fa;">"kategori"</span>: <span style="color: #fbbf24;">"fuel"</span>,
    <span style="color: #60a5fa;">"jumlah"</span>: <span style="color: #fb923c;">30.00</span>,
    <span style="color: #60a5fa;">"status"</span>: <span style="color: #fbbf24;">"pending"</span>,
    <span style="color: #60a5fa;">"resit"</span>: <span style="color: #fbbf24;">"http://localhost:8000/storage/..."</span>
  }
}</code></pre>
                            </div>
                        </div>

                        {{-- Error Responses --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Error Responses:
                            </h4>
                        <div class="space-y-2">
                                <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-sm">
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #991b1b;">
                                        <span style="font-weight: 600;">404 Not Found</span> - Tuntutan tidak dijumpai atau anda tidak mempunyai akses
                                    </p>
                            </div>
                                <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-sm">
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #991b1b;">
                                        <span style="font-weight: 600;">400 Bad Request</span> - Hanya tuntutan dengan status "ditolak" boleh dikemaskini
                                    </p>
                            </div>
                                <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-sm">
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #991b1b;">
                                        <span style="font-weight: 600;">422 Validation Error</span> - Data tidak sah
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Warning --}}
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-sm">
                            <div class="flex gap-3">
                                <span class="material-symbols-outlined text-yellow-600" style="font-size: 20px;">warning</span>
                                <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #92400e; line-height: 1.6;">
                                    <strong style="font-weight: 600;">Penting:</strong> Selepas kemaskini, status akan automatik bertukar dari "ditolak" kepada "pending" dan menunggu kelulusan semula.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Report Vehicle Content --}}
                    <div id="content-report-vehicle" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-sm bg-cyan-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-cyan-600" style="font-size: 24px;">directions_car</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-green-600 text-white text-[10px] font-semibold rounded-sm" style="font-family: Poppins, sans-serif;">GET</span>
                                        <code class="text-[14px] font-mono text-gray-900">/reports/vehicle</code>
                                    </div>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                        Retrieve detailed vehicle usage reports
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-red-50 text-red-700 text-[10px] font-semibold rounded-sm border border-red-200" style="font-family: Poppins, sans-serif;">
                                PROTECTED
                            </span>
                        </div>

                        {{-- Request Headers --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Headers:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">X-API-Key:</span> <span style="color: #a78bfa;">YOUR_GLOBAL_API_KEY</span>
<span style="color: #60a5fa;">Authorization:</span> <span style="color: #34d399;">Bearer USER_SANCTUM_TOKEN</span>
<span style="color: #60a5fa;">Origin:</span> <span style="color: #fbbf24;">YOUR_ALLOWED_ORIGIN</span>
<span style="color: #60a5fa;">Content-Type:</span> <span style="color: #34d399;">application/json</span></code></pre>
                            </div>
                        </div>

                        {{-- Query Parameters --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Query Parameters (Optional):
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">date_from:</span> <span style="color: #fbbf24;">2025-10-01</span>  <span style="color: #94a3b8;">(Format: YYYY-MM-DD)</span>
<span style="color: #60a5fa;">date_to:</span> <span style="color: #fbbf24;">2025-10-31</span>    <span style="color: #94a3b8;">(Format: YYYY-MM-DD)</span></code></pre>
                            </div>
                        </div>

                        {{-- Response --}}
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-3">
                                <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b;">
                                    Response:
                                </h4>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                    200 OK
                                </span>
                            </div>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"success"</span>: <span style="color: #34d399;">true</span>,
  <span style="color: #60a5fa;">"data"</span>: [
    {
      <span style="color: #60a5fa;">"id"</span>: <span style="color: #fb923c;">9</span>,
      <span style="color: #60a5fa;">"no_plat"</span>: <span style="color: #fbbf24;">"QSR43"</span>,
      <span style="color: #60a5fa;">"program"</span>: <span style="color: #fbbf24;">"Program Jelajah Sarawak"</span>,
      <span style="color: #60a5fa;">"distance"</span>: <span style="color: #fb923c;">100</span>,
      <span style="color: #60a5fa;">"vehicle_details"</span>: {
        <span style="color: #60a5fa;">"jenama"</span>: <span style="color: #fbbf24;">"Toyota"</span>,
        <span style="color: #60a5fa;">"model"</span>: <span style="color: #fbbf24;">"Alphard"</span>,
        <span style="color: #60a5fa;">"jenis_bahan_api"</span>: <span style="color: #fbbf24;">"petrol"</span>
      },
      <span style="color: #60a5fa;">"journey_details"</span>: {
        <span style="color: #60a5fa;">"odometer_keluar"</span>: <span style="color: #fb923c;">12345</span>,
        <span style="color: #60a5fa;">"odometer_masuk"</span>: <span style="color: #fb923c;">12445</span>,
        <span style="color: #60a5fa;">"jarak"</span>: <span style="color: #fb923c;">100</span>,
        <span style="color: #60a5fa;">"status"</span>: <span style="color: #fbbf24;">"selesai"</span>
      },
      <span style="color: #60a5fa;">"fuel_details"</span>: {
        <span style="color: #60a5fa;">"kos_minyak"</span>: <span style="color: #fb923c;">120.50</span>,
        <span style="color: #60a5fa;">"liter_minyak"</span>: <span style="color: #fb923c;">45.50</span>,
        <span style="color: #60a5fa;">"stesen_minyak"</span>: <span style="color: #fbbf24;">"Petronas Sibu"</span>
            }
        }
    ]
}</code></pre>
                            </div>
                        </div>

                        {{-- Tips --}}
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-sm">
                            <div class="flex gap-3">
                                <span class="material-symbols-outlined text-blue-600" style="font-size: 20px;">lightbulb</span>
                                <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #1e40af; line-height: 1.6;">
                                    <strong style="font-weight: 600;">Tips:</strong> Use date filters to get reports for specific periods. Images URLs are absolute paths.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Report Cost Content --}}
                    <div id="content-report-cost" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-sm bg-emerald-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-emerald-600" style="font-size: 24px;">attach_money</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-green-600 text-white text-[10px] font-semibold rounded-sm" style="font-family: Poppins, sans-serif;">GET</span>
                                        <code class="text-[14px] font-mono text-gray-900">/reports/cost</code>
                                    </div>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                        Retrieve fuel cost reports with detailed breakdown
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-red-50 text-red-700 text-[10px] font-semibold rounded-sm border border-red-200" style="font-family: Poppins, sans-serif;">
                                PROTECTED
                            </span>
                        </div>

                        {{-- Request Headers --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Headers:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">X-API-Key:</span> <span style="color: #a78bfa;">YOUR_GLOBAL_API_KEY</span>
<span style="color: #60a5fa;">Authorization:</span> <span style="color: #34d399;">Bearer USER_SANCTUM_TOKEN</span>
<span style="color: #60a5fa;">Origin:</span> <span style="color: #fbbf24;">YOUR_ALLOWED_ORIGIN</span>
<span style="color: #60a5fa;">Content-Type:</span> <span style="color: #34d399;">application/json</span></code></pre>
                            </div>
                        </div>

                        {{-- Query Parameters --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Query Parameters (Optional):
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">date_from:</span> <span style="color: #fbbf24;">2025-10-01</span>  <span style="color: #94a3b8;">(Format: YYYY-MM-DD)</span>
<span style="color: #60a5fa;">date_to:</span> <span style="color: #fbbf24;">2025-10-31</span>    <span style="color: #94a3b8;">(Format: YYYY-MM-DD)</span></code></pre>
                            </div>
                        </div>

                        {{-- Response --}}
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-3">
                                <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b;">
                                    Response:
                                </h4>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                    200 OK
                                </span>
                            </div>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"success"</span>: <span style="color: #34d399;">true</span>,
  <span style="color: #60a5fa;">"data"</span>: [
    {
      <span style="color: #60a5fa;">"id"</span>: <span style="color: #fb923c;">9</span>,
      <span style="color: #60a5fa;">"vehicle"</span>: <span style="color: #fbbf24;">"QSR43"</span>,
      <span style="color: #60a5fa;">"program"</span>: <span style="color: #fbbf24;">"Program Jelajah Sarawak"</span>,
      <span style="color: #60a5fa;">"amount"</span>: <span style="color: #fb923c;">120.50</span>,
      <span style="color: #60a5fa;">"liters"</span>: <span style="color: #fb923c;">45.50</span>,
      <span style="color: #60a5fa;">"station"</span>: <span style="color: #fbbf24;">"Petronas Sibu"</span>,
      <span style="color: #60a5fa;">"fuel_details"</span>: {
        <span style="color: #60a5fa;">"kos_minyak"</span>: <span style="color: #fb923c;">120.50</span>,
        <span style="color: #60a5fa;">"liter_minyak"</span>: <span style="color: #fb923c;">45.50</span>,
        <span style="color: #60a5fa;">"stesen_minyak"</span>: <span style="color: #fbbf24;">"Petronas Sibu"</span>
      }
    }
  ],
  <span style="color: #60a5fa;">"total_cost"</span>: <span style="color: #fb923c;">520.50</span>,
  <span style="color: #60a5fa;">"total_liters"</span>: <span style="color: #fb923c;">185.50</span>
}</code></pre>
                            </div>
                        </div>

                        {{-- Tips --}}
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-sm">
                            <div class="flex gap-3">
                                <span class="material-symbols-outlined text-blue-600" style="font-size: 20px;">lightbulb</span>
                                <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #1e40af; line-height: 1.6;">
                                    <strong style="font-weight: 600;">Tips:</strong> Response includes <code class="bg-blue-200 px-2 py-0.5 rounded-sm text-blue-900" style="font-family: 'Courier New', monospace; font-size: 10px;">total_cost</code> and <code class="bg-blue-200 px-2 py-0.5 rounded-sm text-blue-900" style="font-family: 'Courier New', monospace; font-size: 10px;">total_liters</code> for easy summary calculation.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Report Driver Content --}}
                    <div id="content-report-driver" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-sm bg-indigo-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-indigo-600" style="font-size: 24px;">person</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-green-600 text-white text-[10px] font-semibold rounded-sm" style="font-family: Poppins, sans-serif;">GET</span>
                                        <code class="text-[14px] font-mono text-gray-900">/reports/driver</code>
                                    </div>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                        Retrieve driver statistics including all trips
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-red-50 text-red-700 text-[10px] font-semibold rounded-sm border border-red-200" style="font-family: Poppins, sans-serif;">
                                PROTECTED
                            </span>
                        </div>

                        {{-- Request Headers --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Headers:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">X-API-Key:</span> <span style="color: #a78bfa;">YOUR_GLOBAL_API_KEY</span>
<span style="color: #60a5fa;">Authorization:</span> <span style="color: #34d399;">Bearer USER_SANCTUM_TOKEN</span>
<span style="color: #60a5fa;">Origin:</span> <span style="color: #fbbf24;">YOUR_ALLOWED_ORIGIN</span>
<span style="color: #60a5fa;">Content-Type:</span> <span style="color: #34d399;">application/json</span></code></pre>
                            </div>
                        </div>

                        {{-- Query Parameters --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Query Parameters (Optional):
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">date_from:</span> <span style="color: #fbbf24;">2025-10-01</span>  <span style="color: #94a3b8;">(Format: YYYY-MM-DD)</span>
<span style="color: #60a5fa;">date_to:</span> <span style="color: #fbbf24;">2025-10-31</span>    <span style="color: #94a3b8;">(Format: YYYY-MM-DD)</span></code></pre>
                            </div>
                        </div>

                        {{-- Response --}}
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-3">
                                <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b;">
                                    Response:
                                </h4>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                    200 OK
                                </span>
                            </div>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"success"</span>: <span style="color: #34d399;">true</span>,
  <span style="color: #60a5fa;">"data"</span>: [
    {
      <span style="color: #60a5fa;">"program_name"</span>: <span style="color: #fbbf24;">"Program Jelajah Sarawak"</span>,
      <span style="color: #60a5fa;">"total_trips"</span>: <span style="color: #fb923c;">11</span>,
      <span style="color: #60a5fa;">"completed_count"</span>: <span style="color: #fb923c;">11</span>,
      <span style="color: #60a5fa;">"total_distance"</span>: <span style="color: #fb923c;">943</span>,
      <span style="color: #60a5fa;">"total_fuel_cost"</span>: <span style="color: #fb923c;">520.50</span>,
      <span style="color: #60a5fa;">"trips"</span>: [
        {
          <span style="color: #60a5fa;">"id"</span>: <span style="color: #fb923c;">9</span>,
          <span style="color: #60a5fa;">"masa_keluar"</span>: <span style="color: #fbbf24;">"2025-10-02T07:22:21.000000Z"</span>,
          <span style="color: #60a5fa;">"masa_masuk"</span>: <span style="color: #fbbf24;">"2025-10-02T07:22:52.000000Z"</span>,
          <span style="color: #60a5fa;">"jarak"</span>: <span style="color: #fb923c;">100</span>,
          <span style="color: #60a5fa;">"kenderaan"</span>: <span style="color: #fbbf24;">"QSR43"</span>,
          <span style="color: #60a5fa;">"kos_minyak"</span>: <span style="color: #fb923c;">120.50</span>
                }
            ]
        }
    ],
  <span style="color: #60a5fa;">"summary"</span>: {
    <span style="color: #60a5fa;">"total_programs"</span>: <span style="color: #fb923c;">1</span>,
    <span style="color: #60a5fa;">"total_trips"</span>: <span style="color: #fb923c;">11</span>,
    <span style="color: #60a5fa;">"total_distance"</span>: <span style="color: #fb923c;">943</span>,
    <span style="color: #60a5fa;">"total_fuel_cost"</span>: <span style="color: #fb923c;">520.50</span>
    }
}</code></pre>
                            </div>
                        </div>

                        {{-- Tips --}}
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-sm">
                            <div class="flex gap-3">
                                <span class="material-symbols-outlined text-blue-600" style="font-size: 20px;">lightbulb</span>
                                <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #1e40af; line-height: 1.6;">
                                    <strong style="font-weight: 600;">Tips:</strong> Response includes detailed <code class="bg-blue-200 px-2 py-0.5 rounded-sm text-blue-900" style="font-family: 'Courier New', monospace; font-size: 10px;">trips</code> array showing ALL check-in/check-out with timestamp and fuel data. Summary section provides quick overview.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Dashboard Statistics Content --}}
                    <div id="content-dashboard-stats" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-sm bg-violet-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-violet-600" style="font-size: 24px;">bar_chart</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-green-600 text-white text-[10px] font-semibold rounded-sm" style="font-family: Poppins, sans-serif;">GET</span>
                                        <code class="text-[14px] font-mono text-gray-900">/dashboard/statistics</code>
                                    </div>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                        Retrieve dashboard statistics for authenticated driver
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-red-50 text-red-700 text-[10px] font-semibold rounded-sm border border-red-200" style="font-family: Poppins, sans-serif;">
                                PROTECTED
                            </span>
                        </div>

                        {{-- Request Headers --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Headers:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">X-API-Key:</span> <span style="color: #a78bfa;">YOUR_GLOBAL_API_KEY</span>
<span style="color: #60a5fa;">Authorization:</span> <span style="color: #34d399;">Bearer USER_SANCTUM_TOKEN</span>
<span style="color: #60a5fa;">Origin:</span> <span style="color: #fbbf24;">YOUR_ALLOWED_ORIGIN</span>
<span style="color: #60a5fa;">Content-Type:</span> <span style="color: #34d399;">application/json</span></code></pre>
                            </div>
                        </div>

                        {{-- Response --}}
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-3">
                                <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b;">
                                    Response:
                                </h4>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                    200 OK
                                </span>
                            </div>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"success"</span>: <span style="color: #34d399;">true</span>,
  <span style="color: #60a5fa;">"data"</span>: {
    <span style="color: #60a5fa;">"total_trips"</span>: <span style="color: #fb923c;">11</span>,
    <span style="color: #60a5fa;">"total_trips_change"</span>: <span style="color: #fb923c;">100.0</span>,
    <span style="color: #60a5fa;">"total_distance"</span>: <span style="color: #fb923c;">943.0</span>,
    <span style="color: #60a5fa;">"total_distance_change"</span>: <span style="color: #fb923c;">100.0</span>,
    <span style="color: #60a5fa;">"fuel_cost"</span>: <span style="color: #fb923c;">550.50</span>,
    <span style="color: #60a5fa;">"fuel_cost_change"</span>: <span style="color: #fb923c;">100.0</span>,
    <span style="color: #60a5fa;">"maintenance_cost"</span>: <span style="color: #fb923c;">300.00</span>,
    <span style="color: #60a5fa;">"maintenance_change"</span>: <span style="color: #fb923c;">100.0</span>,
    <span style="color: #60a5fa;">"parking_cost"</span>: <span style="color: #fb923c;">0.00</span>,
    <span style="color: #60a5fa;">"current_month"</span>: <span style="color: #fbbf24;">"October 2025"</span>,
    <span style="color: #60a5fa;">"last_month"</span>: <span style="color: #fbbf24;">"September 2025"</span>
    }
}</code></pre>
                            </div>
                        </div>

                        {{-- Response Fields Info --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Response Fields:
                            </h4>
                            <div class="bg-gray-50 rounded-sm p-4 border border-gray-200">
                                <ul style="font-family: Poppins, sans-serif; font-size: 11px; color: #475569; line-height: 1.8; list-style: disc; margin-left: 16px;">
                                <li><strong>total_trips:</strong> Total completed trips in current month</li>
                                    <li><strong>total_trips_change:</strong> Percentage change from last month</li>
                                    <li><strong>fuel_cost:</strong> Total fuel cost in RM (Journey + Claims)</li>
                                    <li><strong>maintenance_cost:</strong> Total maintenance claims (pending + approved)</li>
                                <li><strong>current_month:</strong> Current month name (e.g., "October 2025")</li>
                            </ul>
                            </div>
                        </div>

                        {{-- Tips --}}
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-sm">
                            <div class="flex gap-3">
                                <span class="material-symbols-outlined text-blue-600" style="font-size: 20px;">lightbulb</span>
                                <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #1e40af; line-height: 1.6;">
                                    <strong style="font-weight: 600;">Tips:</strong> Perfect for Overview/Dashboard screens. Percentage changes help visualize trends. All claim categories include both <strong>pending</strong> and <strong>approved</strong> claims.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- App Information Content --}}
                    <div id="content-app-info" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-sm bg-sky-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-sky-600" style="font-size: 24px;">info</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-green-600 text-white text-[10px] font-semibold rounded-sm" style="font-family: Poppins, sans-serif;">GET</span>
                                        <code class="text-[14px] font-mono text-gray-900">/app-info</code>
                                    </div>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                        Get application information including version and contact
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-green-50 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                PUBLIC
                            </span>
                        </div>

                        {{-- Request Headers --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Headers:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">X-API-Key:</span> <span style="color: #a78bfa;">YOUR_GLOBAL_API_KEY</span>
<span style="color: #60a5fa;">Origin:</span> <span style="color: #fbbf24;">http://localhost</span></code></pre>
                            </div>
                        </div>

                        {{-- Response --}}
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-3">
                                <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b;">
                                    Response:
                                </h4>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                    200 OK
                                </span>
                            </div>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;">{
  <span style="color: #60a5fa;">"success"</span>: <span style="color: #34d399;">true</span>,
  <span style="color: #60a5fa;">"data"</span>: {
    <span style="color: #60a5fa;">"app_name"</span>: <span style="color: #fbbf24;">"JARA Mobile App"</span>,
    <span style="color: #60a5fa;">"system_full_name"</span>: <span style="color: #fbbf24;">"JARA (Jejak Aset & Rekod Automotif)"</span>,
    <span style="color: #60a5fa;">"version"</span>: <span style="color: #fbbf24;">"1.6.0"</span>,
    <span style="color: #60a5fa;">"build_number"</span>: <span style="color: #fb923c;">160</span>,
    <span style="color: #60a5fa;">"release_date"</span>: <span style="color: #fbbf24;">"01 October 2025"</span>,
    <span style="color: #60a5fa;">"organization"</span>: <span style="color: #fbbf24;">"RISDA"</span>,
    <span style="color: #60a5fa;">"department"</span>: <span style="color: #fbbf24;">"RISDA Bahagian Sibu"</span>,
    <span style="color: #60a5fa;">"phone"</span>: [<span style="color: #fbbf24;">"084-344712"</span>, <span style="color: #fbbf24;">"084-344713"</span>],
    <span style="color: #60a5fa;">"email"</span>: <span style="color: #fbbf24;">"prbsibu@risda.gov.my"</span>,
    <span style="color: #60a5fa;">"backend_url"</span>: <span style="color: #fbbf24;">"https://jara.my"</span>,
    <span style="color: #60a5fa;">"supported_platforms"</span>: [<span style="color: #fbbf24;">"Android"</span>, <span style="color: #fbbf24;">"iOS"</span>],
    <span style="color: #60a5fa;">"copyright"</span>: <span style="color: #fbbf24;">"© 1973 - 2025 RISDA"</span>
  }
}</code></pre>
                            </div>
                        </div>

                        {{-- Tips --}}
                        <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-sm">
                            <div class="flex gap-3">
                                <span class="material-symbols-outlined text-green-600" style="font-size: 20px;">check_circle</span>
                                <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #065f46; line-height: 1.6;">
                                    <strong style="font-weight: 600;">No Auth Required:</strong> Version and release date auto-synced from Nota Keluaran. Copyright year is dynamic.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Privacy Policy Content --}}
                    <div id="content-privacy-policy" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-sm bg-slate-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-slate-600" style="font-size: 24px;">policy</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-green-600 text-white text-[10px] font-semibold rounded-sm" style="font-family: Poppins, sans-serif;">GET</span>
                                        <code class="text-[14px] font-mono text-gray-900">/privacy-policy</code>
                                    </div>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                        Get privacy policy content for the mobile app
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-green-50 text-green-700 text-[10px] font-semibold rounded-sm border border-green-200" style="font-family: Poppins, sans-serif;">
                                PUBLIC
                            </span>
                        </div>

                        {{-- Request Headers --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Request Headers:
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">X-API-Key:</span> <span style="color: #a78bfa;">YOUR_GLOBAL_API_KEY</span>
<span style="color: #60a5fa;">Origin:</span> <span style="color: #fbbf24;">http://localhost</span></code></pre>
                            </div>
                        </div>

                        {{-- Response Structure Info --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Response Structure:
                            </h4>
                            <div class="bg-gray-50 rounded-sm p-4 border border-gray-200">
                                <ul style="font-family: Poppins, sans-serif; font-size: 11px; color: #475569; line-height: 1.8; list-style: disc; margin-left: 16px;">
                                    <li><strong>title:</strong> Privacy policy title</li>
                                    <li><strong>effective_date:</strong> Date when policy took effect</li>
                                    <li><strong>last_updated:</strong> Last modification date</li>
                                    <li><strong>sections:</strong> Array of policy sections with heading, content, list, footer</li>
                                    <li><strong>acknowledgment:</strong> User acknowledgment text</li>
                                    <li><strong>contact:</strong> Contact information for privacy queries</li>
                                </ul>
                            </div>
                        </div>

                        {{-- Tips --}}
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-sm">
                            <div class="flex gap-3">
                                <span class="material-symbols-outlined text-blue-600" style="font-size: 20px;">security</span>
                                <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #1e40af; line-height: 1.6;">
                                    <strong style="font-weight: 600;">Privacy First:</strong> Complete privacy policy document returned in structured JSON format for display in mobile app.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Chart Overview Content --}}
                    <div id="content-chart-overview" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-sm bg-pink-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-pink-600" style="font-size: 24px;">analytics</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-green-600 text-white text-[10px] font-semibold rounded-sm" style="font-family: Poppins, sans-serif;">GET</span>
                                        <code class="text-[14px] font-mono text-gray-900">/chart/overview</code>
                                    </div>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                        Get overview chart data showing Fuel Cost vs Total Claims
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-red-50 text-red-700 text-[10px] font-semibold rounded-sm border border-red-200" style="font-family: Poppins, sans-serif;">
                                PROTECTED
                            </span>
                        </div>

                        {{-- Query Parameters --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Query Parameters (Optional):
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">period</span>: <span style="color: #fbbf24;">"1month"</span> <span style="color: #94a3b8;">or</span> <span style="color: #fbbf24;">"6months"</span> <span style="color: #94a3b8;">(default)</span></code></pre>
                            </div>
                        </div>

                        {{-- Response Structure --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Response Structure:
                            </h4>
                            <div class="bg-gray-50 rounded-sm p-4 border border-gray-200">
                                <ul style="font-family: Poppins, sans-serif; font-size: 11px; color: #475569; line-height: 1.8; list-style: disc; margin-left: 16px;">
                                    <li><strong>chart_data:</strong> Array of data points</li>
                                    <li><strong>period:</strong> Date period (e.g., "2025-10")</li>
                                    <li><strong>label:</strong> Human-readable label (e.g., "Oct 2025")</li>
                                    <li><strong>fuel_cost:</strong> Total fuel cost (RM)</li>
                                    <li><strong>claims:</strong> Total claims amount (RM)</li>
                                </ul>
                            </div>
                        </div>

                        {{-- Tips --}}
                        <div class="bg-pink-50 border-l-4 border-pink-400 p-4 rounded-sm">
                            <div class="flex gap-3">
                                <span class="material-symbols-outlined text-pink-600" style="font-size: 20px;">timeline</span>
                                <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #831843; line-height: 1.6;">
                                    <strong style="font-weight: 600;">Chart Data:</strong> Use for Overview tab analytics. Compare fuel costs vs total claims over time.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Chart Do Activity Content --}}
                    <div id="content-chart-do-activity" class="content-section hidden bg-white rounded-sm border shadow-sm p-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-sm bg-lime-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-lime-600" style="font-size: 24px;">show_chart</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-green-600 text-white text-[10px] font-semibold rounded-sm" style="font-family: Poppins, sans-serif;">GET</span>
                                        <code class="text-[14px] font-mono text-gray-900">/chart/do-activity</code>
                                    </div>
                                    <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #64748b;">
                                        Get Do tab chart data showing Start vs End Journey counts
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-red-50 text-red-700 text-[10px] font-semibold rounded-sm border border-red-200" style="font-family: Poppins, sans-serif;">
                                PROTECTED
                            </span>
                        </div>

                        {{-- Query Parameters --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Query Parameters (Optional):
                            </h4>
                            <div class="bg-slate-900 rounded-sm p-4">
                                <pre class="text-sm overflow-x-auto"><code style="font-family: 'Courier New', monospace; font-size: 11px;"><span style="color: #60a5fa;">period</span>: <span style="color: #fbbf24;">"1month"</span> <span style="color: #94a3b8;">or</span> <span style="color: #fbbf24;">"6months"</span> <span style="color: #94a3b8;">(default)</span></code></pre>
                            </div>
                        </div>

                        {{-- Response Structure --}}
                        <div class="mb-6">
                            <h4 style="font-family: Poppins, sans-serif; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">
                                Response Structure:
                            </h4>
                            <div class="bg-gray-50 rounded-sm p-4 border border-gray-200">
                                <ul style="font-family: Poppins, sans-serif; font-size: 11px; color: #475569; line-height: 1.8; list-style: disc; margin-left: 16px;">
                                    <li><strong>chart_data:</strong> Array of activity data points</li>
                                    <li><strong>period:</strong> Date period (e.g., "2025-10")</li>
                                    <li><strong>label:</strong> Human-readable label (e.g., "Oct 2025")</li>
                                    <li><strong>start_journey:</strong> Count of started journeys</li>
                                    <li><strong>end_journey:</strong> Count of completed journeys</li>
                                </ul>
                            </div>
                        </div>

                        {{-- Tips --}}
                        <div class="bg-lime-50 border-l-4 border-lime-400 p-4 rounded-sm">
                            <div class="flex gap-3">
                                <span class="material-symbols-outlined text-lime-600" style="font-size: 20px;">trending_up</span>
                                <p style="font-family: Poppins, sans-serif; font-size: 11px; color: #365314; line-height: 1.6;">
                                    <strong style="font-weight: 600;">Activity Tracking:</strong> Use for Do tab trip activity analytics. Track journey completion rates over time.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Coming Soon Content --}}
                    <div id="content-coming-soon" class="content-section hidden">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-purple-600">rocket_launch</span>
                            Endpoints Akan Datang
                        </h3>
                        <div class="space-y-4">
                            <div class="border-l-4 border-purple-500 bg-purple-50 p-4">
                                <h4 class="font-semibold text-purple-900 mb-2">
                                    <span class="px-1.5 py-0.5 bg-blue-600 text-white text-xs font-semibold rounded mr-2">POST</span>
                                    /log-pemandu
                                </h4>
                                <p class="text-sm text-purple-700">Driver logs - Start journey (check-out), End journey (check-in), Fuel tracking</p>
                            </div>
                            <div class="border-l-4 border-purple-500 bg-purple-50 p-4">
                                <h4 class="font-semibold text-purple-900 mb-2">
                                    <span class="px-1.5 py-0.5 bg-green-600 text-white text-xs font-semibold rounded mr-2">GET</span>
                                    /vehicles
                                </h4>
                                <p class="text-sm text-purple-700">Available vehicles for driver based on organization</p>
                            </div>
                            <div class="border-l-4 border-purple-500 bg-purple-50 p-4">
                                <h4 class="font-semibold text-purple-900 mb-2">
                                    <span class="px-1.5 py-0.5 bg-blue-600 text-white text-xs font-semibold rounded mr-2">POST</span>
                                    /claims
                                </h4>
                                <p class="text-sm text-purple-700">Submit claims (Tol, Parking, F&B, Accommodation, Fuel, Maintenance, Others)</p>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </x-ui.page-header>

    @push('scripts')
    <script>
        function showSection(section) {
            // Hide all content sections
            document.querySelectorAll('.content-section').forEach(el => {
                el.classList.add('hidden');
            });
            
            // Remove active state from all menu items
            document.querySelectorAll('.menu-item').forEach(el => {
                el.classList.remove('bg-blue-600', 'text-white');
                el.classList.add('hover:bg-gray-50');
            });
            
            // Show selected content
            const contentElement = document.getElementById('content-' + section);
            contentElement.classList.remove('hidden');
            
            // Add active state to selected menu item
            const menuItem = document.getElementById('menu-' + section);
            menuItem.classList.add('bg-blue-600', 'text-white');
            menuItem.classList.remove('hover:bg-gray-50');
        }

        // Set initial active state
        document.addEventListener('DOMContentLoaded', function() {
            showSection('overview');
        });

        // Search/Filter endpoints
        function filterEndpoints(searchTerm) {
            searchTerm = searchTerm.toLowerCase().trim();
            
            // Get all menu buttons
            const menuButtons = document.querySelectorAll('.menu-item');
            
            menuButtons.forEach(button => {
                const buttonText = button.textContent.toLowerCase();
                const buttonId = button.id.replace('menu-', '');
                
                // Show/hide based on search match
                if (searchTerm === '' || buttonText.includes(searchTerm) || buttonId.includes(searchTerm)) {
                    button.style.display = 'block';
                } else {
                    button.style.display = 'none';
                }
            });

            // If search is active, show first matching result
            if (searchTerm !== '') {
                const firstVisible = Array.from(menuButtons).find(btn => btn.style.display !== 'none');
                if (firstVisible) {
                    const sectionId = firstVisible.id.replace('menu-', '');
                    showSection(sectionId);
                }
            }
        }
    </script>
    @endpush
</x-dashboard-layout>
