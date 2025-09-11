<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssessmentDripEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_response_id',
        'step',
        'subject',
        'body',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'step' => 'integer',
        'sent_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * The statuses that the drip email can have.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_OPENED = 'opened';
    const STATUS_CLICKED = 'clicked';
    const STATUS_BOUNCED = 'bounced';
    const STATUS_FAILED = 'failed';

    /**
     * Get the assessment response that owns this drip email.
     */
    public function assessmentResponse(): BelongsTo
    {
        return $this->belongsTo(AssessmentResponse::class);
    }

    /**
     * Check if the email has been sent.
     */
    public function isSent(): bool
    {
        return in_array($this->status, [self::STATUS_SENT, self::STATUS_DELIVERED, self::STATUS_OPENED, self::STATUS_CLICKED]);
    }

    /**
     * Mark the email as sent.
     */
    public function markAsSent(): void
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark the email as delivered.
     */
    public function markAsDelivered(): void
    {
        $this->update([
            'status' => self::STATUS_DELIVERED,
        ]);
    }

    /**
     * Mark the email as opened.
     */
    public function markAsOpened(): void
    {
        $this->update([
            'status' => self::STATUS_OPENED,
        ]);
    }

    /**
     * Mark the email as clicked.
     */
    public function markAsClicked(): void
    {
        $this->update([
            'status' => self::STATUS_CLICKED,
        ]);
    }

    /**
     * Mark the email as failed.
     */
    public function markAsFailed(): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
        ]);
    }

    /**
     * Scope to get pending emails.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope to get sent emails.
     */
    public function scopeSent($query)
    {
        return $query->whereIn('status', [self::STATUS_SENT, self::STATUS_DELIVERED, self::STATUS_OPENED, self::STATUS_CLICKED]);
    }

    /**
     * Scope to get failed emails.
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', [self::STATUS_BOUNCED, self::STATUS_FAILED]);
    }

    /**
     * Scope to get emails by step.
     */
    public function scopeByStep($query, int $step)
    {
        return $query->where('step', $step);
    }

    /**
     * Scope to get emails for a specific assessment response.
     */
    public function scopeForAssessment($query, int $assessmentResponseId)
    {
        return $query->where('assessment_response_id', $assessmentResponseId);
    }
}
