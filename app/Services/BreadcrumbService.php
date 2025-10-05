<?php

namespace App\Services;

use Illuminate\Support\Facades\Route;

class BreadcrumbService
{
    /**
     * Menu structure with breadcrumb data
     */
    protected static $menuStructure = [
        'dashboard' => [
            'name' => 'Papan Pemuka',
            'icon' => 'dashboard',
            'parent' => null,
        ],
        'program.index' => [
            'name' => 'Program',
            'icon' => 'program',
            'parent' => null,
        ],
        'program.create' => [
            'name' => 'Tambah Program',
            'icon' => null,
            'parent' => 'program.index',
        ],
        'show-program' => [
            'name' => 'Butiran Program',
            'icon' => null,
            'parent' => 'program.index',
        ],
        'edit-program' => [
            'name' => 'Kemaskini Program',
            'icon' => null,
            'parent' => 'program.index',
        ],
        'log-pemandu.index' => [
            'name' => 'Log Pemandu',
            'icon' => 'log',
            'parent' => null,
        ],
        'log-pemandu.show' => [
            'name' => 'Butiran Log',
            'icon' => null,
            'parent' => 'log-pemandu.index',
        ],
        'log-pemandu.edit' => [
            'name' => 'Kemaskini Log',
            'icon' => null,
            'parent' => 'log-pemandu.index',
        ],
        'laporan' => [
            'name' => 'Laporan',
            'icon' => 'report',
            'parent' => null,
        ],
        'laporan.senarai-program' => [
            'name' => 'Senarai Program',
            'icon' => null,
            'parent' => 'laporan',
        ],
        'laporan.senarai-program.show' => [
            'name' => 'Butiran Laporan Program',
            'icon' => null,
            'parent' => 'laporan.senarai-program',
        ],
        'laporan.laporan-kenderaan' => [
            'name' => 'Laporan Kenderaan',
            'icon' => null,
            'parent' => 'laporan',
        ],
        'laporan.laporan-kenderaan.show' => [
            'name' => 'Butiran Laporan Kenderaan',
            'icon' => null,
            'parent' => 'laporan.laporan-kenderaan',
        ],
        'laporan.laporan-kilometer' => [
            'name' => 'Laporan Kilometer',
            'icon' => null,
            'parent' => 'laporan',
        ],
        'laporan.laporan-kilometer.show' => [
            'name' => 'Butiran Laporan Kilometer',
            'icon' => null,
            'parent' => 'laporan.laporan-kilometer',
        ],
        'laporan.laporan-kos' => [
            'name' => 'Laporan Kos',
            'icon' => null,
            'parent' => 'laporan',
        ],
        'laporan.laporan-kos.show' => [
            'name' => 'Butiran Laporan Kos',
            'icon' => null,
            'parent' => 'laporan.laporan-kos',
        ],
        'laporan.laporan-pemandu' => [
            'name' => 'Laporan Pemandu',
            'icon' => null,
            'parent' => 'laporan',
        ],
        'laporan.laporan-pemandu.show' => [
            'name' => 'Butiran Laporan Pemandu',
            'icon' => null,
            'parent' => 'laporan.laporan-pemandu',
        ],
        'laporan.laporan-tuntutan' => [
            'name' => 'Laporan Tuntutan',
            'icon' => null,
            'parent' => 'laporan',
        ],
        'laporan.laporan-tuntutan.show' => [
            'name' => 'Butiran Laporan Tuntutan',
            'icon' => null,
            'parent' => 'laporan.laporan-tuntutan',
        ],
        'pengurusan' => [
            'name' => 'Pengurusan',
            'icon' => 'management',
            'parent' => null,
        ],
        'pengurusan.tetapan-umum' => [
            'name' => 'Tetapan Umum',
            'icon' => null,
            'parent' => 'pengurusan',
        ],
        'pengurusan.senarai-risda' => [
            'name' => 'Senarai RISDA',
            'icon' => null,
            'parent' => 'pengurusan',
        ],
        'pengurusan.tambah-bahagian' => [
            'name' => 'Tambah RISDA Bahagian',
            'icon' => null,
            'parent' => 'pengurusan.senarai-risda',
        ],
        'pengurusan.show-bahagian' => [
            'name' => 'Lihat RISDA Bahagian',
            'icon' => null,
            'parent' => 'pengurusan.senarai-risda',
        ],
        'pengurusan.edit-bahagian' => [
            'name' => 'Edit RISDA Bahagian',
            'icon' => null,
            'parent' => 'pengurusan.senarai-risda',
        ],
        'pengurusan.tambah-stesen' => [
            'name' => 'Tambah RISDA Stesen',
            'icon' => null,
            'parent' => 'pengurusan.senarai-risda',
        ],
        'pengurusan.show-stesen' => [
            'name' => 'Lihat RISDA Stesen',
            'icon' => null,
            'parent' => 'pengurusan.senarai-risda',
        ],
        'pengurusan.edit-stesen' => [
            'name' => 'Edit RISDA Stesen',
            'icon' => null,
            'parent' => 'pengurusan.senarai-risda',
        ],
        'pengurusan.tambah-staf' => [
            'name' => 'Tambah RISDA Staf',
            'icon' => null,
            'parent' => 'pengurusan.senarai-risda',
        ],
        'pengurusan.show-staf' => [
            'name' => 'Lihat RISDA Staf',
            'icon' => null,
            'parent' => 'pengurusan.senarai-risda',
        ],
        'pengurusan.edit-staf' => [
            'name' => 'Edit RISDA Staf',
            'icon' => null,
            'parent' => 'pengurusan.senarai-risda',
        ],
        'pengurusan.senarai-kumpulan' => [
            'name' => 'Senarai Kumpulan',
            'icon' => null,
            'parent' => 'pengurusan',
        ],
        'pengurusan.senarai-pengguna' => [
            'name' => 'Senarai Pengguna',
            'icon' => null,
            'parent' => 'pengurusan',
        ],
        'pengurusan.tambah-pengguna' => [
            'name' => 'Tambah Pengguna',
            'icon' => null,
            'parent' => 'pengurusan.senarai-pengguna',
        ],
        'pengurusan.show-pengguna' => [
            'name' => 'Lihat Pengguna',
            'icon' => null,
            'parent' => 'pengurusan.senarai-pengguna',
        ],
        'pengurusan.edit-pengguna' => [
            'name' => 'Edit Pengguna',
            'icon' => null,
            'parent' => 'pengurusan.senarai-pengguna',
        ],
        'pengurusan.senarai-pengguna' => [
            'name' => 'Senarai Pengguna',
            'icon' => null,
            'parent' => 'pengurusan',
        ],
        'pengurusan.senarai-kenderaan' => [
            'name' => 'Senarai Kenderaan',
            'icon' => null,
            'parent' => 'pengurusan',
        ],
        'pengurusan.senarai-selenggara' => [
            'name' => 'Senarai Selenggara',
            'icon' => null,
            'parent' => 'pengurusan',
        ],
        'pengurusan.integrasi' => [
            'name' => 'Integrasi',
            'icon' => null,
            'parent' => 'pengurusan',
        ],
        'pengurusan.tambah-selenggara' => [
            'name' => 'Tambah Selenggara',
            'icon' => null,
            'parent' => 'pengurusan.senarai-selenggara',
        ],
        'pengurusan.show-selenggara' => [
            'name' => 'Butiran Selenggara',
            'icon' => null,
            'parent' => 'pengurusan.senarai-selenggara',
        ],
        'pengurusan.edit-selenggara' => [
            'name' => 'Kemaskini Selenggara',
            'icon' => null,
            'parent' => 'pengurusan.senarai-selenggara',
        ],
        'pengurusan.aktiviti-log' => [
            'name' => 'Aktiviti Log',
            'icon' => null,
            'parent' => 'pengurusan',
        ],
        'pengurusan.show-aktiviti-log' => [
            'name' => 'Butiran Aktiviti',
            'icon' => null,
            'parent' => 'pengurusan.aktiviti-log',
        ],
        'pengurusan.aktiviti-log-keselamatan' => [
            'name' => 'Aktiviti Log Keselamatan',
            'icon' => null,
            'parent' => 'pengurusan',
        ],

        // Help Routes
        'help' => [
            'name' => 'Bantuan',
            'icon' => 'help',
            'parent' => null,
        ],
        'help.panduan-pengguna' => [
            'name' => 'Panduan Pengguna',
            'icon' => null,
            'parent' => 'help',
        ],
        'help.faq' => [
            'name' => 'Soalan Lazim (FAQ)',
            'icon' => null,
            'parent' => 'help',
        ],
        'help.hubungi-sokongan' => [
            'name' => 'Hubungi Sokongan',
            'icon' => null,
            'parent' => 'help',
        ],
        'help.status-sistem' => [
            'name' => 'Status Sistem',
            'icon' => null,
            'parent' => 'help',
        ],
        'help.nota-keluaran' => [
            'name' => 'Nota Keluaran',
            'icon' => null,
            'parent' => 'help',
        ],

        // User Routes
        'profile.edit' => [
            'name' => 'Profile',
            'icon' => 'profile',
            'parent' => null,
        ],
        'settings.index' => [
            'name' => 'Settings',
            'icon' => 'settings',
            'parent' => null,
        ],
    ];

