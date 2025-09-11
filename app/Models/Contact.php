<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'price_range',
        'tags',
        'counties',
        'zip',
        'deal_type',
        'user_id',
        'email_type',
        'first_name',
        'last_name',
    ];

    protected $casts = [
        'price_range' => 'array',
        'tags' => 'array',
        'counties' => 'array',
        'zip' => 'array',
        'deal_type' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Add these methods after the existing code (around line 37)
    public function hasContactInfo()
    {
        return (!empty($this->phone) && $this->phone !== 'Not Found') ||
            (!empty($this->email) && $this->email !== 'Not Found');
    }

    public function getSkipTracedTagClass()
    {
        if ($this->hasContactInfo()) {
            return 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900 dark:text-green-200 dark:border-green-700';
        } else {
            return 'bg-orange-100 text-orange-800 border-orange-200 dark:bg-orange-900 dark:text-orange-200 dark:border-orange-700';
        }
    }
}
