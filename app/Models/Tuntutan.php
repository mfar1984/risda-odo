<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\RisdaStaf;
use App\Models\RisdaStesen;

class Tuntutan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tuntutan';

    protected $fillable = [
        'log_pemandu_id',
        'kategori',
        'jumlah',
        'keterangan',
        'resit',
        'no_resit',
        'status',
        'alasan_tolak',
        'alasan_gantung',
        'diproses_oleh',
        'tarikh_diproses',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tarikh_diproses' => 'datetime',
    ];

    /**
     * Relationship: Tuntutan belongs to LogPemandu
     */
    public function logPemandu()
    {
        return $this->belongsTo(LogPemandu::class, 'log_pemandu_id');
    }

    /**
     * Relationship: Tuntutan belongs to User (who processed it)
     */
    public function diprosesOleh()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    /**
     * Relationship: Get pemandu through logPemandu
     */
    // Get pemandu (User) through LogPemandu
    public function pemandu()
    {
        return $this->hasOneThrough(
            User::class,
            LogPemandu::class,
            'id',              // Foreign key on LogPemandu
            'id',              // Foreign key on User
            'log_pemandu_id',  // Local key on Tuntutan
            'pemandu_id'       // Local key on LogPemandu (stores user_id)
        );
    }
    
    // Accessor to get RisdaStaf details
    public function getRisdaStafAttribute()
    {
        return $this->logPemandu?->pemandu?->risdaStaf;
    }

    /**
     * Relationship: Get program through logPemandu
     */
    public function program()
    {
        return $this->hasOneThrough(
            Program::class,
            LogPemandu::class,
            'id',              // Foreign key on LogPemandu
            'id',              // Foreign key on Program
            'log_pemandu_id',  // Local key on Tuntutan
            'program_id'       // Local key on LogPemandu
        );
    }

    /**
     * Scope: Filter by current user's organization (multi-tenancy) with hierarchy support
     */
    public function scopeForCurrentUser($query)
    {
        $user = auth()->user();

        if (!$user) {
            return $query->whereRaw('1 = 0'); // No user, no data
        }

        // Administrator (jenis_organisasi = 'semua') sees all
        if ($user->jenis_organisasi === 'semua') {
            return $query;
        }

        // Filter by organization with hierarchy
        return $query->whereHas('logPemandu', function ($q) use ($user) {
            if ($user->jenis_organisasi === 'bahagian') {
                // Get all stesen IDs under this bahagian
                $stesenIds = $this->getStesenIdsForBahagian($user->organisasi_id, $user->stesen_akses_ids);
                
                $q->where(function ($inner) use ($user, $stesenIds) {
                    // Logs from bahagian directly
                    $inner->where('organisasi_id', $user->organisasi_id);
                    
                    // Logs from any stesen under this bahagian
                    if ($stesenIds->isNotEmpty()) {
                        $inner->orWhereIn('organisasi_id', $stesenIds->map(fn ($id) => (string) $id)->all());
                    }
                });
            } else {
                // Stesen user - only see logs from their stesen
                $q->where('organisasi_id', $user->organisasi_id);
            }
        });
    }

    /**
     * Get stesen IDs for a bahagian. If stesen_akses_ids is empty, returns ALL stesen under bahagian.
     */
    private function getStesenIdsForBahagian($bahagianId, $stesenAksesIds = null)
    {
        $userStesenIds = collect($stesenAksesIds ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter();

        if ($userStesenIds->isNotEmpty()) {
            return $userStesenIds;
        }

        return RisdaStesen::where('risda_bahagian_id', $bahagianId)->pluck('id');
    }

    /**
     * Scope: Filter by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Filter by kategori
     */
    public function scopeKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    /**
     * Check if tuntutan can be edited by driver
     */
    public function canBeEditedByDriver(): bool
    {
        return $this->status === 'ditolak';
    }

    /**
     * Check if tuntutan can be approved
     */
    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if tuntutan can be rejected
     */
    public function canBeRejected(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if tuntutan can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'diluluskan', 'ditolak']);
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'diluluskan' => 'green',
            'ditolak' => 'red',
            'digantung' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'diluluskan' => 'Diluluskan',
            'ditolak' => 'Ditolak',
            'digantung' => 'Digantung',
            default => 'Unknown',
        };
    }

    /**
     * Get kategori label (human-readable)
     */
    public function getKategoriLabelAttribute(): string
    {
        return match($this->kategori) {
            'tol' => 'Tol',
            'parking' => 'Parking',
            'f&b' => 'Makanan & Minuman',
            'accommodation' => 'Penginapan',
            'fuel' => 'Minyak',
            'car_maintenance' => 'Penyelenggaraan Kenderaan',
            'others' => 'Lain-lain',
            default => ucfirst($this->kategori),
        };
    }
}