    /**
     * Generate breadcrumb for given route
     */
    public static function generate($routeName)
    {
        $breadcrumbs = [];
        
        // Add home icon
        $breadcrumbs[] = [
            'type' => 'home',
            'url' => route('dashboard'),
            'name' => null,
        ];

        // If not dashboard, build breadcrumb trail
        if ($routeName !== 'dashboard') {
            $trail = self::buildTrail($routeName);

            foreach ($trail as $index => $item) {
                $isLast = $index === array_key_last($trail);

                $breadcrumbs[] = [
                    'type' => $isLast ? 'current' : 'link',
                    'url' => $isLast ? null : self::getRouteUrl($item),
                    'name' => self::$menuStructure[$item]['name'],
                ];
            }
        } else {
            // For dashboard, just show current page
            $breadcrumbs[] = [
                'type' => 'current',
                'url' => null,
                'name' => self::$menuStructure[$routeName]['name'],
            ];
        }

        return $breadcrumbs;
    }

    /**
     * Build breadcrumb trail from current route to root
     */
    protected static function buildTrail($routeName)
    {
        $trail = [];
        $current = $routeName;

        while ($current && isset(self::$menuStructure[$current])) {
            $trail[] = $current;
            $current = self::$menuStructure[$current]['parent'];
        }

        return array_reverse($trail);
    }

    /**
     * Get URL for route name
     */
    protected static function getRouteUrl($routeName)
    {
        try {
            $currentRoute = request()?->route();

            if ($currentRoute) {
                $targetRoute = Route::getRoutes()->getByName($routeName);

                if ($targetRoute) {
                    $parameterNames = array_flip($targetRoute->parameterNames());
                    $currentParameters = $currentRoute->parameters();
                    $filteredParameters = array_intersect_key($currentParameters, $parameterNames);

                    return route($routeName, $filteredParameters);
                }
            }

            return route($routeName);
        } catch (\Exception $e) {
            return '#';
        }
    }

    /**
     * Get menu structure
     */
    public static function getMenuStructure()
    {
        return self::$menuStructure;
    }

    /**
     * Add or update menu item
     */
    public static function addMenuItem($routeName, $name, $parent = null, $icon = null)
    {
        self::$menuStructure[$routeName] = [
            'name' => $name,
            'icon' => $icon,
            'parent' => $parent,
        ];
    }
}
