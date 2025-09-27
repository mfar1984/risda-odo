<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kenderaan extends Model
{
    protected $fillable = [
        'no_plat',
        'jenama',
        'model',
        'tahun',
        'no_enjin',
        'no_casis',
        'jenis_bahan_api',
        'kapasiti_muatan',
        'warna',
        'cukai_tamat_tempoh',
        'tarikh_pendaftaran',
        'status',
        'dokumen_kenderaan',
        'dicipta_oleh',
        'bahagian_id',
        'stesen_id',
    ];

    protected $casts = [
        'cukai_tamat_tempoh' => 'date',
        'tarikh_pendaftaran' => 'date',
        'dokumen_kenderaan' => 'array',
    ];

    /**
     * Get the user who created this vehicle.
     */
    public function pencipta(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicipta_oleh');
    }

    /**
     * Get the bahagian this vehicle belongs to.
     */
    public function bahagian(): BelongsTo
    {
        return $this->belongsTo(RisdaBahagian::class, 'bahagian_id');
    }

    /**
     * Get the stesen this vehicle belongs to.
     */
    public function stesen(): BelongsTo
    {
        return $this->belongsTo(RisdaStesen::class, 'stesen_id');
    }

    /**
     * Get status label in Bahasa Melayu.
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'aktif' => 'Aktif',
            'tidak_aktif' => 'Tidak Aktif',
            'penyelenggaraan' => 'Penyelenggaraan',
            default => $this->status,
        };
    }

    /**
     * Get jenis bahan api label in Bahasa Melayu.
     */
    public function getJenisBahanApiLabelAttribute()
    {
        return match($this->jenis_bahan_api) {
            'petrol' => 'Petrol',
            'diesel' => 'Diesel',
            default => $this->jenis_bahan_api,
        };
    }

    /**
     * Check if cukai is expired.
     */
    public function getIsCukaiExpiredAttribute()
    {
        return $this->cukai_tamat_tempoh < now();
    }
}
