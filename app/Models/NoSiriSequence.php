<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoSiriSequence extends Model
{
    protected $table = 'no_siri_sequences';

    protected $fillable = [
        'key', 'prefix', 'current_number',
    ];

    public static function next(string $key = 'vehicle_usage'): string
    {
        $seq = static::firstOrCreate(['key' => $key], [
            'prefix' => 'A',
            'current_number' => 316320,
        ]);
        $seq->current_number = $seq->current_number + 1;
        $seq->save();
        return trim($seq->prefix . ' ' . $seq->current_number);
    }
}


