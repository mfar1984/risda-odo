<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TetapanUmum;
use App\Models\NotaKeluaran;

class AppInfoController extends Controller
{
    /**
     * Get application information for mobile app.
     */
    public function index()
    {
        try {
            // Get latest version from Nota Keluaran
            $latestVersion = NotaKeluaran::getLatestVersionNumber();
            $latestRelease = NotaKeluaran::orderBy('urutan', 'desc')->first();

            // Get system settings (public info only)
            $settings = TetapanUmum::where('jenis_organisasi', 'semua')->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'app_name' => 'JARA Mobile App',
                    'system_full_name' => 'JARA (Jejak Aset & Rekod Automotif)',
                    'version' => $latestVersion ?? '1.0.0',
                    'build_number' => $latestRelease ? (int) str_replace('.', '', $latestVersion) : 100,
                    'release_date' => $latestRelease ? $latestRelease->tarikh_keluaran->format('d F Y') : now()->format('d F Y'),
                    
                    // Organization info
                    'organization' => 'RISDA',
                    'department' => 'RISDA Bahagian Sibu',
                    
                    // Contact & Address
                    'address' => [
                        'line1' => 'Pejabat RISDA Bahagian Sibu',
                        'line2' => '49, Lorong 51, Jalan Lau King Howe',
                        'postcode' => '96000',
                        'city' => 'Sibu',
                        'state' => 'Sarawak',
                        'country' => 'Malaysia',
                    ],
                    'phone' => ['084-344712', '084-344713'],
                    'fax' => '084-322531',
                    'email' => 'prbsibu@risda.gov.my',
                    
                    // Websites
                    'backend_url' => 'https://jara.my',
                    'website_url' => 'https://www.jara.com.my',
                    
                    // Platform support
                    'supported_platforms' => [
                        'Android',
                        'iOS',
                    ],
                    
                    // Purpose & Description
                    'purpose' => 'JARA (Jejak Aset & Rekod Automotif) is a comprehensive fleet and driver management system designed to streamline vehicle tracking, maintenance scheduling, and driver operations. The system enables digital record-keeping, real-time vehicle tracking, and automated maintenance alerts to ensure optimal fleet performance.',
                    
                    'description' => 'The JARA Mobile App empowers drivers with an efficient digital solution for managing their daily operations. Key features include:

• Digital Trip Recording - Automated trip logging with GPS tracking and odometer readings
• Smart Maintenance Tracking - Real-time vehicle condition monitoring with automated maintenance reminders
• Simplified Claims Process - Easy submission and tracking of travel expenses including fuel, accommodation, meals, and other allowances
• Overtime Calculation - Accurate computation of overtime hours beyond regular working hours
• Real-time Analytics - Comprehensive dashboards for monitoring performance and expenses
• Secure Access - Multi-tenant architecture ensuring data privacy and security

Built to enhance operational efficiency, reduce paperwork, and provide accurate insights for better fleet management decisions.',
                    
                    // Features
                    'key_features' => [
                        'Digital trip and mileage logging',
                        'Vehicle maintenance tracking and alerts',
                        'Travel claims and expense management',
                        'Automated overtime calculation',
                        'Real-time GPS tracking',
                        'Comprehensive reporting and analytics',
                        'Multi-tenant security architecture',
                        'Offline-capable mobile app',
                    ],
                    
                    // Copyright
                    'copyright' => '© 1973 - ' . now()->year . ' RISDA',
                    
                    // System info (from Tetapan Umum)
                    'system_name' => $settings->nama_sistem ?? 'JARA - Jejak Aset & Rekod Automotif',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch app information',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

