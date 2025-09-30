<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriKosSelenggara extends Model
{
    protected $table = 'kategori_kos_selenggara';

    protected $fillable = [
        'nama_kategori',
        'keterangan',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    /**
     * Get all maintenance records for this category.
     */
    public function selenggaraKenderaan(): HasMany
    {
        return $this->hasMany(SelenggaraKenderaan::class, 'kategori_kos_id');
    }

    /**
     * Scope to get only active categories.
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }
}