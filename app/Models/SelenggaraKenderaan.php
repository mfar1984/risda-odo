<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class SelenggaraKenderaan extends Model
{
    protected $table = 'selenggara_kenderaan';

    protected $fillable = [
        'kenderaan_id',
        'kategori_kos_id',
        'dilaksana_oleh',
        'jenis_organisasi',
        'organisasi_id',
        'tarikh_mula',
        'tarikh_selesai',
        'jumlah_kos',
        'keterangan',
        'tukar_minyak',
        'jangka_hayat_km',
        'fail_invois',
        'status',
    ];

    protected $casts = [
        'tarikh_mula' => 'date',
        'tarikh_selesai' => 'date',
        'jumlah_kos' => 'decimal:2',
        'tukar_minyak' => 'boolean',
        'jangka_hayat_km' => 'integer',
    ];

    /**
     * Get the vehicle associated with this maintenance record.
     */
    public function kenderaan(): BelongsTo
    {
        return $this->belongsTo(Kenderaan::class, 'kenderaan_id');
    }

    /**
     * Get the cost category for this maintenance record.
     */
    public function kategoriKos(): BelongsTo
    {
        return $this->belongsTo(KategoriKosSelenggara::class, 'kategori_kos_id');
    }

    /**
     * Get the user who performed/recorded this maintenance.
     */
    public function pelaksana(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dilaksana_oleh');
    }

    /**
     * Get the bahagian if this is bahagian-level maintenance.
     */
    public function bahagian(): BelongsTo
    {
        return $this->belongsTo(RisdaBahagian::class, 'organisasi_id');
    }

    /**
     * Get the stesen if this is stesen-level maintenance.
     */
    public function stesen(): BelongsTo
    {
        return $this->belongsTo(RisdaStesen::class, 'organisasi_id');
    }

    /**
     * Scope to filter maintenance records for the current user based on organizational access.
     */
    public function scopeForCurrentUser(Builder $query, User $user): Builder
    {
        // Administrator can see all
        if ($user->jenis_organisasi === 'semua') {
            return $query;
        }

        // Filter by organizational hierarchy
        return $query->where(function ($q) use ($user) {
            $q->where('jenis_organisasi', 'semua')
              ->orWhere(function ($sq) use ($user) {
                  if ($user->jenis_organisasi === 'bahagian') {
                      $sq->where('jenis_organisasi', 'bahagian')
                         ->where('organisasi_id', $user->organisasi_id);
                  } elseif ($user->jenis_organisasi === 'stesen') {
                      $sq->where('jenis_organisasi', 'stesen')
                         ->where('organisasi_id', $user->organisasi_id)
                         ->orWhere(function ($ssq) use ($user) {
                             // Include bahagian records if user's stesen belongs to that bahagian
                             $stesen = RisdaStesen::find($user->organisasi_id);
                             if ($stesen && $stesen->bahagian_id) {
                                 $ssq->where('jenis_organisasi', 'bahagian')
                                     ->where('organisasi_id', $stesen->bahagian_id);
                             }
                         });
                  }
              });
        });
    }

    /**
     * Get status label in Bahasa Melayu.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'dijadualkan' => 'Dijadualkan',
            'dalam_proses' => 'Dalam Proses',
            'selesai' => 'Selesai',
            default => $this->status,
        };
    }

    /**
     * Get the total number of days for maintenance.
     */
    public function getJumlahHariAttribute(): int
    {
        return $this->tarikh_mula->diffInDays($this->tarikh_selesai) + 1;
    }
}