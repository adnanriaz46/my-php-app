<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserReferralW9 extends Model
{
    protected $table = 'user_referral_w9s';
    protected $fillable = [
        'name',
        'file_url',
        'user_id',
        'approved',
        'remarks',
    ];

    protected $casts = [
        'approved' => 'boolean',
        'remarks' => 'string',
    ];

    /**
     * Get the user that owns the W9 form.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
