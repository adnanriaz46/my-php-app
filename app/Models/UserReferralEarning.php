<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserReferralEarning extends Model
{
    use HasFactory;

    // Earnings statuses
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    // Earnings types
    const TYPE_SUBSCRIPTION_UPGRADE = 'subscription_upgrade';
    const TYPE_FIRST_PAYMENT = 'first_payment';
    const TYPE_RECURRING_PAYMENT = 'recurring_payment';
    const TYPE_BONUS = 'bonus';

    protected $fillable = [
        'user_id',
        'from_user_id',
        'amount',
        'description',
        'status',
        'type',
        'reference_id', // ID of the subscription/payment that triggered this earning
        'reference_type', // Type of reference (subscription, payment, etc.)
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * Get the user who earned this referral
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who generated this earning (the affiliate)
     */
    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    /**
     * Scope for pending earnings
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for approved earnings
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope for paid earnings
     */
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /**
     * Check if earning is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if earning is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if earning is paid
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Mark earning as approved
     */
    public function approve(): bool
    {
        return $this->update(['status' => self::STATUS_APPROVED]);
    }

    /**
     * Mark earning as paid
     */
    public function markAsPaid(): bool
    {
        return $this->update([
            'status' => self::STATUS_PAID,
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark earning as rejected
     */
    public function reject(string $notes = null): bool
    {
        return $this->update([
            'status' => self::STATUS_REJECTED,
            'notes' => $notes,
        ]);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            self::STATUS_APPROVED => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            self::STATUS_PAID => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
            self::STATUS_REJECTED => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            self::STATUS_CANCELLED => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
        };
    }

    /**
     * Get status display text
     */
    public function getStatusText(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_PAID => 'Paid',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_CANCELLED => 'Cancelled',
            default => 'Unknown',
        };
    }

    /**
     * Get type display text
     */
    public function getTypeText(): string
    {
        return match($this->type) {
            self::TYPE_SUBSCRIPTION_UPGRADE => 'Subscription Upgrade',
            self::TYPE_FIRST_PAYMENT => 'First Payment',
            self::TYPE_RECURRING_PAYMENT => 'Recurring Payment',
            self::TYPE_BONUS => 'Bonus',
            default => 'Unknown',
        };
    }
} 