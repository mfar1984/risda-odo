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
        'jenis_organisasi',
        'organisasi_id',
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
        if (!isset($this->kebenaran_matrix[$modul][$aksi])) {
            return false;
        }

        $permission = $this->kebenaran_matrix[$modul][$aksi];

        // Handle multiple truthy values: boolean true, string "1", integer 1, and string "true"
        return $permission === true || $permission === "1" || $permission === 1 || $permission === "true";
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
                'eksport' => false,
            ],
            'log_pemandu' => [
                'lihat' => false,
                'kemaskini' => false,
                'padam' => false,
            ],
            'log_pemandu_semua' => [
                'lihat' => false,
                'kemaskini' => false,
                'padam' => false,
            ],
            'log_pemandu_aktif' => [
                'lihat' => false,
                'kemaskini' => false,
                'padam' => false,
            ],
            'log_pemandu_selesai' => [
                'lihat' => false,
                'kemaskini' => false,
                'padam' => false,
            ],
            'log_pemandu_tertunda' => [
                'lihat' => false,
                'kemaskini' => false,
                'padam' => false,
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
            'laporan_tuntutan' => [
                'lihat' => false,
                'padam' => false,
                'terima' => false,
                'tolak' => false,
                'gantung' => false,
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
            'selenggara_kenderaan' => [
                'tambah' => false,
                'lihat' => false,
                'kemaskini' => false,
                'padam' => false,
            ],
            'integrasi' => [
                'lihat' => false,
                'kemaskini' => false,
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
            'log_pemandu_semua' => 'Log Pemandu - Semua Log',
            'log_pemandu_aktif' => 'Log Pemandu - Log Aktif',
            'log_pemandu_selesai' => 'Log Pemandu - Log Selesai',
            'log_pemandu_tertunda' => 'Log Pemandu - Log Tertunda',
            'laporan_senarai_program' => 'Laporan Senarai Program',
            'laporan_kenderaan' => 'Laporan Kenderaan',
            'laporan_kilometer' => 'Laporan Kilometer',
            'laporan_kos' => 'Laporan Kos',
            'laporan_pemandu' => 'Laporan Pemandu',
            'laporan_tuntutan' => 'Laporan Tuntutan',

            'senarai_kumpulan' => 'Senarai Kumpulan',
            'senarai_pengguna' => 'Senarai Pengguna',
            'senarai_kenderaan' => 'Senarai Kenderaan',
            'selenggara_kenderaan' => 'Selenggara Kenderaan',
            'integrasi' => 'Integrasi (Cuaca, Email)',
            'tetapan_umum' => 'Tetapan Umum',
            'aktiviti_log' => 'Aktiviti Log',
            'aktiviti_log_keselamatan' => 'Aktiviti Log Keselamatan',
        ];
    }
}
