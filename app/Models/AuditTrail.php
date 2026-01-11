<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class AuditTrail extends Model
{
    /**
     * Disable default timestamps since we only use created_at
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'action_type',
        'action_name',
        'url',
        'route_name',
        'http_method',
        'ip_address',
        'user_agent',
        'properties',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Action type constants
     */
    const TYPE_PAGE_VIEW = 'page_view';
    const TYPE_BUTTON_CLICK = 'button_click';
    const TYPE_FORM_SUBMIT = 'form_submit';
    const TYPE_LOGIN = 'login';
    const TYPE_LOGOUT = 'logout';
    const TYPE_LOGIN_FAILED = 'login_failed';

    /**
     * Get the user that owns the audit trail record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by user.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeDateRange(Builder $query, Carbon $from, Carbon $to): Builder
    {
        return $query->whereBetween('created_at', [
            $from->startOfDay(),
            $to->endOfDay()
        ]);
    }

    /**
     * Scope to filter by action type.
     */
    public function scopeByActionType(Builder $query, string $type): Builder
    {
        return $query->where('action_type', $type);
    }

    /**
     * Scope to get records older than specified days.
     */
    public function scopeOlderThan(Builder $query, int $days): Builder
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Get human-readable action type label.
     */
    public function getActionTypeLabelAttribute(): string
    {
        return match ($this->action_type) {
            self::TYPE_PAGE_VIEW => 'Lawatan Halaman',
            self::TYPE_BUTTON_CLICK => 'Klik Butang',
            self::TYPE_FORM_SUBMIT => 'Hantar Borang',
            self::TYPE_LOGIN => 'Log Masuk',
            self::TYPE_LOGOUT => 'Log Keluar',
            self::TYPE_LOGIN_FAILED => 'Log Masuk Gagal',
            default => ucfirst(str_replace('_', ' ', $this->action_type)),
        };
    }

    /**
     * Get action type icon.
     */
    public function getActionTypeIconAttribute(): string
    {
        return match ($this->action_type) {
            self::TYPE_PAGE_VIEW => 'visibility',
            self::TYPE_BUTTON_CLICK => 'touch_app',
            self::TYPE_FORM_SUBMIT => 'send',
            self::TYPE_LOGIN => 'login',
            self::TYPE_LOGOUT => 'logout',
            self::TYPE_LOGIN_FAILED => 'block',
            default => 'info',
        };
    }

    /**
     * Get action type color class.
     */
    public function getActionTypeColorAttribute(): string
    {
        return match ($this->action_type) {
            self::TYPE_PAGE_VIEW => 'bg-blue-100 text-blue-800',
            self::TYPE_BUTTON_CLICK => 'bg-purple-100 text-purple-800',
            self::TYPE_FORM_SUBMIT => 'bg-green-100 text-green-800',
            self::TYPE_LOGIN => 'bg-emerald-100 text-emerald-800',
            self::TYPE_LOGOUT => 'bg-gray-100 text-gray-800',
            self::TYPE_LOGIN_FAILED => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
