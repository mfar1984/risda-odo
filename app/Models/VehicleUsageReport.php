<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class VehicleUsageReport extends Model
{
    protected $table = 'vehicle_usage_reports';

    protected $fillable = [
        'no_siri', 'no_siri_from', 'no_siri_to', 'num_pages',
        'kenderaan_id', 'bulan', 'header', 'rows', 'summary', 'disimpan_oleh',
    ];

    protected $casts = [
        'header' => 'array',
        'rows' => 'array',
        'summary' => 'array',
        'bulan' => 'date:Y-m-d',
        'num_pages' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'disimpan_oleh');
    }
}


