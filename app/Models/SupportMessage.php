<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'role',
        'message',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    /**
     * Get the ticket this message belongs to
     */
    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    /**
     * Get the user who sent this message
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get role label
     */
    public function getRoleLabelAttribute()
    {
        return match($this->role) {
            'pengguna' => 'Pengguna',
            'admin' => 'Administrator',
            'sistem' => 'Sistem',
            default => ucfirst($this->role),
        };
    }

    /**
     * Get role color
     */
    public function getRoleColorAttribute()
    {
        return match($this->role) {
            'pengguna' => 'bg-blue-100 text-blue-800',
            'admin' => 'bg-purple-100 text-purple-800',
            'sistem' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}

