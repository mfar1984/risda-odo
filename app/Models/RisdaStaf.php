<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RisdaStaf extends Model
{
    protected $fillable = [
        'no_pekerja',
        'nama_penuh',
        'no_kad_pengenalan',
        'jantina',
        'bahagian_id',
        'stesen_id',
        'jawatan',
        'no_telefon',
        'email',
        'no_fax',
        'status',
        'alamat_1',
        'alamat_2',
        'poskod',
        'bandar',
        'negeri',
        'negara',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the RISDA Bahagian that owns the staf.
     */
    public function bahagian(): BelongsTo
    {
        return $this->belongsTo(RisdaBahagian::class, 'bahagian_id');
    }

    /**
     * Get the RISDA Stesen that owns the staf.
     */
    public function stesen(): BelongsTo
    {
        return $this->belongsTo(RisdaStesen::class, 'stesen_id');
    }

    /**
     * Get full address.
     */
    public function getFullAddressAttribute(): string
    {
        $address = $this->alamat_1;
        if ($this->alamat_2) {
            $address .= ', ' . $this->alamat_2;
        }
        $address .= ', ' . $this->poskod . ' ' . $this->bandar;
        $address .= ', ' . $this->negeri . ', ' . $this->negara;

        return $address;
    }
}
