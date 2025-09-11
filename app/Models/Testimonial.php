<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Testimonial extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'description',
        'email',
        'name',
        'profile_image',
        'published_date',
        'rate',
        'title',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_date' => 'datetime:Y-m-d H:i:s',
        'rate' => 'integer',
    ];

    /**
     * Scope a query to only include published testimonials.
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_date');
    }

    /**
     * Scope a query to only include testimonials with a specific rating or higher.
     */
    public function scopeWithRating($query, $rating = 4)
    {
        return $query->where('rate', '>=', $rating);
    }

    /**
     * Get the full profile image URL.
     */
    public function getProfileImageUrlAttribute()
    {
        if (empty($this->profile_image)) {
            return null;
        }

        // If it's already a full URL or data URI, return as is
        if (str_starts_with($this->profile_image, 'http') || str_starts_with($this->profile_image, 'data:')) {
            return $this->profile_image;
        }

        // If it's a relative path, prepend the base URL
        return asset($this->profile_image);
    }

    /**
     * Get the display name (fallback to initials if no name).
     */
    public function getDisplayNameAttribute()
    {
        if (!empty($this->name)) {
            return $this->name;
        }

        // Generate initials from email if no name is provided
        if (!empty($this->email)) {
            $parts = explode('@', $this->email);
            $username = $parts[0];
            return strtoupper(substr($username, 0, 2));
        }

        return 'Anonymous';
    }   
}
