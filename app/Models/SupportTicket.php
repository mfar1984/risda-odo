<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Spatie\Activitylog\Traits\LogsActivity; // Disabled - manual logging in controller for better control
use Spatie\Activitylog\LogOptions;

class SupportTicket extends Model
{
    use HasFactory; // LogsActivity disabled - manual logging in controller

    protected $fillable = [
        'ticket_number',
        'subject',
        'category',
        'priority',
        'status',
        'jenis_organisasi',
        'organisasi_id',
        'created_by',
        'assigned_to',
        'attachments',
        'source',
        'ip_address',
        'device',
        'platform',
        'latitude',
        'longitude',
        'last_reply_at',
        'closed_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'last_reply_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /**
     * Generate unique ticket number
     */
    public static function generateTicketNumber()
    {
        do {
            $number = 'TKT-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        } while (self::where('ticket_number', $number)->exists());

        return $number;
    }

    /**
     * Get the user who created the ticket
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the admin assigned to the ticket
     */
    public function assignedAdmin()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get all messages for this ticket
     */
    public function messages()
    {
        return $this->hasMany(SupportMessage::class, 'ticket_id')->orderBy('created_at', 'asc');
    }

    /**
     * Get all participants for this ticket
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'support_ticket_participants', 'ticket_id', 'user_id')
                    ->withPivot(['role', 'added_at', 'added_by'])
                    ->withTimestamps();
    }

    /**
     * Check if user can access this ticket
     */
    public function canBeAccessedBy(User $user)
    {
        // Administrator can access all
        if ($user->jenis_organisasi === 'semua') {
            return true;
        }

        // Creator can access
        if ($this->created_by === $user->id) {
            return true;
        }

        // Assigned person can access
        if ($this->assigned_to === $user->id) {
            return true;
        }

        // Participant can access
        if ($this->participants()->where('user_id', $user->id)->exists()) {
            return true;
        }

        // Unassigned Android tickets in same organization can be accessed
        // (So staff can view and auto-assign themselves)
        if ($this->source === 'android' && 
            $this->assigned_to === null && 
            $this->jenis_organisasi === $user->jenis_organisasi && 
            $this->organisasi_id === $user->organisasi_id) {
            return true;
        }

        return false;
    }

    /**
     * Get the bahagian if applicable
     */
    public function bahagian()
    {
        return $this->belongsTo(RisdaBahagian::class, 'organisasi_id');
    }

    /**
     * Get the stesen if applicable
     */
    public function stesen()
    {
        return $this->belongsTo(RisdaStesen::class, 'organisasi_id');
    }

    /**
     * Get organization name based on jenis_organisasi
     */
    public function getOrganizationNameAttribute()
    {
        if ($this->jenis_organisasi === 'bahagian') {
            return $this->bahagian?->nama_bahagian ?? 'N/A';
        } elseif ($this->jenis_organisasi === 'stesen') {
            return $this->stesen?->nama_stesen ?? 'N/A';
        } elseif ($this->jenis_organisasi === 'semua') {
            return 'RISDA Pusat';
        }
        return 'N/A';
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'baru' => 'bg-blue-100 text-blue-800',
            'dalam_proses' => 'bg-yellow-100 text-yellow-800',
            'dijawab' => 'bg-green-100 text-green-800',
            'ditutup' => 'bg-gray-100 text-gray-800',
            'escalated' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get priority badge color
     */
    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'rendah' => 'bg-gray-100 text-gray-800',
            'sederhana' => 'bg-blue-100 text-blue-800',
            'tinggi' => 'bg-orange-100 text-orange-800',
            'kritikal' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'baru' => 'Baru',
            'dalam_proses' => 'Dalam Proses',
            'dijawab' => 'Dijawab',
            'ditutup' => 'Ditutup',
            'escalated' => 'Escalated',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get priority label
     */
    public function getPriorityLabelAttribute()
    {
        return match($this->priority) {
            'rendah' => 'Rendah',
            'sederhana' => 'Sederhana',
            'tinggi' => 'Tinggi',
            'kritikal' => 'Kritikal',
            default => ucfirst($this->priority),
        };
    }

    /**
     * Spatie Activity Log options
     */
    // Disabled auto-logging - using manual logging in controller for better control and IP tracking
    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //         ->logOnly([
    //             'ticket_number',
    //             'subject',
    //             'category',
    //             'priority',
    //             'status',
    //             'assigned_to',
    //             'ip_address',
    //             'platform',
    //         ])
    //         ->logOnlyDirty()
    //         ->dontSubmitEmptyLogs();
    // }
}

