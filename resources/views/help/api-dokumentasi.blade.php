<x-dashboard-layout title="API Documentation">
    <x-ui.page-header
        title="Dokumentasi API"
        description="Panduan lengkap untuk integrasi API sistem RISDA Odometer"
    >
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
                <div id="content-overview" class="content-section">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600">info</span>
                        Authentication Overview
                    </h3>
                    <div class="prose max-w-none">
                            <p class="text-gray-600 mb-4">
                                RISDA Odometer API menggunakan sistem keselamatan berlapis untuk memastikan hanya aplikasi dan pengguna yang sah dapat mengakses data.
                            </p>
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                                <div class="flex">
                                    <span class="material-symbols-outlined text-yellow-400 mr-3">info</span>
                                    <div class="text-sm text-yellow-700">
                                        <strong>Penting:</strong> Setiap request memerlukan header <code class="bg-yellow-100 px-1 py-0.5 rounded">Accept: application/json</code> untuk memastikan response dalam format JSON.
                                    </div>
                                </div>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Required Headers untuk Semua Requests:</h4>
                            <div class="bg-gray-50 rounded-lg p-4 font-mono text-sm space-y-2 mb-4">
                                <div class="flex gap-2">
                                    <span class="text-gray-500">Content-Type:</span>
                                    <span class="text-blue-600">application/json</span>
                                </div>
                                <div class="flex gap-2">
                                    <span class="text-gray-500">Accept:</span>
                                    <span class="text-blue-600">application/json</span>
                                </div>
                                <div class="flex gap-2">
                                    <span class="text-gray-500">X-API-Key:</span>
                                    <span class="text-purple-600">YOUR_GLOBAL_API_KEY</span>
                                </div>
                                <div class="flex gap-2">
                                    <span class="text-gray-500">Authorization:</span>
                                    <span class="text-green-600">Bearer YOUR_USER_TOKEN</span>
                                    <span class="text-xs text-gray-400">(selepas login)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Security Content --}}
                    <div id="content-security" class="content-section hidden">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-600">security</span>
                            3-Layer Security System
                        </h3>
                        <div class="space-y-4">
                            <div class="border-l-4 border-blue-500 bg-blue-50 p-4">
                                <h4 class="font-semibold text-blue-900 mb-2">Layer 1: Global API Key</h4>
                                <p class="text-sm text-blue-700">
                                    Header: <code class="bg-blue-100 px-1 py-0.5 rounded">X-API-Key</code><br>
                                    Verify bahawa request datang dari aplikasi mobile RISDA yang sah. API Key ini global dan sama untuk semua users.
                                </p>
                            </div>
                            <div class="border-l-4 border-green-500 bg-green-50 p-4">
                                <h4 class="font-semibold text-green-900 mb-2">Layer 2: User Authentication</h4>
                                <p class="text-sm text-green-700">
                                    Endpoint: <code class="bg-green-100 px-1 py-0.5 rounded">POST /auth/login</code><br>
                                    User login dengan email & password menggunakan custom Argon2id + Email Salt untuk maximum security.
                                </p>
                            </div>
                            <div class="border-l-4 border-purple-500 bg-purple-50 p-4">
                                <h4 class="font-semibold text-purple-900 mb-2">Layer 3: Laravel Sanctum Token</h4>
                                <p class="text-sm text-purple-700">
                                    Header: <code class="bg-purple-100 px-1 py-0.5 rounded">Authorization: Bearer TOKEN</code><br>
                                    Setiap request selepas login memerlukan Bearer token yang unique untuk setiap device/session.
                                </p>
                            </div>
                            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                                <h4 class="font-semibold text-indigo-900 mb-2">Multi-Tenancy Data Isolation</h4>
                                <p class="text-sm text-indigo-700">
                                    Setiap user mempunyai <code class="bg-indigo-100 px-1 py-0.5 rounded">jenis_organisasi</code> dan <code class="bg-indigo-100 px-1 py-0.5 rounded">organisasi_id</code>. API akan automatically filter data berdasarkan organizational scope user tersebut.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Health Check Content --}}
                    <div id="content-health" class="content-section hidden">
                        <div class="mb-4">
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded mr-2">GET</span>
                            <code class="text-lg font-mono text-gray-900">/health</code>
                        </div>
                        <p class="text-gray-600 mb-6">Health check endpoint (public, no authentication required)</p>
                        
                        <h4 class="font-semibold text-gray-900 mb-3">Contoh Request:</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>curl -X GET {{ url('/api/health') }}</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Response (200 OK):</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <pre class="text-sm overflow-x-auto"><code>{
  "success": true,
  "message": "RISDA Odometer API is running",
  "version": "1.0.0",
  "timestamp": "2025-10-01T10:34:16+08:00"
}</code></pre>
                        </div>
                    </div>

                    {{-- Login Content --}}
                    <div id="content-login" class="content-section hidden">
                        <div class="mb-4">
                            <span class="px-2 py-1 bg-blue-600 text-white text-xs font-semibold rounded mr-2">POST</span>
                            <code class="text-lg font-mono text-gray-900">/auth/login</code>
                        </div>
                        <p class="text-gray-600 mb-6">Login dan dapatkan Bearer token untuk authentication</p>
                        
                        <h4 class="font-semibold text-gray-900 mb-3">Request Headers:</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>Content-Type: application/json
Accept: application/json
X-API-Key: YOUR_GLOBAL_API_KEY</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Request Body:</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>{
  "email": "user@jara.my",
  "password": "password"
}</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Response (200 OK):</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>{
  "success": true,
  "message": "Login berjaya",
  "data": {
    "token": "23|xY9KpQm2vNrLwE4sHcT8fA5jB6gDz3nU1oP7iV0eR",
    "token_type": "Bearer",
    "user": {
      "id": 10,
      "name": "Adam Bin Abdullah",
      "email": "user@jara.my",
      "profile_picture_url": null,
      "no_telefon": "012-3456789",
      "jenis_organisasi": "stesen",
      "organisasi_id": 3,
      "kumpulan_id": 2,
      "status": "aktif",
      "bahagian": null,
      "stesen": {
        "id": 3,
        "nama": "Pejabat RISDA Stesen Kuala Lumpur",
        "kod": "STESEN-003"
      },
      "kumpulan": {
        "id": 2,
        "nama": "Pemandu",
        "kebenaran_matrix": { ... }
      },
      "staf": {
        "id": 15,
        "no_pekerja": "RS2025-1001",
        "nama_penuh": "Adam Bin Abdullah",
        "no_kad_pengenalan": "850123-10-5678",
        "jawatan": "Pemandu Kenderaan",
        "no_telefon": "012-3456789",
        "alamat": "No. 123, Jalan Merdeka, Taman Sejahtera, 50000 Kuala Lumpur"
      }
    }
  }
}</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Error Responses:</h4>
                        <div class="space-y-2">
                            <div class="bg-red-50 border-l-4 border-red-500 p-3">
                                <span class="font-semibold text-red-700">401 Unauthorized</span> - API Key tidak sah
                            </div>
                            <div class="bg-red-50 border-l-4 border-red-500 p-3">
                                <span class="font-semibold text-red-700">422 Validation Error</span> - Email atau password salah
                            </div>
                            <div class="bg-red-50 border-l-4 border-red-500 p-3">
                                <span class="font-semibold text-red-700">403 Forbidden</span> - Akaun tidak aktif
                            </div>
                        </div>
                    </div>

                    {{-- Get User Content --}}
                    <div id="content-user" class="content-section hidden">
                        <div class="mb-4">
                            <span class="px-2 py-1 bg-green-600 text-white text-xs font-semibold rounded mr-2">GET</span>
                            <code class="text-lg font-mono text-gray-900">/auth/user</code>
                        </div>
                        <p class="text-gray-600 mb-6">Dapatkan maklumat user yang sedang login (authenticated)</p>
                        
                        <h4 class="font-semibold text-gray-900 mb-3">Request Headers:</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>Accept: application/json
