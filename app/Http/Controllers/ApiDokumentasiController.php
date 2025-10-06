<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiDokumentasiController extends Controller
{
    /**
     * Display API documentation page
     */
    public function index()
    {
        // API endpoints grouped by module
        $apiEndpoints = [
            'authentication' => [
                'title' => 'Authentication',
                'description' => 'Endpoints untuk pengesahan pengguna dan pengurusan token',
                'endpoints' => [
                    [
                        'method' => 'POST',
                        'path' => '/api/auth/login',
                        'description' => 'Login dan dapatkan access token',
                        'status' => 'completed',
                        'version' => 'v1.0',
                        'last_updated' => '2025-10-01',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/api/auth/logout',
                        'description' => 'Logout dan batalkan access token',
                        'status' => 'completed',
                        'version' => 'v1.0',
                        'last_updated' => '2025-10-01',
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/api/auth/user',
                        'description' => 'Dapatkan maklumat pengguna semasa',
                        'status' => 'completed',
                        'version' => 'v1.0',
                        'last_updated' => '2025-10-01',
                    ],
                ],
            ],
            'log_pemandu' => [
                'title' => 'Log Pemandu',
                'description' => 'Endpoints untuk pengurusan log pemandu (Start/End Journey)',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'path' => '/api/log-pemandu',
                        'description' => 'Dapatkan senarai semua log pemandu',
                        'status' => 'completed',
                        'version' => 'v1.0',
                        'last_updated' => '2025-10-02',
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/api/log-pemandu/active',
                        'description' => 'Dapatkan log perjalanan aktif (dalam_perjalanan)',
                        'status' => 'completed',
                        'version' => 'v1.0',
                        'last_updated' => '2025-10-02',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/api/log-pemandu/start',
                        'description' => 'Mulakan perjalanan baru (Start Journey)',
                        'status' => 'completed',
                        'version' => 'v1.0',
                        'last_updated' => '2025-10-02',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/api/log-pemandu/{id}/end',
                        'description' => 'Tamatkan perjalanan (End Journey)',
                        'status' => 'completed',
                        'version' => 'v1.0',
                        'last_updated' => '2025-10-02',
                    ],
                ],
            ],
            'program' => [
                'title' => 'Program',
                'description' => 'Endpoints untuk pengurusan program pemandu',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'path' => '/api/programs',
                        'description' => 'Dapatkan senarai program (current/ongoing/past)',
                        'status' => 'completed',
                        'version' => 'v1.0',
                        'last_updated' => '2025-10-02',
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/api/programs/{id}',
                        'description' => 'Dapatkan butiran program tertentu',
                        'status' => 'completed',
                        'version' => 'v1.0',
                        'last_updated' => '2025-10-02',
                    ],
                ],
            ],
            'kenderaan' => [
                'title' => 'Kenderaan',
                'description' => 'Endpoints untuk pengurusan kenderaan',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'path' => '/api/kenderaan',
                        'description' => 'Dapatkan senarai kenderaan',
                        'status' => 'planned',
                        'version' => 'v1.0',
                        'last_updated' => null,
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/api/kenderaan/{id}',
                        'description' => 'Dapatkan maklumat kenderaan',
                        'status' => 'planned',
                        'version' => 'v1.0',
                        'last_updated' => null,
                    ],
                ],
            ],
            'tuntutan' => [
                'title' => 'Tuntutan (Claims)',
                'description' => 'Endpoints untuk pengurusan tuntutan pemandu',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'path' => '/api/tuntutan',
                        'description' => 'Dapatkan senarai tuntutan pemandu',
                        'status' => 'completed',
                        'version' => 'v1.0',
                        'last_updated' => '2025-10-02',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/api/tuntutan',
                        'description' => 'Cipta tuntutan baru',
                        'status' => 'completed',
                        'version' => 'v1.0',
                        'last_updated' => '2025-10-02',
                    ],
                    [
                        'method' => 'PUT',
                        'path' => '/api/tuntutan/{id}',
                        'description' => 'Kemaskini tuntutan (edit & resubmit if ditolak)',
                        'status' => 'completed',
                        'version' => 'v1.0',
                        'last_updated' => '2025-10-02',
                    ],
                    [
                        'method' => 'DELETE',
                        'path' => '/api/tuntutan/{id}',
                        'description' => 'Padam tuntutan',
                        'status' => 'completed',
                        'version' => 'v1.0',
                        'last_updated' => '2025-10-02',
                    ],
                ],
            ],
            'notifications' => [
                'title' => 'Notifications',
                'description' => 'Endpoints untuk pengurusan notifikasi dan FCM',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'path' => '/api/notifications',
                        'description' => 'Dapatkan senarai notifikasi',
                        'status' => 'completed',
                        'version' => 'v1.0',
                        'last_updated' => '2025-10-02',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/api/notifications/{id}/mark-as-read',
                        'description' => 'Tandakan notifikasi sebagai dibaca',
                        'status' => 'completed',
                        'version' => 'v1.0',
                        'last_updated' => '2025-10-02',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/api/notifications/mark-all-as-read',
                        'description' => 'Tandakan semua notifikasi sebagai dibaca',
                        'status' => 'completed',
                        'version' => 'v1.0',
                        'last_updated' => '2025-10-02',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/api/notifications/register-token',
                        'description' => 'Daftar FCM token untuk push notification',
                        'status' => 'completed',
                        'version' => 'v1.0',
                        'last_updated' => '2025-10-02',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/api/notifications/remove-token',
                        'description' => 'Buang FCM token (logout)',
                        'status' => 'completed',
                        'version' => 'v1.0',
                        'last_updated' => '2025-10-02',
                    ],
                ],
            ],
            'support_ticketing' => [
                'title' => 'Support Ticketing',
                'description' => 'Endpoints untuk sistem tiket sokongan dengan real-time chat',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'path' => '/api/support/tickets',
                        'description' => 'Dapatkan senarai tiket untuk pemandu',
                        'status' => 'completed',
                        'version' => 'v2.0',
                        'last_updated' => '2025-10-06',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/api/support/tickets',
                        'description' => 'Buat tiket sokongan baru dari Android',
                        'status' => 'completed',
                        'version' => 'v2.0',
                        'last_updated' => '2025-10-06',
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/api/support/tickets/{id}',
                        'description' => 'Dapatkan butiran tiket dengan semua messages',
                        'status' => 'completed',
                        'version' => 'v2.0',
                        'last_updated' => '2025-10-06',
                    ],
                    [
                        'method' => 'DELETE',
                        'path' => '/api/support/tickets/{id}',
                        'description' => 'Padam tiket (hanya status baru, creator sahaja)',
                        'status' => 'coming_soon',
                        'version' => 'v2.0',
                        'last_updated' => '2025-10-06',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/api/support/tickets/{id}/messages',
                        'description' => 'Hantar mesej/balasan dalam tiket',
                        'status' => 'completed',
                        'version' => 'v2.0',
                        'last_updated' => '2025-10-06',
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/api/support/tickets/{id}/messages',
                        'description' => 'Dapatkan semua mesej (untuk real-time sync)',
                        'status' => 'completed',
                        'version' => 'v2.0',
                        'last_updated' => '2025-10-06',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/api/support/tickets/{id}/attachments',
                        'description' => 'Upload fail lampiran ke tiket',
                        'status' => 'completed',
                        'version' => 'v2.0',
                        'last_updated' => '2025-10-06',
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/api/support/attachments/{path}',
                        'description' => 'Download/preview fail lampiran',
                        'status' => 'completed',
                        'version' => 'v2.0',
                        'last_updated' => '2025-10-06',
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/api/support/tickets/{id}/status',
                        'description' => 'Semak status tiket (untuk polling/sync)',
                        'status' => 'completed',
                        'version' => 'v2.0',
                        'last_updated' => '2025-10-06',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/api/support/tickets/{id}/reopen',
                        'description' => 'Buka semula tiket yang ditutup (creator sahaja)',
                        'status' => 'completed',
                        'version' => 'v2.0',
                        'last_updated' => '2025-10-06',
                    ],
                ],
            ],
            'profile' => [
                'title' => 'Profile & Settings',
                'description' => 'Endpoints untuk pengurusan profil pengguna',
                'endpoints' => [
                    [
                        'method' => 'PUT',
                        'path' => '/api/profile/update',
                        'description' => 'Kemaskini profil pengguna (gambar profil & password)',
                        'status' => 'completed',
                        'version' => 'v1.0',
                        'last_updated' => '2025-10-02',
                    ],
                ],
            ],
            'about' => [
                'title' => 'About & Info',
                'description' => 'Endpoints untuk maklumat aplikasi',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'path' => '/api/about',
                        'description' => 'Dapatkan maklumat aplikasi JARA',
                        'status' => 'completed',
                        'version' => 'v1.0',
                        'last_updated' => '2025-10-02',
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/api/privacy-policy',
                        'description' => 'Dapatkan Privacy Policy',
                        'status' => 'completed',
                        'version' => 'v1.0',
                        'last_updated' => '2025-10-02',
                    ],
                ],
            ],
            'charts' => [
                'title' => 'Charts & Analytics',
                'description' => 'Endpoints untuk chart data (Overview & Do tab)',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'path' => '/api/chart/overview',
                        'description' => 'Dapatkan data chart Overview (Fuel & Claims per month)',
                        'status' => 'completed',
                        'version' => 'v1.0',
                        'last_updated' => '2025-10-02',
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/api/chart/do',
                        'description' => 'Dapatkan data chart Do tab (Journey stats)',
                        'status' => 'completed',
                        'version' => 'v1.0',
                        'last_updated' => '2025-10-02',
                    ],
                ],
            ],
        ];

        return view('help.api-dokumentasi', [
            'apiEndpoints' => $apiEndpoints,
        ]);
    }

    /**
     * Show details of a specific API endpoint
     */
    public function show($module, $endpoint)
    {
        // This will show detailed documentation for a specific endpoint
        return view('help.api-endpoint-detail', [
            'module' => $module,
            'endpoint' => $endpoint,
        ]);
    }
}

