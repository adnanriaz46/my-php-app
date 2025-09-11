<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SendGridEvent extends Model
{
    protected $fillable = [
        'date_time',
        'category',
        'email',
        'event',
        'event_id',
        'machine_open',
        'message_id_full',
        'message_id',
        'reason',
        'response',
        'status',
        'timestamp',
        'type',
    ];

    protected $casts = [
        'date_time' => 'datetime:Y-m-d H:i:s',
        'timestamp' => 'datetime:Y-m-d H:i:s',
        'machine_open' => 'boolean',
    ];

    /**
     * Scope to filter by event type
     */
    public function scopeByEvent($query, $event)
    {
        return $query->where('event', $event);
    }

    /**
     * Scope to filter by email
     */
    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    /**
     * Scope to filter by message ID
     */
    public function scopeByMessageId($query, $messageId)
    {
        return $query->where('message_id', $messageId);
    }

    /**
     * Get events for a specific email address
     */
    public static function getEventsForEmail($email)
    {
        return static::where('email', $email)
            ->orderBy('timestamp', 'desc')
            ->get();
    }

    /**
     * Get events for a specific message ID
     */
    public static function getEventsForMessage($messageId)
    {
        return static::where('message_id', $messageId)
            ->orderBy('timestamp', 'desc')
            ->get();
    }
}
