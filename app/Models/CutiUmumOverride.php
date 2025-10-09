<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CutiUmumOverride extends Model
{
    protected $table = 'cuti_umum_override';

    protected $fillable = [
        'tarikh_mula',
        'tarikh_akhir',
        'nama_cuti',
        'negeri',
        'catatan',
        'aktif',
        'dicipta_oleh',
    ];

    protected $casts = [
        'tarikh_mula' => 'date',
        'tarikh_akhir' => 'date',
        'aktif' => 'boolean',
    ];

    /**
     * Get the user who created this holiday override
     */
    public function pencipta(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicipta_oleh');
    }

    /**
     * Scope to get active holidays only
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /**
     * Scope to get holidays for a specific year
     */
    public function scopeForYear($query, $year)
    {
        return $query->whereYear('tarikh_mula', $year);
    }

    /**
     * Scope to get holidays for a specific state or all states
     */
    public function scopeForNegeri($query, $negeri)
    {
        return $query->where(function ($q) use ($negeri) {
            $q->where('negeri', 'Semua')
              ->orWhere('negeri', $negeri);
        });
    }

    /**
     * Check if a given date falls within this holiday period
     */
    public function coversDate($date): bool
    {
        $checkDate = is_string($date) ? \Carbon\Carbon::parse($date) : $date;
        return $checkDate->between($this->tarikh_mula, $this->tarikh_akhir);
    }
}
