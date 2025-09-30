<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class LogPemandu extends Model
{
    use HasFactory;

    protected $table = 'log_pemandu';

    protected $fillable = [
        'pemandu_id',
        'kenderaan_id',
        'program_id',
        'tarikh_perjalanan',
        'masa_keluar',
        'masa_masuk',
        'destinasi',
        'catatan',
        'odometer_keluar',
        'odometer_masuk',
        'jarak',
        'liter_minyak',
        'kos_minyak',
        'stesen_minyak',
        'resit_minyak',
        'status',
        'organisasi_id',
        'dicipta_oleh',
        'dikemaskini_oleh',
        'lokasi_checkin_lat',
        'lokasi_checkin_long',
        'lokasi_checkout_lat',
        'lokasi_checkout_long',
    ];

    protected $casts = [
        'tarikh_perjalanan' => 'date',
        'masa_keluar' => 'datetime:H:i',
        'masa_masuk' => 'datetime:H:i',
        'odometer_keluar' => 'integer',
        'odometer_masuk' => 'integer',
        'jarak' => 'integer',
        'liter_minyak' => 'decimal:2',
        'kos_minyak' => 'decimal:2',
        'lokasi_checkin_lat' => 'decimal:8',
        'lokasi_checkin_long' => 'decimal:8',
        'lokasi_checkout_lat' => 'decimal:8',
        'lokasi_checkout_long' => 'decimal:8',
    ];

    // Relationships
    public function pemandu(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemandu_id');
    }

    public function kenderaan(): BelongsTo
    {
        return $this->belongsTo(Kenderaan::class, 'kenderaan_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicipta_oleh');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikemaskini_oleh');
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'dalam_perjalanan' => 'Dalam Perjalanan',
            'selesai' => 'Selesai',
            'tertunda' => 'Tertunda',
            default => 'Tidak Diketahui'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'dalam_perjalanan' => 'blue',
            'selesai' => 'green',
            'tertunda' => 'yellow',
            default => 'gray'
        };
    }

    public function getIsSelesaiAttribute(): bool
    {
        return $this->status === 'selesai';
    }

    public function getIsDalamPerjalananAttribute(): bool
    {
        return $this->status === 'dalam_perjalanan';
    }

    public function getIsTertundaAttribute(): bool
    {
        return $this->status === 'tertunda';
    }

    public function getMasaKeluarLabelAttribute(): ?string
    {
        if (!$this->masa_keluar) {
            return null;
        }

        $tarikh = $this->tarikh_perjalanan ?? $this->created_at;
        if ($tarikh instanceof Carbon) {
            $tarikh = $tarikh->copy();
        }

        return ($tarikh ? $tarikh->format('d/m/Y') . ' ' : '') . Carbon::parse($this->masa_keluar)->format('H:i');
    }

    public function getMasaMasukLabelAttribute(): ?string
    {
        if (!$this->masa_masuk) {
            return null;
        }

        $tarikh = $this->tarikh_perjalanan ?? $this->created_at;
        if ($tarikh instanceof Carbon) {
            $tarikh = $tarikh->copy();
        }

        return ($tarikh ? $tarikh->format('d/m/Y') . ' ' : '') . Carbon::parse($this->masa_masuk)->format('H:i');
    }

    public function getOdometerKeluarLabelAttribute(): ?string
    {
        return $this->odometer_keluar ? number_format($this->odometer_keluar) . ' km' : null;
    }

    public function getOdometerMasukLabelAttribute(): ?string
    {
        return $this->odometer_masuk ? number_format($this->odometer_masuk) . ' km' : null;
    }

    public function getJarakLabelAttribute(): ?string
    {
        return $this->jarak ? number_format($this->jarak) . ' km' : null;
    }

    public function getProgramJarakAnggaranLabelAttribute(): ?string
    {
        return $this->program && $this->program->jarak_anggaran
            ? number_format($this->program->jarak_anggaran, 1) . ' km'
            : null;
    }

    public function getLokasiCheckinLabelAttribute(): ?string
    {
        if ($this->lokasi_checkin_lat && $this->lokasi_checkin_long) {
            return $this->lokasi_checkin_lat . ', ' . $this->lokasi_checkin_long;
        }

        return $this->lokasi_checkin ?? $this->destinasi;
    }

    public function getLokasiCheckoutLabelAttribute(): ?string
    {
        if ($this->lokasi_checkout_lat && $this->lokasi_checkout_long) {
            return $this->lokasi_checkout_lat . ', ' . $this->lokasi_checkout_long;
        }

        return $this->lokasi_checkout;
    }

    // Mutators
    public function setOdometerMasukAttribute($value)
    {
        $this->attributes['odometer_masuk'] = $value;
        
        // Auto-calculate jarak when odometer_masuk is set
        if ($value && $this->odometer_keluar) {
            $this->attributes['jarak'] = $value - $this->odometer_keluar;
        }
    }

    // Scopes
    public function scopeByOrganisasi($query, $organisasiId)
    {
        if ($organisasiId) {
            return $query->where('organisasi_id', $organisasiId);
        }
        return $query;
    }

    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function scopeByTarikh($query, $tarikhMula, $tarikhAkhir)
    {
        if ($tarikhMula && $tarikhAkhir) {
            return $query->whereBetween('tarikh_perjalanan', [$tarikhMula, $tarikhAkhir]);
        }

        if ($tarikhMula) {
            return $query->where('tarikh_perjalanan', '>=', $tarikhMula);
        }

        if ($tarikhAkhir) {
            return $query->where('tarikh_perjalanan', '<=', $tarikhAkhir);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->whereHas('pemandu', function ($pemandu) use ($search) {
                    $pemandu->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('kenderaan', function ($kenderaan) use ($search) {
                    $kenderaan->where('no_plat', 'like', "%{$search}%");
                })
                ->orWhere('destinasi', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    // Boot method for auto-setting organisasi_id and audit fields
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->dicipta_oleh = auth()->id();
                $model->dikemaskini_oleh = auth()->id();
                
                // Auto-set organisasi_id from authenticated user
                if (!$model->organisasi_id && auth()->user()->organisasi_id) {
                    $model->organisasi_id = auth()->user()->organisasi_id;
                }
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->dikemaskini_oleh = auth()->id();
            }
        });
    }
}
