<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EmailConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis_organisasi',
        'organisasi_id',
        'smtp_host',
        'smtp_port',
        'smtp_encryption',
        'smtp_authentication',
        'smtp_username',
        'smtp_password',
        'smtp_from_address',
        'smtp_from_name',
        'smtp_reply_to',
        'smtp_connection_timeout',
        'smtp_max_retries',
        'smtp_last_test',
        'smtp_test_status',
        'smtp_test_message',
        'status',
        'dicipta_oleh',
        'dikemaskini_oleh',
    ];

    protected $casts = [
        'smtp_port' => 'integer',
        'smtp_authentication' => 'boolean',
        'smtp_connection_timeout' => 'integer',
        'smtp_max_retries' => 'integer',
        'smtp_last_test' => 'datetime',
    ];

    /**
     * Get email config for current user's organisation
     */
    public static function getForCurrentUser()
    {
        $user = Auth::user();

        if (!$user) {
            return static::getGlobalConfig();
        }

        // Administrator gets global config
        if ($user->jenis_organisasi === 'semua') {
            return static::getGlobalConfig();
        }

        // Try to find organisation-specific config
        $config = static::where('jenis_organisasi', $user->jenis_organisasi)
            ->where('organisasi_id', $user->organisasi_id)
            ->where('status', 'aktif')
            ->first();

        // Fallback to global config if not found
        if (!$config) {
            $config = static::getGlobalConfig();
        }

        return $config;
    }

    /**
     * Get global email configuration (for administrator)
     */
    public static function getGlobalConfig()
    {
        return static::where('jenis_organisasi', 'semua')
            ->where('organisasi_id', null)
            ->first();
    }

    /**
     * Encrypt SMTP password before saving
     */
    public function setSmtpPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['smtp_password'] = encrypt($value);
        }
    }

    /**
     * Decrypt SMTP password when retrieving
     */
    public function getSmtpPasswordAttribute($value)
    {
        if (!$value) {
            return null;
        }

        try {
            return decrypt($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'aktif' => 'Aktif',
            'tidak_aktif' => 'Tidak Aktif',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get test status badge color
     */
    public function getTestStatusBadgeAttribute()
    {
        return match($this->smtp_test_status) {
            'success' => 'green',
            'failed' => 'red',
            'pending' => 'yellow',
            default => 'gray',
        };
    }
}