X-API-Key: YOUR_GLOBAL_API_KEY
Authorization: Bearer YOUR_TOKEN</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Response (200 OK):</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <pre class="text-sm overflow-x-auto"><code>{
  "success": true,
  "data": {
    "id": 10,
    "name": "Adam Bin Abdullah",
    "email": "user@jara.my",
    "profile_picture_url": "http://localhost:8000/storage/profile_pictures/profile_10_1234567890.jpg",
    "no_telefon": "012-3456789",
    "jenis_organisasi": "stesen",
    "organisasi_id": 3,
    "kumpulan_id": 2,
    "status": "aktif",
    "bahagian": null,
    "stesen": {
      "id": 3,
      "nama": "Pejabat RISDA Stesen Kuala Lumpur",
      "kod": "STESEN-003"
    },
    "kumpulan": {
      "id": 2,
      "nama": "Pemandu",
      "kebenaran_matrix": { ... }
    },
    "staf": {
      "id": 15,
      "no_pekerja": "RS2025-1001",
      "nama_penuh": "Adam Bin Abdullah",
      "no_kad_pengenalan": "850123-10-5678",
      "jawatan": "Pemandu Kenderaan",
      "no_telefon": "012-3456789",
      "alamat": "No. 123, Jalan Merdeka, Taman Sejahtera, 50000 Kuala Lumpur"
    }
  }
}</code></pre>
                        </div>
                    </div>

                    {{-- Logout Content --}}
                    <div id="content-logout" class="content-section hidden">
                        <div class="mb-4">
                            <span class="px-2 py-1 bg-red-600 text-white text-xs font-semibold rounded mr-2">POST</span>
                            <code class="text-lg font-mono text-gray-900">/auth/logout</code>
                        </div>
                        <p class="text-gray-600 mb-6">Logout dan delete current token (device ini sahaja)</p>
                        
                        <h4 class="font-semibold text-gray-900 mb-3">Request Headers:</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>Accept: application/json
X-API-Key: YOUR_GLOBAL_API_KEY
Authorization: Bearer YOUR_TOKEN</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Response (200 OK):</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <pre class="text-sm overflow-x-auto"><code>{
  "success": true,
  "message": "Logout berjaya"
}</code></pre>
                        </div>
                    </div>

                    {{-- Logout All Content --}}
                    <div id="content-logout-all" class="content-section hidden">
                        <div class="mb-4">
                            <span class="px-2 py-1 bg-red-600 text-white text-xs font-semibold rounded mr-2">POST</span>
                            <code class="text-lg font-mono text-gray-900">/auth/logout-all</code>
                        </div>
                        <p class="text-gray-600 mb-6">Logout dari semua devices (delete all tokens)</p>
                        
                        <h4 class="font-semibold text-gray-900 mb-3">Request Headers:</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>Accept: application/json
X-API-Key: YOUR_GLOBAL_API_KEY
Authorization: Bearer YOUR_TOKEN</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Response (200 OK):</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <pre class="text-sm overflow-x-auto"><code>{
  "success": true,
  "message": "Logout dari semua devices berjaya"
}</code></pre>
                        </div>
                    </div>

                    {{-- Change Password Content --}}
                    <div id="content-change-password" class="content-section hidden">
                        <div class="mb-4">
                            <span class="px-2 py-1 bg-yellow-600 text-white text-xs font-semibold rounded mr-2">PUT</span>
                            <code class="text-lg font-mono text-gray-900">/user/change-password</code>
                        </div>
                        <p class="text-gray-600 mb-6">Tukar kata laluan user yang sedang login</p>
                        
                        <h4 class="font-semibold text-gray-900 mb-3">Request Headers:</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>Accept: application/json
X-API-Key: YOUR_GLOBAL_API_KEY
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Request Body:</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>{
  "current_password": "oldpassword123",
  "new_password": "newpassword456",
  "new_password_confirmation": "newpassword456"
}</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Response (200 OK):</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>{
  "success": true,
  "message": "Kata laluan berjaya dikemaskini"
}</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Error Responses:</h4>
                        <div class="space-y-2">
                            <div class="bg-red-50 border-l-4 border-red-500 p-3">
                                <span class="font-semibold text-red-700">422 Validation Error</span> - Kata laluan semasa tidak sah atau validation failed
                            </div>
                            <div class="bg-red-50 border-l-4 border-red-500 p-3">
                                <span class="font-semibold text-red-700">401 Unauthorized</span> - Token tidak sah
                            </div>
                        </div>
                    </div>

                    {{-- Upload Profile Picture Content --}}
                    <div id="content-upload-profile" class="content-section hidden">
                        <div class="mb-4">
                            <span class="px-2 py-1 bg-blue-600 text-white text-xs font-semibold rounded mr-2">POST</span>
                            <code class="text-lg font-mono text-gray-900">/user/profile-picture</code>
                        </div>
                        <p class="text-gray-600 mb-6">Upload atau update gambar profil user</p>
                        
                        <h4 class="font-semibold text-gray-900 mb-3">Request Headers:</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>Accept: application/json
X-API-Key: YOUR_GLOBAL_API_KEY
Authorization: Bearer YOUR_TOKEN
Content-Type: multipart/form-data</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Request Body (FormData):</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>profile_picture: (binary file)
// File types: jpeg, jpg, png
// Max size: 2MB</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Response (200 OK):</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>{
  "success": true,
  "message": "Gambar profil berjaya dikemaskini",
  "data": {
    "profile_picture": "profile_pictures/profile_10_1234567890.jpg",
    "profile_picture_url": "http://localhost:8000/storage/profile_pictures/profile_10_1234567890.jpg"
  }
}</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Error Responses:</h4>
                        <div class="space-y-2">
                            <div class="bg-red-50 border-l-4 border-red-500 p-3">
                                <span class="font-semibold text-red-700">422 Validation Error</span> - File type tidak sah atau saiz melebihi 2MB
                            </div>
                            <div class="bg-red-50 border-l-4 border-red-500 p-3">
                                <span class="font-semibold text-red-700">401 Unauthorized</span> - Token tidak sah
                            </div>
                        </div>
                    </div>

                    {{-- Delete Profile Picture Content --}}
                    <div id="content-delete-profile" class="content-section hidden">
                        <div class="mb-4">
                            <span class="px-2 py-1 bg-red-600 text-white text-xs font-semibold rounded mr-2">DELETE</span>
                            <code class="text-lg font-mono text-gray-900">/user/profile-picture</code>
                        </div>
                        <p class="text-gray-600 mb-6">Padam gambar profil user</p>
                        
                        <h4 class="font-semibold text-gray-900 mb-3">Request Headers:</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>Accept: application/json
