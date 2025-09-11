<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpgradeFeature extends Model
{
    protected $fillable = [
        'feature',
        'description',
        'group',
        'order',
        'no_access',
    ];

    protected $casts = [
        'group' => 'integer',
        'order' => 'integer',
        'no_access' => 'boolean',
    ];
}
