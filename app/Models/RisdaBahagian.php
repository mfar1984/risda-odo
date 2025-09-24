<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RisdaBahagian extends Model
{
    protected $fillable = [
        'nama_bahagian',
        'no_telefon',
        'email',
        'no_fax',
        'status',
        'status_dropdown',
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
     * Get the RISDA Stesens for the Bahagian.
     */
    public function risdaStesens(): HasMany
    {
        return $this->hasMany(RisdaStesen::class);
    }
}