X-API-Key: YOUR_GLOBAL_API_KEY
Authorization: Bearer YOUR_TOKEN</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Response (200 OK):</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>{
  "success": true,
  "message": "Gambar profil berjaya dipadam"
}</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Error Responses:</h4>
                        <div class="space-y-2">
                            <div class="bg-red-50 border-l-4 border-red-500 p-3">
                                <span class="font-semibold text-red-700">404 Not Found</span> - Tiada gambar profil untuk dipadam
                            </div>
                            <div class="bg-red-50 border-l-4 border-red-500 p-3">
                                <span class="font-semibold text-red-700">401 Unauthorized</span> - Token tidak sah
                            </div>
                        </div>
                    </div>

                    {{-- Get Programs Content --}}
                    <div id="content-programs" class="content-section hidden">
                        <div class="mb-4">
                            <span class="px-2 py-1 bg-green-600 text-white text-xs font-semibold rounded mr-2">GET</span>
                            <code class="text-lg font-mono text-gray-900">/programs</code>
                        </div>
                        <p class="text-gray-600 mb-6">Dapatkan senarai program yang ditugaskan kepada pemandu yang login. Boleh filter by status: current, ongoing, past</p>
                        
                        <h4 class="font-semibold text-gray-900 mb-3">Request Headers:</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>Accept: application/json
X-API-Key: YOUR_GLOBAL_API_KEY
Authorization: Bearer YOUR_TOKEN</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Query Parameters:</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>status (optional): current | ongoing | past

// Examples:
GET /api/programs                   // All programs
GET /api/programs?status=current    // Programs hari ini
GET /api/programs?status=ongoing    // Programs aktif
GET /api/programs?status=past       // Programs selesai</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Response (200 OK):</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>{
  "success": true,
  "data": [
    {
      "id": 8,
      "nama_program": "Program Pembangunan Komuniti Kampung Sungai Rusa",
      "status": "aktif",
      "status_label": "Aktif",
      "tarikh_mula": "2025-10-01 08:00:00",
      "tarikh_mula_formatted": "01/10/2025",
      "tarikh_selesai": "2025-10-01 17:00:00",
      "tarikh_selesai_formatted": "01/10/2025",
      "tarikh_kelulusan": "2025-09-30 14:30:00",
      "tarikh_mula_aktif": "2025-10-01 08:15:00",
      "tarikh_sebenar_selesai": null,
      "lokasi_program": "Kampung Sungai Rusa, Sibu",
      "lokasi_lat": "2.3225",
      "lokasi_long": "111.8248",
      "jarak_anggaran": 45.50,
      "penerangan": "Program pembangunan komuniti untuk petani getah",
      "permohonan_dari": {
        "id": 5,
        "no_pekerja": "RS2020-0305",
        "nama_penuh": "Ahmad Bin Yusof",
        "no_telefon": "019-7654321"
      },
      "pemandu": {
        "id": 15,
        "no_pekerja": "RS2025-1001",
        "nama_penuh": "Adam Bin Abdullah",
        "no_telefon": "012-3456789"
      },
      "kenderaan": {
        "id": 6,
        "no_plat": "QKS 1234 K",
        "jenama": "Toyota",
        "model": "Hilux",
        "status": "tersedia",
        "latest_odometer": 10900
      },
      "logs": {
        "total": 3,
        "active": 1,
        "completed": 2
      },
      "created_at": "2025-09-28 10:00:00",
      "updated_at": "2025-09-30 14:30:00"
    }
  ],
  "meta": {
    "total": 1,
    "filter": "current"
  }
}</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Multi-Tenancy:</h4>
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                            <p class="text-sm text-blue-700">
                                API ini automatically filter programs berdasarkan <code class="bg-blue-100 px-1 py-0.5 rounded">pemandu_id</code> (staf_id dari user yang login) dan organizational scope user tersebut.
                            </p>
                        </div>
                    </div>

                    {{-- Get Program Detail Content --}}
                    <div id="content-program-detail" class="content-section hidden">
                        <div class="mb-4">
                            <span class="px-2 py-1 bg-green-600 text-white text-xs font-semibold rounded mr-2">GET</span>
                            <code class="text-lg font-mono text-gray-900">/programs/{id}</code>
                        </div>
                        <p class="text-gray-600 mb-6">Dapatkan detail program berdasarkan ID</p>
                        
                        <h4 class="font-semibold text-gray-900 mb-3">Request Headers:</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>Accept: application/json
X-API-Key: YOUR_GLOBAL_API_KEY
Authorization: Bearer YOUR_TOKEN</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">URL Parameters:</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>id (required): Program ID

