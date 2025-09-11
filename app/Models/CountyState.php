<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountyState extends Model
{
    use HasFactory;

    protected $fillable = [
        'county',
        'display',
        'fips',
        'state',
        'slug',
    ];

    protected $casts = [
        'county' => 'string',
        'display' => 'string',
        'fips' => 'string',
        'state' => 'string',
        'slug' => 'string',
    ];

}
