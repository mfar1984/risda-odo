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
                'description' => 'Endpoints untuk pengurusan log pemandu (check-in/check-out)',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'path' => '/api/log-pemandu',
                        'description' => 'Dapatkan senarai log pemandu',
                        'status' => 'in_progress',
                        'version' => 'v1.0',
                        'last_updated' => '2025-10-01',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/api/log-pemandu',
                        'description' => 'Cipta log pemandu baru (Start Journey)',
                        'status' => 'planned',
                        'version' => 'v1.0',
                        'last_updated' => null,
                    ],
                    [
                        'method' => 'PUT',
                        'path' => '/api/log-pemandu/{id}',
                        'description' => 'Kemaskini log pemandu (End Journey)',
                        'status' => 'planned',
                        'version' => 'v1.0',
                        'last_updated' => null,
                    ],
                    [
                        'method' => 'PUT',
                        'path' => '/api/log-pemandu/{id}/fuel',
                        'description' => 'Tambah maklumat bahan api',
                        'status' => 'planned',
                        'version' => 'v1.0',
                        'last_updated' => null,
                    ],
                ],
            ],
            'program' => [
                'title' => 'Program',
                'description' => 'Endpoints untuk pengurusan program',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'path' => '/api/programs',
                        'description' => 'Dapatkan senarai program',
                        'status' => 'planned',
                        'version' => 'v1.0',
                        'last_updated' => null,
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/api/programs/{id}',
                        'description' => 'Dapatkan maklumat program',
                        'status' => 'planned',
                        'version' => 'v1.0',
                        'last_updated' => null,
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
                'title' => 'Tuntutan',
                'description' => 'Endpoints untuk pengurusan tuntutan',
                'endpoints' => [
                    [
                        'method' => 'POST',
                        'path' => '/api/tuntutan',
                        'description' => 'Cipta tuntutan baru',
                        'status' => 'planned',
                        'version' => 'v1.0',
                        'last_updated' => null,
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/api/tuntutan',
                        'description' => 'Dapatkan senarai tuntutan',
                        'status' => 'planned',
                        'version' => 'v1.0',
                        'last_updated' => null,
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

