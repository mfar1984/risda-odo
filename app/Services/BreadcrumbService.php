<?php

namespace App\Services;

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
        'log-pemandu.index' => [
            'name' => 'Log Pemandu',
            'icon' => 'log',
            'parent' => null,
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
        'laporan.laporan-kenderaan' => [
            'name' => 'Laporan Kenderaan',
            'icon' => null,
            'parent' => 'laporan',
        ],
        'laporan.laporan-kilometer' => [
            'name' => 'Laporan Kilometer',
            'icon' => null,
            'parent' => 'laporan',
        ],
        'laporan.laporan-kos' => [
            'name' => 'Laporan Kos',
            'icon' => null,
            'parent' => 'laporan',
        ],
        'laporan.laporan-pemandu' => [
            'name' => 'Laporan Pemandu',
            'icon' => null,
            'parent' => 'laporan',
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
        'pengurusan.senarai-kenderaan' => [
            'name' => 'Senarai Kenderaan',
            'icon' => null,
            'parent' => 'pengurusan',
        ],
        'pengurusan.aktiviti-log' => [
            'name' => 'Aktiviti Log',
            'icon' => null,
            'parent' => 'pengurusan',
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
        'help.element' => [
            'name' => 'Element',
            'icon' => null,
            'parent' => 'help',
        ],
        'help.komponen' => [
            'name' => 'Komponen',
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
            
            foreach ($trail as $item) {
                $breadcrumbs[] = [
                    'type' => 'link',
                    'url' => self::getRouteUrl($item),
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
