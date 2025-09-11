<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AskQuestion extends Model
{
    protected $fillable = [
        'full_street_address',
        'city',
        'state',
        'zip',
        'name',
        'email',
        'phone',
        'preferred_contact_method',
        'property_id',
        'question',
        'wholesale_id',
        'user_id',
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