// Example:
GET /api/programs/8</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Response (200 OK):</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>{
  "success": true,
  "data": {
    "id": 8,
    "nama_program": "Program Pembangunan Komuniti Kampung Sungai Rusa",
    "status": "aktif",
    "status_label": "Aktif",
    "tarikh_mula": "2025-10-01 08:00:00",
    "tarikh_mula_formatted": "01/10/2025",
    "tarikh_selesai": "2025-10-01 17:00:00",
    "tarikh_selesai_formatted": "01/10/2025",
    "tarikh_kelulusan": "2025-09-30 14:30:00",
    "tarikh_mula_aktif": "2025-10-01 08:15:00",
    "tarikh_sebenar_selesai": null,
    "lokasi_program": "Kampung Sungai Rusa, Sibu",
    "lokasi_lat": "2.3225",
    "lokasi_long": "111.8248",
    "jarak_anggaran": 45.50,
    "penerangan": "Program pembangunan komuniti untuk petani getah",
    "permohonan_dari": {
      "id": 5,
      "no_pekerja": "RS2020-0305",
      "nama_penuh": "Ahmad Bin Yusof",
      "no_telefon": "019-7654321"
    },
    "pemandu": {
      "id": 15,
      "no_pekerja": "RS2025-1001",
      "nama_penuh": "Adam Bin Abdullah",
      "no_telefon": "012-3456789"
    },
    "kenderaan": {
      "id": 6,
      "no_plat": "QKS 1234 K",
      "jenama": "Toyota",
      "model": "Hilux",
      "status": "tersedia",
      "latest_odometer": 10900
    },
    "logs": {
      "total": 3,
      "active": 1,
      "completed": 2
    },
    "created_at": "2025-09-28 10:00:00",
    "updated_at": "2025-09-30 14:30:00"
  }
}</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Error Responses:</h4>
                        <div class="space-y-2 mb-6">
                            <div class="bg-red-50 border-l-4 border-red-500 p-3">
                                <span class="font-semibold text-red-700">404 Not Found</span> - Program tidak dijumpai atau tidak diberikan akses
                            </div>
                            <div class="bg-red-50 border-l-4 border-red-500 p-3">
                                <span class="font-semibold text-red-700">401 Unauthorized</span> - Token tidak sah
                            </div>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">📅 Date Fields Explanation:</h4>
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                            <ul class="text-sm text-blue-700 space-y-2">
                                <li><strong>tarikh_kelulusan:</strong> Tarikh & masa program diluluskan oleh admin</li>
                                <li><strong>tarikh_mula_aktif:</strong> Tarikh & masa program jadi aktif (bila driver mula journey pertama)</li>
                                <li><strong>tarikh_sebenar_selesai:</strong> Tarikh & masa program sebenarnya selesai (tarikh end journey terakhir atau auto-close)</li>
                                <li><strong>Note:</strong> Fields ini boleh jadi <code>null</code> jika program belum sampai stage tersebut</li>
                            </ul>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">🚗 Vehicle Latest Odometer:</h4>
                        <div class="bg-green-50 border-l-4 border-green-500 p-4">
                            <ul class="text-sm text-green-700 space-y-2">
                                <li><strong>latest_odometer:</strong> Bacaan odometer terkini dari perjalanan yang telah selesai</li>
                                <li>Dikira dari <code>odometer_masuk</code> journey paling baharu (berdasarkan ID, bukan timestamp)</li>
                                <li>Guna untuk display "Current Vehicle Odometer" di Start Journey screen</li>
                                <li>Boleh jadi <code>null</code> jika kenderaan belum ada journey selesai</li>
                            </ul>
                        </div>
                    </div>

                    {{-- Get Active Journey Content --}}
                    <div id="content-log-active" class="content-section hidden">
                        <div class="mb-4">
                            <span class="px-2 py-1 bg-green-600 text-white text-xs font-semibold rounded mr-2">GET</span>
                            <code class="text-lg font-mono text-gray-900">/log-pemandu/active</code>
                        </div>
                        <p class="text-gray-600 mb-6">Semak sama ada driver mempunyai perjalanan aktif atau tidak</p>

                        <h4 class="font-semibold text-gray-900 mb-3">Required Headers:</h4>
                        <div class="bg-gray-50 p-4 rounded-lg mb-6 space-y-2">
                            <div><code class="text-sm">X-API-Key: YOUR_GLOBAL_API_KEY</code></div>
                            <div><code class="text-sm">Authorization: Bearer YOUR_USER_TOKEN</code></div>
                            <div><code class="text-sm">Accept: application/json</code></div>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Success Response (Ada Journey Aktif):</h4>
                        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg mb-4 overflow-x-auto">
<pre class="text-sm"><code>{
  "success": true,
  "data": {
    "id": 9,
    "program_id": 9,
    "program": {
      "id": 9,
      "nama": "Program Jelajah Sarawak",
      "lokasi": "Dewan Suarah Sibu"
    },
    "kenderaan": {
      "id": 6,
      "no_plat": "QSR43",
      "jenama": "Toyota",
      "model": "Alphard"
    },
    "status": "dalam_perjalanan",
    "masa_keluar": "2025-10-01T07:22:21.000000Z",
    "odometer_keluar": 12345
  }
}</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Success Response (Tiada Journey Aktif):</h4>
                        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg mb-4 overflow-x-auto">
<pre class="text-sm"><code>{
  "success": true,
  "data": null,
  "message": "Tiada perjalanan aktif"
}</code></pre>
                        </div>
                    </div>

                    {{-- Get All Logs Content --}}
                    <div id="content-log-list" class="content-section hidden">
                        <div class="mb-4">
                            <span class="px-2 py-1 bg-green-600 text-white text-xs font-semibold rounded mr-2">GET</span>
                            <code class="text-lg font-mono text-gray-900">/log-pemandu</code>
                        </div>
                        <p class="text-gray-600 mb-6">Dapatkan senarai semua log perjalanan driver</p>

                        <h4 class="font-semibold text-gray-900 mb-3">Query Parameters (Optional):</h4>
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <div><code class="text-sm">?status=aktif</code> - Filter log aktif sahaja</div>
                            <div><code class="text-sm">?status=selesai</code> - Filter log selesai sahaja</div>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Success Response:</h4>
                        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg mb-4 overflow-x-auto">
<pre class="text-sm"><code>{
  "success": true,
  "data": [
    {
      "id": 9,
      "program": {
        "nama": "Program Jelajah Sarawak"
      },
      "kenderaan": {
        "no_plat": "QSR43",
        "jenama": "Toyota"
      },
      "status": "selesai",
      "masa_keluar": "2025-10-01T07:22:21.000000Z",
      "masa_masuk": "2025-10-01T07:30:00.000000Z",
      "jarak": 100
    }
  ],
  "meta": {
    "total": 1,
    "filter": "all"
  }
}</code></pre>
                        </div>
                    </div>

                    {{-- Start Journey Content --}}
                    <div id="content-start-journey" class="content-section hidden">
                        <div class="mb-4">
                            <span class="px-2 py-1 bg-blue-600 text-white text-xs font-semibold rounded mr-2">POST</span>
                            <code class="text-lg font-mono text-gray-900">/log-pemandu/start</code>
                        </div>
                        <p class="text-gray-600 mb-6">Mulakan perjalanan baru (Start Journey / Check-Out)</p>

                        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6">
                            <p class="text-sm text-yellow-800"><strong>⚠️ Perhatian:</strong> Driver hanya boleh mempunyai SATU perjalanan aktif pada satu masa. Jika ada journey aktif, request akan ditolak.</p>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Request Body:</h4>
                        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg mb-4 overflow-x-auto">
<pre class="text-sm"><code>{
  "program_id": 9,
  "kenderaan_id": 6,
  "odometer_keluar": 12345,
  "lokasi_keluar_lat": 2.310332,
  "lokasi_keluar_long": 111.831561,
  "catatan": "Start journey from office"
}</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Success Response:</h4>
                        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg mb-4 overflow-x-auto">
<pre class="text-sm"><code>{
  "success": true,
  "message": "Perjalanan dimulakan",
  "data": {
    "id": 9,
    "program_id": 9,
    "status": "dalam_perjalanan",
    "odometer_keluar": 12345,
    "masa_keluar": "2025-10-01T07:22:21.000000Z"
  }
}</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Error Response (Ada Journey Aktif):</h4>
                        <div class="bg-red-50 border-l-4 border-red-500 p-3">
                            <span class="font-semibold text-red-700">400 Bad Request</span> - Anda masih mempunyai perjalanan aktif
                        </div>
                    </div>

                    {{-- End Journey Content --}}
                    <div id="content-end-journey" class="content-section hidden">
                        <div class="mb-4">
                            <span class="px-2 py-1 bg-yellow-600 text-white text-xs font-semibold rounded mr-2">PUT</span>
                            <code class="text-lg font-mono text-gray-900">/log-pemandu/{id}/end</code>
                        </div>
                        <p class="text-gray-600 mb-6">Tamatkan perjalanan aktif (End Journey / Check-In)</p>

                        <h4 class="font-semibold text-gray-900 mb-3">URL Parameters:</h4>
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <div><code class="text-sm">{id}</code> - ID log perjalanan yang aktif</div>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Request Body:</h4>
                        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg mb-4 overflow-x-auto">
