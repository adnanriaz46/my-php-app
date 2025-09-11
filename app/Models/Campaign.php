<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'subject',
        'email_header',
        'email_description',
        'property_id',
        'property_data',
        'status',
        'scheduled_at',
        'sent_at',
    ];

    protected $casts = [
        'property_id' => 'integer',
        'property_data' => 'array', 
        'scheduled_at' => 'datetime:Y-m-d H:i:s',
        'sent_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(CampaignRecipient::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(CampaignProperty::class);
    }
} 