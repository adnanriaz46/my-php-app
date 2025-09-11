<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestAShowing extends Model
{
    protected $fillable = [
        'full_street_address',
        'city',
        'state',
        'zip',
        'property_id',
        'preferred_time',
        'message',
        'wholesale_id',
        'user_id',
    ];

    protected $casts = [
        'preferred_time' => 'datetime:Y-m-d H:i:s',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wholesale()
    {
        return $this->belongsTo(WholesaleProperty::class, 'wholesale_id');
    }
}
