<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserGroup extends Model
{
    protected $fillable = [
        'nama_kumpulan',
        'kebenaran_matrix',
        'keterangan',
        'status',
        'dicipta_oleh',
    ];

    protected $casts = [
        'kebenaran_matrix' => 'array',
    ];



    /**
     * Get the user who created this group.
     */
    public function pencipta(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicipta_oleh');
    }

    /**
     * Get users assigned to this group.
     */
    public function pengguna(): HasMany
    {
        return $this->hasMany(User::class, 'kumpulan_id');
    }



    /**
     * Check if group has specific permission for a module.
     */
    public function adaKebenaran($modul, $aksi)
    {
        return isset($this->kebenaran_matrix[$modul][$aksi]) && 
               $this->kebenaran_matrix[$modul][$aksi] === true;
    }

    /**
     * Get default permission matrix structure.
     */
    public static function getDefaultPermissionMatrix()
    {
        return [
            'dashboard' => [
                'lihat' => false,
            ],
            'program' => [
                'tambah' => false,
                'lihat' => false,
                'kemaskini' => false,
                'padam' => false,
                'terima' => false,
                'tolak' => false,
                'gantung' => false,
                'aktifkan' => false,
            ],
            'log_pemandu' => [
                'tambah' => false,
                'lihat' => false,
                'kemaskini' => false,
                'padam' => false,
                'terima' => false,
                'tolak' => false,
                'gantung' => false,
                'aktifkan' => false,
            ],
            'laporan_senarai_program' => [
                'lihat' => false,
                'eksport' => false,
            ],
            'laporan_kenderaan' => [
                'lihat' => false,
                'eksport' => false,
            ],
            'laporan_kilometer' => [
                'lihat' => false,
                'eksport' => false,
            ],
            'laporan_kos' => [
                'lihat' => false,
                'eksport' => false,
            ],
            'laporan_pemandu' => [
                'lihat' => false,
                'eksport' => false,
            ],

            'senarai_kumpulan' => [
                'tambah' => false,
                'lihat' => false,
                'kemaskini' => false,
                'padam' => false,
                'terima' => false,
                'tolak' => false,
                'gantung' => false,
                'aktifkan' => false,
            ],
            'senarai_pengguna' => [
                'tambah' => false,
                'lihat' => false,
                'kemaskini' => false,
                'padam' => false,
                'terima' => false,
                'tolak' => false,
                'gantung' => false,
                'aktifkan' => false,
            ],
            'senarai_kenderaan' => [
                'tambah' => false,
                'lihat' => false,
                'kemaskini' => false,
                'padam' => false,
                'terima' => false,
                'tolak' => false,
                'gantung' => false,
                'aktifkan' => false,
            ],
            'tetapan_umum' => [
                'lihat' => false,
                'kemaskini' => false,
            ],
            'aktiviti_log' => [
                'lihat' => false,
            ],
            'aktiviti_log_keselamatan' => [
                'lihat' => false,
            ],
        ];
    }

    /**
     * Get permission labels in Bahasa Melayu.
     */
    public static function getPermissionLabels()
    {
        return [
            'tambah' => 'Tambah',
            'lihat' => 'Lihat',
            'kemaskini' => 'Kemaskini',
            'padam' => 'Padam',
            'terima' => 'Terima',
            'tolak' => 'Tolak',
            'gantung' => 'Gantung',
            'aktifkan' => 'Aktifkan',
            'eksport' => 'Eksport',
        ];
    }

    /**
     * Get module labels in Bahasa Melayu.
     */
    public static function getModuleLabels()
    {
        return [
            'dashboard' => 'Dashboard',
            'program' => 'Program',
            'log_pemandu' => 'Log Pemandu',
            'laporan_senarai_program' => 'Laporan Senarai Program',
            'laporan_kenderaan' => 'Laporan Kenderaan',
            'laporan_kilometer' => 'Laporan Kilometer',
            'laporan_kos' => 'Laporan Kos',
            'laporan_pemandu' => 'Laporan Pemandu',

            'senarai_kumpulan' => 'Senarai Kumpulan',
            'senarai_pengguna' => 'Senarai Pengguna',
            'senarai_kenderaan' => 'Senarai Kenderaan',
            'tetapan_umum' => 'Tetapan Umum',
            'aktiviti_log' => 'Aktiviti Log',
            'aktiviti_log_keselamatan' => 'Aktiviti Log Keselamatan',
        ];
    }
}
