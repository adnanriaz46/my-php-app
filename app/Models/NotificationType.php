<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'icon',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the notifications for this type.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Scope a query to only include active notification types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get notification type by name.
     */
    public static function findByName(string $name): ?self
    {
        return static::where('name', $name)->first();
    }

    /**
     * Get all active notification types.
     */
    public static function getActiveTypes()
    {
        return static::active()->get();
    }

    /**
     * Check if the notification type is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Get the color class for this notification type.
     */
    public function getColorClass(): string
    {
        $colorMap = [
            'blue' => 'text-blue-600 bg-blue-50 border-blue-200',
            'green' => 'text-green-600 bg-green-50 border-green-200',
            'purple' => 'text-purple-600 bg-purple-50 border-purple-200',
            'orange' => 'text-orange-600 bg-orange-50 border-orange-200',
            'indigo' => 'text-indigo-600 bg-indigo-50 border-indigo-200',
            'red' => 'text-red-600 bg-red-50 border-red-200',
        ];

        return $colorMap[$this->color] ?? 'text-gray-600 bg-gray-50 border-gray-200';
    }
}
