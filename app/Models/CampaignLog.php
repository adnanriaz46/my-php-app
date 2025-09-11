<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignLog extends Model
{
    protected $fillable = [
        'campaign_id',
        'recipients_ids',
        'user_id',
        'from',
        'errors',
        'sendgrid_email_id',
        'success',
    ];

    protected $casts = [
        'recipients_ids' => 'array',
        'success' => 'boolean',
    ];

    /**
     * Get the campaign that owns the log.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Get the user that owns the log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