<pre class="text-sm"><code>{
  "odometer_masuk": 12445,
  "lokasi_checkin_lat": 2.310332,
  "lokasi_checkin_long": 111.831561,
  "catatan": "Journey completed",
  "liter_minyak": 45.5,
  "kos_minyak": 120.50,
  "stesen_minyak": "Petronas Sibu"
}</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Success Response:</h4>
                        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg mb-4 overflow-x-auto">
<pre class="text-sm"><code>{
  "success": true,
  "message": "Perjalanan berjaya ditamatkan",
  "data": {
    "id": 9,
    "status": "selesai",
    "odometer_keluar": 12345,
    "odometer_masuk": 12445,
    "jarak": 100,
    "kos_minyak": "120.50",
    "masa_keluar": "2025-10-01T07:22:21.000000Z",
    "masa_masuk": "2025-10-01T07:30:00.000000Z"
  }
}</code></pre>
                        </div>

                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                            <p class="text-sm text-blue-800"><strong>💡 Tips:</strong> Field `jarak` akan dikira secara automatik: odometer_masuk - odometer_keluar</p>
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
                    <div id="content-tuntutan-list" class="content-section hidden">
                        <div class="mb-4">
                            <span class="px-2 py-1 bg-green-600 text-white text-xs font-semibold rounded mr-2">GET</span>
                            <code class="text-lg font-mono text-gray-900">/tuntutan</code>
                        </div>
                        <p class="text-gray-600 mb-6">Dapatkan senarai semua tuntutan untuk pemandu yang log masuk</p>

                        <h4 class="font-semibold text-gray-900 mb-3">Query Parameters (Optional):</h4>
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <div class="space-y-2 text-sm">
                                <div><code>status</code> - Filter by status: <code>pending</code>, <code>diluluskan</code>, <code>ditolak</code>, <code>digantung</code></div>
                            </div>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Request Headers:</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>Accept: application/json
X-API-Key: YOUR_GLOBAL_API_KEY
Authorization: Bearer YOUR_SANCTUM_TOKEN</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Success Response:</h4>
                        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg mb-4 overflow-x-auto">
<pre class="text-sm"><code>{
  "success": true,
  "data": [
    {
      "id": 1,
      "log_pemandu_id": 19,
      "kategori": "fuel",
      "kategori_label": "Fuel",
      "jumlah": 30.00,
      "keterangan": "Minyak untuk perjalanan ke Sibu",
      "resit": "http://localhost:8000/storage/claim_receipts/abc123.jpg",
      "status": "pending",
      "status_label": "Pending",
      "status_badge_color": "yellow",
      "alasan_tolak": null,
      "alasan_gantung": null,
      "can_edit": false,
      "diproses_oleh": null,
      "tarikh_diproses": null,
      "program": {
        "id": 9,
        "nama_program": "Program Jelajah Sarawak",
        "lokasi_program": "Sibu, Sarawak"
      },
      "kenderaan": {
        "id": 6,
        "no_plat": "QSR43",
        "jenama": "Toyota",
        "model": "Hilux"
      }
    }
  ]
}</code></pre>
                        </div>

                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                            <p class="text-sm text-blue-800"><strong>💡 Tips:</strong> Gunakan query parameter `status=ditolak` untuk mendapatkan tuntutan yang boleh diedit semula.</p>
                        </div>
                    </div>

                    {{-- GET Tuntutan Detail Content --}}
                    <div id="content-tuntutan-detail" class="content-section hidden">
                        <div class="mb-4">
                            <span class="px-2 py-1 bg-green-600 text-white text-xs font-semibold rounded mr-2">GET</span>
                            <code class="text-lg font-mono text-gray-900">/tuntutan/{id}</code>
                        </div>
                        <p class="text-gray-600 mb-6">Dapatkan maklumat terperinci untuk satu tuntutan</p>

                        <h4 class="font-semibold text-gray-900 mb-3">URL Parameters:</h4>
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <div><code class="text-sm">{id}</code> - ID tuntutan</div>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Request Headers:</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>Accept: application/json
X-API-Key: YOUR_GLOBAL_API_KEY
Authorization: Bearer YOUR_SANCTUM_TOKEN</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Success Response:</h4>
                        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg mb-4 overflow-x-auto">
<pre class="text-sm"><code>{
  "success": true,
  "data": {
    "id": 1,
    "log_pemandu_id": 19,
    "kategori": "fuel",
    "kategori_label": "Fuel",
    "jumlah": 30.00,
    "keterangan": "Minyak untuk perjalanan ke Sibu",
    "resit": "http://localhost:8000/storage/claim_receipts/abc123.jpg",
    "status": "pending",
    "status_label": "Pending",
    "status_badge_color": "yellow",
    "alasan_tolak": null,
    "alasan_gantung": null,
    "can_edit": false,
    "diproses_oleh": null,
    "tarikh_diproses": null,
    "program": {
      "id": 9,
      "nama_program": "Program Jelajah Sarawak",
      "lokasi_program": "Sibu, Sarawak"
    },
    "kenderaan": {
      "id": 6,
      "no_plat": "QSR43",
      "jenama": "Toyota",
      "model": "Hilux"
    }
  }
}</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Error Responses:</h4>
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                            <p class="text-sm text-red-800"><strong>404 Not Found</strong> - Tuntutan tidak dijumpai atau anda tidak mempunyai akses</p>
                        </div>
                    </div>

                    {{-- POST Create Tuntutan Content --}}
                    <div id="content-create-tuntutan" class="content-section hidden">
                        <div class="mb-4">
                            <span class="px-2 py-1 bg-blue-600 text-white text-xs font-semibold rounded mr-2">POST</span>
                            <code class="text-lg font-mono text-gray-900">/tuntutan</code>
                        </div>
                        <p class="text-gray-600 mb-6">Buat tuntutan baru untuk perjalanan yang telah selesai</p>

                        <h4 class="font-semibold text-gray-900 mb-3">Request Headers:</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>Content-Type: multipart/form-data
Accept: application/json
X-API-Key: YOUR_GLOBAL_API_KEY
Authorization: Bearer YOUR_SANCTUM_TOKEN</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Request Body (Form Data):</h4>
                        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg mb-4 overflow-x-auto">
<pre class="text-sm"><code>log_pemandu_id: 19 (required, integer)
kategori: "fuel" (required, string: tol|parking|f&b|accommodation|fuel|car_maintenance|others)
jumlah: 30.00 (required, numeric)
keterangan: "Minyak untuk perjalanan ke Sibu" (optional, string)
resit: [FILE] (optional, image: jpg, jpeg, png, pdf, max 5MB)</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Success Response:</h4>
                        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg mb-4 overflow-x-auto">
