<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RisdaStesen extends Model
{
    protected $fillable = [
        'risda_bahagian_id',
        'nama_stesen',
        'no_telefon',
        'no_fax',
        'email',
        'status_dropdown',
        'status',
        'alamat_1',
        'alamat_2',
        'poskod',
        'bandar',
        'negeri',
        'negara',
    ];

    protected $casts = [
        'status_dropdown' => 'string',
    ];

    /**
     * Get the RISDA Bahagian that owns the Stesen.
     */
    public function risdaBahagian(): BelongsTo
    {
        return $this->belongsTo(RisdaBahagian::class);
    }
}
