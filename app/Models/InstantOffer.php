<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstantOffer extends Model
{
    protected $fillable = [
        'address',
        'full_street_address',
        'city',
        'state',
        'zip',
        'property_id',
        'name',
        'email',
        'phone',
        'buyer_name_llc',
        'deposit_price',
        'offer_price',
        'preferred_closing_date',
        'note',
        'agent_name',
        'agent_email',
        'agent_commission',
        'tin',
        'assignor_name',
        'wholesale_id',
        'user_id',
    ];

    protected $casts = [
        'deposit_price' => 'decimal:2',
        'offer_price' => 'decimal:2',
        'agent_commission' => 'decimal:2',
        'preferred_closing_date' => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wholesale()
    {
        return $this->belongsTo(WholesaleProperty::class);
    }
}
