<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailLog extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'to_email',
        'from_email',
        'subject',
        'template',
        'body',
        'status',
        'error_message',
        'sent_at',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    /**
     * User relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for sent emails.
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope for failed emails.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for queued emails.
     */
    public function scopeQueued($query)
    {
        return $query->where('status', 'queued');
    }

    /**
     * Scope for emails by template.
     */
    public function scopeByTemplate($query, string $template)
    {
        return $query->where('template', $template);
    }

    /**
     * Scope for emails to a specific recipient.
     */
    public function scopeToRecipient($query, string $email)
    {
        return $query->where('to_email', $email);
    }
}
