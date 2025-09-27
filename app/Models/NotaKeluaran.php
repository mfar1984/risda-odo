<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotaKeluaran extends Model
{
    protected $fillable = [
        'versi',
        'nama_versi',
        'jenis_keluaran',
        'tarikh_keluaran',
        'penerangan',
        'ciri_baharu',
        'penambahbaikan',
        'pembetulan_pepijat',
        'perubahan_teknikal',
        'status',
        'is_latest',
        'urutan',
        'dicipta_oleh',
        'dikemaskini_oleh',
    ];

    protected $casts = [
        'tarikh_keluaran' => 'date',
        'ciri_baharu' => 'array',
        'penambahbaikan' => 'array',
        'pembetulan_pepijat' => 'array',
        'perubahan_teknikal' => 'array',
        'is_latest' => 'boolean',
        'urutan' => 'integer',
    ];

    /**
     * Get the user who created this release note.
     */
    public function pencipta(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicipta_oleh');
    }

    /**
     * Get the user who last updated this release note.
     */
    public function pengemas_kini(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikemaskini_oleh');
    }

    /**
     * Get the latest published version.
     */
    public static function getLatestVersion()
    {
        return static::where('status', 'published')
                    ->where('is_latest', true)
                    ->orderBy('tarikh_keluaran', 'desc')
                    ->first();
    }

    /**
     * Get the latest version number only.
     */
    public static function getLatestVersionNumber()
    {
        $latest = static::getLatestVersion();
        return $latest ? $latest->versi : '1.0.0';
    }

    /**
     * Mark this version as the latest and unmark others.
     */
    public function markAsLatest()
    {
        // Unmark all other versions
        static::where('is_latest', true)->update(['is_latest' => false]);

        // Mark this version as latest
        $this->update(['is_latest' => true]);

        // Update all TetapanUmum records with new version
        \App\Models\TetapanUmum::query()->update([
            'versi_sistem' => $this->versi,
            'dikemaskini_oleh' => auth()->id(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Get version display name.
     */
    public function getDisplayNameAttribute()
    {
        return $this->nama_versi ?? "v{$this->versi}";
    }

    /**
     * Get jenis keluaran label.
     */
    public function getJenisKeluaranLabelAttribute()
    {
        return match($this->jenis_keluaran) {
            'blue' => 'Nota Keluaran Blue',
            'green' => 'Nota Keluaran Green',
            default => $this->jenis_keluaran,
        };
    }

    /**
     * Scope for published versions only.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for latest version.
     */
    public function scopeLatest($query)
    {
        return $query->where('is_latest', true);
    }
}