<pre class="text-sm"><code>{
  "success": true,
  "message": "Tuntutan berjaya dibuat",
  "data": {
    "id": 1,
    "log_pemandu_id": 19,
    "kategori": "fuel",
    "kategori_label": "Fuel",
    "jumlah": 30.00,
    "keterangan": "Minyak untuk perjalanan ke Sibu",
    "resit": "http://localhost:8000/storage/claim_receipts/abc123.jpg",
    "status": "pending",
    "status_label": "Pending",
    "status_badge_color": "yellow",
    "can_edit": false,
    "created_at": "2025-10-02T10:30:00.000000Z"
  }
}</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Error Responses:</h4>
                        <div class="space-y-2">
                            <div class="bg-red-50 border-l-4 border-red-500 p-4">
                                <p class="text-sm text-red-800"><strong>422 Validation Error</strong> - Data tidak sah atau log_pemandu_id tidak wujud</p>
                            </div>
                            <div class="bg-red-50 border-l-4 border-red-500 p-4">
                                <p class="text-sm text-red-800"><strong>403 Forbidden</strong> - Anda tidak mempunyai akses ke log perjalanan ini</p>
                            </div>
                        </div>
                    </div>

                    {{-- PUT Update Tuntutan Content --}}
                    <div id="content-update-tuntutan" class="content-section hidden">
                        <div class="mb-4">
                            <span class="px-2 py-1 bg-yellow-600 text-white text-xs font-semibold rounded mr-2">PUT</span>
                            <code class="text-lg font-mono text-gray-900">/tuntutan/{id}</code>
                        </div>
                        <p class="text-gray-600 mb-6">Kemaskini tuntutan yang ditolak (status: ditolak sahaja)</p>

                        <h4 class="font-semibold text-gray-900 mb-3">URL Parameters:</h4>
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <div><code class="text-sm">{id}</code> - ID tuntutan yang ingin dikemaskini</div>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Request Headers:</h4>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <pre class="text-sm overflow-x-auto"><code>Content-Type: multipart/form-data
Accept: application/json
X-API-Key: YOUR_GLOBAL_API_KEY
Authorization: Bearer YOUR_SANCTUM_TOKEN</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Request Body (Form Data):</h4>
                        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg mb-4 overflow-x-auto">
<pre class="text-sm"><code>kategori: "fuel" (required, string: tol|parking|f&b|accommodation|fuel|car_maintenance|others)
jumlah: 30.00 (required, numeric)
keterangan: "Minyak untuk perjalanan ke Sibu - Updated" (optional, string)
resit: [FILE] (optional, image: jpg, jpeg, png, pdf, max 5MB)</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Success Response:</h4>
                        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg mb-4 overflow-x-auto">
<pre class="text-sm"><code>{
  "success": true,
  "message": "Tuntutan berjaya dikemaskini dan status ditukar ke pending",
  "data": {
    "id": 1,
    "log_pemandu_id": 19,
    "kategori": "fuel",
    "kategori_label": "Fuel",
    "jumlah": 30.00,
    "keterangan": "Minyak untuk perjalanan ke Sibu - Updated",
    "resit": "http://localhost:8000/storage/claim_receipts/new_abc123.jpg",
    "status": "pending",
    "status_label": "Pending",
    "status_badge_color": "yellow",
    "alasan_tolak": null,
    "can_edit": false,
    "updated_at": "2025-10-02T11:00:00.000000Z"
  }
}</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Error Responses:</h4>
                        <div class="space-y-2">
                            <div class="bg-red-50 border-l-4 border-red-500 p-4">
                                <p class="text-sm text-red-800"><strong>404 Not Found</strong> - Tuntutan tidak dijumpai atau anda tidak mempunyai akses</p>
                            </div>
                            <div class="bg-red-50 border-l-4 border-red-500 p-4">
                                <p class="text-sm text-red-800"><strong>400 Bad Request</strong> - Hanya tuntutan dengan status "ditolak" boleh dikemaskini</p>
                            </div>
                            <div class="bg-red-50 border-l-4 border-red-500 p-4">
                                <p class="text-sm text-red-800"><strong>422 Validation Error</strong> - Data tidak sah</p>
                            </div>
                        </div>

                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mt-4">
                            <p class="text-sm text-yellow-800"><strong>⚠️ Penting:</strong> Selepas kemaskini, status akan automatik bertukar dari "ditolak" kepada "pending" dan menunggu kelulusan semula.</p>
                        </div>
                    </div>

                    {{-- Report Vehicle Content --}}
                    <div id="content-report-vehicle" class="content-section hidden">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="px-2 py-1 bg-green-600 text-white text-xs font-semibold rounded">GET</span>
                            /api/reports/vehicle
                        </h3>
                        
                        <p class="text-gray-600 mb-4">Retrieve detailed vehicle usage reports including journey details, fuel consumption, and distance traveled.</p>
                        
                        <h4 class="font-semibold text-gray-900 mb-3">Request Headers:</h4>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4">
                            <pre class="text-sm"><code>X-API-Key: YOUR_GLOBAL_API_KEY
Authorization: Bearer USER_SANCTUM_TOKEN
Origin: YOUR_ALLOWED_ORIGIN
Content-Type: application/json</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Query Parameters (Optional):</h4>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4">
                            <pre class="text-sm"><code>date_from: 2025-10-01  (Format: YYYY-MM-DD)
date_to: 2025-10-31    (Format: YYYY-MM-DD)</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Success Response (200 OK):</h4>
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200 mb-4">
                            <pre class="text-sm text-green-900"><code>{
    "success": true,
    "data": [
        {
            "id": 9,
            "no_plat": "QSR43",
            "program": "Program Jelajah Sarawak",
            "location": "Dewan Suarah Sibu",
            "distance": 100,
            "date": "2025-09-30T16:00:00.000000Z",
            "vehicle_details": {
                "id": 6,
                "no_plat": "QSR43",
                "jenama": "Toyota",
                "model": "Alphard",
                "jenis_bahan_api": "petrol"
            },
            "program_details": {
                "id": 9,
                "nama_program": "Program Jelajah Sarawak",
                "lokasi_program": "Dewan Suarah Sibu",
                "permohonan_dari": "Mohamad Faizan Bin Abdul Rahman"
            },
            "journey_details": {
                "tarikh": "2025-09-30T16:00:00.000000Z",
                "masa_keluar": "2025-10-02T07:22:21.000000Z",
                "masa_masuk": "2025-10-02T07:22:52.000000Z",
                "odometer_keluar": 12345,
                "odometer_masuk": 12445,
                "jarak": 100,
                "status": "selesai",
                "catatan": "Journey completed successfully"
            },
            "fuel_details": {
                "kos_minyak": 120.50,
                "liter_minyak": 45.50,
                "stesen_minyak": "Petronas Sibu"
            },
            "images": {
                "foto_odometer_keluar": "/storage/odometer_photos/abc123.jpg",
                "foto_odometer_masuk": "/storage/odometer_photos/def456.jpg",
                "resit_minyak": "/storage/fuel_receipts/ghi789.jpg"
            }
        }
    ]
}</code></pre>
                        </div>

                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mt-4">
                            <p class="text-sm text-blue-800"><strong>💡 Tips:</strong> Use date filters to get reports for specific periods. Images URLs are absolute paths.</p>
                        </div>
                    </div>

                    {{-- Report Cost Content --}}
                    <div id="content-report-cost" class="content-section hidden">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="px-2 py-1 bg-green-600 text-white text-xs font-semibold rounded">GET</span>
                            /api/reports/cost
                        </h3>
                        
                        <p class="text-gray-600 mb-4">Retrieve fuel cost reports with detailed breakdown by program, vehicle, and fuel station.</p>
                        
                        <h4 class="font-semibold text-gray-900 mb-3">Request Headers:</h4>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4">
                            <pre class="text-sm"><code>X-API-Key: YOUR_GLOBAL_API_KEY
