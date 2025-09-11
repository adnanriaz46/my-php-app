<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignProperty extends Model
{
    protected $fillable = [
        'campaign_id',
        'property_id',
        'property_data',
    ];

    protected $casts = [
        'property_data' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
} 