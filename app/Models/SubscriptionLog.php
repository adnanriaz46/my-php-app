<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionLog extends Model
{
    use HasFactory;



    protected $fillable = [
        'user_id',
        'stripe_subscription_id',
        'stripe_price_id',
        'quantity',
        'is_monthly',
        'starts_at',
        'ends_at',
        'status',
        'extra',
    ];

    protected $casts = [
        'is_monthly' => 'boolean',
        'starts_at' => 'datetime:Y-m-d H:i:s',
        'ends_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * Get the user that owns the subscription log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
