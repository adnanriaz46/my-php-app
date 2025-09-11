<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnlistedViewHistory extends Model
{

    protected $fillable = [
        'zpid',
        'address',
        'platform',
        'useragent',
        'is_mobile',
        'ip',
        'ip_city',
        'ip_region',
        'ip_country',
        'user_id',
    ];
}
