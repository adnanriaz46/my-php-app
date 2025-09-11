<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime:Y-m-d H:i:s',
            'password' => 'hashed',
        ];
    }

            // Casts for new fields
            'wholesale_enabled' => 'boolean',
            'new_user_tour' => 'array',
            'total_rating_sum' => 'float',
            'searches_count' => 'integer',
            'affiliate_eligible' => 'boolean',
            'assessment' => 'integer',
            'assessment_filled' => 'boolean',
            'email_verified' => 'boolean',
            'email_unsubscribed_global' => 'boolean',
            'email_unsubscribed_list_preference' => 'array',
            'ip_address' => 'array',
            'last_login' => 'datetime:Y-m-d H:i:s',
            'visit_counts' => 'integer',
            'subscription_period_monthly' => 'boolean',
            'subscription_start' => 'datetime:Y-m-d H:i:s',
            'subscription_end' => 'datetime:Y-m-d H:i:s',
            'subscribed_counties' => 'array',
            'company_email_verified_at' => 'datetime:Y-m-d H:i:s',
            'company_email_verified' => 'boolean',
        ];
    }

    /**
     * Get the notifications for the user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get unread notifications for the user.
     */
    public function unreadNotifications()
    {
        return $this->notifications()->unread();
    }

    /**
     * Get read notifications for the user.
     */
    public function readNotifications()
    {
        return $this->notifications()->read();
    }

    /**
     * Get notifications by type for the user.
     */
    public function notificationsByType(string $typeName)
    {
        return $this->notifications()->byType($typeName);
    }

    /**
     * Get the count of unread notifications.
     */
    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->unread()->count();
    }

    /**
     * Mark all notifications as read for the user.
     */
    public function markAllNotificationsAsRead(): int
    {
        return $this->notifications()->unread()->update([
            'read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Get the property filter histories for the user. // Search History
     */
    public function propertyFilterHistories(): HasMany
    {
        return $this->hasMany(PropertyFilterHistory::class);
    }

    /**
     * Get the property view histories for the user. // View History
     */
    public function propertyViewHistories(): HasMany
    {
        return $this->hasMany(PropertyViewHistory::class);
    }

    /**
     * Get the unlimited view histories for the user. // Unlisted View History
     */
    public function unlistedViewHistories(): HasMany
    {
        return $this->hasMany(UnlistedViewHistory::class);
    }

    /**
     * Get the saved searches for the user. // Saved Searches
     */
    public function savedSearches(): HasMany
    {
        return $this->hasMany(SavedSearch::class);
    }

    /**
     * Get the W9 forms for the user.
     */
    public function referralW9s(): HasMany
    {
        return $this->hasMany(UserReferralW9::class);
    }

    /**
     * Get the affiliates (users who signed up through this user's referral link)
     */
    public function affiliates(): HasMany
    {
        return $this->hasMany(User::class, 'affiliated_user');
    }

    /**
     * Get the eligible affiliates (non-free users who signed up through this user's referral link)
     */
    public function eligibleAffiliates(): HasMany
    {
        return $this->hasMany(User::class, 'affiliated_user')
            ->where('user_type', '!=', self::FREE);
    }

    /**
     * Get the earnings for this user
     */
    public function earnings(): HasMany
    {
        return $this->hasMany(UserReferralEarning::class);
    }



    public function getBuyBoxAttribute()
    {
        return $this->hasOne(BuyBox::class)->first() ?? null;
    }

    /**
     * Get total property views count
     */
    public function getTotalViewsAttribute()
    {
        return $this->propertyViewHistories()->count();
    }

    /**
     * Get unlisted property views count
     */
    public function getUnlistedViewsAttribute()
    {
        return $this->unlistedViewHistories()->count();
    }

    /**
     * Get property searches count
     */
    public function getSearchesCountAttribute()
    {
        return $this->propertyFilterHistories()->count();
    }

    /**
     * Get saved searches count
     */
    public function getSavedSearchesCountAttribute()
    {
        return $this->savedSearches()->count();
    }

    /**
     * Get up to 3 recent property views (no property relationship)
     */
    public function getRecentViewsAttribute()
    {
        return $this->propertyViewHistories()->latest()->take(3)->get();
    }

    /**
     * Get up to 3 recent saved searches
     */
    public function getRecentSavedSearchesAttribute()
    {
        return $this->savedSearches()->latest()->take(3)->get();
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->user_type === self::ADMIN;
    }

    /**
     * Check if user is free user
     */
    public function isFree(): bool
    {
        return $this->user_type === self::FREE;
    }

    /**
     * Check if user is premium user
     */
    public function isPremium(): bool
    {
        return $this->user_type === self::PREMIUM;
    }

    /**
     * Check if user has any of the specified types
     */
    public function hasAnyType(array $types): bool
    {
        $typeValues = array_map(function($type) {
            return constant("self::$type");
        }, $types);
        
        return in_array($this->user_type, $typeValues);
    }

    /**
     * Get user type name
     */
    public function getUserTypeName(): string
    {
        return match($this->user_type) {
            self::ADMIN => 'Admin',
            self::FREE => 'Free',
            self::PREMIUM => 'Premium',
            default => 'Unknown'
        };
    }

    /**
     * Check if company email is verified
     */
    public function hasVerifiedCompanyEmail(): bool
    {
        return !is_null($this->company_email_verified_at) && $this->company_email_verified;
    }

    /**
     * Mark company email as verified
     */
    public function markCompanyEmailAsVerified(): bool
    {
        if ($this->hasVerifiedCompanyEmail()) {
            return false;
        }

        $this->forceFill([
            'company_email_verified_at' => $this->freshTimestamp(),
            'company_email_verified' => true,
        ])->save();

        return true;
    }

    /**
     * Send company email verification notification
     * @deprecated Use EmailService directly instead
     */
    public function sendCompanyEmailVerificationNotification(): void
    {
        // This method is deprecated - use EmailService directly
        // Kept for backward compatibility
    }

    /**
     * Get company email verification URL
     */
    public function getCompanyEmailVerificationUrl(): string
    {
        return URL::signedRoute(
            'company-email.verify',
            [
                'id' => $this->getKey(),
                'hash' => sha1($this->company_email),
            ]
        );
    }

    /**
     * Send the email verification notification.
     * @deprecated Use direct EmailService instead
     */
    public function sendEmailVerificationNotification(): void
    {
        // This method is deprecated - use EmailService directly
        // Kept for backward compatibility
    }

    /**
     * Get the email verification URL for the user.
     */
    public function getEmailVerificationUrl(): string
    {
        return URL::signedRoute(
            'verification.verify',
            [
                'id' => $this->getKey(),
                'hash' => sha1($this->email),
            ]
        );
    }

    /**
     * Get the lead sources for the user.
     */
    public function leadSources(): HasMany
    {
        return $this->hasMany(LeadSource::class);
    }

    /**
     * Get the most recent lead source for the user.
     */
    public function getLatestLeadSource()
    {
        return $this->leadSources()->latest()->first();
    }

    /**
     * Get the primary lead source (first page visited before registration).
     */
    public function getPrimaryLeadSource()
    {
        return $this->leadSources()->orderBy('visited_at', 'asc')->first();
    }
}