Authorization: Bearer USER_SANCTUM_TOKEN
Origin: YOUR_ALLOWED_ORIGIN
Content-Type: application/json</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Query Parameters (Optional):</h4>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4">
                            <pre class="text-sm"><code>date_from: 2025-10-01  (Format: YYYY-MM-DD)
date_to: 2025-10-31    (Format: YYYY-MM-DD)</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Success Response (200 OK):</h4>
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200 mb-4">
                            <pre class="text-sm text-green-900"><code>{
    "success": true,
    "data": [
        {
            "id": 9,
            "date": "2025-09-30T16:00:00.000000Z",
            "vehicle": "QSR43",
            "program": "Program Jelajah Sarawak",
            "amount": 120.50,
            "liters": 45.50,
            "station": "Petronas Sibu",
            "vehicle_details": {
                "id": 6,
                "no_plat": "QSR43",
                "jenama": "Toyota",
                "model": "Alphard"
            },
            "program_details": {
                "id": 9,
                "nama_program": "Program Jelajah Sarawak",
                "lokasi_program": "Dewan Suarah Sibu",
                "permohonan_dari": "Mohamad Faizan Bin Abdul Rahman"
            },
            "journey_details": {
                "tarikh": "2025-09-30T16:00:00.000000Z",
                "masa_keluar": "2025-10-02T07:22:21.000000Z",
                "masa_masuk": "2025-10-02T07:22:52.000000Z",
                "odometer_keluar": 12345,
                "odometer_masuk": 12445,
                "jarak": 100,
                "status": "selesai"
            },
            "fuel_details": {
                "kos_minyak": 120.50,
                "liter_minyak": 45.50,
                "stesen_minyak": "Petronas Sibu",
                "resit_minyak": "/storage/fuel_receipts/ghi789.jpg"
            },
            "images": {
                "foto_odometer_keluar": "/storage/odometer_photos/abc123.jpg",
                "foto_odometer_masuk": "/storage/odometer_photos/def456.jpg",
                "resit_minyak": "/storage/fuel_receipts/ghi789.jpg"
            }
        }
    ],
    "total_cost": 520.50,
    "total_liters": 185.50
}</code></pre>
                        </div>

                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mt-4">
                            <p class="text-sm text-blue-800"><strong>💡 Tips:</strong> Response includes total_cost and total_liters for easy summary calculation.</p>
                        </div>
                    </div>

                    {{-- Report Driver Content --}}
                    <div id="content-report-driver" class="content-section hidden">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="px-2 py-1 bg-green-600 text-white text-xs font-semibold rounded">GET</span>
                            /api/reports/driver
                        </h3>
                        
                        <p class="text-gray-600 mb-4">Retrieve driver statistics including all trips with check-in/check-out details, fuel costs, and distances per program.</p>
                        
                        <h4 class="font-semibold text-gray-900 mb-3">Request Headers:</h4>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4">
                            <pre class="text-sm"><code>X-API-Key: YOUR_GLOBAL_API_KEY
Authorization: Bearer USER_SANCTUM_TOKEN
Origin: YOUR_ALLOWED_ORIGIN
Content-Type: application/json</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Query Parameters (Optional):</h4>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4">
                            <pre class="text-sm"><code>date_from: 2025-10-01  (Format: YYYY-MM-DD)
date_to: 2025-10-31    (Format: YYYY-MM-DD)</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Success Response (200 OK):</h4>
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200 mb-4">
                            <pre class="text-sm text-green-900"><code>{
    "success": true,
    "data": [
        {
            "program_id": 9,
            "program_name": "Program Jelajah Sarawak",
            "program_location": "Dewan Suarah Sibu",
            "permohonan_dari": "Mohamad Faizan Bin Abdul Rahman",
            "total_trips": 11,
            "check_out_count": 11,
            "check_in_count": 11,
            "completed_count": 11,
            "total_distance": 943,
            "total_fuel_cost": 520.50,
            "status": "selesai",
            "trips": [
                {
                    "id": 9,
                    "tarikh": "2025-09-30T16:00:00.000000Z",
                    "masa_keluar": "2025-10-02T07:22:21.000000Z",
                    "masa_masuk": "2025-10-02T07:22:52.000000Z",
                    "odometer_keluar": 12345,
                    "odometer_masuk": 12445,
                    "jarak": 100,
                    "status": "selesai",
                    "kenderaan": "QSR43",
                    "kos_minyak": 120.50,
                    "liter_minyak": 45.50
                },
                {
                    "id": 10,
                    "tarikh": "2025-09-30T16:00:00.000000Z",
                    "masa_keluar": "2025-10-02T08:42:00.000000Z",
                    "masa_masuk": "2025-10-02T09:03:00.000000Z",
                    "odometer_keluar": 10000,
                    "odometer_masuk": 10100,
                    "jarak": 100,
                    "status": "selesai",
                    "kenderaan": "QSR43",
                    "kos_minyak": null,
                    "liter_minyak": null
                }
            ]
        }
    ],
    "summary": {
        "total_programs": 1,
        "total_trips": 11,
        "completed_trips": 11,
        "total_distance": 943,
        "total_fuel_cost": 520.50,
        "active_trips": 0
    }
}</code></pre>
                        </div>

                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mt-4">
                            <p class="text-sm text-blue-800"><strong>💡 Tips:</strong> Response includes detailed trips array showing ALL check-in/check-out with timestamp and fuel data. Summary section provides quick overview.</p>
                        </div>
                    </div>

                    {{-- Dashboard Statistics Content --}}
                    <div id="content-dashboard-stats" class="content-section hidden">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="px-2 py-1 bg-green-600 text-white text-xs font-semibold rounded">GET</span>
                            /api/dashboard/statistics
                        </h3>
                        
                        <p class="text-gray-600 mb-4">Retrieve dashboard statistics for the authenticated driver, including current month data and comparison with last month.</p>
                        
                        <h4 class="font-semibold text-gray-900 mb-3">Request Headers:</h4>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4">
                            <pre class="text-sm"><code>X-API-Key: YOUR_GLOBAL_API_KEY
