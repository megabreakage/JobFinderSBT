<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'phone',
        'message',
        'type',
        'status',
        'gateway',
        'message_id',
        'error_message',
        'gateway_response',
        'sent_at',
        'delivered_at',
        'failed_at',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'gateway_response' => 'array',
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    /**
     * Scope for sent SMS.
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope for delivered SMS.
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Scope for failed SMS.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for SMS by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
