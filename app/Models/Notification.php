<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'notification_type_id',
        'title',
        'message',
        'severity',
        'read',
        'read_at',
        'action_label',
        'action_url',
        'action_data',
        'related_entity_type',
        'related_entity_id',
        'metadata',
        'scheduled_at',
        'sent_at',
    ];

    protected $casts = [
        'read' => 'boolean',
        'read_at' => 'datetime:Y-m-d H:i:s',
        'action_data' => 'array',
        'metadata' => 'array',
        'scheduled_at' => 'datetime:Y-m-d H:i:s',
        'sent_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $dates = [
        'read_at',
        'scheduled_at',
        'sent_at',
    ];

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the notification type.
     */
    public function notificationType(): BelongsTo
    {
        return $this->belongsTo(NotificationType::class);
    }

    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    /**
     * Scope a query to only include read notifications.
     */
    public function scopeRead($query)
    {
        return $query->where('read', true);
    }

    /**
     * Scope a query to only include notifications by type.
     */
    public function scopeByType($query, $typeName)
    {
        return $query->whereHas('notificationType', function ($q) use ($typeName) {
            $q->where('name', $typeName);
        });
    }

    /**
     * Scope a query to only include notifications by severity.
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead(): bool
    {
        return $this->update([
            'read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Mark the notification as unread.
     */
    public function markAsUnread(): bool
    {
        return $this->update([
            'read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Get the action data as an array.
     */
    public function getActionDataAttribute($value)
    {
        return $value ? json_decode($value, true) : null;
    }

    /**
     * Set the action data as JSON.
     */
    public function setActionDataAttribute($value)
    {
        $this->attributes['action_data'] = $value ? json_encode($value) : null;
    }

    /**
     * Get the metadata as an array.
     */
    public function getMetadataAttribute($value)
    {
        return $value ? json_decode($value, true) : null;
    }

    /**
     * Set the metadata as JSON.
     */
    public function setMetadataAttribute($value)
    {
        $this->attributes['metadata'] = $value ? json_encode($value) : null;
    }

    /**
     * Check if the notification has an action.
     */
    public function hasAction(): bool
    {
        return !empty($this->action_label);
    }

    /**
     * Check if the notification is scheduled.
     */
    public function isScheduled(): bool
    {
        return !is_null($this->scheduled_at);
    }

    /**
     * Check if the notification has been sent.
     */
    public function isSent(): bool
    {
        return !is_null($this->sent_at);
    }
}
