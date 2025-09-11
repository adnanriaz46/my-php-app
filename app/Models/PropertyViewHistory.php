<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyViewHistory extends Model
{
    protected $fillable = [
        'property_id',
        'geo_address',
        'street_address',
        'city',
        'state',
        'zip',
        'status',
        'platform',
        'useragent',
        'is_mobile',
        'ip',
        'ip_city',
        'ip_region',
        'ip_country',
        'user_id',
        'is_locked'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
