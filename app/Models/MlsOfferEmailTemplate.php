<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MlsOfferEmailTemplate extends Model
{
    protected $table = 'mls_offer_email_templates';
    protected $fillable = [
        'body',
        'name',
        'subject',
        'user_id',
    ];
} 