Authorization: Bearer USER_SANCTUM_TOKEN
Origin: YOUR_ALLOWED_ORIGIN
Content-Type: application/json</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Success Response (200 OK):</h4>
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200 mb-4">
                            <pre class="text-sm text-green-900"><code>{
    "success": true,
    "data": {
        "total_trips": 11,
        "total_trips_change": 100.0,
        "total_distance": 943.0,
        "total_distance_change": 100.0,
        "fuel_cost": 550.50,
        "fuel_cost_change": 100.0,
        "maintenance_cost": 300.00,
        "maintenance_change": 100.0,
        "parking_cost": 0.00,
        "parking_change": 0.0,
        "fnb_cost": 0.00,
        "fnb_change": 0.0,
        "accommodation_cost": 0.00,
        "accommodation_change": 0.0,
        "others_cost": 0.00,
        "others_change": 0.0,
        "current_month": "October 2025",
        "last_month": "September 2025"
    }
}</code></pre>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3">Response Fields:</h4>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4">
                            <ul class="text-sm space-y-2">
                                <li><strong>total_trips:</strong> Total completed trips in current month</li>
                                <li><strong>total_trips_change:</strong> Percentage change from last month (positive = increase, negative = decrease)</li>
                                <li><strong>total_distance:</strong> Total distance traveled in KM for current month</li>
                                <li><strong>total_distance_change:</strong> Percentage change from last month</li>
                                <li><strong>fuel_cost:</strong> Total fuel cost in RM (COMBINED: End Journey direct + Fuel claims)</li>
                                <li><strong>fuel_cost_change:</strong> Percentage change from last month</li>
                                <li><strong>maintenance_cost:</strong> Total maintenance claims (pending + approved) in RM</li>
                                <li><strong>maintenance_change:</strong> Percentage change from last month</li>
                                <li><strong>parking_cost:</strong> Total parking claims (pending + approved) in RM</li>
                                <li><strong>parking_change:</strong> Percentage change from last month</li>
                                <li><strong>fnb_cost:</strong> Total F&B claims (pending + approved) in RM</li>
                                <li><strong>fnb_change:</strong> Percentage change from last month</li>
                                <li><strong>accommodation_cost:</strong> Total accommodation claims (pending + approved) in RM</li>
                                <li><strong>accommodation_change:</strong> Percentage change from last month</li>
                                <li><strong>others_cost:</strong> Total other claims (pending + approved) in RM</li>
                                <li><strong>others_change:</strong> Percentage change from last month</li>
                                <li><strong>current_month:</strong> Current month name (e.g., "October 2025")</li>
                                <li><strong>last_month:</strong> Last month name (e.g., "September 2025")</li>
                            </ul>
                        </div>

                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mt-4">
                            <p class="text-sm text-blue-800"><strong>💡 Tips:</strong> Perfect for Overview/Dashboard screens in mobile apps. Percentage changes help visualize trends. If no data exists for last month, change will be 100% (new data). All claim categories (Fuel, Maintenance, Parking, F&B, Accommodation, Others) include both <strong>pending</strong> and <strong>approved</strong> claims. Note: "Tol" category is excluded as Sarawak doesn't have toll roads.</p>
                        </div>
                    </div>

                    {{-- App Information Content --}}
                    <div id="content-app-info" class="content-section hidden">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="px-2 py-1 bg-green-600 text-white text-xs font-semibold rounded">GET</span>
                            /api/app-info
                        </h3>
                        
                        <p class="text-gray-600 mb-4">Get application information including version, organization details, and contact information. <strong>This is a public endpoint</strong> (no authentication required).</p>

                        <div class="bg-white border border-gray-200 rounded-lg p-4 mb-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Headers (Required)</h4>
                            <div class="bg-gray-50 p-3 rounded">
                                <code class="text-sm">X-API-Key: {your_api_key}</code><br>
                                <code class="text-sm">Origin: http://localhost</code>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-lg p-4 mb-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Success Response (200)</h4>
                            <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>{
  "success": true,
  "data": {
    "app_name": "JARA Mobile App",
    "system_full_name": "JARA (Jejak Aset & Rekod Automotif)",
    "version": "1.6.0",
    "build_number": 160,
    "release_date": "01 October 2025",
    "organization": "RISDA",
    "department": "RISDA Bahagian Sibu",
    "address": {...},
    "phone": ["084-344712", "084-344713"],
    "email": "prbsibu@risda.gov.my",
    "backend_url": "https://jara.my",
    "website_url": "https://www.jara.com.my",
    "supported_platforms": ["Android", "iOS"],
    "copyright": "© 1973 - 2025 RISDA"
  }
}</code></pre>
                        </div>

                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mt-4">
                            <p class="text-sm text-blue-800"><strong>💡 Tips:</strong> Version and release date are automatically synced from Nota Keluaran. Copyright year is dynamic (current year).</p>
                        </div>
                    </div>

                    {{-- Privacy Policy Content --}}
                    <div id="content-privacy-policy" class="content-section hidden">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="px-2 py-1 bg-green-600 text-white text-xs font-semibold rounded">GET</span>
                            /api/privacy-policy
                        </h3>
                        
                        <p class="text-gray-600 mb-4">Get privacy policy content for the mobile app. <strong>This is a public endpoint</strong> (no authentication required).</p>

                        <div class="bg-white border border-gray-200 rounded-lg p-4 mb-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Headers (Required)</h4>
                            <div class="bg-gray-50 p-3 rounded">
                                <code class="text-sm">X-API-Key: {your_api_key}</code><br>
                                <code class="text-sm">Origin: http://localhost</code>
                            </div>
                        </div>

                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mt-4">
                            <p class="text-sm text-blue-800"><strong>💡 Tips:</strong> Response includes title, effective_date, last_updated, sections array, and acknowledgment. Sections may contain heading, content, list, footer, and contact fields.</p>
                        </div>
                    </div>

                    {{-- Chart Overview Content --}}
                    <div id="content-chart-overview" class="content-section hidden">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="px-2 py-1 bg-green-600 text-white text-xs font-semibold rounded">GET</span>
                            /api/chart/overview
                        </h3>
                        
                        <p class="text-gray-600 mb-4">Get overview chart data showing Fuel Cost vs Total Claims over time.</p>

                        <div class="bg-white border border-gray-200 rounded-lg p-4 mb-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Query Parameters (Optional)</h4>
                            <div class="bg-gray-50 p-3 rounded">
                                <code class="text-sm font-semibold">period</code>: <code>1month</code> or <code>6months</code> (default)
                            </div>
                        </div>

                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mt-4">
                            <p class="text-sm text-blue-800"><strong>💡 Tips:</strong> Returns chart_data array with period, label, fuel_cost, and claims. Use for Overview tab analytics.</p>
                        </div>
                    </div>

                    {{-- Chart Do Activity Content --}}
                    <div id="content-chart-do-activity" class="content-section hidden">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="px-2 py-1 bg-green-600 text-white text-xs font-semibold rounded">GET</span>
                            /api/chart/do-activity
                        </h3>
                        
                        <p class="text-gray-600 mb-4">Get Do tab chart data showing Start Journey vs End Journey counts over time.</p>

                        <div class="bg-white border border-gray-200 rounded-lg p-4 mb-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Query Parameters (Optional)</h4>
                            <div class="bg-gray-50 p-3 rounded">
                                <code class="text-sm font-semibold">period</code>: <code>1month</code> or <code>6months</code> (default)
                            </div>
                        </div>

                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mt-4">
                            <p class="text-sm text-blue-800"><strong>💡 Tips:</strong> Returns chart_data array with period, label, start_journey, and end_journey counts. Use for Do tab trip activity analytics.</p>
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
    </script>
    @endpush
</x-dashboard-layout>
