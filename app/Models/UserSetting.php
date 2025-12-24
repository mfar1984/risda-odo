<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    protected $fillable = [
        'user_id',
        'format_eksport',
        'format_tarikh',
        'format_masa',
        'format_nombor',
        'mata_wang',
    ];

    // Default values
    public const DEFAULT_FORMAT_EKSPORT = 'pdf';
    public const DEFAULT_FORMAT_TARIKH = 'DD/MM/YYYY';
    public const DEFAULT_FORMAT_MASA = '24';
    public const DEFAULT_FORMAT_NOMBOR = '1,234.56';
    public const DEFAULT_MATA_WANG = 'MYR';

    /**
     * Relationship: Belongs to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get or create settings for a user
     */
    public static function getOrCreateForUser($userId)
    {
        return static::firstOrCreate(
            ['user_id' => $userId],
            [
                'format_eksport' => self::DEFAULT_FORMAT_EKSPORT,
                'format_tarikh' => self::DEFAULT_FORMAT_TARIKH,
                'format_masa' => self::DEFAULT_FORMAT_MASA,
                'format_nombor' => self::DEFAULT_FORMAT_NOMBOR,
                'mata_wang' => self::DEFAULT_MATA_WANG,
            ]
        );
    }
}
