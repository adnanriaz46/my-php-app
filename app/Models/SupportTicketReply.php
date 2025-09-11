<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicketReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'support_ticket_id',
        'user_id',
        'message',
        'attachments',
        'is_admin_reply',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_admin_reply' => 'boolean',
    ];

    /**
     * Get the support ticket that owns the reply.
     */
    public function supportTicket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class);
    }

    /**
     * Get the user that made the reply.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include admin replies.
     */
    public function scopeAdminReplies($query)
    {
        return $query->where('is_admin_reply', true);
    }

    /**
     * Scope a query to only include user replies.
     */
    public function scopeUserReplies($query)
    {
        return $query->where('is_admin_reply', false);
    }
} 