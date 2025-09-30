<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TetapanUmum extends Model
{
    protected $fillable = [
        'nama_sistem',
        'versi_sistem',
        'alamat_1',
        'alamat_2',
        'poskod',
        'bandar',
        'negeri',
        'negara',
        'maksimum_percubaan_login',
        'masa_tamat_sesi_minit',
        'jenis_organisasi',
        'organisasi_id',
        'dicipta_oleh',
        'dikemaskini_oleh',
        'operasi_jam',
        'alamat_pejabat',
        'mata_hubungan',
        'media_sosial',
        'konfigurasi_notifikasi',
        'map_provider',
        'map_api_key',
        'map_style_url',
        'map_default_lat',
        'map_default_long',
    ];

    protected $casts = [
        'maksimum_percubaan_login' => 'integer',
        'masa_tamat_sesi_minit' => 'integer',
        'operasi_jam' => 'array',
        'alamat_pejabat' => 'array',
        'mata_hubungan' => 'array',
        'media_sosial' => 'array',
        'konfigurasi_notifikasi' => 'array',
        'map_default_lat' => 'decimal:7',
        'map_default_long' => 'decimal:7',
    ];

    /**
     * Get the user who created this setting.
     */
    public function pencipta(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicipta_oleh');
    }

    /**
     * Get the user who last updated this setting.
     */
    public function pengemas_kini(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikemaskini_oleh');
    }

    /**
     * Get the organization-specific settings for current user.
     */
    public static function getForCurrentUser()
    {
        $user = auth()->user();

        // Administrator can access global settings
        if ($user->jenis_organisasi === 'semua') {
            $settings = static::where('jenis_organisasi', 'semua')->first()
                ?? static::createDefaultSettings();
        } else {
            // Regular users get their organization-specific settings
            $settings = static::where('jenis_organisasi', $user->jenis_organisasi)
                        ->where('organisasi_id', $user->organisasi_id)
                        ->first()
                ?? static::createDefaultSettingsForUser($user);
        }

        // Auto-update version if needed
        $settings->syncVersionFromNotaKeluaran();

        return $settings;
    }

    /**
     * Sync version from latest Nota Keluaran.
     */
    public function syncVersionFromNotaKeluaran()
    {
        $latestVersion = \App\Models\NotaKeluaran::getLatestVersionNumber();

        if ($this->versi_sistem !== $latestVersion) {
            $this->update([
                'versi_sistem' => $latestVersion,
                'dikemaskini_oleh' => auth()->id(),
            ]);
        }
    }

    /**
     * Create default global settings.
     */
    public static function createDefaultSettings()
    {
        $latestVersion = \App\Models\NotaKeluaran::getLatestVersionNumber();

        return static::create([
            'nama_sistem' => 'RISDA Odometer System',
            'versi_sistem' => $latestVersion,
            'negara' => 'Malaysia',
            'maksimum_percubaan_login' => 3,
            'masa_tamat_sesi_minit' => 60,
            'jenis_organisasi' => 'semua',
            'map_provider' => 'maptiler',
            'map_api_key' => null,
            'map_style_url' => null,
            'map_default_lat' => 3.139000,
            'map_default_long' => 101.686900,
            'dicipta_oleh' => auth()->id(),
        ]);
    }

    /**
     * Create default settings for specific user organization.
     */
    public static function createDefaultSettingsForUser($user)
    {
        $latestVersion = \App\Models\NotaKeluaran::getLatestVersionNumber();

        return static::create([
            'nama_sistem' => 'RISDA Odometer System',
            'versi_sistem' => $latestVersion,
            'negara' => 'Malaysia',
            'maksimum_percubaan_login' => 3,
            'masa_tamat_sesi_minit' => 60,
            'jenis_organisasi' => $user->jenis_organisasi,
            'organisasi_id' => $user->organisasi_id,
            'map_provider' => 'maptiler',
            'map_default_lat' => 3.139000,
            'map_default_long' => 101.686900,
            'dicipta_oleh' => $user->id,
        ]);
    }

    public function getDefaultLatitude(): float
    {
        return (float) ($this->map_default_lat ?? 3.139000);
    }

    public function getDefaultLongitude(): float
    {
        return (float) ($this->map_default_long ?? 101.686900);
    }
}
