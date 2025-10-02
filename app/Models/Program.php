<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\LogPemandu;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class Program extends Model
{
    protected $fillable = [
        'nama_program',
        'status',
        'tarikh_mula',
        'tarikh_selesai',
        'tarikh_kelulusan',
        'tarikh_mula_aktif',
        'tarikh_sebenar_selesai',
        'lokasi_program',
        'lokasi_lat',
        'lokasi_long',
        'jarak_anggaran',
        'penerangan',
        'permohonan_dari',
        'pemandu_id',
        'kenderaan_id',
        'jenis_organisasi',
        'organisasi_id',
        'dicipta_oleh',
        'dikemaskini_oleh',
    ];

    protected $casts = [
        'tarikh_mula' => 'datetime',
        'tarikh_selesai' => 'datetime',
        'tarikh_kelulusan' => 'datetime',
        'tarikh_mula_aktif' => 'datetime',
        'tarikh_sebenar_selesai' => 'datetime',
        'jarak_anggaran' => 'decimal:2',
    ];

    /**
     * Get the staff who made the request.
     */
    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(RisdaStaf::class, 'permohonan_dari');
    }

    /**
     * Get the staff assigned as driver.
     */
    public function pemandu(): BelongsTo
    {
        return $this->belongsTo(RisdaStaf::class, 'pemandu_id');
    }

    /**
     * Get the assigned vehicle.
     */
    public function kenderaan(): BelongsTo
    {
        return $this->belongsTo(Kenderaan::class, 'kenderaan_id');
    }

    /**
     * Get all logs linked to this program.
     */
    public function logPemandu(): HasMany
    {
        return $this->hasMany(LogPemandu::class, 'program_id');
    }

    /**
     * Get the user who created this program.
     */
    public function pencipta(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicipta_oleh');
    }

    /**
     * Get the user who last updated this program.
     */
    public function pengemas_kini(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikemaskini_oleh');
    }

    /**
     * Get status label in Bahasa Melayu.
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'draf' => 'Draf',
            'lulus' => 'Lulus',
            'tolak' => 'Tolak',
            'aktif' => 'Aktif',
            'tertunda' => 'Tertunda',
            'selesai' => 'Selesai',
            default => $this->status,
        };
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            'draf' => 'bg-gray-100 text-gray-800',
            'lulus' => 'bg-green-100 text-green-800',
            'tolak' => 'bg-red-100 text-red-800',
            'aktif' => 'bg-blue-100 text-blue-800',
            'tertunda' => 'bg-yellow-100 text-yellow-800',
            'selesai' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Scope for filtering by current user's organization.
     */
    public function scopeForCurrentUser($query)
    {
        $user = auth()->user();

        // Administrator can see all programs
        if ($user->jenis_organisasi === 'semua') {
            return $query;
        }

        // Filter by user's organization
        return $query->where('jenis_organisasi', $user->jenis_organisasi)
                    ->where('organisasi_id', $user->organisasi_id);
    }

    /**
     * Get programs for current user with proper data isolation.
     */
    public static function getForCurrentUser()
    {
        return static::forCurrentUser()
                    ->with(['pemohon', 'pemandu', 'kenderaan', 'pencipta'])
                    ->orderBy('created_at', 'desc');
    }

    public static function applyListFilters($query, $request)
    {
        $statuses = Arr::wrap($request->get('status'));
        if ($statuses && count(array_filter($statuses)) > 0) {
            $query->whereIn('status', array_filter($statuses));
        }

        if ($search = $request->get('search')) {
            $query->where(function ($inner) use ($search) {
                $inner->where('nama_program', 'like', "%{$search}%")
                    ->orWhere('lokasi_program', 'like', "%{$search}%")
                    ->orWhere('penerangan', 'like', "%{$search}%")
                    ->orWhereHas('pemohon', function ($sub) use ($search) {
                        $sub->where('nama_penuh', 'like', "%{$search}%");
                    })
                    ->orWhereHas('pemandu', function ($sub) use ($search) {
                        $sub->where('nama_penuh', 'like', "%{$search}%");
                    })
                    ->orWhereHas('kenderaan', function ($sub) use ($search) {
                        $sub->where('no_plat', 'like', "%{$search}%");
                    });
            });
        }
    }

    /**
     * Relationship to fetch the staff model for the assigned driver.
     */
    public function pemanduStaf(): BelongsTo
    {
        return $this->belongsTo(RisdaStaf::class, 'pemandu_id');
    }
}
